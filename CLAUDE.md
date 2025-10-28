# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Jake and Co WordPress project built on Pantheon's WordPress Composer Managed upstream. It features:
- **Timber/Twig** templating for theme development
- **ACF Pro** with ACF Extended Pro for custom fields
- **Composer** for PHP dependency management
- **Laravel Mix** for asset compilation
- **Bootstrap 5** for front-end framework
- **Custom block system** with scaffolding tools
- **Storybook** for component development

## Environment Setup

### Starting the Development Environment

**DDEV (Recommended):**
```bash
ddev start
```
The site will be available at `https://stanford-del-wordpress.ddev.site`

**Docker Compose (Alternative):**
```bash
docker-compose up -d
```
The site will be available at `http://localhost:8000`

### Initial Setup

1. **Install dependencies:**
```bash
# Root level (WordPress core and plugins)
composer install

# Theme level
cd web/app/themes/limerock
composer install
npm install  # or pnpm install (preferred package manager)
```

2. **Build assets:**
```bash
cd web/app/themes/limerock
npm run build    # One-time build
npm run watch    # Watch mode for development
```

## Theme Development

The active custom theme is located at `web/app/themes/limerock/`.

### Directory Structure

- `lib/` - PHP backend functionality, including ACF composer field definitions
- `src/` - Source files for assets (compiled to `dist/`)
  - `src/js/` - JavaScript modules
  - `src/scss/` - SCSS styles with Bootstrap customization
  - `src/assets/` - Images and fonts
- `views/` - Twig templates (follows WordPress template hierarchy)
  - `views/blocks/` - Gutenberg block templates
  - `views/partial/` - Reusable template partials
  - `views/parts/` - Theme parts (header, footer, etc.)
- `acf-json/` - ACF field group definitions (managed via WordPress admin)
- `skel/` - Scaffolding tool source code
- `.storybook/` - Storybook configuration for component development

### Key Commands

**Asset Compilation:**
```bash
cd web/app/themes/limerock
npm run build        # Production build
npm run watch        # Development with file watching
npm run storybook    # Launch Storybook on port 6006
```

**Scaffolding:**
```bash
cd web/app/themes/limerock
npm run generate                           # Interactive mode
npm run generate block -- --name="My Block"
npm run generate post-type -- --name="My Post Type"
npm run generate options -- --name="My Options Page"
```

**Testing (Theme):**
```bash
cd web/app/themes/limerock
composer test    # Run PHPUnit tests
```

## Code Quality

### Linting

**Root Level:**
```bash
composer lint              # Run all linters
composer lint:php          # PHP syntax check
composer lint:phpcs        # PHP CodeSniffer
composer lint:phpcbf       # PHP Code Beautifier and Fixer (auto-fix)
composer lint:bash         # Shellcheck for bash scripts
```

**PHPCS Configuration:**
- Uses Pantheon WordPress Coding Standards
- Excludes: WordPress core (`web/wp`), plugins, mu-plugins, vendor
- Theme code in `web/app/themes/limerock` is linted

### Standards
- Follow WordPress coding standards (enforced by PHPCS)
- Use Timber/Twig for all template rendering (no direct PHP in templates)
- Keep business logic in PHP classes within `lib/`, not in template files

## WordPress Configuration

**Configuration Files:**
- `.env` - Local environment variables (not in git, see `.env.example`)
- `config/application.php` - Main WP configuration (uses environment variables)
- `wp-cli.yml` - WP-CLI configuration

**WordPress Installation:**
- Core: `web/wp/` (managed by Composer, do not edit)
- Content: `web/app/` (themes, plugins, uploads)
- Document root: `web/`

## ACF Block Development

Blocks use a custom ACF Composer system for reusable field definitions.

**Including Composed Fields in `acf-composed.json`:**
```json
{
  "fields": [
    "LimeRockTheme/ACF/fields/body-copy",
    {
      "acf_composer_extend": "LimeRockTheme/ACF/fields/body-copy",
      "name": "overridden",
      "label": "Custom Label"
    }
  ]
}
```

Field definitions are stored in `lib/acf-composer/<type>/<field>.json`.

**Block Structure:**
Each block in `views/blocks/<block-name>/` typically has:
- `<block-name>.twig` - Template
- `acf-composed.json` - Field definitions

