<?php
/**
 * Airtable API Client
 *
 * Handles all communication with the Airtable API.
 *
 * @package Airtable_Sync
 */

class Airtable_API {

	/**
	 * API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Base ID.
	 *
	 * @var string
	 */
	private $base_id;

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private $api_url = 'https://api.airtable.com/v0';

	/**
	 * Constructor.
	 *
	 * @param string $api_key API key.
	 * @param string $base_id Base ID.
	 */
	public function __construct( $api_key, $base_id ) {
		$this->api_key = $api_key;
		$this->base_id = $base_id;
	}

	/**
	 * Get records from a table.
	 *
	 * @param string $table_id Table ID.
	 * @param string $view_id  Optional view ID to filter records.
	 * @return array|WP_Error Array of records or WP_Error on failure.
	 */
	public function get_records( $table_id, $view_id = '' ) {
		$all_records = array();
		$offset = null;

		do {
			$url = $this->api_url . '/' . $this->base_id . '/' . $table_id;
			$args = array();

			// Return fields by field ID instead of field name
			$args['returnFieldsByFieldId'] = 'true';

			// Add view filter if specified
			if ( ! empty( $view_id ) ) {
				$args['view'] = $view_id;
			}

			// Add offset for pagination
			if ( $offset ) {
				$args['offset'] = $offset;
			}

			// Build URL with query parameters
			if ( ! empty( $args ) ) {
				$url = add_query_arg( $args, $url );
			}

			$response = $this->make_request( $url );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			// Add records to our collection
			if ( isset( $response['records'] ) && is_array( $response['records'] ) ) {
				$all_records = array_merge( $all_records, $response['records'] );
			}

			// Check if there are more records to fetch
			$offset = isset( $response['offset'] ) ? $response['offset'] : null;

		} while ( $offset );

		return $all_records;
	}

	/**
	 * Get a single record by ID.
	 *
	 * @param string $table_id  Table ID.
	 * @param string $record_id Record ID.
	 * @return array|WP_Error Record data or WP_Error on failure.
	 */
	public function get_record( $table_id, $record_id ) {
		$url = $this->api_url . '/' . $this->base_id . '/' . $table_id . '/' . $record_id;
		$url = add_query_arg( 'returnFieldsByFieldId', 'true', $url );
		return $this->make_request( $url );
	}

	/**
	 * Download an attachment from Airtable.
	 *
	 * @param array $attachment Attachment data from Airtable.
	 * @return int|WP_Error Attachment ID on success, WP_Error on failure.
	 */
	public function download_attachment( $attachment ) {
		if ( empty( $attachment['url'] ) ) {
			return new WP_Error( 'invalid_attachment', __( 'Invalid attachment data.', 'airtable-sync' ) );
		}

		$url = $attachment['url'];
		$filename = isset( $attachment['filename'] ) ? $attachment['filename'] : basename( wp_parse_url( $url, PHP_URL_PATH ) );

		// Download file
		$tmp_file = download_url( $url );

		if ( is_wp_error( $tmp_file ) ) {
			return $tmp_file;
		}

		// Prepare file array
		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $tmp_file,
		);

		// Import to media library
		$attachment_id = media_handle_sideload( $file_array, 0 );

		// Clean up temp file on error
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp_file );
			return $attachment_id;
		}

		return $attachment_id;
	}

	/**
	 * Make an API request.
	 *
	 * @param string $url     Request URL.
	 * @param string $method  HTTP method (GET, POST, etc).
	 * @param array  $body    Request body for POST/PATCH requests.
	 * @return array|WP_Error Response data or WP_Error on failure.
	 */
	private function make_request( $url, $method = 'GET', $body = null ) {
		$args = array(
			'method'  => $method,
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'  => 'application/json',
			),
			'timeout' => 30,
		);

		if ( $body ) {
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Handle error responses
		if ( $status_code >= 400 ) {
			$error_message = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'Unknown API error', 'airtable-sync' );
			return new WP_Error( 'api_error', $error_message, array( 'status' => $status_code ) );
		}

		return $data;
	}
}
