=== Cideapps GP ===
Contributors: marcoaspeitia31
Tags: custom post types, testimonials, services, theme options, business
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generic foundation plugin for custom WordPress themes: CPTs, native metaboxes, theme options, and developer-friendly meta keys.

== Description ==

**Cideapps GP** helps agencies and developers bootstrap custom WordPress sites with a consistent data layer:

* Custom post types: **Services** and **Testimonials**
* Taxonomy: **service_category**
* Native WordPress metaboxes (no third-party field framework required)
* **Theme options** page for branding, contact details, map location, and social URLs
* Registered post/term meta with REST API support

Theme developers read data using standard WordPress APIs: `get_option()`, `get_post_meta()`, and `get_term_meta()`. See the developer guide in the plugin repository: `docs/guia-desarrollo-temas.md`.

= Features =

* Services CPT with pricing, slogan, icon image, and custom excerpt meta
* Testimonials CPT with job title, opinion, and photo
* Service category term images
* Tabbed Theme options admin UI
* Google Maps picker in admin for business location (optional)
* Reverse geocoding when moving the map marker (address suggestion; editable before save)
* Media library integration for image fields (click preview to re-select attachment)

= For theme developers =

All meta keys are stable. Image fields store both URL and attachment ID (`{field}` and `{field}_id`). Prefer `wp_get_attachment_image()` with the `_id` value in templates.

Example:

`get_post_meta( get_the_ID(), 'service_slogan', true );`

`get_option( 'cagp_theme_options' );`

Full reference: `docs/guia-desarrollo-temas.md` (Spanish, comprehensive).

= External services =

This plugin can use the **Google Maps Platform** (Maps JavaScript API and Geocoding API) in the WordPress admin when you provide an API key under **Theme options → Settings**. No Google requests are made on the public site unless your theme adds them separately.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/cideapps-gp/` or install from your distribution package.
2. Activate **Cideapps GP** through the **Plugins** menu.
3. Configure **Theme options** and **Settings** (Google API key, if using the map).
4. In your theme, load options and meta as documented in `docs/guia-desarrollo-temas.md`.

== Frequently Asked Questions ==

= Does this plugin require CMB2? =

No. Version 1.0+ uses native WordPress metaboxes and the Settings API.

= Will data be deleted when I uninstall? =

By default, **no**. Post meta, term meta, and options are preserved. Developers can enable removal via the `cideapps_gp_uninstall_delete_data` filter (see `uninstall.php`).

= Can I use the data in the block editor or REST API? =

Yes. Services and testimonials expose registered meta in the REST API when requested.

= Where is the developer documentation? =

* Theme guide: `docs/guia-desarrollo-temas.md`
* Migration notes (CMB2 → native): `docs/migracion-cmb2-metaboxes-nativos.md`
* Overview: `README.md`

== Screenshots ==

1. Theme options with tabbed sections (Branding, Contact, Social Media)
2. Services edit screen with native metaboxes
3. Map field with address suggestion

== Changelog ==

= 1.0.0 =
* Initial public architecture: Services, Testimonials, service_category, theme options.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
