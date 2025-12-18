# ACF Field Types Reference

Complete reference for ACF Pro and ACF Extended Pro field types, including JSON specifications and Twig template usage.

## Field Access Patterns in Twig

**Important:** Field access varies by context:

- **Blocks**: Use `fields.field_name` (direct access via special `fields` context key)
- **Post Types**: Use `post.meta('field_name')`
- **Taxonomies**: Use `taxonomy.meta('field_name')`
- **Options Pages**: Use `get_field('field_name', 'option')`
- **Users**: Use `user.meta('field_name')`

---

## Basic Fields

### Text

**JSON Configuration:**

```json
{
  "label": "Text Field",
  "name": "text_field",
  "type": "text",
  "default_value": "",
  "maxlength": "",
  "placeholder": "",
  "prepend": "",
  "append": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{{ fields.text_field }}

{# In post types #}
{{ post.meta('text_field') }}

{# In taxonomies #}
{{ taxonomy.meta('text_field') }}
```

---

### Textarea

**JSON Configuration:**

```json
{
  "label": "Textarea Field",
  "name": "textarea_field",
  "type": "textarea",
  "default_value": "",
  "maxlength": "",
  "rows": "",
  "placeholder": "",
  "new_lines": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{{ fields.textarea_field|nl2br }}

{# In post types #}
{{ post.meta('textarea_field')|nl2br }}
```

**Note:** `new_lines` options: `""` (none), `"br"` (auto `<br>`), `"wpautop"` (auto `<p>`)

---

### Number

**JSON Configuration:**

```json
{
  "label": "Number Field",
  "name": "number_field",
  "type": "number",
  "default_value": "",
  "min": "",
  "max": "",
  "step": "",
  "placeholder": "",
  "prepend": "",
  "append": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{{ fields.number_field }}

{# With formatting #}
{{ fields.number_field|number_format(2) }}
```

---

### Range

**JSON Configuration:**

```json
{
  "label": "Range Field",
  "name": "range_field",
  "type": "range",
  "default_value": "",
  "min": 0,
  "max": 100,
  "step": 1,
  "prepend": "",
  "append": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div style="width: {{ fields.range_field }}%">Progress</div>
```

---

### Email

**JSON Configuration:**

```json
{
  "label": "Email Field",
  "name": "email_field",
  "type": "email",
  "default_value": "",
  "placeholder": "",
  "prepend": "",
  "append": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<a href="mailto:{{ fields.email_field }}">{{ fields.email_field }}</a>

{# In post types #}
<a href="mailto:{{ post.meta('email_field') }}">Contact Us</a>
```

---

### URL

**JSON Configuration:**

```json
{
  "label": "URL Field",
  "name": "url_field",
  "type": "url",
  "default_value": "",
  "placeholder": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<a href="{{ fields.url_field }}" target="_blank" rel="noopener">Visit Site</a>

{# In post types #}
<a href="{{ post.meta('url_field') }}">Link</a>
```

---

### Password

**JSON Configuration:**

```json
{
  "label": "Password Field",
  "name": "password_field",
  "type": "password",
  "placeholder": "",
  "prepend": "",
  "append": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# Rarely used in templates - typically for admin/backend only #}
{{ fields.password_field }}
```

---

## Choice Fields

### Select

**JSON Configuration:**

```json
{
  "label": "Select Field",
  "name": "select_field",
  "type": "select",
  "choices": {
    "red": "Red",
    "blue": "Blue",
    "green": "Green"
  },
  "default_value": "",
  "allow_null": 0,
  "multiple": 0,
  "ui": 0,
  "ajax": 0,
  "placeholder": "",
  "return_format": "value",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "value" #}
{% if fields.select_field == 'red' %}
  <div class="text-danger">Red selected</div>
{% endif %}

{# In blocks - return_format: "label" #}
<p>You selected: {{ fields.select_field }}</p>

{# Multiple select #}
{% for value in fields.select_field %}
  <span class="badge">{{ value }}</span>
{% endfor %}
```

