<?php

namespace LimeRockTheme\PostType;

/**
 * Class Publication
 */
class Publication extends PostTypeClass
{
  public static string $post_slug = 'publication';
  public static ?int $posts_per_page = 15;

  // public static array $post_type_template = [
  //   [
  //     'limerock/page-header',
  //     [
  //       'name' => 'limerock/page-header',
  //       'data' => [
  //         'title_size' => 'medium',
  //         'intro_copy' => '',
  //       ],

  //     ]
  //   ],

  //   [
  //     'limerock/image-and-text-callout',
  //     [
  //       'name' => 'limerock/image-and-text-callout',
  //       'data' => [
  //         'image' => 316,
  //         'text' => '<h5>Donec ullamcorper nulla non metus auctor fringilla. Cras justo odio, dapibus ac facilisis in, egestas eget quam sit dolor amet odum.</h5>',
  //         'image_size' => 'large',
  //       ],

  //     ]
  //   ],

  //   [
  //     'limerock/two-column-text-block',
  //     [
  //       'name' => 'limerock/two-column-text-block',
  //       'data' => [
  //         'heading' => 'About',
  //         'variation' => 'two_cols_sidebar',
  //         'column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'sidebar_content' => 'Get involved\r\n\r\nInterested in getting participating? Contact <a href="mailto:christieko@stanford.edu">christieko@stanford.edu</a>\r\n\r\n&nbsp;\r\n\r\n&nbsp;',
  //       ],

  //       'backgroundColor' => 'warm-light',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|10'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/large-centered-cta',
  //     [
  //       'name' => 'limerock/large-centered-cta',
  //       'data' => [
  //         'heading' => 'Get involved lorem',
  //         'content' => 'If you would like to participate ras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus.',
  //         'button' => ['title' => 'Optional button', 'url' => '#', 'target' => ''],
  //       ],

  //       'backgroundColor' => 'warm-light'
  //     ]
  //   ],

  //   [
  //     'limerock/featured-work',
  //     [
  //       'name' => 'limerock/featured-work',
  //       'data' => [
  //         'intro_copy' => '',
  //         'headline' => 'Work',
  //         'archive_link' => '',
  //         'show_link_at_bottom' => '0',
  //         'featured_work' => '',
  //         'additional_items' => ['363', '325', '1288', '1289', '1291', '1284'],
  //       ],

  //       'backgroundColor' => 'light-text'
  //     ]
  //   ],

  //   [
  //     'limerock/year-carousel',
  //     [
  //       'name' => 'limerock/year-carousel',
  //       'data' => [
  //         'headline' => 'Project Timeline',
  //         'carousel_0_year' => '20251111',
  //         'carousel_0_title' => 'Headline lorem ipsum dolor sit amet',
  //         'carousel_0_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_0_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel_1_year' => '20241114',
  //         'carousel_1_title' => '2024',
  //         'carousel_1_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_1_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel_2_year' => '20221117',
  //         'carousel_2_title' => '2022',
  //         'carousel_2_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_2_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel_3_year' => '20201119',
  //         'carousel_3_title' => '2020',
  //         'carousel_3_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_3_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel_4_year' => '20191114',
  //         'carousel_4_title' => '2019',
  //         'carousel_4_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_4_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel_5_year' => '20151112',
  //         'carousel_5_title' => '2015',
  //         'carousel_5_column_1' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent',
  //         'carousel_5_column_2' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
  //         'carousel' => 6,
  //       ],

  //     ]
  //   ],

  //   [
  //     'limerock/single-media-item',
  //     [
  //       'name' => 'limerock/single-media-item',
  //       'data' => [
  //         'headline' => '',
  //         'media_item' => 1519,
  //         'media_item_title' => '',
  //       ],
  //       'align' => 'center',

  //       'backgroundColor' => 'stanford-red',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|10',
  //             'bottom' => 'var:preset|spacing|10'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/body-copy',
  //     [
  //       'name' => 'limerock/body-copy',
  //       'data' => [
  //         'title' => '',
  //         'heading' => 'Etiam porta sem malesuada magna mollis euismod.',
  //         'content' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed.',
  //         'image' => '',
  //         'button' => '',
  //       ],

  //     ]
  //   ],

  //   [
  //     'limerock/quote',
  //     [
  //       'name' => 'limerock/quote',
  //       'data' => [
  //         'selection_mode' => 'quote_post',
  //         'quote_source' => 112,
  //         'cta' => '',
  //       ],

