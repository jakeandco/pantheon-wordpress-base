<?php
/**
 * Admin functionality for Airtable Sync
 *
 * @package Airtable_Sync
 */

class Airtable_Sync_Admin {

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	private $option_name = 'airtable_sync_settings';

	/**
	 * Initialize the admin functionality.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_ajax_airtable_sync_get_bases', array( $this, 'ajax_get_bases' ) );
		add_action( 'wp_ajax_airtable_sync_validate_config', array( $this, 'ajax_validate_config' ) );
		add_action( 'wp_ajax_airtable_sync_get_tables', array( $this, 'ajax_get_tables' ) );
		add_action( 'wp_ajax_airtable_sync_get_table_schema', array( $this, 'ajax_get_table_schema' ) );
		add_action( 'wp_ajax_airtable_sync_run_sync', array( $this, 'ajax_run_sync' ) );
	}

	/**
	 * Add admin menu page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Airtable Sync', 'airtable-sync' ),
			__( 'Airtable Sync', 'airtable-sync' ),
			'manage_options',
			'airtable-sync',
			array( $this, 'render_settings_page' ),
			'dashicons-update',
			30
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'airtable_sync_settings_group',
			$this->option_name,
			array( $this, 'sanitize_settings' )
		);

		// API Configuration Section
		add_settings_section(
			'airtable_sync_api_section',
			__( 'API Configuration', 'airtable-sync' ),
			array( $this, 'render_api_section' ),
			'airtable-sync'
		);

		add_settings_field(
			'api_key',
			__( 'Airtable API Key', 'airtable-sync' ),
			array( $this, 'render_api_key_field' ),
			'airtable-sync',
			'airtable_sync_api_section'
		);

		add_settings_field(
			'base_id',
			__( 'Airtable Base', 'airtable-sync' ),
			array( $this, 'render_base_selector_field' ),
			'airtable-sync',
			'airtable_sync_api_section'
		);

		// Configuration Status Section
		add_settings_section(
			'airtable_sync_status_section',
			__( 'Configuration Status', 'airtable-sync' ),
			array( $this, 'render_status_section' ),
			'airtable-sync'
		);

		// Field ID Inspector Section
		add_settings_section(
			'airtable_sync_inspector_section',
			__( 'Field ID Inspector', 'airtable-sync' ),
			array( $this, 'render_inspector_section' ),
			'airtable-sync'
		);

		// Table Mappings Section (Read-only display)
		add_settings_section(
			'airtable_sync_mappings_section',
			__( 'Table Mappings', 'airtable-sync' ),
			array( $this, 'render_mappings_section' ),
			'airtable-sync'
		);
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @param array $input The input values.
	 * @return array Sanitized values.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		if ( isset( $input['api_key'] ) ) {
			$sanitized['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		if ( isset( $input['base_id'] ) ) {
			$sanitized['base_id'] = sanitize_text_field( $input['base_id'] );
		}

		return $sanitized;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_airtable-sync' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'airtable-sync-admin',
			AIRTABLE_SYNC_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			AIRTABLE_SYNC_VERSION
		);

		wp_enqueue_script(
			'airtable-sync-admin',
			AIRTABLE_SYNC_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			AIRTABLE_SYNC_VERSION,
			true
		);

		wp_localize_script(
			'airtable-sync-admin',
			'airtableSyncAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'airtable_sync_nonce' ),
			)
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show success message if settings were saved
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				'airtable_sync_messages',
				'airtable_sync_message',
				__( 'Settings saved successfully.', 'airtable-sync' ),
				'success'
			);
		}

		settings_errors( 'airtable_sync_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="airtable-sync-intro">
				<p><?php esc_html_e( 'Configure your Airtable API credentials below. Table mappings are managed in code at:', 'airtable-sync' ); ?></p>
				<code>web/app/plugins/airtable-sync/config/mappings.php</code>
			</div>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'airtable_sync_settings_group' );
				do_settings_sections( 'airtable-sync' );
				submit_button( __( 'Save Settings', 'airtable-sync' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render API section description.
	 */
	public function render_api_section() {
		echo '<p>' . esc_html__( 'Configure your Airtable API credentials. These are stored in the WordPress database and not committed to version control.', 'airtable-sync' ) . '</p>';
	}

