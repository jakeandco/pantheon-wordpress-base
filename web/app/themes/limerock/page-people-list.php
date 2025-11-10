<?php
/**
 * Template Name: People List (Diagnostic)
 * Description: Displays all people with photos for debugging Airtable sync
 *
 * @package  WordPress
 * @subpackage  Timber
 */

$context = Timber::context();

// Query all people, sorted by last_name
$args = array(
	'post_type'      => 'person',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_key'       => 'last_name',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
);

$people_posts = Timber::get_posts( $args );

// Build array with all diagnostic data
$people_data = array();
foreach ( $people_posts as $person ) {
	$has_thumbnail = has_post_thumbnail( $person->ID );
	$thumbnail_id = get_post_thumbnail_id( $person->ID );

	$people_data[] = array(
		'id'              => $person->ID,
		'title'           => $person->title,
		'link'            => $person->link,
		'thumbnail'       => $person->thumbnail,
		'first_name'      => get_field( 'first_name', $person->ID ),
		'last_name'       => get_field( 'last_name', $person->ID ),
		'airtable_id'     => get_post_meta( $person->ID, '_airtable_id', true ),
		'airtable_synced' => get_post_meta( $person->ID, '_airtable_last_synced', true ),
		'has_thumbnail'   => $has_thumbnail,
		'thumbnail_id'    => $thumbnail_id ? $thumbnail_id : '',
	);
}

// Calculate statistics
$people_with_photos = array_filter( $people_data, function( $person ) {
	return $person['has_thumbnail'];
} );

$context['people'] = $people_data;
$context['total_people'] = count( $people_data );
$context['total_with_photos'] = count( $people_with_photos );
$context['total_without_photos'] = count( $people_data ) - count( $people_with_photos );

Timber::render( 'page-people-list.twig', $context );