  //       'backgroundColor' => 'warm-dark'
  //     ]
  //   ],

  //   [
  //     'limerock/single-media-item',
  //     [
  //       'name' => 'limerock/single-media-item',
  //       'data' => [
  //         'headline' => 'Chart headline or title lorem ipsum',
  //         'media_item' => 1522,
  //         'media_item_title' => '',
  //       ],
  //       'align' => 'center',

  //     ]
  //   ],

  //   [
  //     'limerock/body-copy',
  //     [
  //       'name' => 'limerock/body-copy',
  //       'data' => [
  //         'title' => '',
  //         'heading' => '',
  //         'content' => 'Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.\r\n\r\nMattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed consectetur.Cras mattis consectetur purus sit amet fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Aenean lacinia bibendum nulla sed.',
  //         'image' => '',
  //         'button' => '',
  //       ],

  //     ]
  //   ],

  //   [
  //     'limerock/accordion',
  //     [
  //       'name' => 'limerock/accordion',
  //       'data' => [
  //         'headline' => '',
  //         'rows_0_title' => 'Accordion medium text lorem ipsum dolor sit',
  //         'rows_0_subtitle' => '',
  //         'rows_0_title_size' => 'regular',
  //         'rows_0_content_0_body_copy' => 'Accordion medium text lorem ipsum dolor sit',
  //         'rows_0_content_0_image' => '',
  //         'rows_0_content' => ['body_copy'],
  //         'rows_1_title' => 'Duis mollis, est non commodo luctus, nisi erat porttitor',
  //         'rows_1_subtitle' => '',
  //         'rows_1_title_size' => 'regular',
  //         'rows_1_content_0_body_copy' => 'Duis mollis, est non commodo luctus, nisi erat porttitor',
  //         'rows_1_content_0_image' => '',
  //         'rows_1_content' => ['body_copy'],
  //         'rows_2_title' => 'Duis mollis, est non commodo luctus, nisi erat porttitor',
  //         'rows_2_subtitle' => '',
  //         'rows_2_title_size' => 'regular',
  //         'rows_2_content_0_body_copy' => 'Duis mollis, est non commodo luctus, nisi erat porttitor',
  //         'rows_2_content_0_image' => '',
  //         'rows_2_content' => ['body_copy'],
  //         'rows' => 3,
  //       ],

  //       'backgroundColor' => 'green',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|4'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/single-media-item',
  //     [
  //       'name' => 'limerock/single-media-item',
  //       'data' => [
  //         'headline' => '',
  //         'media_item' => 489,
  //         'media_item_title' => 'Video title lorem ipsum dolor sit amet',
  //       ],
  //       'align' => 'full',

  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'bottom' => 'var:preset|spacing|1'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/people-callout',
  //     [
  //       'name' => 'limerock/people-callout',
  //       'data' => [
  //         'heading' => 'Project team',
  //         'people' => ['544', '919', '608', '799'],
  //         'variation' => 'cards',
  //         'items_per_row' => '4',
  //       ],

  //       'backgroundColor' => 'warm-light',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'bottom' => '0'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/people-callout',
  //     [
  //       'name' => 'limerock/people-callout',
  //       'data' => [
  //         'heading' => 'Advisory Commitee Members',
  //         'people' => ['608', '544', '919', '799'],
  //         'variation' => 'cards',
  //         'items_per_row' => '4',
  //       ],

  //       'backgroundColor' => 'warm-light',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|2'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/news-cards',
  //     [
  //       'name' => 'limerock/news-cards',
  //       'data' => [
  //         'title' => 'Related News & Press',
  //         'variation' => 'without_image',
  //         'selection_type' => 'manual',
  //         'selections' => ['1313', '1320', '1316'],
  //       ],

  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|12'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],

  //   [
  //     'limerock/body-copy',
  //     [
  //       'name' => 'limerock/body-copy',
  //       'data' => [
  //         'title' => '',
  //         'heading' => '',
  //         'content' => "Support<br>\r\n<br>\r\nThe GDP-B project is supported by the National Science Foundation. NSF’s purpose is to advance the progress of science, a mission accomplished by funding proposals for research and education.",
  //         'image' => '',
  //         'button' => '',
  //       ],

  //       'backgroundColor' => 'warm-light',
  //       'style' => [
  //         'spacing' => [
  //           'padding' => [
  //             'top' => 'var:preset|spacing|8',
  //             'bottom' => 'var:preset|spacing|8'
  //           ]
  //         ]
  //       ]
  //     ]
  //   ],
  // ];
}
