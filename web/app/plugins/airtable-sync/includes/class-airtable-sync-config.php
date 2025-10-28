<?php
/**
 * Configuration loader for Airtable Sync
 *
 * @package Airtable_Sync
 */

class Airtable_Sync_Config {

	/**
	 * Cached mappings.
	 *
	 * @var array|null
	 */
	private static $mappings = null;

	/**
	 * Get API credentials from WordPress options.
	 *
	 * @return array Array with 'api_key' and 'base_id' keys.
	 */
	public static function get_credentials() {
		$settings = get_option( 'airtable_sync_settings', array() );

		return array(
			'api_key' => isset( $settings['api_key'] ) ? $settings['api_key'] : '',
			'base_id' => isset( $settings['base_id'] ) ? $settings['base_id'] : '',
		);
	}

	/**
	 * Get all table mappings from config file.
	 *
	 * @return array Array of table mapping configurations.
	 */
	public static function get_mappings() {
		// Return cached mappings if available
		if ( self::$mappings !== null ) {
			return self::$mappings;
		}

		$config_file = AIRTABLE_SYNC_PLUGIN_DIR . 'config/mappings.php';

		// Check if config file exists
		if ( ! file_exists( $config_file ) ) {
			self::$mappings = array();
			return self::$mappings;
		}

		// Load and validate config file
		$mappings = include $config_file;

		if ( ! is_array( $mappings ) ) {
			self::$mappings = array();
			return self::$mappings;
		}

		// Cache and return
		self::$mappings = $mappings;
		return self::$mappings;
	}

	/**
	 * Get mapping for a specific table ID.
	 *
	 * @param string $table_id The Airtable table ID.
	 * @return array|null The mapping configuration or null if not found.
	 */
	public static function get_mapping_by_table_id( $table_id ) {
		$mappings = self::get_mappings();

		foreach ( $mappings as $mapping ) {
			if ( isset( $mapping['table_id'] ) && $mapping['table_id'] === $table_id ) {
				return $mapping;
			}
		}

		return null;
	}

	/**
	 * Get mapping for a specific post type.
	 *
	 * @param string $post_type The WordPress post type.
	 * @return array|null The mapping configuration or null if not found.
	 */
	public static function get_mapping_by_post_type( $post_type ) {
		$mappings = self::get_mappings();

		foreach ( $mappings as $mapping ) {
			if ( isset( $mapping['post_type'] ) && $mapping['post_type'] === $post_type ) {
				return $mapping;
			}
		}

		return null;
	}

