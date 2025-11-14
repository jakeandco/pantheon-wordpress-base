<?php

namespace LimeRockTheme\PostType;

/**
 * Class Person
 */
class Person extends PostTypeClass
{
  public static string $post_slug = 'person';
  public static ?int $posts_per_page = 30;

  public static array $post_type_template = [
    [
      'limerock/page-header',
      [
        'name' => 'limerock/page-header',
        'data' => [
          'title_size' => 'small',
          'intro_copy' => '',
        ],

      ]
    ],

    [
      'limerock/two-column-text-block',
      [
        'name' => 'limerock/two-column-text-block',
        'data' => [
          'heading' => 'About',
          'variation' => 'two_cols_sidebar',
          'column_1' => "<strong>Bios can range but should all be at least 800 characters.</strong> Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent",
          'column_2' => "Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.",
          'sidebar_content' => '',
        ],

      ]
    ],

    [
      'limerock/featured-work',
      [
        'name' => 'limerock/featured-work',
        'data' => [
          'intro_copy' => '',
          'headline' => 'Publications',
          'archive_link' => '',
          'show_link_at_bottom' => '0',
          'featured_work' => '',
          'additional_items' => ['325', '1281', '1282', '1289', '1955', '1288'],
        ],

      ]
    ],

    [
      'limerock/featured-work-carousel',
      [
        'name' => 'limerock/featured-work-carousel',
        'data' => [
          'intro_copy' => '',
          'headline' => 'Featured Work',
          'archive_link' => '',
          'show_link_at_bottom' => '0',
          'featured_works' => ['1288', '1955', '1289'],
        ],
        'backgroundColor' => 'green',
        'style' => [
          'spacing' => [
            'padding' => [
              'top' => 'var:preset|spacing|12',
              'bottom' => 'var:preset|spacing|12'
            ]
          ]
        ]
      ]
    ],

    [
      'limerock/news-cards',
      [
        'name' => 'limerock/news-cards',
        'data' => [
          'title' => 'News',
          'variation' => 'with_image',
          'selection_type' => 'manual',
          'selections' => ['1295', '1294', '1293', '1308'],
        ],

      ]
    ],

    [
      'limerock/single-media-item',
      [
        'name' => 'limerock/single-media-item',
        'data' => [
          'headline' => '',
          'media_item' => 1439,
          'media_item_title' => 'The key to growth? Race with the machines',
        ],

      ]
    ],

  ];
}
