<?php
/**
 * Airtable Field Transformer
 *
 * Transforms Airtable field values to WordPress/ACF compatible formats.
 *
 * @package Airtable_Sync
 */

class Airtable_Field_Transformer {

	/**
	 * Transform a field value based on its type.
	 *
	 * @param mixed $value         The field value from Airtable.
	 * @param string $field_type   The Airtable field type.
	 * @param array  $field_mapping The complete field mapping configuration.
	 * @return mixed Transformed value.
	 */
	public static function transform( $value, $field_type, $field_mapping ) {
		// Handle empty values
		if ( $value === null || $value === '' ) {
			return self::get_empty_value( $field_type, $field_mapping );
		}

		// Check if this is a repeater field
		if ( isset( $field_mapping['destination_subfield'] ) ) {
			return self::transform_repeater_field( $value, $field_type, $field_mapping );
		}

		// Transform based on field type
		switch ( $field_type ) {
			case 'attachment':
			case 'multipleAttachments':
				return self::transform_attachment( $value );

			case 'url':
				return self::transform_url( $value, $field_mapping );

			case 'email':
				return sanitize_email( $value );

			case 'checkbox':
				return self::transform_checkbox( $value );

			case 'multipleSelects':
			case 'multipleLookupValues':
				return self::transform_multiple_values( $value );

			case 'number':
			case 'currency':
			case 'percent':
			case 'duration':
				return self::transform_number( $value );

			case 'date':
			case 'dateTime':
				return self::transform_date( $value );

			case 'singleLineText':
			case 'multilineText':
			case 'richText':
			case 'formula':
			case 'rollup':
			default:
				return self::transform_text( $value );
		}
	}

	/**
	 * Transform attachment field.
	 *
	 * @param mixed $value Attachment data from Airtable.
	 * @return int|null Attachment ID or null.
	 */
	private static function transform_attachment( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}

		// Handle single attachment (take first if multiple)
		$attachment = is_array( $value[0] ) ? $value[0] : $value;

		// Download and import the attachment
		$credentials = Airtable_Sync_Config::get_credentials();
		$api = new Airtable_API( $credentials['api_key'], $credentials['base_id'] );
		$attachment_id = $api->download_attachment( $attachment );

		return is_wp_error( $attachment_id ) ? null : $attachment_id;
	}

	/**
	 * Transform URL field.
	 *
	 * @param string $value         URL value.
	 * @param array  $field_mapping Field mapping configuration.
	 * @return string|array URL string or ACF link array.
	 */
	private static function transform_url( $value, $field_mapping ) {
		// If this is going into an ACF link field, format as link array
		if ( isset( $field_mapping['destination_subfield'] ) && $field_mapping['destination_subfield'] === 'social_link' ) {
			return array(
				'url'    => esc_url( $value ),
				'title'  => isset( $field_mapping['link_title'] ) ? $field_mapping['link_title'] : '',
				'target' => '',
			);
		}

		return esc_url( $value );
	}

	/**
	 * Transform checkbox field.
	 *
	 * @param mixed $value Checkbox value from Airtable.
	 * @return bool Boolean value.
	 */
	private static function transform_checkbox( $value ) {
		// Airtable checkboxes are either true or don't exist
		return (bool) $value;
	}

	/**
	 * Transform multiple values (multipleSelects, etc).
	 *
	 * @param mixed $value Array of values from Airtable.
	 * @return array Array of values.
	 */
	private static function transform_multiple_values( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		return array_map( 'sanitize_text_field', $value );
	}

	/**
	 * Transform number field.
	 *
	 * @param mixed $value Number value from Airtable.
	 * @return int|float Number value.
	 */
	private static function transform_number( $value ) {
		return is_numeric( $value ) ? ( strpos( $value, '.' ) !== false ? floatval( $value ) : intval( $value ) ) : 0;
	}

	/**
	 * Transform date field.
	 *
	 * @param string $value Date string from Airtable.
	 * @return string Date in WordPress format.
	 */
	private static function transform_date( $value ) {
		// Airtable dates are ISO 8601 format
		$timestamp = strtotime( $value );
		return $timestamp ? gmdate( 'Y-m-d H:i:s', $timestamp ) : '';
	}

	/**
	 * Transform text field.
	 *
	 * @param string $value Text value from Airtable.
	 * @return string Sanitized text.
	 */
	private static function transform_text( $value ) {
		return sanitize_textarea_field( $value );
	}

	/**
	 * Transform a field that goes into a repeater.
	 *
	 * @param mixed $value         Field value from Airtable.
	 * @param string $field_type   Airtable field type.
	 * @param array  $field_mapping Field mapping configuration.
	 * @return array Repeater row data.
	 */
	private static function transform_repeater_field( $value, $field_type, $field_mapping ) {
		$subfield_name = $field_mapping['destination_subfield'];
		$repeater_mode = isset( $field_mapping['repeater_mode'] ) ? $field_mapping['repeater_mode'] : 'append';

		// Transform the value based on field type
		$transformed_value = null;

		switch ( $field_type ) {
			case 'url':
				// For link fields, create ACF link array
				$transformed_value = array(
					'url'    => esc_url( $value ),
					'title'  => isset( $field_mapping['link_title'] ) ? $field_mapping['link_title'] : '',
					'target' => '',
				);
				break;

			default:
				// For text fields, just sanitize
				$transformed_value = sanitize_text_field( $value );
				break;
		}

		// Return as single-row array
		// The sync engine will handle appending multiple rows
		return array(
			array(
				$subfield_name => $transformed_value,
			),
		);
	}

	/**
	 * Get empty value for a field type.
	 *
	 * @param string $field_type   Field type.
	 * @param array  $field_mapping Field mapping.
	 * @return mixed Empty value appropriate for the field type.
	 */
	private static function get_empty_value( $field_type, $field_mapping ) {
		// Handle repeater fields
		if ( isset( $field_mapping['destination_subfield'] ) ) {
			return array(); // Empty repeater
		}

		switch ( $field_type ) {
			case 'checkbox':
				return false;

			case 'number':
			case 'currency':
			case 'percent':
			case 'duration':
				return 0;

			case 'multipleSelects':
			case 'multipleLookupValues':
			case 'attachment':
			case 'multipleAttachments':
				return array();

			default:
				return '';
		}
	}
}