## Asset Pipeline

**Laravel Mix Configuration** (`webpack.mix.js`):
- JavaScript: `src/js/index.js` → `dist/js/main.js`
- Admin JavaScript: `src/js/admin/index.js` → `dist/js/admin.js`
- Styles: `src/scss/index.scss` → `dist/css/main.css`
- Editor Styles: `src/scss/_editor-base.scss` → `dist/css/editor-base.css`
- Images: Optimized and converted to WebP
- Fonts: Copied to `dist/assets/fonts/`

**Bootstrap Customization:**
Bootstrap variables are overridden in `src/scss/abstracts/bootstrap-vars/`.

## Database Management

**Import Database:**
Use Sequel Pro or similar MySQL client:
- Host: 127.0.0.1
- Port: 8081 (docker-compose) or check DDEV config
- User/Password: Check `.env` file

**WP-CLI Access:**
```bash
# With DDEV
ddev wp <command>

# With Docker
docker-compose exec wordpress wp <command>
```

## Required Plugins

Theme requires these plugins to be active:
- Advanced Custom Fields Pro
- ACF Field Group Composer

Without these, the theme will deactivate and show an error.

## Airtable Sync Plugin

This project includes a custom **Airtable Sync** plugin (`web/app/plugins/airtable-sync/`) that syncs content between Airtable bases and WordPress. Uses a **developer-friendly, version-controlled configuration approach**.

### Architecture Philosophy

The plugin uses a **hybrid configuration approach**:

- **API credentials** (API key, base ID) → Stored in WordPress database (environment-specific secrets)
- **Field mappings** → Defined in PHP config files (version controlled, shared across all environments)

**Benefits:**
- ✅ Mappings stay in sync across dev/staging/prod
- ✅ Changes tracked in git with full history
- ✅ Configuration changes go through code review
- ✅ Rollback capability via git
- ✅ Testable and auditable
- ✅ No database exports needed for config sync

### Features

- **Code-Based Configuration**: Field mappings in version-controlled PHP files
- **API Credential Management**: Store secrets securely in WordPress admin (per environment)
- **Table-to-Post-Type Mapping**: Map multiple Airtable tables to WordPress post types
- **View Filtering**: Optionally sync only records from specific Airtable views
- **Field-Level Mapping**: Map to core WP fields, taxonomies, or ACF custom fields (including repeaters)
- **Smart Change Detection**: Automatically skips unchanged records using Airtable's lastModifiedTime field
- **Orphaned Post Management**: Auto-unpublishes posts removed from Airtable view (preserves data)
- **Configuration Validation**: Built-in validation checks for post types, taxonomies, and ACF fields
- **Admin UI**: Run syncs with "Sync Now" buttons, view real-time feedback and statistics
- **WP-CLI Commands**: Full command-line interface for automated syncs and cron jobs
- **Field ID Inspector**: Discover Airtable field IDs directly in the admin UI

### Configuration

**1. API Credentials (WordPress Admin)**

Access **WP Admin > Airtable Sync**:

```
1. Enter your Airtable Personal Access Token (create at https://airtable.com/create/tokens)
2. Click "Load Bases" to fetch available bases
3. Select the target base from the dropdown
4. Click "Save Settings"
```

**2. Table Mappings (Code)**

Edit `web/app/plugins/airtable-sync/config/mappings.php`:

```php
return array(
    array(
        'table_id' => 'tblXXXXXXXXXXXXXX',  // From Airtable table URL
        'table_name' => 'Projects',          // Human-readable name
        'post_type' => 'project',            // WordPress post type
        'view_id' => '',                     // Optional: filter by view
        'field_mappings' => array(
            // Map to core WordPress field
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

See `config/mappings.example.php` for detailed examples and all available options.

**3. Validate Configuration**

In the admin UI, click **Revalidate Configuration** to check:
- Config file exists and is valid
- Required fields are present
- WordPress post types exist
- Taxonomies exist
- ACF fields exist (when applicable)

### Field Mapping Types

**Core WordPress Fields:**
- `post_title` - Post title
- `post_content` - Post content/body
- `post_excerpt` - Post excerpt
- `post_name` - Post slug
- `post_date` - Post published date

**Taxonomies:**
Use taxonomy name (e.g., `category`, `post_tag`, `project_type`). Works with hierarchical and flat taxonomies.

**ACF Fields:**
Use ACF field key (e.g., `field_XXXXXXXXXXXXXXXX`) or field name. Requires ACF Pro.

### Running Syncs

**Admin UI (Manual Syncs):**

1. Go to **WP Admin > Airtable Sync**
2. Scroll to **Table Mappings** section
3. Click **Sync Now** button for the table you want to sync
4. View real-time statistics:
   - **Processed**: Total records fetched
   - **Created**: New posts created
   - **Updated**: Existing posts updated
   - **Skipped**: Unchanged records (no update needed)
   - **Unpublished**: Posts removed from view (set to draft)
   - **Errors**: Failed operations

**WP-CLI (Automated Syncs):**

```bash
# Sync all configured tables
wp airtable sync

