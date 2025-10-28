<?php
/**
 * Airtable Sync Engine
 *
 * Core sync logic for syncing Airtable records to WordPress.
 *
 * @package Airtable_Sync
 */

class Airtable_Sync_Engine {

	/**
	 * Airtable API client.
	 *
	 * @var Airtable_API
	 */
	private $api;

	/**
	 * Mapping configuration.
	 *
	 * @var array
	 */
	private $mapping;

	/**
	 * Sync statistics.
	 *
	 * @var array
	 */
	private $stats = array(
		'processed'   => 0,
		'created'     => 0,
		'updated'     => 0,
		'skipped'     => 0,
		'unpublished' => 0,
		'errors'      => 0,
	);

	/**
	 * Constructor.
	 *
	 * @param Airtable_API $api     Airtable API client.
	 * @param array        $mapping Mapping configuration.
	 */
	public function __construct( $api, $mapping ) {
		$this->api = $api;
		$this->mapping = $mapping;
	}

	/**
	 * Sync records from Airtable to WordPress.
	 *
	 * @param bool $dry_run Whether to run in dry-run mode (no changes).
	 * @return array Sync statistics.
	 */
	public function sync( $dry_run = false ) {
		// Reset stats
		$this->stats = array(
			'processed'   => 0,
			'created'     => 0,
			'updated'     => 0,
			'skipped'     => 0,
			'unpublished' => 0,
			'errors'      => 0,
		);

		// Get records from Airtable
		$records = $this->api->get_records(
			$this->mapping['table_id'],
			isset( $this->mapping['view_id'] ) ? $this->mapping['view_id'] : ''
		);

		if ( is_wp_error( $records ) ) {
			$this->log_error( 'Failed to fetch records: ' . $records->get_error_message() );
			return $this->stats;
		}

		$this->log( sprintf( 'Found %d records to sync', count( $records ) ) );

		// Track synced Airtable IDs to identify orphaned posts
		$synced_airtable_ids = array();

		// Process each record
		foreach ( $records as $record ) {
			$this->stats['processed']++;
			$airtable_id = $record['id'];
			$synced_airtable_ids[] = $airtable_id;

			$result = $this->sync_record( $record, $dry_run );

			if ( is_wp_error( $result ) ) {
				$this->stats['errors']++;
				$this->log_error( sprintf(
					'Error syncing record %s: %s',
					$record['id'],
					$result->get_error_message()
				) );
			} elseif ( $result === 'created' ) {
				$this->stats['created']++;
			} elseif ( $result === 'updated' ) {
				$this->stats['updated']++;
			} elseif ( $result === 'skipped' ) {
				$this->stats['skipped']++;
			}
		}

		// Handle orphaned posts (no longer in Airtable view)
		$this->unpublish_orphaned_posts( $synced_airtable_ids, $dry_run );

		return $this->stats;
	}

