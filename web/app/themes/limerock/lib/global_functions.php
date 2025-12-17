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

    if ($is_preview && empty($context['fields']) && !empty($block['data'])) {
        $context['fields'] = $block['data'];
    }

	if (! empty($block['data']['is_example'])) {
		$context['is_example'] = true;
		$context['fields'] = $block['data'];
	}

	$classes =  [];

	$block['className'] = implode(' ', $classes);

	$context['block']      = $block;
	// Render the block.

    $filepath = '@blocks/' . $slug . '/' . $slug . '.twig';

    $context = apply_filters('limerock/block_context', $context, $filepath);
    $context = apply_filters('limerock/block_context/' . $slug, $context, $filepath);

    if ($is_preview && !empty($block['title'])) {
        echo
        '<div class="limerock-gutenberg-block-title">Block: '
        . esc_html($block['title'])
        . '</div>';
    }

	Timber\Timber::render($filepath, $context);
}

// Enable pages for 'styleguide' post
function custom_template_redirect() {

    if(is_singular('styleguide')) {
        global $wp_query;
        $page = (int)$wp_query->get('page');
        if($page > 1) {
            $wp_query->set('page', 1);
            $wp_query->set('paged', $page);
        }
        remove_action('template_redirect', 'redirect_canonical');
    }
}

add_action('template_redirect', 'custom_template_redirect', 0);