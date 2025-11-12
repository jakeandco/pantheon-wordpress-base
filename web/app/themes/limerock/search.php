<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$templates = array('search.twig', 'archive.twig', 'index.twig');
$context = Timber::context();

$research_terms = get_terms([
    'taxonomy'   => 'tax-research-area',
    'hide_empty' => true,
]);

$searchable_post_types = get_post_types([
    'public' => true,
    'exclude_from_search' => false,
], 'objects');

$post_type_options = [];

foreach ($searchable_post_types as $post_type) {
    if ($post_type->name === 'attachment') {
        continue;
    }

    $post_type_options[] = [
        'value' => $post_type->name,
        'label' => $post_type->label,
    ];
}

$context['posts']          = Timber::get_posts();
$context['search_query']   = get_search_query();
$context['results_total']  = $context['posts']->found_posts;
$context['ajax_url']       = esc_url(home_url());
$context['sort_options']   = get_archive_sort_options();
$context['research_terms'] = $research_terms;
$context['type_options']   = $post_type_options;
$context['is_search']      = is_search();

Timber::render($templates, $context);