**Note:** `return_format` options: `"value"`, `"label"`, `"array"`

---

### Checkbox

**JSON Configuration:**

```json
{
  "label": "Checkbox Field",
  "name": "checkbox_field",
  "type": "checkbox",
  "choices": {
    "option1": "Option 1",
    "option2": "Option 2",
    "option3": "Option 3"
  },
  "default_value": [],
  "layout": "vertical",
  "toggle": 0,
  "return_format": "value",
  "allow_custom": 0,
  "save_custom": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if 'option1' in fields.checkbox_field %}
  <p>Option 1 is checked</p>
{% endif %}

{# Loop through all checked values #}
<ul>
{% for value in fields.checkbox_field %}
  <li>{{ value }}</li>
{% endfor %}
</ul>

{# In post types #}
{% if 'premium' in post.meta('checkbox_field') %}
  <span class="badge">Premium</span>
{% endif %}
```

**Note:** `layout` options: `"vertical"`, `"horizontal"`

---

### Radio Button

**JSON Configuration:**

```json
{
  "label": "Radio Field",
  "name": "radio_field",
  "type": "radio",
  "choices": {
    "yes": "Yes",
    "no": "No",
    "maybe": "Maybe"
  },
  "default_value": "",
  "layout": "vertical",
  "return_format": "value",
  "allow_null": 0,
  "other_choice": 0,
  "save_other_choice": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.radio_field == 'yes' %}
  <p class="text-success">Confirmed!</p>
{% endif %}

{# In post types #}
{{ post.meta('radio_field') }}
```

---

### Button Group

**JSON Configuration:**

```json
{
  "label": "Button Group Field",
  "name": "button_group_field",
  "type": "button_group",
  "choices": {
    "left": "Left",
    "center": "Center",
    "right": "Right"
  },
  "default_value": "",
  "layout": "horizontal",
  "return_format": "value",
  "allow_null": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div class="text-{{ fields.button_group_field }}">
  Aligned content
</div>

{# In post types #}
<div class="align-{{ post.meta('button_group_field') }}">
  Content here
</div>
```

---

### True / False

**JSON Configuration:**

```json
{
  "label": "True/False Field",
  "name": "true_false_field",
  "type": "true_false",
  "message": "",
  "default_value": 0,
  "ui": 0,
  "ui_on_text": "",
  "ui_off_text": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.true_false_field %}
  <div class="featured">Featured Content</div>
{% endif %}

{# In post types #}
{% if post.meta('is_featured') %}
  <span class="badge badge-primary">Featured</span>
{% endif %}
```

---

## Content Fields

### Image

**JSON Configuration:**

```json
{
  "label": "Image Field",
  "name": "image_field",
  "type": "image",
  "return_format": "array",
  "preview_size": "medium",
  "library": "all",
  "min_width": "",
  "min_height": "",
  "min_size": "",
  "max_width": "",
  "max_height": "",
  "max_size": "",
  "mime_types": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "array" (ACF returns array with url, alt, etc.) #}
{% if fields.image_field %}
  <img
    src="{{ fields.image_field.url }}"
    alt="{{ fields.image_field.alt }}"
    width="{{ fields.image_field.width }}"
    height="{{ fields.image_field.height }}"
  />

  {# Responsive image with sizes #}
  <img
    src="{{ fields.image_field.sizes.large }}"
    srcset="{{ fields.image_field.sizes.medium }} 768w,
            {{ fields.image_field.sizes.large }} 1024w"
    sizes="(max-width: 768px) 100vw, 1024px"
    alt="{{ fields.image_field.alt }}"
  />
{% endif %}

{# In blocks - return_format: "id" (wrap with Timber's Image for object access) #}
{% if fields.image_field %}
  {% set image = Image(fields.image_field) %}
  <img src="{{ image.src }}" alt="{{ image.alt }}">

  {# Timber Image responsive #}
  <img
    src="{{ image.src('large') }}"
    srcset="{{ image.srcset }}"
    sizes="{{ image.img_sizes }}"
    alt="{{ image.alt }}"
  />
{% endif %}

{# In blocks - return_format: "url" (just a string) #}
<img src="{{ fields.image_field }}" alt="">

{# In post types - ACF returns array #}
{% set image = post.meta('featured_image') %}
{% if image %}
  <img src="{{ image.url }}" alt="{{ image.alt }}">
{% endif %}

{# In post types - if ACF returns ID, wrap with Image() #}
{% set image_id = post.meta('featured_image') %}
{% if image_id %}
  {% set image = Image(image_id) %}
  <img src="{{ image.src }}" alt="{{ image.alt }}">
{% endif %}
```

