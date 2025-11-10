<?php
/**
 * WP-CLI commands for Airtable Sync
 *
 * @package Airtable_Sync
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

class Airtable_Sync_CLI {

	/**
	 * Sync all configured tables from Airtable to WordPress.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Run in dry-run mode (no changes will be made).
	 *
	 * ## EXAMPLES
	 *
	 *     wp airtable sync
	 *     wp airtable sync --dry-run
	 *
	 * @when after_wp_load
	 */
	public function sync( $args, $assoc_args ) {
		$dry_run = isset( $assoc_args['dry-run'] );

		if ( $dry_run ) {
			WP_CLI::warning( 'Running in DRY-RUN mode. No changes will be made.' );
		}

		// Get credentials
		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			WP_CLI::error( 'API credentials not configured. Please configure them in WP Admin > Airtable Sync.' );
			return;
		}

		// Get mappings
		$mappings = Airtable_Sync_Config::get_mappings();

		if ( empty( $mappings ) ) {
			WP_CLI::error( 'No table mappings configured. Please add mappings to config/mappings.php.' );
			return;
		}

		// Initialize API client
		$api = new Airtable_API( $credentials['api_key'], $credentials['base_id'] );

		// Sync each mapping
		$total_stats = array(
			'processed' => 0,
			'created'   => 0,
			'updated'   => 0,
			'skipped'   => 0,
			'errors'    => 0,
		);

		foreach ( $mappings as $mapping ) {
			$table_name = isset( $mapping['table_name'] ) ? $mapping['table_name'] : $mapping['table_id'];

			WP_CLI::log( '' );
			WP_CLI::log( WP_CLI::colorize( "%BSync ing table: {$table_name}%n" ) );
			WP_CLI::log( str_repeat( '=', 50 ) );

			$engine = new Airtable_Sync_Engine( $api, $mapping );
			$stats = $engine->sync( $dry_run );

			// Accumulate stats
			foreach ( $stats as $key => $value ) {
				$total_stats[ $key ] += $value;
			}

			// Display table stats
			$this->display_stats( $stats );
		}

		// Display total stats
		WP_CLI::log( '' );
		WP_CLI::log( WP_CLI::colorize( '%G' . str_repeat( '=', 50 ) . '%n' ) );
		WP_CLI::log( WP_CLI::colorize( '%GTotals:%n' ) );
		$this->display_stats( $total_stats );

		if ( $total_stats['errors'] > 0 ) {
			WP_CLI::warning( sprintf( 'Sync completed with %d error(s).', $total_stats['errors'] ) );
		} else {
			WP_CLI::success( 'Sync completed successfully!' );
		}
	}

	/**
	 * Sync a specific table from Airtable to WordPress.
	 *
	 * ## OPTIONS
	 *
	 * <table_id>
	 * : The Airtable table ID to sync.
	 *
	 * [--dry-run]
	 * : Run in dry-run mode (no changes will be made).
	 *
	 * ## EXAMPLES
	 *
	 *     wp airtable sync-table tblMObBbMdYgDLpFp
	 *     wp airtable sync-table tblMObBbMdYgDLpFp --dry-run
	 *
	 * @when after_wp_load
	 */
	public function sync_table( $args, $assoc_args ) {
		$table_id = $args[0];
		$dry_run = isset( $assoc_args['dry-run'] );

		if ( $dry_run ) {
			WP_CLI::warning( 'Running in DRY-RUN mode. No changes will be made.' );
		}

		// Get credentials
		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			WP_CLI::error( 'API credentials not configured. Please configure them in WP Admin > Airtable Sync.' );
			return;
		}

		// Find mapping for this table
		$mapping = Airtable_Sync_Config::get_mapping_by_table_id( $table_id );

		if ( ! $mapping ) {
			WP_CLI::error( sprintf( 'No mapping found for table ID: %s', $table_id ) );
			return;
		}

		$table_name = isset( $mapping['table_name'] ) ? $mapping['table_name'] : $table_id;

		WP_CLI::log( WP_CLI::colorize( "%BSyncing table: {$table_name}%n" ) );
		WP_CLI::log( str_repeat( '=', 50 ) );

		// Initialize API and sync
		$api = new Airtable_API( $credentials['api_key'], $credentials['base_id'] );
		$engine = new Airtable_Sync_Engine( $api, $mapping );
		$stats = $engine->sync( $dry_run );

		// Display stats
		$this->display_stats( $stats );

		if ( $stats['errors'] > 0 ) {
			WP_CLI::warning( sprintf( 'Sync completed with %d error(s).', $stats['errors'] ) );
		} else {
			WP_CLI::success( 'Sync completed successfully!' );
		}
	}

	/**
	 * Validate the Airtable Sync configuration.
	 *
	 * ## EXAMPLES
	 *
	 *     wp airtable validate
	 *
	 * @when after_wp_load
	 */
	public function validate( $args, $assoc_args ) {
		WP_CLI::log( 'Validating Airtable Sync configuration...' );
		WP_CLI::log( '' );

		// Check credentials
		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) ) {
			WP_CLI::error( 'API key not configured.', false );
		} else {
			WP_CLI::success( 'API key configured.' );
		}

		if ( empty( $credentials['base_id'] ) ) {
			WP_CLI::error( 'Base ID not configured.', false );
		} else {
			WP_CLI::success( 'Base ID configured.' );
		}

		// Check mappings
		$mappings = Airtable_Sync_Config::get_mappings();

		if ( empty( $mappings ) ) {
			WP_CLI::error( 'No table mappings configured.' );
			return;
		}

		WP_CLI::log( sprintf( 'Found %d table mapping(s).', count( $mappings ) ) );
		WP_CLI::log( '' );

		// Validate configuration
		$validation = Airtable_Sync_Config::validate();

		if ( $validation['valid'] ) {
			WP_CLI::success( 'Configuration is valid!' );
		} else {
			WP_CLI::error( 'Configuration has errors:', false );
			foreach ( $validation['errors'] as $error ) {
				WP_CLI::log( '  - ' . $error );
			}
			WP_CLI::error( 'Please fix the errors above.' );
		}
	}

	/**
	 * List all configured table mappings.
	 *
	 * ## EXAMPLES
	 *
	 *     wp airtable list
	 *
	 * @when after_wp_load
	 */
	public function list_mappings( $args, $assoc_args ) {
		$mappings = Airtable_Sync_Config::get_mappings();

		if ( empty( $mappings ) ) {
			WP_CLI::warning( 'No table mappings configured.' );
			return;
		}

		$rows = array();
		foreach ( $mappings as $mapping ) {
			$rows[] = array(
				'Table ID'   => $mapping['table_id'],
				'Table Name' => isset( $mapping['table_name'] ) ? $mapping['table_name'] : 'N/A',
				'Post Type'  => $mapping['post_type'],
				'View'       => ! empty( $mapping['view_id'] ) ? $mapping['view_id'] : 'All records',
				'Fields'     => isset( $mapping['field_mappings'] ) ? count( $mapping['field_mappings'] ) : 0,
			);
		}

		WP_CLI\Utils\format_items( 'table', $rows, array( 'Table ID', 'Table Name', 'Post Type', 'View', 'Fields' ) );
	}

	/**
	 * Sync photos for people from Airtable.
	 *
	 * By default, syncs photos for all people who don't have a featured image.
	 * Can optionally sync a specific Airtable record.
	 *
	 * ## OPTIONS
	 *
	 * [--record-id=<record_id>]
	 * : Sync only a specific Airtable record ID (e.g., recXXXXXXXXXXXXXX).
	 *
	 * [--force]
	 * : Force re-download photos even if they already exist.
	 *
	 * [--limit=<number>]
	 * : Limit the number of records to process (useful for batch processing).
	 *
	 * [--offset=<number>]
	 * : Skip the first N records (useful for batch processing).
	 *
	 * [--dry-run]
	 * : Run in dry-run mode (no changes will be made).
	 *
	 * ## EXAMPLES
	 *
	 *     # Sync all missing photos
	 *     wp airtable sync-photos
	 *
	 *     # Sync photo for specific Airtable record
	 *     wp airtable sync-photos --record-id=recXXXXXXXXXXXXXX
	 *
	 *     # Force re-download all photos
	 *     wp airtable sync-photos --force
	 *
	 *     # Sync first 20 people only (batch 1)
	 *     wp airtable sync-photos --limit=20
	 *
	 *     # Sync next 20 people (batch 2)
	 *     wp airtable sync-photos --limit=20 --offset=20
	 *
	 *     # Preview changes without applying
	 *     wp airtable sync-photos --dry-run
	 *
	 * @when after_wp_load
	 */
	public function sync_photos( $args, $assoc_args ) {
		$record_id = isset( $assoc_args['record-id'] ) ? $assoc_args['record-id'] : null;
		$force = isset( $assoc_args['force'] );
		$dry_run = isset( $assoc_args['dry-run'] );
		$limit = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : null;
		$offset = isset( $assoc_args['offset'] ) ? intval( $assoc_args['offset'] ) : 0;

		if ( $dry_run ) {
			WP_CLI::warning( 'Running in DRY-RUN mode. No changes will be made.' );
		}

		// Get credentials
		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			WP_CLI::error( 'API credentials not configured. Please configure them in WP Admin > Airtable Sync.' );
			return;
		}

		// Find People table mapping
		$mappings = Airtable_Sync_Config::get_mappings();
		$people_mapping = null;

		foreach ( $mappings as $mapping ) {
			if ( $mapping['post_type'] === 'person' ) {
				$people_mapping = $mapping;
				break;
			}
		}

		if ( ! $people_mapping ) {
			WP_CLI::error( 'No mapping found for "person" post type.' );
			return;
		}

		// Find photo field mapping
		$photo_field = null;
		foreach ( $people_mapping['field_mappings'] as $field_mapping ) {
			if ( isset( $field_mapping['destination_type'] ) && $field_mapping['destination_type'] === 'core' &&
			     isset( $field_mapping['destination_key'] ) && $field_mapping['destination_key'] === 'post_thumbnail' ) {
				$photo_field = $field_mapping;
				break;
			}
		}

		if ( ! $photo_field ) {
			WP_CLI::error( 'No photo field mapping found for People. Expected a field mapped to post_thumbnail.' );
			return;
		}

		$photo_field_id = $photo_field['airtable_field_id'];

		WP_CLI::log( WP_CLI::colorize( '%BSyncing People Photos%n' ) );
		WP_CLI::log( str_repeat( '=', 50 ) );

		// Initialize API client
		$api = new Airtable_API( $credentials['api_key'], $credentials['base_id'] );

		// Stats
		$stats = array(
			'processed' => 0,
			'updated'   => 0,
			'skipped'   => 0,
			'errors'    => 0,
		);

		$total_records = 0;

		// If specific record ID provided, sync only that one
		if ( $record_id ) {
			WP_CLI::log( sprintf( 'Syncing photo for record: %s', $record_id ) );
			$result = $this->sync_single_photo( $api, $people_mapping, $photo_field_id, $record_id, $force, $dry_run );
			$stats['processed']++;
			if ( $result === 'updated' ) {
				$stats['updated']++;
			} elseif ( $result === 'skipped' ) {
				$stats['skipped']++;
			} elseif ( $result === 'error' ) {
				$stats['errors']++;
			}
		} else {
			// Get all records from Airtable
			WP_CLI::log( 'Fetching records from Airtable...' );
			$records = $api->get_records( $people_mapping['table_id'], $people_mapping['view_id'] );

			if ( is_wp_error( $records ) ) {
				WP_CLI::error( sprintf( 'Failed to fetch records: %s', $records->get_error_message() ) );
				return;
			}

			$total_records = count( $records );
			WP_CLI::log( sprintf( 'Found %d records in Airtable', $total_records ) );

			// Apply offset and limit
			if ( $offset > 0 || $limit !== null ) {
				$records = array_slice( $records, $offset, $limit );
				WP_CLI::log( sprintf( 'Processing records %d to %d (limit: %d, offset: %d)',
					$offset + 1,
					$offset + count( $records ),
					$limit !== null ? $limit : 'none',
					$offset
				) );
			}

			WP_CLI::log( '' );

			// Process each record
			$progress = \WP_CLI\Utils\make_progress_bar( 'Syncing photos', count( $records ) );

			foreach ( $records as $record ) {
				$airtable_record_id = $record['id'];
				$result = $this->sync_single_photo( $api, $people_mapping, $photo_field_id, $airtable_record_id, $force, $dry_run, $record );
				$stats['processed']++;
				if ( $result === 'updated' ) {
					$stats['updated']++;
				} elseif ( $result === 'skipped' ) {
					$stats['skipped']++;
				} elseif ( $result === 'error' ) {
					$stats['errors']++;
				}
				$progress->tick();
			}

			$progress->finish();
		}

		// Display stats
		WP_CLI::log( '' );
		WP_CLI::log( str_repeat( '=', 50 ) );
		WP_CLI::log( sprintf( 'Processed:  %d', $stats['processed'] ) );
		WP_CLI::log( WP_CLI::colorize( sprintf( '%%GUpdated:    %d%%n', $stats['updated'] ) ) );
		WP_CLI::log( sprintf( 'Skipped:    %d', $stats['skipped'] ) );
		if ( $stats['errors'] > 0 ) {
			WP_CLI::log( WP_CLI::colorize( sprintf( '%%RErrors:     %d%%n', $stats['errors'] ) ) );
		} else {
			WP_CLI::log( sprintf( 'Errors:     %d', $stats['errors'] ) );
		}

		// Show batch info if using limit/offset
		if ( ! $record_id && ( $limit !== null || $offset > 0 ) ) {
			WP_CLI::log( '' );
			WP_CLI::log( sprintf( 'Batch: Records %d-%d of %d total',
				$offset + 1,
				min( $offset + ( $limit !== null ? $limit : $total_records ), $total_records ),
				$total_records
			) );

			// Calculate and show next batch command
			$next_offset = $offset + ( $limit !== null ? $limit : 0 );
			if ( $next_offset < $total_records ) {
				$remaining = $total_records - $next_offset;
				WP_CLI::log( WP_CLI::colorize( sprintf(
					'%%yNext batch: wp airtable sync-photos --limit=%d --offset=%d (%d remaining)%%n',
					$limit !== null ? $limit : 20,
					$next_offset,
					$remaining
				) ) );
			}
		}

		if ( $stats['errors'] > 0 ) {
			WP_CLI::warning( sprintf( 'Photo sync completed with %d error(s).', $stats['errors'] ) );
		} else {
			WP_CLI::success( 'Photo sync completed successfully!' );
		}
	}

	/**
	 * Sync photo for a single person record.
	 *
	 * @param Airtable_API $api            API client instance.
	 * @param array        $mapping        People table mapping.
	 * @param string       $photo_field_id Airtable photo field ID.
	 * @param string       $record_id      Airtable record ID.
	 * @param bool         $force          Force re-download even if exists.
	 * @param bool         $dry_run        Dry run mode.
	 * @param array|null   $record         Optional: Pre-fetched record data.
	 * @return string Result: 'updated', 'skipped', or 'error'.
	 */
	private function sync_single_photo( $api, $mapping, $photo_field_id, $record_id, $force, $dry_run, $record = null ) {
		// Find WordPress post by Airtable ID
		$query_args = array(
			'post_type'      => 'person',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'     => '_airtable_id',
					'value'   => $record_id,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
		);

		$posts = get_posts( $query_args );

		if ( empty( $posts ) ) {
			WP_CLI::warning( sprintf( 'No WordPress post found for Airtable record: %s', $record_id ) );
			return 'error';
		}

		$post_id = $posts[0];
		$post_title = get_the_title( $post_id );

		// Check if post already has thumbnail
		if ( ! $force && has_post_thumbnail( $post_id ) ) {
			return 'skipped';
		}

		// Fetch record from Airtable if not provided
		if ( ! $record ) {
			$record = $api->get_record( $mapping['table_id'], $record_id );

			if ( is_wp_error( $record ) ) {
				WP_CLI::warning( sprintf( 'Failed to fetch record %s: %s', $record_id, $record->get_error_message() ) );
				return 'error';
			}
		}

		// Get photo field value using field ID
		$fields = isset( $record['fields'] ) ? $record['fields'] : array();
		$photo_value = isset( $fields[ $photo_field_id ] ) ? $fields[ $photo_field_id ] : null;

		if ( empty( $photo_value ) ) {
			WP_CLI::log( sprintf( 'No photo in Airtable for: %s (ID: %s)', $post_title, $record_id ) );
			return 'skipped';
		}

		// Photo value is an array of attachments - take first one
		$attachment_data = is_array( $photo_value ) && isset( $photo_value[0] ) ? $photo_value[0] : null;

		if ( ! $attachment_data || empty( $attachment_data['url'] ) ) {
			WP_CLI::log( sprintf( 'Invalid photo data for: %s (ID: %s)', $post_title, $record_id ) );
			return 'skipped';
		}

		// Debug: Show file info
		$filename = isset( $attachment_data['filename'] ) ? $attachment_data['filename'] : 'unknown';
		$file_type = isset( $attachment_data['type'] ) ? $attachment_data['type'] : 'unknown';
		WP_CLI::log( sprintf( 'File: %s (Type: %s)', $filename, $file_type ) );
		WP_CLI::log( sprintf( 'URL: %s', $attachment_data['url'] ) );

		if ( $dry_run ) {
			WP_CLI::log( sprintf( 'Would download photo for: %s', $post_title ) );
			return 'updated';
		}

		// Download and attach photo
		WP_CLI::log( sprintf( 'Downloading photo for: %s', $post_title ) );
		$attachment_id = $api->download_attachment( $attachment_data );

		if ( is_wp_error( $attachment_id ) ) {
			WP_CLI::warning( sprintf( 'Failed to download photo for %s: %s', $post_title, $attachment_id->get_error_message() ) );
			return 'error';
		}

		// Set as featured image
		set_post_thumbnail( $post_id, $attachment_id );
		WP_CLI::log( WP_CLI::colorize( sprintf( '%%GPhoto set for: %s%%n', $post_title ) ) );

		return 'updated';
	}

	/**
	 * Display sync statistics.
	 *
	 * @param array $stats Sync statistics.
	 */
	private function display_stats( $stats ) {
		WP_CLI::log( sprintf( 'Processed:    %d', $stats['processed'] ) );
		WP_CLI::log( WP_CLI::colorize( sprintf( '%%GCreated:      %d%%n', $stats['created'] ) ) );
		WP_CLI::log( WP_CLI::colorize( sprintf( '%%YUpdated:      %d%%n', $stats['updated'] ) ) );
		WP_CLI::log( sprintf( 'Skipped:      %d', $stats['skipped'] ) );

		if ( isset( $stats['unpublished'] ) && $stats['unpublished'] > 0 ) {
			WP_CLI::log( WP_CLI::colorize( sprintf( '%%yUnpublished: %d%%n', $stats['unpublished'] ) ) );
		} else {
			WP_CLI::log( sprintf( 'Unpublished:  %d', isset( $stats['unpublished'] ) ? $stats['unpublished'] : 0 ) );
		}

		if ( $stats['errors'] > 0 ) {
			WP_CLI::log( WP_CLI::colorize( sprintf( '%%RErrors:       %d%%n', $stats['errors'] ) ) );
		} else {
			WP_CLI::log( sprintf( 'Errors:       %d', $stats['errors'] ) );
		}
	}
}

// Register WP-CLI commands
WP_CLI::add_command( 'airtable sync', array( 'Airtable_Sync_CLI', 'sync' ) );
WP_CLI::add_command( 'airtable sync-table', array( 'Airtable_Sync_CLI', 'sync_table' ) );
WP_CLI::add_command( 'airtable sync-photos', array( 'Airtable_Sync_CLI', 'sync_photos' ) );
WP_CLI::add_command( 'airtable validate', array( 'Airtable_Sync_CLI', 'validate' ) );
WP_CLI::add_command( 'airtable list', array( 'Airtable_Sync_CLI', 'list_mappings' ) );