	/**
	 * Validate configuration.
	 *
	 * Checks if:
	 * - Config file exists
	 * - Mappings are properly formatted
	 * - Required fields are present
	 * - WordPress post types exist
	 * - ACF fields exist (if ACF is active)
	 *
	 * @return array Array of validation results with 'valid' bool and 'errors' array.
	 */
	public static function validate() {
		$errors = array();

		// Check if config file exists
		$config_file = AIRTABLE_SYNC_PLUGIN_DIR . 'config/mappings.php';
		if ( ! file_exists( $config_file ) ) {
			$errors[] = __( 'Configuration file not found: config/mappings.php', 'airtable-sync' );
			return array(
				'valid' => false,
				'errors' => $errors,
			);
		}

		$mappings = self::get_mappings();

		// Check if mappings is an array
		if ( ! is_array( $mappings ) ) {
			$errors[] = __( 'Configuration file must return an array.', 'airtable-sync' );
			return array(
				'valid' => false,
				'errors' => $errors,
			);
		}

		// Validate each mapping
		foreach ( $mappings as $index => $mapping ) {
			$mapping_label = isset( $mapping['table_name'] ) ? $mapping['table_name'] : "Mapping #{$index}";

			// Check required fields
			if ( empty( $mapping['table_id'] ) ) {
				$errors[] = sprintf(
					/* translators: %s: mapping identifier */
					__( '%s: Missing required field "table_id"', 'airtable-sync' ),
					$mapping_label
				);
			}

			if ( empty( $mapping['post_type'] ) ) {
				$errors[] = sprintf(
					/* translators: %s: mapping identifier */
					__( '%s: Missing required field "post_type"', 'airtable-sync' ),
					$mapping_label
				);
				continue; // Skip further checks for this mapping
			}

			// Check if post type exists
			if ( ! post_type_exists( $mapping['post_type'] ) ) {
				$errors[] = sprintf(
					/* translators: 1: mapping identifier, 2: post type name */
					__( '%1$s: Post type "%2$s" does not exist', 'airtable-sync' ),
					$mapping_label,
					$mapping['post_type']
				);
			}

			// Validate field mappings
			if ( isset( $mapping['field_mappings'] ) && is_array( $mapping['field_mappings'] ) ) {
				foreach ( $mapping['field_mappings'] as $field_index => $field_mapping ) {
					$field_label = isset( $field_mapping['airtable_field_name'] )
						? $field_mapping['airtable_field_name']
						: "Field #{$field_index}";

					// Check required fields
					if ( empty( $field_mapping['airtable_field_id'] ) ) {
						$errors[] = sprintf(
							/* translators: 1: mapping identifier, 2: field identifier */
							__( '%1$s > %2$s: Missing "airtable_field_id"', 'airtable-sync' ),
							$mapping_label,
							$field_label
						);
					}

					if ( empty( $field_mapping['destination_type'] ) ) {
						$errors[] = sprintf(
							/* translators: 1: mapping identifier, 2: field identifier */
							__( '%1$s > %2$s: Missing "destination_type"', 'airtable-sync' ),
							$mapping_label,
							$field_label
						);
					}

					if ( empty( $field_mapping['destination_key'] ) ) {
						$errors[] = sprintf(
							/* translators: 1: mapping identifier, 2: field identifier */
							__( '%1$s > %2$s: Missing "destination_key"', 'airtable-sync' ),
							$mapping_label,
							$field_label
						);
					}

					// Validate destination type
					$valid_types = array( 'core', 'taxonomy', 'acf' );
					if ( ! empty( $field_mapping['destination_type'] ) && ! in_array( $field_mapping['destination_type'], $valid_types, true ) ) {
						$errors[] = sprintf(
							/* translators: 1: mapping identifier, 2: field identifier, 3: destination type */
							__( '%1$s > %2$s: Invalid destination_type "%3$s". Must be "core", "taxonomy", or "acf"', 'airtable-sync' ),
							$mapping_label,
							$field_label,
							$field_mapping['destination_type']
						);
					}

					// Validate taxonomy exists
					if ( ! empty( $field_mapping['destination_type'] ) && $field_mapping['destination_type'] === 'taxonomy' ) {
						if ( ! empty( $field_mapping['destination_key'] ) && ! taxonomy_exists( $field_mapping['destination_key'] ) ) {
							$errors[] = sprintf(
								/* translators: 1: mapping identifier, 2: field identifier, 3: taxonomy name */
								__( '%1$s > %2$s: Taxonomy "%3$s" does not exist', 'airtable-sync' ),
								$mapping_label,
								$field_label,
								$field_mapping['destination_key']
							);
						}
					}

					// Validate ACF field exists (if ACF is active)
					if ( ! empty( $field_mapping['destination_type'] ) && $field_mapping['destination_type'] === 'acf' ) {
						if ( ! function_exists( 'acf_get_field' ) ) {
							$errors[] = sprintf(
								/* translators: 1: mapping identifier, 2: field identifier */
								__( '%1$s > %2$s: ACF field mapping requires ACF Pro to be installed and active', 'airtable-sync' ),
								$mapping_label,
								$field_label
							);
						} elseif ( ! empty( $field_mapping['destination_key'] ) ) {
							$acf_field = acf_get_field( $field_mapping['destination_key'] );
							if ( ! $acf_field ) {
								$errors[] = sprintf(
									/* translators: 1: mapping identifier, 2: field identifier, 3: ACF field key */
									__( '%1$s > %2$s: ACF field "%3$s" not found', 'airtable-sync' ),
									$mapping_label,
									$field_label,
									$field_mapping['destination_key']
								);
							}
						}
					}
				}
			}
		}

		return array(
			'valid' => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Check if configuration is set up (has mappings).
	 *
	 * @return bool True if at least one mapping exists.
	 */
	public static function has_mappings() {
		$mappings = self::get_mappings();
		return ! empty( $mappings );
	}

	/**
	 * Check if API credentials are configured.
	 *
	 * @return bool True if API key and base ID are set.
	 */
	public static function has_credentials() {
		$credentials = self::get_credentials();
		return ! empty( $credentials['api_key'] ) && ! empty( $credentials['base_id'] );
	}

	/**
	 * Clear cached mappings.
	 *
	 * Useful after config file changes during development.
	 */
	public static function clear_cache() {
		self::$mappings = null;
	}
}