**Note:** `return_format` options: `"array"` (recommended - returns ACF array), `"url"` (string), `"id"` (integer - use with Timber's `Image()`)

**ACF Array vs Timber Image:**
- ACF array format: Access with `.url`, `.alt`, `.width`, `.height` (array keys)
- Timber Image object: Access with `.src`, `.alt`, `.width()`, `.height()` (methods/properties)

---

### File

**JSON Configuration:**

```json
{
  "label": "File Field",
  "name": "file_field",
  "type": "file",
  "return_format": "array",
  "library": "all",
  "min_size": "",
  "max_size": "",
  "mime_types": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "array" #}
{% if fields.file_field %}
  <a href="{{ fields.file_field.url }}" download>
    {{ fields.file_field.title }}
    <span>({{ fields.file_field.filesize }})</span>
  </a>
{% endif %}

{# In post types #}
{% set pdf = post.meta('pdf_download') %}
{% if pdf %}
  <a href="{{ pdf.url }}" class="btn btn-primary">
    Download PDF ({{ (pdf.filesize / 1024 / 1024)|number_format(2) }} MB)
  </a>
{% endif %}
```

**Note:** `return_format` options: `"array"` (recommended), `"url"`, `"id"`

---

### Gallery

**JSON Configuration:**

```json
{
  "label": "Gallery Field",
  "name": "gallery_field",
  "type": "gallery",
  "return_format": "array",
  "preview_size": "medium",
  "insert": "append",
  "library": "all",
  "min": "",
  "max": "",
  "min_width": "",
  "min_height": "",
  "min_size": "",
  "max_width": "",
  "max_height": "",
  "max_size": "",
  "mime_types": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div class="gallery">
  {% for image in fields.gallery_field %}
    <div class="gallery-item">
      <img src="{{ image.sizes.medium }}" alt="{{ image.alt }}">
      {% if image.caption %}
        <p class="caption">{{ image.caption }}</p>
      {% endif %}
    </div>
  {% endfor %}
</div>

{# In post types with lightbox #}
{% set gallery = post.meta('photo_gallery') %}
{% if gallery %}
  <div class="row">
    {% for image in gallery %}
      <div class="col-md-4">
        <a href="{{ image.url }}" data-fancybox="gallery">
          <img src="{{ image.sizes.thumbnail }}" alt="{{ image.alt }}" class="img-fluid">
        </a>
      </div>
    {% endfor %}
  </div>
{% endif %}
```

---

### oEmbed

**JSON Configuration:**

```json
{
  "label": "oEmbed Field",
  "name": "oembed_field",
  "type": "oembed",
  "width": "",
  "height": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.oembed_field %}
  <div class="video-embed">
    {{ fields.oembed_field }}
  </div>
{% endif %}

{# In post types #}
<div class="ratio ratio-16x9">
  {{ post.meta('video_url') }}
</div>
```

**Note:** Supports YouTube, Vimeo, SoundCloud, etc.

---

### WYSIWYG Editor

**JSON Configuration:**

```json
{
  "label": "WYSIWYG Field",
  "name": "wysiwyg_field",
  "type": "wysiwyg",
  "default_value": "",
  "tabs": "all",
  "toolbar": "full",
  "media_upload": 1,
  "delay": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div class="content">
  {{ fields.wysiwyg_field|raw }}
</div>

{# In post types #}
<div class="post-content">
  {{ post.meta('body_content')|raw }}
</div>
```

**Note:** `tabs` options: `"all"`, `"visual"`, `"text"`. `toolbar` options: `"full"`, `"basic"`

---

## jQuery Fields

### Color Picker

**JSON Configuration:**

```json
{
  "label": "Color Picker Field",
  "name": "color_picker_field",
  "type": "color_picker",
  "default_value": "",
  "enable_opacity": 0,
  "return_format": "string",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div style="background-color: {{ fields.color_picker_field }};">
  Colored content
</div>

{# In post types #}
<h2 style="color: {{ post.meta('heading_color') }}">
  Custom colored heading
</h2>
```

---

### Date Picker

**JSON Configuration:**

```json
{
  "label": "Date Picker Field",
  "name": "date_picker_field",
  "type": "date_picker",
  "display_format": "d/m/Y",
  "return_format": "d/m/Y",
  "first_day": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<time datetime="{{ fields.date_picker_field }}">
  {{ fields.date_picker_field }}
</time>

{# Format date #}
{{ fields.date_picker_field|date('F j, Y') }}

{# In post types #}
<p>Event Date: {{ post.meta('event_date')|date('F j, Y') }}</p>
```

---

### Date Time Picker

**JSON Configuration:**

```json
{
  "label": "Date Time Picker Field",
  "name": "date_time_picker_field",
  "type": "date_time_picker",
  "display_format": "d/m/Y g:i a",
  "return_format": "d/m/Y g:i a",
  "first_day": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<time datetime="{{ fields.date_time_picker_field }}">
  {{ fields.date_time_picker_field|date('F j, Y g:i A') }}
</time>

{# In post types #}
<p>Published: {{ post.meta('publish_datetime')|date('F j, Y \a\t g:i A') }}</p>
```

---

### Time Picker

**JSON Configuration:**

```json
{
  "label": "Time Picker Field",
  "name": "time_picker_field",
  "type": "time_picker",
  "display_format": "g:i a",
  "return_format": "g:i a",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Event starts at {{ fields.time_picker_field }}</p>

{# In post types #}
<p>Opening time: {{ post.meta('opening_time') }}</p>
```

---

### Google Map

**JSON Configuration:**

```json
{
  "label": "Google Map Field",
  "name": "google_map_field",
  "type": "google_map",
  "center_lat": "",
  "center_lng": "",
  "zoom": "",
  "height": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% set map = fields.google_map_field %}
{% if map %}
  <div class="acf-map" data-zoom="16">
    <div class="marker"
         data-lat="{{ map.lat }}"
         data-lng="{{ map.lng }}">
      <h4>{{ map.address }}</h4>
    </div>
  </div>
{% endif %}

{# In post types #}
{% set location = post.meta('location') %}
<a href="https://maps.google.com/?q={{ location.lat }},{{ location.lng }}" target="_blank">
  View on Google Maps
</a>
```

---

### Icon Picker

**JSON Configuration:**

```json
{
  "label": "Icon Picker Field",
  "name": "icon_picker_field",
  "type": "icon_picker",
  "return_format": "value",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<span class="dashicons dashicons-{{ fields.icon_picker_field }}"></span>

{# In post types #}
<i class="dashicons dashicons-{{ post.meta('menu_icon') }}"></i>
```

---

## Layout Fields

### Group

**JSON Configuration:**

```json
{
  "label": "Group Field",
  "name": "group_field",
  "type": "group",
  "layout": "block",
  "sub_fields": [
    {
      "label": "Name",
      "name": "name",
      "type": "text"
    },
    {
      "label": "Email",
      "name": "email",
      "type": "email"
    }
  ],
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% set contact = fields.group_field %}
<div class="contact-info">
  <h4>{{ contact.name }}</h4>
  <a href="mailto:{{ contact.email }}">{{ contact.email }}</a>
</div>

{# In post types #}
{% set author = post.meta('author_info') %}
{% if author %}
  <p>By {{ author.name }} ({{ author.email }})</p>
{% endif %}
```

**Note:** `layout` options: `"block"`, `"table"`, `"row"`

---

### Repeater

**JSON Configuration:**

```json
{
  "label": "Repeater Field",
  "name": "repeater_field",
  "type": "repeater",
  "layout": "block",
  "button_label": "Add Row",
  "min": 0,
  "max": 0,
  "collapsed": "",
  "sub_fields": [
    {
      "label": "Title",
      "name": "title",
      "type": "text"
    },
    {
      "label": "Description",
      "name": "description",
      "type": "textarea"
    }
  ],
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.repeater_field %}
  <div class="items">
    {% for item in fields.repeater_field %}
      <div class="item">
        <h3>{{ item.title }}</h3>
        <p>{{ item.description }}</p>
      </div>
    {% endfor %}
  </div>
{% endif %}

{# In post types #}
{% set features = post.meta('features') %}
{% if features %}
  <ul class="feature-list">
    {% for feature in features %}
      <li>
        <strong>{{ feature.title }}</strong>: {{ feature.description }}
      </li>
    {% endfor %}
  </ul>
{% endif %}
```

**Note:** `layout` options: `"block"`, `"table"`, `"row"`

---

### Flexible Content

**JSON Configuration:**

```json
{
  "label": "Flexible Content Field",
  "name": "flexible_content_field",
  "type": "flexible_content",
  "button_label": "Add Row",
  "min": "",
  "max": "",
  "layouts": [
    {
      "key": "layout_text",
      "name": "text_block",
      "label": "Text Block",
      "display": "block",
      "sub_fields": [
        {
          "label": "Content",
          "name": "content",
          "type": "wysiwyg"
        }
      ]
    },
    {
      "key": "layout_image",
      "name": "image_block",
      "label": "Image Block",
      "display": "block",
      "sub_fields": [
        {
          "label": "Image",
          "name": "image",
          "type": "image"
        }
      ]
    }
  ],
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.flexible_content_field %}
  {% for layout in fields.flexible_content_field %}
    {% if layout.acf_fc_layout == 'text_block' %}
      <div class="text-block">
        {{ layout.content|raw }}
      </div>
    {% elseif layout.acf_fc_layout == 'image_block' %}
      <div class="image-block">
        <img src="{{ layout.image.url }}" alt="{{ layout.image.alt }}">
      </div>
    {% endif %}
  {% endfor %}
{% endif %}

{# In post types #}
{% set content = post.meta('page_builder') %}
{% for section in content %}
  {% include 'partial/flexible/' ~ section.acf_fc_layout ~ '.twig' with section %}
{% endfor %}
```

---

### Clone

**JSON Configuration:**

```json
{
  "label": "Clone Field",
  "name": "clone_field",
  "type": "clone",
  "clone": ["field_group_key"],
  "display": "seamless",
  "layout": "block",
  "prefix_label": 0,
  "prefix_name": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# Clone fields appear as if they were native fields #}
{# Access cloned fields directly by their names #}

{# In blocks #}
{{ fields.cloned_field_name }}

{# In post types #}
{{ post.meta('cloned_field_name') }}
```

**Note:** `display` options: `"seamless"`, `"group"`. Clone allows you to reuse field groups.

---

### Accordion

**JSON Configuration:**

```json
{
  "label": "Accordion",
  "name": "",
  "type": "accordion",
  "open": 0,
  "multi_expand": 0,
  "endpoint": 0
}
```

**Twig Usage:**

```twig
{# Accordion is layout-only field, doesn't store data #}
{# Used only in WordPress admin for organizing fields #}
```

---

### Tab

**JSON Configuration:**

```json
{
  "label": "Tab",
  "name": "",
  "type": "tab",
  "placement": "top",
  "endpoint": 0
}
```

**Twig Usage:**

```twig
{# Tab is layout-only field, doesn't store data #}
{# Used only in WordPress admin for organizing fields #}
```

---

## Relational Fields

### Link

**JSON Configuration:**

```json
{
  "label": "Link Field",
  "name": "link_field",
  "type": "link",
  "return_format": "array",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "array" #}
{% set link = fields.link_field %}
{% if link %}
  <a href="{{ link.url }}" target="{{ link.target }}">
    {{ link.title }}
  </a>
{% endif %}

{# In post types #}
{% set cta = post.meta('cta_link') %}
{% if cta %}
  <a href="{{ cta.url }}" class="btn btn-primary" target="{{ cta.target }}">
    {{ cta.title }}
  </a>
{% endif %}
```

**Note:** `return_format` options: `"array"` (recommended), `"url"`

---

### Post Object

**JSON Configuration:**

```json
{
  "label": "Post Object Field",
  "name": "post_object_field",
  "type": "post_object",
  "post_type": ["post"],
  "taxonomy": [],
  "allow_null": 0,
  "multiple": 0,
  "return_format": "object",
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "object" (returns WP_Post) #}
{% set related = fields.post_object_field %}
{% if related %}
  {% set post_obj = Post(related) %}
  <a href="{{ post_obj.link }}">{{ post_obj.title }}</a>
{% endif %}

{# Multiple posts #}
{% for related_post in fields.post_object_field %}
  {% set post_obj = Post(related_post) %}
  <div class="related-post">
    <h4>{{ post_obj.title }}</h4>
    <p>{{ post_obj.preview.read_more('Read more') }}</p>
  </div>
{% endfor %}

{# In post types #}
{% set featured = Post(post.meta('featured_post')) %}
{% if featured %}
  <a href="{{ featured.link }}">{{ featured.title }}</a>
{% endif %}
```

**Note:** `return_format` options: `"object"` (WP_Post), `"id"`

---

### Page Link

**JSON Configuration:**

```json
{
  "label": "Page Link Field",
  "name": "page_link_field",
  "type": "page_link",
  "post_type": ["page"],
  "taxonomy": [],
  "allow_null": 0,
  "allow_archives": 1,
  "multiple": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - returns URL string #}
<a href="{{ fields.page_link_field }}">Visit Page</a>

{# Multiple pages #}
{% for page_url in fields.page_link_field %}
  <li><a href="{{ page_url }}">Link</a></li>
{% endfor %}

{# In post types #}
<a href="{{ post.meta('parent_page') }}" class="back-link">Back to Parent</a>
```

---

### Relationship

**JSON Configuration:**

```json
{
  "label": "Relationship Field",
  "name": "relationship_field",
  "type": "relationship",
  "post_type": ["post"],
  "taxonomy": [],
  "filters": ["search", "post_type", "taxonomy"],
  "elements": ["featured_image"],
  "min": "",
  "max": "",
  "return_format": "object",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% if fields.relationship_field %}
  <div class="related-posts">
    {% for related_post in fields.relationship_field %}
      {% set post_obj = Post(related_post) %}
      <article class="related-post">
        {% if post_obj.thumbnail %}
          <img src="{{ post_obj.thumbnail.src }}" alt="{{ post_obj.title }}">
        {% endif %}
        <h3><a href="{{ post_obj.link }}">{{ post_obj.title }}</a></h3>
        <p>{{ post_obj.preview.read_more('Read more') }}</p>
      </article>
    {% endfor %}
  </div>
{% endif %}

{# In post types #}
{% set related = post.meta('related_articles') %}
{% if related %}
  <aside class="related">
    <h3>Related Articles</h3>
    <ul>
      {% for article in related %}
        {% set article_post = Post(article) %}
        <li><a href="{{ article_post.link }}">{{ article_post.title }}</a></li>
      {% endfor %}
    </ul>
  </aside>
{% endif %}
```

**Note:** `return_format` options: `"object"` (WP_Post), `"id"`

---

### Taxonomy

**JSON Configuration:**

```json
{
  "label": "Taxonomy Field",
  "name": "taxonomy_field",
  "type": "taxonomy",
  "taxonomy": "category",
  "field_type": "select",
  "allow_null": 0,
  "add_term": 1,
  "save_terms": 0,
  "load_terms": 0,
  "return_format": "object",
  "multiple": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "object" (returns WP_Term) #}
{% set category = fields.taxonomy_field %}
{% if category %}
  <a href="{{ category.link }}">{{ category.name }}</a>
{% endif %}

{# Multiple terms #}
{% for term in fields.taxonomy_field %}
  <a href="{{ term.link }}" class="badge">{{ term.name }}</a>
{% endfor %}

{# In post types - return_format: "id" #}
{% set term_id = post.meta('primary_category') %}
{% if term_id %}
  {% set term = Term(term_id) %}
  <a href="{{ term.link }}">{{ term.name }}</a>
{% endif %}

{# In blocks - return_format: "id" #}
{% if fields.taxonomy_field %}
  {% set term = Term(fields.taxonomy_field) %}
  <a href="{{ term.link }}">{{ term.name }}</a>
{% endif %}
```

**Note:** `field_type` options: `"select"`, `"checkbox"`, `"radio"`, `"multi_select"`
`return_format` options: `"object"` (WP_Term object), `"id"` (integer)

---

### User

**JSON Configuration:**

```json
{
  "label": "User Field",
  "name": "user_field",
  "type": "user",
  "role": [],
  "allow_null": 0,
  "multiple": 0,
  "return_format": "array",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks - return_format: "array" #}
{% set user = fields.user_field %}
{% if user %}
  <div class="author">
    {{ user.user_avatar }}
    <h4>{{ user.display_name }}</h4>
    <a href="mailto:{{ user.user_email }}">{{ user.user_email }}</a>
  </div>
{% endif %}

{# Multiple users #}
{% for contributor in fields.user_field %}
  <span class="contributor">{{ contributor.display_name }}</span>
{% endfor %}

{# In post types #}
{% set author = post.meta('guest_author') %}
{% if author %}
  <p>Guest post by {{ author.display_name }}</p>
{% endif %}
```

**Note:** `return_format` options: `"array"`, `"object"`, `"id"`

---

## ACF Extended Fields (Free)

### Advanced Link

**JSON Configuration:**

```json
{
  "label": "Advanced Link Field",
  "name": "advanced_link_field",
  "type": "acfe_advanced_link",
  "post_type": [],
  "taxonomy": [],
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% set link = fields.advanced_link_field %}
{% if link %}
  <a href="{{ link.url }}"
     title="{{ link.title }}"
     target="{{ link.target }}">
    {{ link.title }}
  </a>
{% endif %}
```

---

### Button

**JSON Configuration:**

```json
{
  "label": "Button Field",
  "name": "button_field",
  "type": "acfe_button",
  "button_value": "Click me",
  "button_ajax": 0,
  "required": 0
}
```

**Twig Usage:**

```twig
{# Button field is for admin interface actions, not template display #}
```

---

### Code Editor

**JSON Configuration:**

```json
{
  "label": "Code Editor Field",
  "name": "code_editor_field",
  "type": "acfe_code_editor",
  "mode": "text/html",
  "lines": 1,
  "indent_unit": 4,
  "max_lines": "",
  "return_format": "",
  "default_value": "",
  "placeholder": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<pre><code>{{ fields.code_editor_field }}</code></pre>

{# Raw HTML output (use with caution) #}
{{ fields.code_editor_field|raw }}
```

**Note:** `mode` options: `"text/html"`, `"text/css"`, `"application/javascript"`, `"application/json"`, `"application/x-httpd-php"`, etc.

---

### Hidden Input

**JSON Configuration:**

```json
{
  "label": "Hidden Input Field",
  "name": "hidden_input_field",
  "type": "acfe_hidden",
  "default_value": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{{ fields.hidden_input_field }}
```

---

### Slug

**JSON Configuration:**

```json
{
  "label": "Slug Field",
  "name": "slug_field",
  "type": "acfe_slug",
  "default_value": "",
  "placeholder": "",
  "prepend": "",
  "append": "",
  "maxlength": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<div id="{{ fields.slug_field }}">Content</div>

{# In post types #}
<article class="item-{{ post.meta('custom_slug') }}">
  Content
</article>
```

---

### Post Statuses (Selector)

**JSON Configuration:**

```json
{
  "label": "Post Statuses Field",
  "name": "post_statuses_field",
  "type": "acfe_post_statuses",
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Status: {{ fields.post_statuses_field }}</p>
```

---

### Post Types (Selector)

**JSON Configuration:**

```json
{
  "label": "Post Types Field",
  "name": "post_types_field",
  "type": "acfe_post_types",
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Selected Post Type: {{ fields.post_types_field }}</p>
```

---

### Taxonomies (Selector)

**JSON Configuration:**

```json
{
  "label": "Taxonomies Field",
  "name": "taxonomies_field",
  "type": "acfe_taxonomies",
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Selected Taxonomy: {{ fields.taxonomies_field }}</p>
```

---

### Taxonomy Terms (Selector)

**JSON Configuration:**

```json
{
  "label": "Taxonomy Terms Field",
  "name": "taxonomy_terms_field",
  "type": "acfe_taxonomy_terms",
  "taxonomy": ["category"],
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% for term in fields.taxonomy_terms_field %}
  <span class="badge">{{ term }}</span>
{% endfor %}
```

---

## ACF Extended PRO Fields

### Columns

**JSON Configuration:**

```json
{
  "label": "Columns",
  "name": "",
  "type": "acfe_column",
  "columns": "6/12",
  "endpoint": 0
}
```

**Twig Usage:**

```twig
{# Columns is layout-only field, doesn't store data #}
{# Used only in WordPress admin for organizing fields in columns #}
```

---

### Phone Number

**JSON Configuration:**

```json
{
  "label": "Phone Number Field",
  "name": "phone_number_field",
  "type": "acfe_phone_number",
  "default_value": "",
  "placeholder": "",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<a href="tel:{{ fields.phone_number_field }}">
  {{ fields.phone_number_field }}
</a>

{# In post types #}
<p>Call us: <a href="tel:{{ post.meta('contact_phone') }}">{{ post.meta('contact_phone') }}</a></p>
```

---

### Countries

**JSON Configuration:**

```json
{
  "label": "Countries Field",
  "name": "countries_field",
  "type": "acfe_countries",
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Country: {{ fields.countries_field }}</p>

{# Multiple countries #}
{% for country in fields.countries_field %}
  <span class="flag-{{ country|lower }}">{{ country }}</span>
{% endfor %}
```

---

### Currencies

**JSON Configuration:**

```json
{
  "label": "Currencies Field",
  "name": "currencies_field",
  "type": "acfe_currencies",
  "field_type": "select",
  "allow_null": 0,
  "multiple": 0,
  "ui": 1,
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
<p>Currency: {{ fields.currencies_field }}</p>
```

---

### Date Range Picker

**JSON Configuration:**

```json
{
  "label": "Date Range Picker Field",
  "name": "date_range_picker_field",
  "type": "acfe_date_range_picker",
  "display_format": "d/m/Y",
  "return_format": "d/m/Y",
  "separator": " - ",
  "required": 0
}
```

**Twig Usage:**

```twig
{# In blocks #}
{% set range = fields.date_range_picker_field %}
<p>From {{ range.start|date('F j, Y') }} to {{ range.end|date('F j, Y') }}</p>

{# In post types #}
{% set event_dates = post.meta('event_date_range') %}
<p>Event: {{ event_dates.start }} - {{ event_dates.end }}</p>
```

---

## Sources

- [ACF Resources & Documentation](https://www.advancedcustomfields.com/resources/)
- [ACF Field Types Archive](https://www.advancedcustomfields.com/resource-category/field-types/)
- [ACF Extended Fields Documentation](https://www.acf-extended.com/features/fields)
- [ACF Extended PRO](https://www.acf-extended.com/pro)
