# Cideapps GP

Plugin foundation for custom WordPress projects: **Custom Post Types**, **taxonomies**, **native metaboxes**, and **theme options** (Settings API) to centralize business data for bespoke themes.

Based on the [WordPress Plugin Boilerplate](http://wppb.me/).

---

## Documentation

| Document | Audience | Description |
|----------|----------|-------------|
| **[Guía para desarrolladores de temas](docs/guia-desarrollo-temas.md)** | Theme & block developers | **Main reference** — meta keys, `get_option` / `get_post_meta`, examples, security, REST |
| [Migración CMB2 → nativo](docs/migracion-cmb2-metaboxes-nativos.md) | Maintainers | Internal migration history and phases |
| [readme.txt](readme.txt) | WordPress.org | Plugin directory listing (English) |

---

## Quick start (theme developers)

1. Activate **Cideapps GP**.
2. Configure **Theme options** and **Settings** in wp-admin.
3. In your theme `functions.php`:

```php
if ( ! defined( 'CIDEAPPS_GP' ) ) {
	$opts = get_option( 'cagp_theme_options', array() );
	define( 'CIDEAPPS_GP', is_array( $opts ) ? $opts : array() );
}
```

4. Read post meta in templates:

```php
echo esc_html( get_post_meta( get_the_ID(), 'service_slogan', true ) );
```

5. For images, prefer attachment IDs:

```php
$logo_id = (int) ( CIDEAPPS_GP['menu_logo_id'] ?? 0 );
if ( $logo_id ) {
	echo wp_get_attachment_image( $logo_id, 'full' );
}
```

**Full tables, keys, and best practices:** [docs/guia-desarrollo-temas.md](docs/guia-desarrollo-temas.md).

---

## Features

- **Native WordPress metaboxes** — `Cideapps_Gp_Metabox_Base`, `Meta_Registry`
- **CPTs:** `services`, `testimonials`
- **Taxonomy:** `service_category`
- **Options:** `cagp_theme_options`, `cagp_settings_options` (Google Maps API key, admin only)
- **REST API** — post and term meta registered with `show_in_rest`
- **i18n** — `languages/` (`es`, `es_MX`, `es_ES`)

---

## Data overview

### Options

| Option key | Purpose |
|------------|---------|
| `cagp_theme_options` | Branding, contact, map, social URLs |
| `cagp_settings_options` | Google Maps API key (admin) |

### Post meta — `testimonials`

`testimonials_job`, `testimonials_opinion`, `testimonials_image`, `testimonials_image_id`

### Post meta — `services`

`service_pricing`, `service_slogan`, `cwt_services_image`, `cwt_services_image_id`, `cwt_services_excerpt`

### Term meta — `service_category`

`service_taxonomy_image`, `service_taxonomy_image_id`

---

## Project structure

```
cideapps-gp/
├── admin/                  # CPTs, taxonomies, options UI, assets
├── includes/meta/          # Metabox base, options base, meta registry
├── docs/                   # Theme developer guide + migration doc
├── languages/
├── readme.txt              # WordPress.org
├── uninstall.php
└── cideapps-gp.php
```

---

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Optional: **Google Maps Platform** (admin map & geocoding only)

---

## Installation

```bash
git clone https://github.com/marcoAspeitia31/cideapps-gp.git
```

Activate in **Plugins → Cideapps GP**, then open **Theme options**.

---

## Uninstall behavior

By default, uninstalling the plugin **does not delete** posts, meta, or options (safe for client sites). To remove plugin options on uninstall, use the filter documented in `uninstall.php`:

```php
add_filter( 'cagp_uninstall_delete_data', '__return_true' );
```

---

## Development (plugin maintainers)

- New CPTs: `admin/custom-post-types/`
- New taxonomies: `admin/custom-taxonomies/`
- New metabox fields: extend `Cideapps_Gp_Metabox_Base` + register in `Cideapps_Gp_Meta_Registry`
- New options: extend `Cideapps_Gp_Options_Page_Base`

Before release, run [Plugin Check](https://wordpress.org/plugins/plugin-check/) and PHPCS with WordPress Coding Standards when preparing for WordPress.org.

---

## License

GPL-2.0+ — see [LICENSE.txt](LICENSE.txt).

---

## Author

**Marco Aspeitia** — [@marcoAspeitia31](https://github.com/marcoAspeitia31)
