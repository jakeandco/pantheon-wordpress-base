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
				'airtable_field_type' => 'multipleSelects',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'appointment-type',
			),

		),
	),

	// Research Projects
	array(
		'table_id'   => 'tbl5SbpY1bl46agle',
		'table_name' => 'Research Projects',
		'post_type'  => 'project',
		'view_id'    => 'viwfklFj5OMUVvX6s',
		'view_name'  => 'Web Sync',
		'field_mappings' => array(

			// ======================
			// CORE WORDPRESS FIELDS
			// ======================

			// Title → Post Title
			array(
				'airtable_field_id'   => 'fldW98rMPAraQaHCr',
				'airtable_field_name' => 'Name',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
				'destination_name'    => 'Post Title',
			),

			// ======================
			// ACF RELATIONSHIP FIELDS
			// ======================

			// Researchers → researchers (linked to People)
			array(
				'airtable_field_id'   => 'fld75QZrdCtJA6Ada',
				'airtable_field_name' => 'Researchers',
				'airtable_field_type' => 'linkedRecord',
				'destination_type'    => 'acf',
				'destination_key'     => 'researchers',
				'destination_name'    => 'Researchers',
				'linked_post_type'    => 'person',
			),

			// ======================
			// TAXONOMY
			// ======================

			// Research Area → research-area taxonomy
			array(
				'airtable_field_id'   => 'fldZDQKZgLZFDmLTJ',
				'airtable_field_name' => 'Research Areas Lookup',
				'airtable_field_type' => 'multipleLookupValues',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'tax-research-area',
				'destination_name'    => 'Research Area',
			),

		),
	),

	// Publications
	array(
		'table_id'   => 'tblZDd5cZnIzlK4Bf',
		'table_name' => 'Publications',
		'post_type'  => 'publication',
		'view_id'    => 'viwoB9m67sdj6SAqf',
		'view_name'  => 'Web Sync',
		'field_mappings' => array(

			// ======================
			// CORE WORDPRESS FIELDS
			// ======================

			// Title → Post Title
			array(
				'airtable_field_id'   => 'fldoWrgN9nN0uyjP9',
				'airtable_field_name' => 'Title',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
				'destination_name'    => 'Post Title',
			),

			// ======================
			// ACF RELATIONSHIP FIELDS
			// ======================

			// Author(s) → authors (linked to People)
			array(
				'airtable_field_id'   => 'fldLvckPrvxRvZA9O',
				'airtable_field_name' => 'Author(s)',
				'airtable_field_type' => 'linkedRecord',
				'destination_type'    => 'acf',
				'destination_key'     => 'authors', // ACF relationship field
				'destination_name'    => 'Authors',
				'linked_post_type'    => 'person', // Links to Person post type
			),

			// Projects → projects (linked to Projects)
			array(
				'airtable_field_id'   => 'fldmo6QDi3cst2dNV',
				'airtable_field_name' => 'Projects',
				'airtable_field_type' => 'linkedRecord',
				'destination_type'    => 'acf',
				'destination_key'     => 'projects',
				'destination_name'    => 'Projects',
				'linked_post_type'    => 'project',
			),

			// ======================
			// ACF TEXT/URL/DATE FIELDS
			// ======================

			// Publication → publication (text field)
			array(
				'airtable_field_id'   => 'fldQ1aPHNozZFliLP',
				'airtable_field_name' => 'Publication',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'acf',
				'destination_key'     => 'publication_name',
				'destination_name'    => 'Publication',
			),

			// Date → date (date field)
			array(
				'airtable_field_id'   => 'fld2sBl9KH8ifp2RZ',
				'airtable_field_name' => 'Date',
				'airtable_field_type' => 'date',
				'destination_type'    => 'acf',
				'destination_key'     => 'date',
				'destination_name'    => 'Date',
			),

			// URL → url (url field)
			array(
				'airtable_field_id'   => 'fldjfeBLWjoJkenVu',
				'airtable_field_name' => 'URL',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'acf',
				'destination_key'     => 'url',
				'destination_name'    => 'URL',
			),

			// ======================
			// TAXONOMY
			// ======================

			// Publication Type → publication-type taxonomy
			array(
				'airtable_field_id'   => 'fldkywrEOkfEIm5H9',
				'airtable_field_name' => 'Publication Type',
				'airtable_field_type' => 'singleSelect',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'publication-type',
				'destination_name'    => 'Publication Type',
			),

			// Research Area → tax-research-area taxonomy
			array(
				'airtable_field_id'   => 'fldV7gFEGmGTMJT5l',
				'airtable_field_name' => 'Research Areas Lookup',
				'airtable_field_type' => 'multipleLookupValues',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'tax-research-area',
				'destination_name'    => 'Research Area',
			),

		),
	),

	// News
	array(
		'table_id'   => 'tblIQYdgKCL3MdK3p',
		'table_name' => 'News + Media',
		'post_type'  => 'post',
		'view_id'    => 'viwkBli7bRXuSBW8W', // 
		'view_name'  => 'Web Sync', // Optional: Add view name if filtering
		'field_mappings' => array(

			// ======================
			// CORE WORDPRESS FIELDS
			// ======================

			// Title → Post Title
			array(
				'airtable_field_id'   => 'fldJrlb3D6JoYXDLZ',
				'airtable_field_name' => 'Title',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
				'destination_name'    => 'Post Title',
			),

			// Date → Post Date
			array(
				'airtable_field_id'   => 'fldPWiohqLzwPDogo',
				'airtable_field_name' => 'Date',
				'airtable_field_type' => 'date',
				'destination_type'    => 'core',
				'destination_key'     => 'post_date',
				'destination_name'    => 'Post Date',
			),

			// ======================
			// ACF TEXT FIELDS
			// ======================

			// Publication → publication_name (text field)
			array(
				'airtable_field_id'   => 'fldE4w2cuoq8bdEy0',
				'airtable_field_name' => 'Publication',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'acf',
				'destination_key'     => 'publication_name',
				'destination_name'    => 'Publication',
			),

			// ======================
			// ACF CHECKBOX FIELDS
			// ======================

			// External Link → is_external_link (checkbox field)
			array(
				'airtable_field_id'   => 'fldIycP6FiCTWF45I',
				'airtable_field_name' => 'External Link',
				'airtable_field_type' => 'checkbox',
				'destination_type'    => 'acf',
				'destination_key'     => 'is_external_link',
				'destination_name'    => 'Is External Link',
			),

			// ======================
			// ACF RELATIONSHIP FIELDS
			// ======================

			// Related People → related_people (linked to People)
			array(
				'airtable_field_id'   => 'fldkxbk6BaU5r1EIG',
				'airtable_field_name' => 'Related People',
				'airtable_field_type' => 'linkedRecord',
				'destination_type'    => 'acf',
				'destination_key'     => 'related_people', // ACF relationship field
				'destination_name'    => 'Related People',
				'linked_post_type'    => 'person', // Links to Person post type
			),

			// ======================
			// ACF LINK FIELD
			// ======================

			// External URL → external_link (ACF link field - URL property)
			array(
				'airtable_field_id'   => 'fldd9neUqLPUJo9WT',
				'airtable_field_name' => 'URL',
				'airtable_field_type' => 'url',
				'destination_type'    => 'acf',
				'destination_key'     => 'external_link',
				'destination_name'    => 'External Link',
				'acf_link_property'   => 'url',
			),

			// Publication → external_link (ACF link field - Title property)
			array(
				'airtable_field_id'   => 'fldE4w2cuoq8bdEy0',
				'airtable_field_name' => 'Publication',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'acf',
				'destination_key'     => 'external_link',
				'destination_name'    => 'External Link Title',
				'acf_link_property'   => 'title',
			),

			// ======================
			// TAXONOMY
			// ======================

			// Category → category taxonomy
			array(
				'airtable_field_id'   => 'fld3cPyu9uVVhOqmG',
				'airtable_field_name' => 'Category',
				'airtable_field_type' => 'singleSelect',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'category',
				'destination_name'    => 'Category',
			),

			// Research Area → tax-research-area taxonomy
			array(
				'airtable_field_id'   => 'fldQOLgAED3r5voNN',
				'airtable_field_name' => 'Research Area',
				'airtable_field_type' => 'multipleLookupValues',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'tax-research-area',
				'destination_name'    => 'Research Area',
			),

		),
	),

	// Events
	array(
		'table_id'   => 'tblT75S68TxYfZJzP',
		'table_name' => 'Events',
		'post_type'  => 'event',
		'view_id'    => 'viwuCjynfgvHlafT6', // Optional: Add view ID if filtering
		'view_name'  => 'Web Sync', // Optional: Add view name if filtering
		'field_mappings' => array(

			// ======================
			// CORE WORDPRESS FIELDS
			// ======================

			// Title → Post Title
			array(
				'airtable_field_id'   => 'fld5rPEkZW2EMOyW7',
				'airtable_field_name' => 'Title',
				'airtable_field_type' => 'singleLineText',
				'destination_type'    => 'core',
				'destination_key'     => 'post_title',
				'destination_name'    => 'Post Title',
			),

			// ======================
			// ACF DATE/TIME FIELDS
			// ======================

			// Start Date → event_start_date (date_time_picker)
			array(
				'airtable_field_id'   => 'fldvvD4uvVC0AdTw6',
				'airtable_field_name' => 'Start Date',
				'airtable_field_type' => 'dateTime',
				'destination_type'    => 'acf',
				'destination_key'     => 'event_start_date',
				'destination_name'    => 'Event Start Date',
			),

			// End Date → event_end_date (date_time_picker)
			array(
				'airtable_field_id'   => 'fldY4owpZXnayYRMZ',
				'airtable_field_name' => 'End Date',
				'airtable_field_type' => 'date',
				'destination_type'    => 'acf',
				'destination_key'     => 'event_end_date',
				'destination_name'    => 'Event End Date',
			),

			// ======================
			// ACF RELATIONSHIP FIELDS
			// ======================

			// Related People → related_people (linked to People)
			array(
				'airtable_field_id'   => 'fldSXkyhY7l54KaxK',
				'airtable_field_name' => 'Related People',
				'airtable_field_type' => 'linkedRecord',
				'destination_type'    => 'acf',
				'destination_key'     => 'related_people',
				'destination_name'    => 'Related People',
				'linked_post_type'    => 'person', // Links to Person post type
			),

			// ======================
			// ACF REPEATER FIELDS
			// ======================

			// Registration URL → registration_links repeater
			// NOTE: Appends as new row with link subfield
			array(
				'airtable_field_id'   => 'fldu8PLfGctGxTSSe',
				'airtable_field_name' => 'Registration URL',
				'airtable_field_type' => 'url',
				'destination_type'    => 'acf',
				'destination_key'     => 'registration_links', // Repeater field
				'destination_subfield' => 'link', // Link subfield within repeater
				'destination_name'    => 'Registration Links',
				'repeater_mode'       => 'append', // Append as new row
				'link_title'          => 'Register', // Title for the ACF link field
			),

			// ======================
			// TAXONOMY
			// ======================

			// Event Type/Category → event-category taxonomy
			array(
				'airtable_field_id'   => 'fldd1Fz8nX5gXRBSs',
				'airtable_field_name' => 'Event Type',
				'airtable_field_type' => 'multipleSelects',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'event-category',
				'destination_name'    => 'Event Category',
			),

			// Modality → modality taxonomy
			array(
				'airtable_field_id'   => 'fldKDmThXHrHrIaD7',
				'airtable_field_name' => 'Modality',
				'airtable_field_type' => 'singleSelect',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'modality',
				'destination_name'    => 'Modality',
			),

			// Research Area → tax-research-area taxonomy
			array(
				'airtable_field_id'   => 'fldNo2NEytxt7ZzV6',
				'airtable_field_name' => 'Research Area',
				'airtable_field_type' => 'multipleLookupValues',
				'destination_type'    => 'taxonomy',
				'destination_key'     => 'tax-research-area',
				'destination_name'    => 'Research Area',
			),

		),
	),

);
