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
WP_CLI::add_command( 'airtable validate', array( 'Airtable_Sync_CLI', 'validate' ) );
WP_CLI::add_command( 'airtable list', array( 'Airtable_Sync_CLI', 'list_mappings' ) );
