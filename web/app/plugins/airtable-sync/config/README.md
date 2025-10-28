# Airtable Sync Configuration

This directory contains configuration files for the Airtable Sync plugin.

## Files

- **`mappings.php`** - Active field mappings (committed to git)
- **`mappings.example.php`** - Example configuration with documentation

## Setup

1. Edit `mappings.php` to define your table-to-post-type mappings
2. Configure API credentials in WordPress admin (not stored here)
3. Validate configuration: `wp airtable validate` (coming soon)

## Configuration Structure

Each mapping defines how an Airtable table syncs to a WordPress post type:

```php
array(
    'table_id' => 'tblXXXXXXXXXXXXXX',  // From Airtable table URL
    'table_name' => 'Projects',          // Human-readable
    'post_type' => 'project',            // WordPress post type
    'view_id' => '',                     // Optional: filter by view
    'field_mappings' => array(
        array(
            'airtable_field_id' => 'fldXXXXXXXXXXXXXX',
            'airtable_field_name' => 'Project Name',
            'destination_type' => 'core',  // 'core', 'taxonomy', or 'acf'
            'destination_key' => 'post_title',
        ),
        // ... more fields
    ),
)
```

## Finding Airtable IDs

### Table ID
- Open your table in Airtable
- Look at the URL: `https://airtable.com/appXXXXXX/tblYYYYYY/viwZZZZZZ`
- The part starting with `tbl` is your table ID

### Field ID
- Use the Airtable API to get field IDs
- Or use the admin UI's "Load Fields" feature to inspect field IDs
- Field IDs start with `fld`

### View ID (Optional)
- From the table URL when viewing a specific view
- View IDs start with `viw`

## Destination Types

### Core WordPress Fields
- `post_title` - Post title
- `post_content` - Post content/body
- `post_excerpt` - Post excerpt
- `post_name` - Post slug
- `post_date` - Post date

### Taxonomies
- Use taxonomy name (e.g., `category`, `post_tag`, `project_type`)
- Works with hierarchical and non-hierarchical taxonomies

### ACF Fields
- Use ACF field key (e.g., `field_XXXXXXXXXXXXXXXX`)
- Or field name (e.g., `budget`)
- ACF Pro must be installed and active

## Best Practices

1. **Version control** - Commit `mappings.php` to track changes
2. **Document changes** - Add comments explaining complex mappings
3. **Test first** - Use dry-run sync to validate before running
4. **Field validation** - Ensure destination fields exist before mapping
5. **Naming** - Use descriptive names for maintainability
