<?php

/**
 * Render callback to prepare and display a registered block using Timber.
 *
 * @param    array    $block The block block.
 * @param    string   $content The block content.
 * @param    bool     $is_preview Whether or not the block is being rendered for editing preview.
 * @param    int      $post_id The current post being edited or viewed.
 * @param    WP_Block $wp_block The block instance (since WP 5.5).
 * @return   void
 */
function LimeRockTheme_block_render_callback($block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null)
{
	// Create the slug of the block using the name property in the block.json.
	$slug = str_replace('acf/', '', $block['name']);
	$slug = str_replace('limerock/', '', $block['name']);

	$context = Timber\Timber::context();

	// Store block attributes.
	$context['post_id']    = $post_id;
	$context['slug']       = $slug;

	// Store whether the block is being rendered in the editor or on the frontend.
	$context['is_preview'] = $is_preview;

	// Store field values. These are the fields from your ACF field group for the block.
	$context['fields'] = get_fields();
	$context['field_objects'] = get_field_objects();

    if ($slug === 'work-archive') {
        $featured_post = $context['fields']['featured_work'] ?? null;
        $posts_per_page = $context['fields']['posts_per_page'] ?? 12;
        $paged = get_query_var('paged') ?: 1;

        $result = limerock_get_work_query_args([
            'featured_post'  => $featured_post,
            'paged'          => $paged,
            'posts_per_page' => $posts_per_page,
            'search'         => $_GET['search'] ?? '',
            'type'           => $_GET['type'] ?? [],
            'research_area'  => $_GET['research_area'] ?? [],
            'sort'           => $_GET['sort'] ?? '',
        ]);

        $post_type_options = [
            [ 'value' => 'post', 'label' => 'Post' ],
            [ 'value' => 'project', 'label' => 'Project' ],
            [ 'value' => 'publication', 'label' => 'Publication' ]
        ];

        $wp_query = new WP_Query($result['query_args']);

        $context['posts'] = new Timber\PostQuery($wp_query);
        $context['hide_featured'] = $result['hide_featured'];
        $context['ajax_url'] = esc_url(get_permalink());
        $context['type_options'] = $post_type_options;
        $context['research_terms'] = get_research_terms_for_work();
        $context['sort_options'] = get_archive_sort_options();
    }

    if ($slug === 'people-archive') {
        $paged = get_query_var('paged') ?: 1;
        $posts_per_page = $context['fields']['posts_per_page'] ?? 30;
        $leadership_ids = get_people_leadership_ids();
        $leadership_people = !empty($leadership_ids)
        ? Timber::get_posts($leadership_ids)
        : [];
        $appointment_terms = get_terms([
            'taxonomy'   => 'appointment-type',
            'hide_empty' => true,
        ]);

        $result = limerock_get_people_query_args([
            'paged'          => $paged,
            'posts_per_page' => $posts_per_page,
            'search'         => $_GET['search'] ?? '',
            'research_area'  => $_GET['research_area'] ?? [],
            'appointment'    => $_GET['appointment'] ?? [],
            'sort'           => $_GET['sort'] ?? '',
            'leadership_ids' => $leadership_ids,
        ]);

        $wp_query = new WP_Query($result['query_args']);

        $context['posts'] = new Timber\PostQuery($wp_query);
        $context['hide_featured'] = $result['hide_featured'];
        $context['ajax_url'] = esc_url(get_permalink());
        $context['research_terms'] = get_research_terms_for_people();
        $context['appointment_terms'] = $appointment_terms;
        $context['sort_options'] = get_archive_sort_options();
        $context['leadership_people'] = $leadership_people;
    }

    if ($slug === 'news-list') {
        $post_type = 'post';
        $fields = $context['fields'];
        $selections = [];

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $posts_per_page = !empty($fields['posts_per_page'])
            ? intval($fields['posts_per_page'])
            : 3;

        if (!empty($fields['selection_type'])) {
            switch ($fields['selection_type']) {
                case 'manual':
                    $selections = $fields['selections'] ?? [];
                    break;

                case 'all':
                    $selections = Timber::get_posts([
                        'post_type'      => $post_type,
                        'post_status'    => ['publish'],
                        'posts_per_page' => $posts_per_page,
                        'paged'          => $paged,
                    ]);
                    break;

                case 'related':
                    $query_args = [
                        'post_type'      => $post_type,
                        'post_status'    => ['publish'],
                        'posts_per_page' => $posts_per_page,
                        'paged'          => $paged,
                    ];

                    if (!empty($fields['related_research_areas'])) {
                        $query_args['tax_query'] = [
                            [
                                'taxonomy' => 'tax-research-area',
                                'field'    => 'term_id',
                                'terms'    => $fields['related_research_areas'],
                            ],
                        ];
                        $selections = Timber::get_posts($query_args);
                    }
                    break;
            }
        }

        $context['selections'] = $selections;
    }

    if ($slug === 'events-list') {
        $post_type = 'event';
        $fields = $context['fields'];
        $selections = [];

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $posts_per_page = !empty($fields['posts_per_page'])
            ? intval($fields['posts_per_page'])
            : 3;

        if (!empty($fields['selection_type'])) {
            switch ($fields['selection_type']) {
                case 'manual':
                    $selections = $fields['selections'] ?? [];
                    break;

                case 'all':
                    $selections = Timber::get_posts([
                        'post_type'      => $post_type,
                        'post_status'    => ['publish', 'future'],
                        'posts_per_page' => $posts_per_page,
                        'paged'          => $paged,
                    ]);
                    break;

                case 'past':
                    $today = date('Y-m-d');
                    $selections = Timber::get_posts([
                        'post_type'      => $post_type,
                        'post_status'    => ['publish', 'future'],
                        'posts_per_page' => $posts_per_page,
                        'paged'          => $paged,
                        'meta_query'     => [
                            [
                                'key'     => 'event_start_date',
                                'value'   => $today,
                                'compare' => 'LIKE',
                            ],
                        ],
                    ]);
                    break;

                case 'related':
                    $query_args = [
                        'post_type'      => $post_type,
                        'post_status'    => ['publish', 'future'],
                        'posts_per_page' => $posts_per_page,
                        'paged'          => $paged,
                    ];

                    $tax_query = ['relation' => 'OR'];

                    if (!empty($fields['related_research_areas'])) {
                        $tax_query[] = [
                            'taxonomy' => 'tax-research-area',
                            'field'    => 'term_id',
                            'terms'    => $fields['related_research_areas'],
                        ];
                    }

                    if (!empty($fields['related_event_categories'])) {
                        $tax_query[] = [
                            'taxonomy' => 'event-category',
                            'field'    => 'term_id',
                            'terms'    => $fields['related_event_categories'],
                        ];
                    }

                    if (count($tax_query) > 1) {
                        $query_args['tax_query'] = $tax_query;
                        $selections = Timber::get_posts($query_args);
                    }
                    break;
            }
        }

        $context['selections'] = $selections;
    }

	if (! empty($block['data']['is_example'])) {
		$context['is_example'] = true;
		$context['fields'] = $block['data'];
	}

	$classes =  [];

	$block['className'] = implode(' ', $classes);

	$context['block']      = $block;
	// Render the block.
	Timber\Timber::render(
		'@blocks/' . $slug . '/' . $slug . '.twig',
		$context
	);
}