	/**
	 * Sync a single record.
	 *
	 * @param array $record  Airtable record data.
	 * @param bool  $dry_run Whether to run in dry-run mode.
	 * @return string|WP_Error 'created', 'updated', 'skipped', or WP_Error on failure.
	 */
	private function sync_record( $record, $dry_run = false ) {
		$airtable_id = $record['id'];
		$fields = isset( $record['fields'] ) ? $record['fields'] : array();

		// Check if post already exists
		$existing_post_id = $this->find_post_by_airtable_id( $airtable_id );

		// Find lastModifiedTime field (if present)
		$last_modified = $this->find_last_modified_time( $fields );

		// If post exists, check if it needs updating
		if ( $existing_post_id && $last_modified ) {
			$stored_last_modified = get_post_meta( $existing_post_id, '_airtable_last_modified', true );

			// Skip if record hasn't changed
			if ( $stored_last_modified === $last_modified ) {
				$this->log( sprintf( 'Skipped post %d (Airtable ID: %s) - no changes', $existing_post_id, $airtable_id ) );
				return 'skipped';
			}
		}

		// Prepare post data
		$post_data = array(
			'post_type'   => $this->mapping['post_type'],
			'post_status' => 'publish',
		);

		// Transform Airtable fields to WordPress fields
		$transformed = $this->transform_fields( $fields );

		// Merge core fields
		if ( isset( $transformed['core'] ) ) {
			$post_data = array_merge( $post_data, $transformed['core'] );
		}

		// Update existing or create new
		if ( $existing_post_id ) {
			$post_data['ID'] = $existing_post_id;

			if ( $dry_run ) {
				$this->log( sprintf( 'Would update post %d (Airtable ID: %s)', $existing_post_id, $airtable_id ) );
				return 'updated';
			}

			$post_id = wp_update_post( $post_data, true );
			$action = 'updated';
		} else {
			if ( $dry_run ) {
				$this->log( sprintf( 'Would create new post (Airtable ID: %s)', $airtable_id ) );
				return 'created';
			}

			$post_id = wp_insert_post( $post_data, true );
			$action = 'created';
		}

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Store Airtable ID for future syncs
		update_post_meta( $post_id, '_airtable_id', $airtable_id );
		update_post_meta( $post_id, '_airtable_last_synced', current_time( 'mysql' ) );

		// Store last modified time if available
		if ( $last_modified ) {
			update_post_meta( $post_id, '_airtable_last_modified', $last_modified );
		}

		// Sync ACF fields
		if ( isset( $transformed['acf'] ) ) {
			foreach ( $transformed['acf'] as $field_key => $value ) {
				update_field( $field_key, $value, $post_id );
			}
		}

		// Sync taxonomies
		if ( isset( $transformed['taxonomy'] ) ) {
			foreach ( $transformed['taxonomy'] as $taxonomy => $terms ) {
				wp_set_object_terms( $post_id, $terms, $taxonomy );
			}
		}

		// Sync featured image
		if ( isset( $transformed['thumbnail'] ) && $transformed['thumbnail'] ) {
			set_post_thumbnail( $post_id, $transformed['thumbnail'] );
		}

		$this->log( sprintf( '%s post %d (Airtable ID: %s)', ucfirst( $action ), $post_id, $airtable_id ) );

		return $action;
	}

	/**
	 * Transform Airtable fields to WordPress format.
	 *
	 * @param array $airtable_fields Airtable field data.
	 * @return array Transformed fields grouped by type.
	 */
	private function transform_fields( $airtable_fields ) {
		$transformed = array(
			'core'     => array(),
			'acf'      => array(),
			'taxonomy' => array(),
			'thumbnail' => null,
		);

		require_once AIRTABLE_SYNC_PLUGIN_DIR . 'includes/class-airtable-field-transformer.php';

		foreach ( $this->mapping['field_mappings'] as $field_mapping ) {
			$airtable_field_id = $field_mapping['airtable_field_id'];

			// Skip if field doesn't exist in Airtable record
			if ( ! isset( $airtable_fields[ $airtable_field_id ] ) ) {
				continue;
			}

			$value = $airtable_fields[ $airtable_field_id ];
			$destination_type = $field_mapping['destination_type'];
			$destination_key = $field_mapping['destination_key'];

			// Get field type
			$field_type = isset( $field_mapping['airtable_field_type'] ) ? $field_mapping['airtable_field_type'] : 'text';

			// Transform the value
			$transformed_value = Airtable_Field_Transformer::transform( $value, $field_type, $field_mapping );

			// Handle special case: post_thumbnail
			if ( $destination_type === 'core' && $destination_key === 'post_thumbnail' ) {
				$transformed['thumbnail'] = $transformed_value;
				continue;
			}

			// Store by destination type
			if ( $destination_type === 'core' ) {
				$transformed['core'][ $destination_key ] = $transformed_value;
			} elseif ( $destination_type === 'acf' ) {
				// Handle ACF link fields with multiple source fields
				if ( isset( $field_mapping['acf_link_property'] ) ) {
					$link_property = $field_mapping['acf_link_property'];

					// Initialize link array if it doesn't exist
					if ( ! isset( $transformed['acf'][ $destination_key ] ) ) {
						$transformed['acf'][ $destination_key ] = array(
							'url'    => '',
							'title'  => '',
							'target' => '',
						);
					}

					// Set the specific property
					$transformed['acf'][ $destination_key ][ $link_property ] = $transformed_value;
				} else {
					// Standard ACF field - direct assignment
					$transformed['acf'][ $destination_key ] = $transformed_value;
				}
			} elseif ( $destination_type === 'taxonomy' ) {
				if ( ! isset( $transformed['taxonomy'][ $destination_key ] ) ) {
					$transformed['taxonomy'][ $destination_key ] = array();
				}
				// Merge if array, otherwise add single value
				if ( is_array( $transformed_value ) ) {
					$transformed['taxonomy'][ $destination_key ] = array_merge(
						$transformed['taxonomy'][ $destination_key ],
						$transformed_value
					);
				} else {
					$transformed['taxonomy'][ $destination_key ][] = $transformed_value;
				}
			}
		}

		return $transformed;
	}

