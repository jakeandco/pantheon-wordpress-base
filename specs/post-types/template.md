---
title: "Post Type Name"
plural_name: "Post Type Names"
slug: post-type-slug
hierarchical: false
menu_icon: admin-post
supports:
  - title
  - editor
  - thumbnail
  - excerpt
  - revisions
public: true
include_in_search: true
taxonomies: []

---

# Post Type Name

## Description
One sentence description of what this post type represents and its purpose within the WordPress site.

## ACF Fields
| Field Name | Field Type | Required | Notes |
|------------|------------|----------|-------|
| example_text | Text | Yes | Max 100 chars |
| example_image | Image | No | Return format: array |
| example_repeater | Repeater | No | Min 0, Max 10 |
| example_repeater.subfield | Text | Yes | Subfield within repeater |

## Archive Page
Description of how the archive/listing page should work:
- What information should display for each post in the listing
- Pagination requirements
- Filtering or sorting options
- Archive page title and description
- Layout considerations (grid, list, etc.)

## Single Page
Description of how individual post pages should display:
- Content layout and structure
- What ACF fields should appear and where
- Featured image usage
- Related content or navigation
- Breadcrumbs or contextual navigation
- Any special template requirements

## Related Taxonomies
List of taxonomies that should be associated with this post type:
- Taxonomy Name 1 (slug: taxonomy-slug-1) - Purpose description
- Taxonomy Name 2 (slug: taxonomy-slug-2) - Purpose description

If no custom taxonomies are needed, state "None" or "Standard categories and tags only"

## Development Notes
- Any special development considerations
- Custom query requirements
- Integration with other post types or features
- URL structure and rewrite rules
- Admin interface customizations
- Special permissions or capabilities

## Open Questions
1. Questions that need to be clarified with the client or design team
2. Decisions pending about functionality or structure
3. Clarifications needed about content strategy