function limerock_get_work_query_args($args = []) {
    $featured_post = $args['featured_post'] ?? null;
    $paged         = $args['paged'] ?? 1;
    $featured_id   = $featured_post ? [$featured_post->ID] : [];

    $search_query    = sanitize_text_field($args['search'] ?? '');
    $type_filter     = array_filter((array) ($args['type'] ?? []));
    $research_filter = array_filter((array) ($args['research_area'] ?? []));
    $sort_raw        = $args['sort'] ?? '';
    $sort_filter     = is_array($sort_raw) ? reset($sort_raw) : $sort_raw;
    $sort_filter     = sanitize_text_field($sort_filter);

    $query_args = [
        'post_type'      => ['post', 'project', 'publication'],
        'posts_per_page' => $args['posts_per_page'],
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => $featured_id,
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'is_external_link',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => 'is_external_link',
                'value'   => '0',
                'compare' => '=',
            ],
        ],
    ];

    $hide_featured = false;

    // Apply search
    if ($search_query) {
        $query_args['s'] = $search_query;
        $hide_featured = true;
    }

    // Apply type filter
    if ($type_filter) {
        $query_args['post_type'] = $type_filter;
        $hide_featured = true;
    }

    // Apply research area filter
    if ($research_filter) {
        $query_args['tax_query'] = [
            [
                'taxonomy' => 'tax-research-area',
                'field'    => 'slug',
                'terms'    => $research_filter,
            ],
        ];
        $hide_featured = true;
    }

    // Apply sorting
    $sort_options = get_archive_sort_query_options();
    if (isset($sort_options[$sort_filter])) {
        $query_args = array_merge($query_args, $sort_options[$sort_filter]);
        $hide_featured = true; // Hide featured if not "newest" default sorting
    }

    // Remove featured if needed
    if ($hide_featured) {
        unset($query_args['post__not_in']);
    }

    return [
        'query_args' => $query_args,
        'hide_featured' => $hide_featured
    ];
}