# Sync specific table
wp airtable sync-table tblMObBbMdYgDLpFp

# Dry run (preview changes without applying)
wp airtable sync --dry-run
wp airtable sync-table tblMObBbMdYgDLpFp --dry-run

# Validate configuration
wp airtable validate

# List all configured mappings
wp airtable list
```

### Smart Change Detection

The sync engine uses Airtable's `lastModifiedTime` field to skip unchanged records:

1. Add a "Last Modified Time" field to your Airtable table (field type: **Last modified time**)
2. Configure it to watch all fields or specific fields you care about
3. The sync engine automatically detects this field (no configuration needed)
4. On subsequent syncs, records with matching timestamps are skipped
5. Only new or modified records are updated

**Benefits:**
- ⚡ Much faster sync times for large datasets
- 🔋 Reduces server load and database writes
- 📊 Accurate "skipped" statistics in sync reports

### Orphaned Post Management

Posts that are no longer in the Airtable sync results are automatically unpublished (set to draft):

**How it works:**
1. After syncing all records, the engine identifies all published posts with an `_airtable_id` meta field
2. Compares these posts against the Airtable records that were just synced
3. Any post whose Airtable ID is not in the sync results is automatically unpublished
4. Posts are **not deleted**, just unpublished, preserving all data and history

**Common scenarios:**
- Record removed from synced Airtable view → Post unpublished
- Record deleted from Airtable → Post unpublished
- Record returns to view → Post automatically re-published on next sync
- Record filtered out by view criteria → Post unpublished until criteria matches

**Important:** Only posts originally synced from Airtable (have `_airtable_id` meta) are affected. Manually created posts are never touched.

### Finding Airtable IDs

**Table ID:**
- Open table in Airtable
- URL: `https://airtable.com/appXXXXX/tblYYYYY/viwZZZZZ`
- Table ID: `tblYYYYY`

**Field ID:**
- Use Airtable Metadata API
- Field IDs start with `fld`

**View ID (Optional):**
- From table URL when viewing specific view
- View IDs start with `viw`

### File Structure

```
web/app/plugins/airtable-sync/
├── airtable-sync.php                       # Main plugin file
├── config/
│   ├── mappings.php                        # Active mappings (commit to git)
│   ├── mappings.example.php                # Example configuration
│   └── README.md                           # Configuration guide
├── includes/
│   ├── class-airtable-sync-config.php      # Configuration loader & validation
│   ├── class-airtable-sync-admin.php       # Admin UI (Sync Now, Field Inspector)
│   ├── class-airtable-api.php              # Airtable API client
│   ├── class-airtable-sync-engine.php      # Core sync engine
│   ├── class-airtable-field-transformer.php # Field type transformations
│   └── class-airtable-sync-cli.php         # WP-CLI commands
├── assets/
│   ├── css/admin.css                       # Admin interface styles
│   └── js/admin.js                         # Admin interface JavaScript (AJAX sync)
└── README.md                               # Plugin documentation
```

### Key Implementation Details

**Sync Engine Flow:**
1. Fetches records from Airtable API with `returnFieldsByFieldId=true` parameter
2. For each record:
   - Checks if WordPress post exists via `_airtable_id` post meta
   - Compares `_airtable_last_modified` meta with record's lastModifiedTime field
   - Skips sync if timestamps match (no changes)
   - Creates new post or updates existing post
   - Stores `_airtable_id` and `_airtable_last_modified` meta fields
3. After sync, queries all published posts with `_airtable_id` meta
4. Unpublishes any posts whose Airtable ID is not in the sync results