	/**
	 * Render API key field.
	 */
	public function render_api_key_field() {
		$settings = get_option( $this->option_name, array() );
		$api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
		?>
		<input
			type="password"
			id="airtable_sync_api_key"
			name="<?php echo esc_attr( $this->option_name ); ?>[api_key]"
			value="<?php echo esc_attr( $api_key ); ?>"
			class="regular-text"
			autocomplete="off"
		/>
		<p class="description">
			<?php
			printf(
				/* translators: %s: URL to Airtable API documentation */
				esc_html__( 'Enter your Airtable Personal Access Token. Create one at %s.', 'airtable-sync' ),
				'<a href="https://airtable.com/create/tokens" target="_blank">' . esc_html__( 'airtable.com/create/tokens', 'airtable-sync' ) . '</a>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render base selector field.
	 */
	public function render_base_selector_field() {
		$settings = get_option( $this->option_name, array() );
		$base_id = isset( $settings['base_id'] ) ? $settings['base_id'] : '';
		$api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
		?>
		<select
			id="airtable_sync_base_id"
			name="<?php echo esc_attr( $this->option_name ); ?>[base_id]"
			class="regular-text"
			<?php echo empty( $api_key ) ? 'disabled' : ''; ?>
		>
			<option value=""><?php esc_html_e( 'Select a base...', 'airtable-sync' ); ?></option>
			<?php if ( ! empty( $base_id ) ) : ?>
				<option value="<?php echo esc_attr( $base_id ); ?>" selected><?php echo esc_html( $base_id ); ?></option>
			<?php endif; ?>
		</select>
		<button type="button" id="airtable_sync_load_bases" class="button" <?php echo empty( $api_key ) ? 'disabled' : ''; ?>>
			<?php esc_html_e( 'Load Bases', 'airtable-sync' ); ?>
		</button>
		<p class="description">
			<?php esc_html_e( 'Select the Airtable base you want to sync with WordPress.', 'airtable-sync' ); ?>
		</p>
		<div id="airtable_sync_base_loading" style="display:none;">
			<span class="spinner is-active"></span>
		</div>
		<?php
	}

	/**
	 * Render configuration status section.
	 */
	public function render_status_section() {
		$validation = Airtable_Sync_Config::validate();
		$has_credentials = Airtable_Sync_Config::has_credentials();
		$has_mappings = Airtable_Sync_Config::has_mappings();

		echo '<div class="airtable-sync-status">';

		// API Credentials Status
		if ( $has_credentials ) {
			echo '<div class="notice notice-success inline"><p>';
			echo '<span class="dashicons dashicons-yes-alt"></span> ';
			esc_html_e( 'API credentials configured', 'airtable-sync' );
			echo '</p></div>';
		} else {
			echo '<div class="notice notice-warning inline"><p>';
			echo '<span class="dashicons dashicons-warning"></span> ';
			esc_html_e( 'API credentials not configured', 'airtable-sync' );
			echo '</p></div>';
		}

		// Mappings Status
		if ( $has_mappings ) {
			echo '<div class="notice notice-success inline"><p>';
			echo '<span class="dashicons dashicons-yes-alt"></span> ';
			/* translators: %d: number of mappings */
			echo esc_html( sprintf( _n( '%d table mapping configured', '%d table mappings configured', count( Airtable_Sync_Config::get_mappings() ), 'airtable-sync' ), count( Airtable_Sync_Config::get_mappings() ) ) );
			echo '</p></div>';
		} else {
			echo '<div class="notice notice-warning inline"><p>';
			echo '<span class="dashicons dashicons-warning"></span> ';
			esc_html_e( 'No table mappings configured in config/mappings.php', 'airtable-sync' );
			echo '</p></div>';
		}

		// Validation Status
		if ( $has_mappings ) {
			if ( $validation['valid'] ) {
				echo '<div class="notice notice-success inline"><p>';
				echo '<span class="dashicons dashicons-yes-alt"></span> ';
				esc_html_e( 'Configuration is valid', 'airtable-sync' );
				echo '</p></div>';
			} else {
				echo '<div class="notice notice-error inline"><p>';
				echo '<span class="dashicons dashicons-no"></span> ';
				esc_html_e( 'Configuration has errors:', 'airtable-sync' );
				echo '</p><ul>';
				foreach ( $validation['errors'] as $error ) {
					echo '<li>' . esc_html( $error ) . '</li>';
				}
				echo '</ul></div>';
			}
		}

		// Validate button
		echo '<button type="button" id="airtable_sync_validate" class="button">';
		esc_html_e( 'Revalidate Configuration', 'airtable-sync' );
		echo '</button>';

		echo '</div>';
	}

	/**
	 * Render field ID inspector section.
	 */
	public function render_inspector_section() {
		$credentials = Airtable_Sync_Config::get_credentials();
		$has_credentials = ! empty( $credentials['api_key'] ) && ! empty( $credentials['base_id'] );

		if ( ! $has_credentials ) {
			echo '<div class="notice notice-warning inline"><p>';
			esc_html_e( 'Configure API credentials above to use the Field ID Inspector.', 'airtable-sync' );
			echo '</p></div>';
			return;
		}

		?>
		<p><?php esc_html_e( 'Use this tool to discover Airtable field IDs for your configuration. Select a table to view all its fields with their IDs and types.', 'airtable-sync' ); ?></p>

		<div class="airtable-sync-inspector">
			<div class="inspector-controls">
				<label for="airtable_inspector_table">
					<?php esc_html_e( 'Select Table:', 'airtable-sync' ); ?>
				</label>
				<select id="airtable_inspector_table" class="regular-text">
					<option value=""><?php esc_html_e( 'Select a table...', 'airtable-sync' ); ?></option>
				</select>
				<button type="button" id="airtable_sync_load_table_fields" class="button" disabled>
					<?php esc_html_e( 'Load Fields', 'airtable-sync' ); ?>
				</button>
				<span id="airtable_inspector_loading" class="spinner" style="display:none;"></span>
			</div>

			<div id="airtable_inspector_results" style="display:none;">
				<h4><?php esc_html_e( 'Table Fields', 'airtable-sync' ); ?></h4>
				<p class="description">
					<?php esc_html_e( 'Copy these field IDs into your config/mappings.php file.', 'airtable-sync' ); ?>
				</p>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Field Name', 'airtable-sync' ); ?></th>
							<th><?php esc_html_e( 'Field ID', 'airtable-sync' ); ?></th>
							<th><?php esc_html_e( 'Field Type', 'airtable-sync' ); ?></th>
						</tr>
					</thead>
					<tbody id="airtable_inspector_fields">
						<!-- Populated via JavaScript -->
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Render mappings section description.
	 */
	public function render_mappings_section() {
		$mappings = Airtable_Sync_Config::get_mappings();

		if ( empty( $mappings ) ) {
			echo '<div class="notice notice-info inline"><p>';
			esc_html_e( 'No table mappings configured. Edit config/mappings.php to add mappings.', 'airtable-sync' );
			echo '</p></div>';
			echo '<p>';
			/* translators: %s: file path */
			echo wp_kses_post( sprintf( __( 'Table mappings are defined in code at <code>%s</code>', 'airtable-sync' ), 'web/app/plugins/airtable-sync/config/mappings.php' ) );
			echo '</p>';
			echo '<p>';
			/* translators: %s: file path */
			echo wp_kses_post( sprintf( __( 'See <code>%s</code> for examples.', 'airtable-sync' ), 'config/mappings.example.php' ) );
			echo '</p>';
			return;
		}

		echo '<p>' . esc_html__( 'Current table mappings defined in config/mappings.php:', 'airtable-sync' ) . '</p>';

		echo '<div class="airtable-sync-mappings">';
		foreach ( $mappings as $index => $mapping ) {
			$this->render_mapping_display( $mapping, $index );
		}
		echo '</div>';
	}

	/**
	 * Render a single mapping display (read-only).
	 *
	 * @param array $mapping The mapping configuration.
	 * @param int   $index   The mapping index.
	 */
	private function render_mapping_display( $mapping, $index ) {
		$table_name = isset( $mapping['table_name'] ) ? $mapping['table_name'] : $mapping['table_id'];
		$post_type_obj = get_post_type_object( $mapping['post_type'] );
		$post_type_label = $post_type_obj ? $post_type_obj->labels->name : $mapping['post_type'];
		?>
		<div class="airtable-sync-mapping-display">
			<div class="mapping-header">
				<div class="mapping-header-left">
					<h3>
						<span class="dashicons dashicons-database"></span>
						<?php echo esc_html( $table_name ); ?>
						<span class="mapping-arrow">→</span>
						<span class="dashicons dashicons-wordpress"></span>
						<?php echo esc_html( $post_type_label ); ?>
					</h3>
					<div class="mapping-meta">
						<code>table_id: <?php echo esc_html( $mapping['table_id'] ); ?></code>
						<?php if ( ! empty( $mapping['view_id'] ) ) : ?>
							<code>view_id: <?php echo esc_html( $mapping['view_id'] ); ?></code>
							<?php if ( ! empty( $mapping['view_name'] ) ) : ?>
								<span>(<?php echo esc_html( $mapping['view_name'] ); ?>)</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="mapping-header-right">
					<button type="button" class="button button-primary airtable-sync-now-btn" data-table-id="<?php echo esc_attr( $mapping['table_id'] ); ?>">
						<span class="dashicons dashicons-update"></span>
						<?php esc_html_e( 'Sync Now', 'airtable-sync' ); ?>
					</button>
					<div class="sync-status" style="display: none;">
						<span class="sync-spinner spinner"></span>
						<span class="sync-message"></span>
					</div>
				</div>
			</div>

			<?php if ( ! empty( $mapping['field_mappings'] ) ) : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Airtable Field', 'airtable-sync' ); ?></th>
							<th><?php esc_html_e( 'Field Type', 'airtable-sync' ); ?></th>
							<th><?php esc_html_e( 'WordPress Destination', 'airtable-sync' ); ?></th>
							<th><?php esc_html_e( 'Destination Type', 'airtable-sync' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $mapping['field_mappings'] as $field_mapping ) : ?>
							<tr>
								<td>
									<strong>
										<?php
										echo esc_html(
											! empty( $field_mapping['airtable_field_name'] )
												? $field_mapping['airtable_field_name']
												: $field_mapping['airtable_field_id']
										);
										?>
									</strong>
									<?php if ( ! empty( $field_mapping['airtable_field_name'] ) ) : ?>
										<br>
										<code class="small"><?php echo esc_html( $field_mapping['airtable_field_id'] ); ?></code>
									<?php endif; ?>
								</td>
								<td>
									<?php echo esc_html( isset( $field_mapping['airtable_field_type'] ) ? $field_mapping['airtable_field_type'] : 'N/A' ); ?>
								</td>
								<td>
									<strong>
										<?php
										echo esc_html(
											! empty( $field_mapping['destination_name'] )
												? $field_mapping['destination_name']
												: $field_mapping['destination_key']
										);
										?>
									</strong>
									<?php if ( ! empty( $field_mapping['destination_name'] ) ) : ?>
										<br>
										<code class="small"><?php echo esc_html( $field_mapping['destination_key'] ); ?></code>
									<?php endif; ?>
								</td>
								<td>
									<span class="badge badge-<?php echo esc_attr( $field_mapping['destination_type'] ); ?>">
										<?php echo esc_html( $field_mapping['destination_type'] ); ?>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p class="description"><?php esc_html_e( 'No field mappings configured for this table.', 'airtable-sync' ); ?></p>
			<?php endif; ?>

			<div class="sync-results" style="display: none;">
				<div class="sync-results-header">
					<h4><?php esc_html_e( 'Sync Results', 'airtable-sync' ); ?></h4>
				</div>
				<div class="sync-results-body">
					<div class="sync-stats">
						<div class="stat-item stat-processed">
							<span class="stat-label"><?php esc_html_e( 'Processed:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="processed">0</span>
						</div>
						<div class="stat-item stat-created">
							<span class="stat-label"><?php esc_html_e( 'Created:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="created">0</span>
						</div>
						<div class="stat-item stat-updated">
							<span class="stat-label"><?php esc_html_e( 'Updated:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="updated">0</span>
						</div>
						<div class="stat-item stat-skipped">
							<span class="stat-label"><?php esc_html_e( 'Skipped:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="skipped">0</span>
						</div>
						<div class="stat-item stat-unpublished">
							<span class="stat-label"><?php esc_html_e( 'Unpublished:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="unpublished">0</span>
						</div>
						<div class="stat-item stat-errors">
							<span class="stat-label"><?php esc_html_e( 'Errors:', 'airtable-sync' ); ?></span>
							<span class="stat-value" data-stat="errors">0</span>
						</div>
					</div>
					<div class="sync-message-box"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler to get Airtable bases.
	 */
	public function ajax_get_bases() {
		check_ajax_referer( 'airtable_sync_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'airtable-sync' ) ) );
		}

		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';

		if ( empty( $api_key ) ) {
			wp_send_json_error( array( 'message' => __( 'API key is required.', 'airtable-sync' ) ) );
		}

		$response = wp_remote_get(
			'https://api.airtable.com/v0/meta/bases',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			wp_send_json_error( array( 'message' => $data['error']['message'] ) );
		}

		wp_send_json_success( $data );
	}

	/**
	 * AJAX handler to validate configuration.
	 */
	public function ajax_validate_config() {
		check_ajax_referer( 'airtable_sync_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'airtable-sync' ) ) );
		}

		// Clear cache before validation
		Airtable_Sync_Config::clear_cache();

		$validation = Airtable_Sync_Config::validate();

		if ( $validation['valid'] ) {
			wp_send_json_success( array(
				'message' => __( 'Configuration is valid!', 'airtable-sync' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Configuration has errors', 'airtable-sync' ),
				'errors' => $validation['errors'],
			) );
		}
	}

	/**
	 * AJAX handler to get tables from a base.
	 */
	public function ajax_get_tables() {
		check_ajax_referer( 'airtable_sync_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'airtable-sync' ) ) );
		}

		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'API credentials not configured.', 'airtable-sync' ) ) );
		}

		$response = wp_remote_get(
			'https://api.airtable.com/v0/meta/bases/' . $credentials['base_id'] . '/tables',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $credentials['api_key'],
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			wp_send_json_error( array( 'message' => $data['error']['message'] ) );
		}

		wp_send_json_success( $data );
	}

	/**
	 * AJAX handler to get table schema (fields) from Airtable.
	 */
	public function ajax_get_table_schema() {
		check_ajax_referer( 'airtable_sync_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'airtable-sync' ) ) );
		}

		$table_id = isset( $_POST['table_id'] ) ? sanitize_text_field( $_POST['table_id'] ) : '';

		if ( empty( $table_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Table ID is required.', 'airtable-sync' ) ) );
		}

		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'API credentials not configured.', 'airtable-sync' ) ) );
		}

		$response = wp_remote_get(
			'https://api.airtable.com/v0/meta/bases/' . $credentials['base_id'] . '/tables',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $credentials['api_key'],
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			wp_send_json_error( array( 'message' => $data['error']['message'] ) );
		}

		// Find the specific table and return its fields
		$table_schema = null;
		if ( isset( $data['tables'] ) ) {
			foreach ( $data['tables'] as $table ) {
				if ( $table['id'] === $table_id ) {
					$table_schema = $table;
					break;
				}
			}
		}

		if ( ! $table_schema ) {
			wp_send_json_error( array( 'message' => __( 'Table not found.', 'airtable-sync' ) ) );
		}

		wp_send_json_success( $table_schema );
	}

	/**
	 * AJAX handler to run sync for a specific table.
	 */
	public function ajax_run_sync() {
		check_ajax_referer( 'airtable_sync_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'airtable-sync' ) ) );
		}

		$table_id = isset( $_POST['table_id'] ) ? sanitize_text_field( $_POST['table_id'] ) : '';

		if ( empty( $table_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Table ID is required.', 'airtable-sync' ) ) );
		}

		// Get credentials
		$credentials = Airtable_Sync_Config::get_credentials();

		if ( empty( $credentials['api_key'] ) || empty( $credentials['base_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'API credentials not configured.', 'airtable-sync' ) ) );
		}

		// Get mapping for this table
		$mapping = Airtable_Sync_Config::get_mapping_by_table_id( $table_id );

		if ( ! $mapping ) {
			wp_send_json_error( array( 'message' => sprintf( __( 'No mapping found for table ID: %s', 'airtable-sync' ), $table_id ) ) );
		}

		// Initialize API and sync engine
		$api = new Airtable_API( $credentials['api_key'], $credentials['base_id'] );
		$engine = new Airtable_Sync_Engine( $api, $mapping );

		// Run the sync
		$stats = $engine->sync( false ); // Not a dry run

		// Check for errors in stats
		if ( isset( $stats['errors'] ) && $stats['errors'] > 0 ) {
			wp_send_json_success( array(
				'message' => sprintf(
					__( 'Sync completed with %d error(s).', 'airtable-sync' ),
					$stats['errors']
				),
				'stats' => $stats,
				'hasErrors' => true,
			) );
		} else {
			wp_send_json_success( array(
				'message' => __( 'Sync completed successfully!', 'airtable-sync' ),
				'stats' => $stats,
				'hasErrors' => false,
			) );
		}
	}
}
