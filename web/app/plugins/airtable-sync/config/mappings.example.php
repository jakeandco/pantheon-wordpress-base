<?php
/**
 * Example Airtable field mappings configuration
 *
 * Copy this file to mappings.php and customize for your project.
 *
 * @package Airtable_Sync
 */

return array(
	// Example mapping: Airtable "Projects" table to WordPress "project" post type
	array(
		'table_id'   => 'tblXXXXXXXXXXXXXX', // Get from Airtable table URL
		'table_name' => 'Projects', // Human-readable name
		'post_type'  => 'project', // WordPress post type
		'view_id'    => '', // Optional: Only sync records from this view
		'view_name'  => '', // Optional: Human-readable view name
		'field_mappings' => array(
			// Map Airtable "Project Name" field to WordPress post title
			array(
				'airtable_field_id'   => 'fldXXXXXXXXXXXXXX',
				'airtable_field_name' => 'Project Name',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
				'destination_name'    => 'Post Title',
			),
			// Map Airtable "Description" field to WordPress post content
			array(
				'airtable_field_id'   => 'fldYYYYYYYYYYYYYY',
				'airtable_field_name' => 'Description',
				'airtable_field_type' => 'multilineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_content',
				'destination_name'    => 'Post Content',
			),
			// Map Airtable "Summary" field to WordPress post excerpt
			array(
				'airtable_field_id'   => 'fldZZZZZZZZZZZZZZ',
				'airtable_field_name' => 'Summary',
				'airtable_field_type' => 'multilineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_excerpt',
				'destination_name'    => 'Post Excerpt',
			),
			// Map Airtable "Project Type" field to WordPress taxonomy
			array(
				'airtable_field_id'   => 'fldAAAAAAAAAAAAA',
				'airtable_field_name' => 'Project Type',
				'airtable_field_type' => 'multipleSelects',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'project_type',
				'destination_name'    => 'Project Type',
			),
			// Map Airtable "Budget" field to ACF custom field
			array(
				'airtable_field_id'   => 'fldBBBBBBBBBBBBB',
				'airtable_field_name' => 'Budget',
				'airtable_field_type' => 'number',
				'destination_type'    => 'acf',
				'destination_key'     => 'field_budget', // ACF field key or name
				'destination_name'    => 'Budget',
			),
			// Map Airtable "Team Members" linked record field to ACF relationship field
			// This maps Airtable record IDs to WordPress post IDs by looking up posts
			// with matching _airtable_id post meta values
			array(
				'airtable_field_id'   => 'fldCCCCCCCCCCCCC',
				'airtable_field_name' => 'Team Members',
				'airtable_field_type' => 'linkedRecord', // Important: marks this as a relationship field
				'destination_type'    => 'acf',
				'destination_key'     => 'team_members', // ACF relationship field key or name
				'destination_name'    => 'Team Members',
				'linked_post_type'    => 'person', // Optional: restrict lookup to this post type for better performance
			),
			// Map multiple Airtable fields to a single ACF link field
			// ACF link fields have 'url', 'title', and 'target' properties
			// You can map different Airtable fields to each property using 'acf_link_property'
			array(
				'airtable_field_id'   => 'fldDDDDDDDDDDDDD',
				'airtable_field_name' => 'External URL',
				'airtable_field_type' => 'url',
				'destination_type'    => 'acf',
				'destination_key'     => 'external_link', // Same destination_key for both mappings
				'destination_name'    => 'External Link',
				'acf_link_property'   => 'url', // Specifies this maps to the 'url' property
			),
			array(
				'airtable_field_id'   => 'fldEEEEEEEEEEEEE',
				'airtable_field_name' => 'Link Title',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'acf',
				'destination_key'     => 'external_link', // Same destination_key - will be merged
				'destination_name'    => 'External Link Title',
				'acf_link_property'   => 'title', // Specifies this maps to the 'title' property
			),
		),
	),

	// Add more table mappings as needed
	// array(
	//     'table_id' => 'tblAnotherTable',
	//     'table_name' => 'Team Members',
	//     'post_type' => 'team_member',
	//     'field_mappings' => array(
	//         // ... field mappings
	//     ),
	// ),
);
