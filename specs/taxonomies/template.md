---
title: "Taxonomy Name (Plural)"
singular_name: "Taxonomy Name (Singular)"
slug: taxonomy-slug
description: "Brief description of what this taxonomy organizes"
hierarchical: false
tag_cloud: false
post_types:
  - post-type-slug-1
  - post-type-slug-2

---

# Taxonomy Name (Plural)

## Description
Explain what this taxonomy organizes and how it will be used to categorize content within the WordPress site. Describe the purpose and the benefit to content editors and site visitors.

## ACF Fields
| Field Name | Field Type | Required | Notes |
|------------|------------|----------|-------|
| example_text | Text | Yes | Max 100 chars |
| example_image | Image | No | Return format: array |
| example_color | Color Picker | No | For category display styling |

If no custom fields are needed for taxonomy terms, state "None"

## Associated Post Types
List which post types use this taxonomy:
- Post Type Name 1 (slug: post-type-slug-1) - How this taxonomy is used with this post type
- Post Type Name 2 (slug: post-type-slug-2) - How this taxonomy is used with this post type

## Configuration Notes
Detail any special setup requirements or configuration:
- **Hierarchical**: Whether this taxonomy is hierarchical (like categories) or flat (like tags)
- **URL Structure**: Custom rewrite rules or slug requirements
- **Admin Interface**: Any custom admin columns, filters, or metaboxes
- **Capabilities**: Custom capability requirements or role restrictions
- **Default Terms**: Any terms that should be created by default
- **Archive Pages**: Whether taxonomy archives should be enabled and how they should display
- **Display Settings**: Tag cloud, menu locations, or other display considerations

## Open Questions
1. Questions that need to be clarified with the client or design team
2. Decisions pending about taxonomy structure or usage
3. Clarifications needed about categorization strategy