function limerock_get_people_query_args($args = []) {
    $paged              = $args['paged'] ?? 1;
    $search_query       = sanitize_text_field($args['search'] ?? '');
    $research_filter    = array_filter((array) ($args['research_area'] ?? []));
    $appointment_filter = array_filter((array) ($args['appointment'] ?? []));
    $sort_raw           = $args['sort'] ?? '';
    $sort_filter        = is_array($sort_raw) ? reset($sort_raw) : $sort_raw;
    $sort_filter        = sanitize_text_field($sort_filter);

    $query_args = [
        'post_type'      => ['person'],
        'posts_per_page' => $args['posts_per_page'],
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => $args['leadership_ids'],
        'meta_query'     => [
            [
                'key'     => 'display_on_archive',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
        'tax_query' => ['relation' => 'AND'],
    ];

    $hide_featured_leadership = false;

    // Apply search
    if ($search_query) {
        $query_args['s'] = $search_query;
        $hide_featured_leadership = true;
    }

    // Apply research area filter
    if ($research_filter) {
        $query_args['tax_query'][] = [
            'taxonomy' => 'tax-research-area',
            'field'    => 'slug',
            'terms'    => $research_filter,
        ];
        $hide_featured_leadership = true;
    }

    // Apply appointment type filter
    if (!empty($appointment_filter) && !in_array('all', $appointment_filter, true)) {
        $query_args['tax_query'][] = [
            'taxonomy' => 'appointment-type',
            'field'    => 'slug',
            'terms'    => $appointment_filter,
        ];
        $hide_featured_leadership = true;
    }

    // Apply sorting
    $sort_options = get_archive_sort_query_options();
    if (isset($sort_options[$sort_filter])) {
        $query_args = array_merge($query_args, $sort_options[$sort_filter]);
        $hide_featured_leadership = true; // Hide featured if not "newest" default sorting
    }

    // Remove featured if needed
    if ($hide_featured_leadership) {
        unset($query_args['post__not_in']);
    }

    return [
        'query_args' => $query_args,
        'hide_featured' => $hide_featured_leadership
    ];
}

function get_people_leadership_ids() {
    $leadership_args = [
        'post_type'      => ['person'],
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'appointment-type',
                'field'    => 'slug',
                'terms'    => ['leadership'],
            ],
        ],
        'meta_query' => [
            [
                'key'     => 'display_on_archive',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
        'fields'  => 'ids',
    ];

    $leadership_query = new WP_Query($leadership_args);
    return $leadership_query->posts;
}

function get_research_terms_for_work() {
    $posts = get_posts([
        'post_type'      => ['post', 'project', 'publication'],
        'fields'         => 'ids',
        'posts_per_page' => -1,
    ]);

    if (empty($posts)) {
        return [];
    }

    return get_terms([
        'taxonomy'   => 'tax-research-area',
        'hide_empty' => true,
        'object_ids' => $posts,
    ]);
}

function get_research_terms_for_people() {
    $posts = get_posts([
        'post_type'      => ['person'],
        'fields'         => 'ids',
        'posts_per_page' => -1,
    ]);

    if (empty($posts)) {
        return [];
    }

    return get_terms([
        'taxonomy'   => 'tax-research-area',
        'hide_empty' => true,
        'object_ids' => $posts,
    ]);
}

function get_archive_sort_options() {
    return [
        [ 'value' => 'newest', 'label' => 'Newest' ],
        [ 'value' => 'oldest', 'label' => 'Oldest' ],
        [ 'value' => 'a_z',    'label' => 'A to Z' ],
        [ 'value' => 'z_a',    'label' => 'Z to A' ],
    ];
}

function get_archive_sort_query_options() {
    return [
        'oldest' => ['orderby' => 'date',  'order' => 'ASC'],
        'a_z'    => ['orderby' => 'title', 'order' => 'ASC'],
        'z_a'    => ['orderby' => 'title', 'order' => 'DESC'],
    ];
}

add_filter('timber/context', 'add_to_context');

function add_to_context($context) {
    if (!empty($_GET)) $context['request']['get'] = $_GET;
    if (!empty($_POST)) $context['request']['post'] = $_POST;

    return $context;
}

add_action('pre_get_posts', 'customize_search_page_query');

function customize_search_page_query(WP_Query $query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {

        // Apply post type filter
        if (!empty($_GET['type'])) {
            $query->set('post_type', array_map('sanitize_text_field', (array) $_GET['type']));
        }

        // Apply research area filter
        $tax_query = [];
        if (!empty($_GET['research_area'])) {
            $tax_query[] = [
                'taxonomy' => 'tax-research-area',
                'field'    => 'slug',
                'terms'    => array_map('sanitize_text_field', (array) $_GET['research_area']),
            ];
        }
        if ($tax_query) {
            $query->set('tax_query', $tax_query);
        }

        // Apply sorting
        if (!empty($_GET['sort'])) {
            $sort_options = get_archive_sort_query_options();
            $sort_key = sanitize_text_field(is_array($_GET['sort']) ? reset($_GET['sort']) : $_GET['sort']);
            if (isset($sort_options[$sort_key])) {
                foreach ($sort_options[$sort_key] as $key => $value) {
                    $query->set($key, $value);
                }
            }
        }

        $query->set('posts_per_page', 8);
    }
}

function highlight_words($text, $search_query) {

    $text = mb_convert_encoding($text, 'UTF-8', 'auto');
    $search_query = mb_convert_encoding($search_query, 'UTF-8', 'auto');
    $text = strip_tags($text);
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    $words = preg_split('/\s+/u', $search_query, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($words as $word) {
        if (!$word) continue;

        $pattern = '/' . preg_quote($word, '/') . '/iu';
        $text = preg_replace($pattern, '<b>$0</b>', $text);
    }

    return $text;
}

// Enable pages for 'styleguide' post
function custom_template_redirect() {

    if(is_singular('styleguide')) {
        global $wp_query;
        $page = (int)$wp_query->get('page');
        if($page > 1) {
            $query->set('page', 1);
            $query->set('paged', $page);
        }
        remove_action('template_redirect', 'redirect_canonical');
    }
}
add_action('template_redirect', 'custom_template_redirect', 0);

function acf_event_datetime( $date_string ) {
    if ( empty( $date_string ) ) {
        return null;
    }

    $dt = DateTime::createFromFormat('d/m/Y h:i a', $date_string);

    if ( ! $dt ) {
        return null;
    }

    return $dt;
}

add_filter('timber/twig', function($twig) {
    $twig->addFilter(new \Twig\TwigFilter('file_get_contents_raw', function($url) {
        $uploads = wp_upload_dir();
        $baseurl = $uploads['baseurl'];   // URL to the uploads folder
        $basedir = $uploads['basedir'];   // Physical path to the uploads folder

        $relative_path = str_replace($baseurl, '', $url); // /2025/10/research-area-icon-1.svg
        $path = $basedir . $relative_path;

        return file_exists($path) ? file_get_contents($path) : '';
    }));

    $twig->addFunction(new \Twig\TwigFunction('acf_event_datetime', 'acf_event_datetime'));

    return $twig;
});
