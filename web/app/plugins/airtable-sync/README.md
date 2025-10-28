# Airtable Sync Plugin

A WordPress plugin that syncs content and metadata between Airtable bases and WordPress post types. Built for developers with version-controlled configuration.

## Features

- **Code-Based Configuration** - Field mappings stored in PHP config files (version controlled)
- **API Credential Management** - Store API keys securely in WordPress admin
- **Multiple Table Support** - Map multiple Airtable tables to WordPress post types
- **Flexible Field Mapping** - Map to core WP fields, taxonomies, or ACF custom fields
- **View Filtering** - Optionally sync only records from specific Airtable views
- **Smart Change Detection** - Automatically skips unchanged records using Airtable's lastModifiedTime
- **Orphaned Post Management** - Automatically unpublishes posts removed from Airtable view
- **Admin UI Sync** - Run syncs directly from WordPress admin with real-time feedback
- **Configuration Validation** - Built-in validation checks for post types, taxonomies, and ACF fields
- **WP-CLI Commands** - Full command-line interface for automated syncs
- **Developer-Friendly** - Stable, testable, and environment-agnostic

## Philosophy

This plugin uses a **hybrid approach** to configuration:

- **API credentials** (api_key, base_id) are stored in the WordPress database (environment-specific)
- **Field mappings** are defined in PHP config files (version controlled, shared across environments)

This ensures:
✅ Mappings stay in sync across dev/staging/prod
✅ Changes are tracked in git history
✅ Configuration can be code-reviewed
✅ Secrets don't get committed to version control

## Installation

1. The plugin is already installed at `web/app/plugins/airtable-sync/`
2. Activate through the WordPress admin 'Plugins' menu
3. Navigate to **Airtable Sync** in the admin menu

## Configuration

### 1. API Credentials (WordPress Admin)

1. Go to **WP Admin > Airtable Sync**
2. Enter your Airtable Personal Access Token
   - Create one at https://airtable.com/create/tokens
   - Required scopes: `data.records:read`, `schema.bases:read`
3. Click **Load Bases** and select your base
4. Click **Save Settings**

### 2. Table Mappings (Code)

Edit `config/mappings.php` to define your table-to-post-type mappings:

```php
return array(
    array(
        'table_id' => 'tblXXXXXXXXXXXXXX',  // From Airtable table URL
        'table_name' => 'Projects',          // Human-readable name
        'post_type' => 'project',            // WordPress post type
        'view_id' => '',                     // Optional: filter by view
        'field_mappings' => array(
            // Map Airtable field to WP post title
            array(
                'airtable_field_id' => 'fldXXXXXXXXXXXXXX',
                'airtable_field_name' => 'Project Name',
                'airtable_field_type' => 'singleLineText',
                'destination_type' => 'core',
                'destination_key' => 'post_title',
                'destination_name' => 'Post Title',
            ),
            // Map to ACF field
            array(
                'airtable_field_id' => 'fldYYYYYYYYYYYYYY',
                'airtable_field_name' => 'Budget',
                'airtable_field_type' => 'number',
                'destination_type' => 'acf',
                'destination_key' => 'field_budget', // ACF field key
                'destination_name' => 'Budget',
            ),
            // Map to taxonomy
            array(
                'airtable_field_id' => 'fldZZZZZZZZZZZZZZ',
                'airtable_field_name' => 'Categories',
                'airtable_field_type' => 'multipleSelects',
                'destination_type' => 'taxonomy',
                'destination_key' => 'category',
                'destination_name' => 'Categories',
            ),
        ),
    ),
);
```

See `config/mappings.example.php` for detailed examples.

## Field Mapping Types

### Core WordPress Fields
- `post_title` - Post title
- `post_content` - Post content/body
- `post_excerpt` - Post excerpt
- `post_name` - Post slug
- `post_date` - Post published date

### Taxonomies
Use the taxonomy name (e.g., `category`, `post_tag`, `project_type`). Works with both hierarchical and flat taxonomies.

### ACF Fields
Use the ACF field key (e.g., `field_XXXXXXXXXXXXXXXX`) or field name. Requires ACF Pro to be installed and active.

## Finding Airtable IDs

### Table ID
1. Open your table in Airtable
2. Check the URL: `https://airtable.com/appXXXXX/tblYYYYY/viwZZZZZ`
3. The `tblYYYYY` part is your table ID

### Field ID
1. Use the Airtable Metadata API
2. Or inspect the admin UI after loading fields
3. Field IDs start with `fld`

### View ID (Optional)
1. From the table URL when viewing a specific view
2. View IDs start with `viw`

## Performance Optimization

### Smart Change Detection

The sync engine automatically detects and skips records that haven't changed since the last sync, making subsequent syncs much faster.

**How it works:**
1. Add a "Last Modified Time" field to your Airtable table
   - Field type: **Last modified time**
   - Configure it to watch all fields (or specific fields you care about)
2. The sync engine automatically detects this field (no configuration needed)
3. On subsequent syncs, records with matching timestamps are skipped
4. Only new or modified records are updated in WordPress

**Benefits:**
- ⚡ Much faster sync times for large datasets
- 🔋 Reduces server load and database writes
- 📊 Provides accurate "skipped" statistics in sync reports

**Note:** If you don't add a Last Modified Time field, all records will be updated on every sync (safe but slower).

