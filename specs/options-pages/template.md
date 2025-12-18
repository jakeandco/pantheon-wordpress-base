---
title: "Options Page Title"
slug: options_page_slug
menu_title: "Menu Title"
parent_slug: options-general.php|null
capability: manage_options
icon: admin-settings|dashicons-admin-generic|dashicons-admin-site|dashicons-admin-tools|dashicons-dashboard|dashicons-settings|dashicons-admin-network|dashicons-admin-home|dashicons-admin-collapse|dashicons-filter|dashicons-admin-customizer|dashicons-admin-multisite|null
---

# Options Page Title

## Description
Brief description of what site-wide settings this options page manages and how it affects the site's behavior or appearance.

## ACF Fields
| Field Name | Field Type | Required | Default Value | Notes |
|------------|------------|----------|---------------|-------|
| example_text_field | Text | Yes | | Placeholder text or usage notes |
| example_wysiwyg | WYSIWYG | No | | Used for rich text content |
| example_image | Image | No | | Return format: array |
| example_true_false | True/False | No | false | Controls feature visibility |
| example_select | Select | Yes | option1 | Choices: option1, option2, option3 |

## Field Organization
Describe how fields are organized on the options page:
- Tab structure (if using tabs)
- Field groups and their purposes
- Conditional logic between fields
- Any accordion groups or sections

Example:
- General Settings tab: Contains site-wide text and image fields
- Display Options tab: Contains true/false toggles for feature visibility
- Advanced tab: Contains technical configuration options

## Usage Notes
When and how site administrators should use these options:
- What scenarios require updating these settings
- Best practices for configuration
- Impact on front-end display or site behavior
- Any dependencies on theme features or other plugins

## Development Notes
How to access and use these options in code:

### Getting Option Values
```php
// Single field
$value = get_field('field_name', 'option');

// From specific options page (if multiple exist)
$value = get_field('field_name', 'options_page_slug');
```

### Common Usage Patterns
- Where these values are typically used in templates
- Any helper functions or wrappers
- Caching considerations
- Default/fallback value handling

## Open Questions
1. List any items that need clarification with the client or design team
2. Outstanding decisions about field configuration
3. Integration questions with other systems