	/**
	 * Unpublish posts that are no longer in the Airtable sync results.
	 *
	 * @param array $synced_airtable_ids Array of Airtable IDs that were just synced.
	 * @param bool  $dry_run             Whether to run in dry-run mode.
	 */
	private function unpublish_orphaned_posts( $synced_airtable_ids, $dry_run = false ) {
		// Get all published posts for this post type that have an Airtable ID
		$all_synced_posts = get_posts( array(
			'post_type'      => $this->mapping['post_type'],
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_airtable_id',
					'compare' => 'EXISTS',
				),
			),
			'fields'         => 'ids',
		) );

		if ( empty( $all_synced_posts ) ) {
			return;
		}

		$this->log( sprintf( 'Checking %d published posts for orphaned records', count( $all_synced_posts ) ) );

		// Check each post to see if it's still in the sync
		foreach ( $all_synced_posts as $post_id ) {
			$airtable_id = get_post_meta( $post_id, '_airtable_id', true );

			// If this Airtable ID is not in our synced list, unpublish the post
			if ( ! in_array( $airtable_id, $synced_airtable_ids, true ) ) {
				if ( $dry_run ) {
					$this->log( sprintf( 'Would unpublish post %d (Airtable ID: %s) - no longer in view', $post_id, $airtable_id ) );
					$this->stats['unpublished']++;
				} else {
					$result = wp_update_post( array(
						'ID'          => $post_id,
						'post_status' => 'draft',
					), true );

					if ( is_wp_error( $result ) ) {
						$this->stats['errors']++;
						$this->log_error( sprintf(
							'Error unpublishing post %d: %s',
							$post_id,
							$result->get_error_message()
						) );
					} else {
						$this->stats['unpublished']++;
						$this->log( sprintf( 'Unpublished post %d (Airtable ID: %s) - no longer in view', $post_id, $airtable_id ) );
					}
				}
			}
		}
	}

	/**
	 * Find a post by Airtable ID.
	 *
	 * @param string $airtable_id Airtable record ID.
	 * @return int|null Post ID or null if not found.
	 */
	private function find_post_by_airtable_id( $airtable_id ) {
		$posts = get_posts( array(
			'post_type'      => $this->mapping['post_type'],
			'meta_key'       => '_airtable_id',
			'meta_value'     => $airtable_id,
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		) );

		return ! empty( $posts ) ? $posts[0] : null;
	}

	/**
	 * Find the lastModifiedTime value in Airtable fields.
	 *
	 * Automatically detects any field that contains a lastModifiedTime value.
	 *
	 * @param array $fields Airtable field data.
	 * @return string|null Last modified time or null if not found.
	 */
	private function find_last_modified_time( $fields ) {
		// Look through all fields for a lastModifiedTime value
		// These are typically ISO 8601 formatted date strings
		foreach ( $fields as $field_id => $value ) {
			// Check if this looks like an ISO 8601 date string
			// Format: YYYY-MM-DDTHH:MM:SS.MMMZ or similar
			if ( is_string( $value ) && preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value ) ) {
				// Verify it's a valid timestamp
				if ( strtotime( $value ) !== false ) {
					return $value;
				}
			}
		}

		return null;
	}

	/**
	 * Log a message.
	 *
	 * @param string $message Message to log.
	 */
	private function log( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $message );
		}
		do_action( 'airtable_sync_log', $message );
	}

	/**
	 * Log an error message.
	 *
	 * @param string $message Error message to log.
	 */
	private function log_error( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( $message, false );
		}
		do_action( 'airtable_sync_error', $message );
	}

	/**
	 * Get sync statistics.
	 *
	 * @return array Sync statistics.
	 */
	public function get_stats() {
		return $this->stats;
	}
}