**Field Transformation:**
- `Airtable_Field_Transformer` class handles type conversions
- Supports: core WP fields, ACF fields (including repeaters), taxonomies, attachments
- Attachments are downloaded via `media_handle_sideload()` and imported to media library
- URLs mapped to ACF link fields are formatted as `{url, title, target}` arrays
- Repeater fields use `destination_subfield` parameter to map to specific subfield

**Admin UI:**
- AJAX-powered sync with real-time feedback
- JavaScript displays statistics in styled result boxes
- Uses WordPress nonces for security
- Sync runs in same process (not background job)

**Post Meta Fields:**
- `_airtable_id` (string): Airtable record ID (e.g., "recXXXXXXXXXXXXXX")
- `_airtable_last_synced` (datetime): WordPress datetime of last sync
- `_airtable_last_modified` (string): ISO 8601 timestamp from Airtable's lastModifiedTime field

### Development Workflow

1. **Configure API credentials** in WordPress admin (once per environment)
2. **Edit `config/mappings.php`** to add/modify field mappings
3. **Use Field ID Inspector** in admin UI to discover Airtable field IDs
4. **Validate** configuration in admin UI or via `wp airtable validate`
5. **Test with dry-run**: `wp airtable sync --dry-run`
6. **Run sync** via admin UI or WP-CLI
7. **Commit** changes to git
8. **Deploy** - mappings automatically available in all environments

### Requirements

- WordPress 5.0+
- PHP 7.4+
- Valid Airtable Personal Access Token with `data.records:read` and `schema.bases:read` scopes
- For ACF field mapping: ACF Pro must be installed and active

### API Endpoints Used

- `https://api.airtable.com/v0/meta/bases` - List accessible bases
- `https://api.airtable.com/v0/meta/bases/{baseId}/tables` - Get base schema (table and field information)
- `https://api.airtable.com/v0/{baseId}/{tableId}?returnFieldsByFieldId=true` - Get records (with field IDs as keys)
- Attachment downloads via URLs provided in record data

### Best Practices

- **Always commit** `config/mappings.php` to version control
- **Add comments** to complex mappings for documentation
- **Use field keys** for ACF fields (not names) for stability
- **Add "Last Modified Time" field** to Airtable tables for optimal performance
- **Validate configuration** after making changes
- **Test mappings** in development before production deployment
- **Use dry-run mode** before running actual syncs on production data

### Troubleshooting

**Empty posts created:**
- Ensure Airtable API returns fields with field IDs as keys (`returnFieldsByFieldId=true` parameter is used)
- Check that field IDs in `config/mappings.php` match actual Airtable field IDs
- Use Field ID Inspector in admin UI to verify correct field IDs

**Posts not updating on subsequent syncs:**
- Add "Last Modified Time" field to Airtable table
- Check that `_airtable_last_modified` meta field is being stored
- Verify timestamps are in ISO 8601 format

**Posts not being unpublished when removed from view:**
- Verify the view ID is correctly specified in mapping configuration
- Check that posts have `_airtable_id` meta field (only synced posts are unpublished)
- Ensure sync is completing successfully without errors

**ACF repeater fields not populating:**
- Use `destination_subfield` parameter to specify the subfield name
- For ACF link fields, ensure `airtable_field_type` is set to `'url'`
- Verify ACF field keys are correct (use Field ID Inspector)

## Deployment Notes

This project is configured for deployment to Pantheon:
- Uses Pantheon's mu-plugin for platform integration
- Environment-specific configuration via `.env.pantheon`
- Push to Pantheon git remote or use standard Pantheon workflow

## Front-End Libraries

**JavaScript:**
- GSAP for animations
- Fancybox for lightboxes
- Swiper for carousels
- Masonry for grid layouts
- Accordion.js for accordions

**CSS:**
- Bootstrap 5.3+
- Normalize SCSS
- Custom SCSS architecture following ITCSS principles

## Important Notes

- Never edit files in `web/wp/` - these are managed by Composer
- ACF field groups can be edited in WordPress admin and will sync to `acf-json/`
- Use the scaffolding tool (`npm run generate`) to create new blocks/post types for consistency
- Storybook is available for developing and documenting components in isolation
- Theme uses PHP 8.1+ and requires Timber 2.x
