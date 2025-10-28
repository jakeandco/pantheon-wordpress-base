<?php
/**
 * Airtable field mappings configuration
 *
 * Define your table-to-post-type mappings here.
 * See mappings.example.php for detailed examples.
 *
 * @package Airtable_Sync
 */

return array(

	// People
	array(
		'table_id'   => 'tblMObBbMdYgDLpFp',
		'table_name' => 'People',
		'post_type'  => 'person',
		'view_id'    => 'viwKvWxPJfEuuL882', // Web Sync view
		'view_name'  => 'Web Sync',
		'field_mappings' => array(

			// ======================
			// CORE WORDPRESS FIELDS
			// ======================

			// Name (Full) → Post Title
			array(
				'airtable_field_id'   => 'fldBHE9CMtN35wgRL',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
			),

			// Photo → Featured Image
			array(
				'airtable_field_id'   => 'fldrWSwWu5iD9caih',
				'airtable_field_type' => 'attachment',
				'destination_type'    => 'core',
				'destination_key'     => 'post_thumbnail',
			),

			// ======================
			// ACF TEXT FIELDS
			// ======================

			// Firstname → first_name
			array(
				'airtable_field_id'   => 'fldkTBsLyTm9KWLLI',
				'destination_type'    => 'acf',
				'destination_key'     => 'first_name',
			),

			// Lastname → last_name
			array(
				'airtable_field_id'   => 'fldjxHtMqIDjtFxCK',
				'destination_type'    => 'acf',
				'destination_key'     => 'last_name',
			),

			// Affiliation → affiliation
			array(
				'airtable_field_id'   => 'fldX9hYQhhNjYMvqJ',
				'destination_type'    => 'acf',
				'destination_key'     => 'affiliation',
			),

			// Email address → email
			array(
				'airtable_field_id'   => 'fldavD5o31iGIu3Jb',
				'airtable_field_type' => 'email',
				'destination_type'    => 'acf',
				'destination_key'     => 'email',
			),

			// ======================
			// ACF REPEATER FIELDS
			// ======================

			// Professional Title → titles repeater
			// NOTE: Creates a single row in the titles repeater with the title subfield populated
			array(
				'airtable_field_id'     => 'fldiJxlAn0T4A5iBo',
				'destination_type'      => 'acf',
				'destination_key'       => 'titles',
				'destination_subfield'  => 'title', // The subfield within the repeater
				'repeater_mode'         => 'single_row', // Creates one row per field
			),

			// LinkedIn → social_channels repeater
			// NOTE: Creates a row in social_channels with a link field
			array(
				'airtable_field_id'     => 'fldGj757UP31nYX7P',
				'airtable_field_type'   => 'url',
				'destination_type'      => 'acf',
				'destination_key'       => 'social_channels',
				'destination_subfield'  => 'social_link', // The link subfield
				'repeater_mode'         => 'append', // Append as new row
				'link_title'            => 'LinkedIn', // Title for the ACF link field
			),

			// Google Scholar → social_channels repeater
			array(
				'airtable_field_id'     => 'fldXkWYrXujZ65hCn',
				'airtable_field_type'   => 'url',
				'destination_type'      => 'acf',
				'destination_key'       => 'social_channels',
				'destination_subfield'  => 'social_link',
				'repeater_mode'         => 'append',
				'link_title'            => 'Google Scholar',
			),

			// Personal Website → social_channels repeater
			array(
				'airtable_field_id'     => 'fldqfQHE28rmcEvq3',
				'airtable_field_type'   => 'url',
				'destination_type'      => 'acf',
				'destination_key'       => 'social_channels',
				'destination_subfield'  => 'social_link',
				'repeater_mode'         => 'append',
				'link_title'            => 'Personal Website',
			),

			// ======================
			// ACF TRUE/FALSE FIELDS
			// ======================

			// Display on People Page → display_on_archive
			array(
				'airtable_field_id'   => 'fld1lyZiltpvLnF9j',
				'airtable_field_type' => 'checkbox',
				'destination_type'    => 'acf',
				'destination_key'     => 'display_on_archive',
			),

			// Detail Page? → display_detail_page
			array(
				'airtable_field_id'   => 'fldYaPu3yq32jR7pl',
				'airtable_field_type' => 'checkbox',
				'destination_type'    => 'acf',
				'destination_key'     => 'display_detail_page',
			),

			// ======================
			// TAXONOMY
			// ======================

			// DEL Appointment Type → appointment-type taxonomy
			// NOTE: This is a multipleSelects field - can contain multiple values
			// The sync engine will create/assign multiple taxonomy terms
			array(
				'airtable_field_id'   => 'fldchLUmsSHk3e97S',
				'airtable_field_type' => 'multipleSelects', // Important: tells sync to handle as array
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'appointment-type',
			),

		),
	),

);