## Orphaned Post Management

The sync engine automatically manages posts that are no longer present in the Airtable sync results.

**How it works:**
1. After syncing all records, the engine identifies published posts with an `_airtable_id` meta field
2. Compares these posts against the Airtable records that were just synced
3. Any post whose Airtable ID is **not** in the sync results is automatically unpublished (set to draft)
4. Posts are **not deleted**, just unpublished, preserving all data and history

**Common scenarios:**
- ✅ Record removed from synced Airtable view → Post unpublished
- ✅ Record deleted from Airtable → Post unpublished
- ✅ Record returns to view → Post automatically re-published on next sync
- ✅ Record filtered out by view criteria → Post unpublished until it matches criteria again

**Benefits:**
- 🔒 Prevents outdated content from appearing on the site
- ♻️ Posts can be restored if records return to the view
- 📊 "Unpublished" count shown in sync statistics
- 🛡️ Safe: data is preserved, not deleted

**Important:** Only posts that were originally synced from Airtable (have `_airtable_id` meta) are affected. Manually created posts are never touched.

## Validation

The plugin validates your configuration and checks:
- Config file exists and is valid PHP
- All required fields are present
- WordPress post types exist
- Taxonomies exist
- ACF fields exist (when ACF is active)

Click **Revalidate Configuration** in the admin UI to run validation.

## Running Syncs

The plugin provides two ways to run syncs: through the WordPress admin or via WP-CLI commands.

### Admin UI (Recommended for Manual Syncs)

1. Go to **WP Admin > Airtable Sync**
2. Scroll to the **Table Mappings** section
3. Click the **Sync Now** button for the table you want to sync
4. View real-time progress and results:
   - **Processed**: Total records fetched from Airtable
   - **Created**: New posts created
   - **Updated**: Existing posts updated with changes
   - **Skipped**: Unchanged records (no update needed)
   - **Unpublished**: Posts removed from Airtable view (set to draft)
   - **Errors**: Failed operations

The admin interface is ideal for:
- Manual syncs by content editors
- Testing configuration changes
- Viewing detailed sync statistics
- Quick on-demand updates

### WP-CLI Commands (Recommended for Automation)

Use WP-CLI for scheduled syncs via cron jobs or deployment scripts.

#### Sync All Tables

```bash
wp airtable sync
```

#### Sync Specific Table

```bash
wp airtable sync-table tblMObBbMdYgDLpFp
```

### Dry Run (No Changes)

```bash
wp airtable sync --dry-run
wp airtable sync-table tblMObBbMdYgDLpFp --dry-run
```

### Validate Configuration

```bash
wp airtable validate
```

### List Configured Mappings

```bash
wp airtable list
```

## File Structure

```
web/app/plugins/airtable-sync/
├── airtable-sync.php                       # Main plugin file
├── config/
│   ├── mappings.php                        # Your mappings (commit to git)
│   ├── mappings.example.php                # Example configuration
│   └── README.md                           # Configuration guide
├── includes/
│   ├── class-airtable-sync-config.php      # Configuration loader
│   ├── class-airtable-sync-admin.php       # Admin UI
│   ├── class-airtable-api.php              # Airtable API client
│   ├── class-airtable-sync-engine.php      # Core sync engine
│   ├── class-airtable-field-transformer.php # Field transformation logic
│   └── class-airtable-sync-cli.php         # WP-CLI commands
├── assets/
│   ├── css/admin.css                       # Admin styles
│   └── js/admin.js                         # Admin JavaScript
└── README.md                               # This file
```

## Development Workflow

1. **Configure API credentials** in WordPress admin (per environment)
2. **Edit `config/mappings.php`** to add/modify field mappings
3. **Validate configuration**: `wp airtable validate`
4. **Test with dry-run**: `wp airtable sync --dry-run`
5. **Run actual sync**: `wp airtable sync`
6. **Commit mappings** to git
7. **Deploy** - mappings automatically available in all environments

## Best Practices

✅ **Version control** - Always commit `config/mappings.php`
✅ **Document mappings** - Add comments explaining complex configurations
✅ **Validate first** - Check configuration before syncing
✅ **Test in dev** - Try mappings in development before production
✅ **Use field keys** - For ACF, use field keys not names for stability

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Valid Airtable Personal Access Token with appropriate scopes
- For ACF field mapping: ACF Pro must be installed and active

## Features Implemented

✅ Code-based configuration
✅ API credential management
✅ Multiple table support
✅ Field-level mapping
✅ View filtering
✅ Smart change detection (lastModifiedTime)
✅ Orphaned post management (auto-unpublish)
✅ Configuration validation
✅ Admin UI with Sync Now buttons
✅ Real-time sync feedback and statistics
✅ WP-CLI sync commands
✅ Dry-run mode
✅ Core WordPress fields support
✅ ACF fields support (including repeaters)
✅ Taxonomy support (including multipleSelects)
✅ Attachment/image syncing

## Roadmap

- [ ] Webhook support for real-time sync
- [ ] Bidirectional sync (WordPress → Airtable)
- [ ] Scheduled/cron syncs
- [ ] Sync history/logging in admin UI

## Developer Information

- **Developer:** Jake and Co.
- **Website:** https://jakeandco.com
- **Version:** 1.0.0

## License

GPL v2 or later
