<?php

namespace LimeRockTheme;

/**
 * Class AdminCustomizer
 */
class AdminCustomizer
{
  public static function init()
  {
    add_action('admin_enqueue_scripts', 'LimeRockTheme\AdminCustomizer::includes');
    add_action('enqueue_block_assets', 'LimeRockTheme\AdminCustomizer::gutenberg_iframe_scripts');
    add_action('after_setup_theme', 'LimeRockTheme\AdminCustomizer::mce');
    add_filter('upload_mimes', 'LimeRockTheme\AdminCustomizer::mime_types');
  }

  public static function includes()
  {
    wp_enqueue_style('editor-stylesheet', get_template_directory_uri() . '/dist/css/editor-base.css', [], time());
    wp_enqueue_script('editor-script', get_template_directory_uri() . '/dist/js/admin.js', ['acf-input', 'jquery'], time());
    wp_deregister_style('wp-block-library-theme');
  }

  public static function mce()
  {
    add_filter('tiny_mce_before_init', 'LimeRockTheme\AdminCustomizer::mce_before_init_insert_formats');
    add_filter('mce_buttons', 'LimeRockTheme\AdminCustomizer::mce_buttons');
    add_filter('mce_external_plugins', 'LimeRockTheme\AdminCustomizer::mce_external_plugins');
  }

  public static function mime_types($mimes)
  {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }

  public static $tinymce_styles = [
    [
      'title' => 'H1 (110px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h1',
      'wrapper' => false
    ],
    [
      'title' => 'H2 (75px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h2',
      'wrapper' => false,
    ],
    [
      'title' => 'H3 (65px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h3',
      'wrapper' => false
    ],
    [
      'title' => 'H4 (40px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h4',
      'wrapper' => false
    ],
    [
      'title' => 'H5 (24px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h5',
      'wrapper' => false
    ],
    [
      'title' => 'H6 (16px)',
      'selector' => 'p,h1,h2,h3,h4,h5,h6',
      'classes' => 'h6',
      'wrapper' => false
    ],
  ];

  public static function mce_buttons($buttons)
  {
    array_splice($buttons, 1, 0, 'styleselect');
    array_splice($buttons, 2, 0, 'limerock_shortcodes');
    return $buttons;
  }

  public static function mce_external_plugins($plugin_array)
  {
    $plugin_array['limerock_shortcodes'] = get_template_directory_uri() . '/dist/js/custom_plugins/limerock_shortcodes.js';
    return $plugin_array;
  }

  public static function mce_before_init_insert_formats($init_array)
  {
    $init_array['style_formats'] = json_encode(self::$tinymce_styles);

    return $init_array;
  }

  public static function gutenberg_iframe_scripts()
  {
    if (!is_admin()) return;

    global $pagenow, $post;

    $classes = '';

    if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
      $post_type = get_post_type($post->ID);
      $class1 = 'my-default-class-1';
      $class2 = 'my-default-class-2';

      if (class_exists('ACF')) {
        if ($post_type === 'wp_block') {
          $class1 = 'pattern-class-1';
          $class2 = 'pattern-class-2';
        } else {
          $class1 = get_field('my_key', $post->ID) ?: $class1;
          $class2 = get_field('my_other_key', $post->ID) ?: $class2;
        }
      }

      $classes .= 'post-type-' . $post_type . ' ';
      $classes .= 'my-class-' . $class1 . ' ';
      $classes .= 'my-class-' . $class2;
    }

    wp_enqueue_script(
      'custom-iframe-classes',
      get_template_directory_uri() . '/dist/js/admin.js',
      ['wp-blocks', 'wp-dom'],
      filemtime(get_template_directory() . '/dist/js/admin.js'),
      true
    );

    wp_localize_script('custom-iframe-classes', 'iframeBodyData', [
      'classes' => $classes,
    ]);
  }
}
