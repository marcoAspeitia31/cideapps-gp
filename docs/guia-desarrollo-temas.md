# Guía para desarrolladores de temas — Cideapps GP

Esta guía explica cómo consumir los datos que expone **Cideapps GP** desde un tema personalizado o un plugin hijo, siguiendo las prácticas recomendadas de WordPress.

**Audiencia:** equipos de desarrollo y la comunidad que reutilice el plugin en proyectos a medida.

**Requisito:** tener el plugin activo. Los meta keys y opciones descritos aquí son estables; no dependen de CMB2.

---

## Tabla de contenidos

1. [Principios](#principios)
2. [Opciones globales del sitio](#opciones-globales-del-sitio)
3. [Post meta — Testimonials](#post-meta--testimonials)
4. [Post meta — Services](#post-meta--services)
5. [Term meta — service_category](#term-meta--service_category)
6. [Campos tipo imagen (file)](#campos-tipo-imagen-file)
7. [REST API](#rest-api)
8. [Ejemplos en plantillas](#ejemplos-en-plantillas)
9. [Seguridad y rendimiento](#seguridad-y-rendimiento)
10. [Depuración](#depuración)
11. [Extender el plugin](#extender-el-plugin)

---

## Principios

| Regla | Detalle |
|-------|---------|
| Usar APIs de WordPress | `get_option()`, `get_post_meta()`, `get_term_meta()` |
| No acceder a tablas directamente | Los datos viven en `wp_options`, `wp_postmeta`, `wp_termmeta` |
| Escapar en salida | `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()` según el tipo |
| Imágenes | Preferir `{campo}_id` + `wp_get_attachment_image()` frente a URL suelta |
| Claves exactas | Los nombres de meta son **contrato público**; no los renombres en el tema |

---

## Opciones globales del sitio

### `cagp_theme_options` (array)

Un solo registro en `wp_options`. Configuración de negocio, branding, contacto y redes.

**Admin:** menú **Theme options** (pestañas Branding, Contact, Social Media).

| Clave | Tipo | Descripción |
|-------|------|-------------|
| `menu_logo` | string (URL) | Logo del menú |
| `menu_logo_id` | int | ID de attachment (usar para imágenes) |
| `footer_logo` | string (URL) | Logo del footer |
| `footer_logo_id` | int | ID de attachment |
| `about_business` | string (HTML) | Texto “Sobre el negocio” (WYSIWYG) |
| `business_main_phone` | string | Teléfono principal |
| `business_secondary_phones` | string[] | Teléfonos adicionales (repeatable) |
| `whatsapp_phone` | string | WhatsApp con prefijo país |
| `contact_email` | string | Email de contacto |
| `location` | array | `latitude`, `longitude` (mapa) |
| `business_address` | string | Dirección (editable; prioridad en frontend) |
| `business_working_hours` | string[] | Horarios (repeatable) |
| `main_contact_form_shortcode` | string | Shortcode CF7 u otro |
| `main_cta_url` | string (URL) | URL del CTA principal |
| `facebook_url` | string (URL) | Red social |
| `instagram_url` | string (URL) | Red social |
| `youtube_url` | string (URL) | Red social |
| `twitter_url` | string (URL) | Red social |
| `tiktok_url` | string (URL) | Red social |
| `linkedin_url` | string (URL) | Red social |

### `cagp_settings_options` (array)

**Admin:** Theme options → **Settings**.

| Clave | Tipo | Descripción |
|-------|------|-------------|
| `google_api_key` | string | API key de Google Maps (solo admin; no exponer en el tema) |

El tema **no necesita** la API key para mostrar datos; solo el plugin la usa en el mapa y geocoding del admin.

### Patrón recomendado en `functions.php`

Cargar las opciones una vez y reutilizarlas:

```php
/**
 * Theme options from Cideapps GP.
 */
if ( ! defined( 'CIDEAPPS_GP' ) ) {
	$options = get_option( 'cagp_theme_options', array() );
	define( 'CIDEAPPS_GP', is_array( $options ) ? $options : array() );
}
```

Uso en plantillas:

```php
$phone = ! empty( CIDEAPPS_GP['business_main_phone'] )
	? CIDEAPPS_GP['business_main_phone']
	: '';

if ( $phone ) {
	printf(
		'<a href="tel:%s">%s</a>',
		esc_attr( preg_replace( '/\s+/', '', $phone ) ),
		esc_html( $phone )
	);
}
```

### HTML en `about_business`

```php
if ( ! empty( CIDEAPPS_GP['about_business'] ) ) {
	echo wp_kses_post( CIDEAPPS_GP['about_business'] );
}
```

### Repeatables (`business_secondary_phones`, `business_working_hours`)

Siempre comprobar que sea array:

```php
$hours = ! empty( CIDEAPPS_GP['business_working_hours'] ) && is_array( CIDEAPPS_GP['business_working_hours'] )
	? CIDEAPPS_GP['business_working_hours']
	: array();

foreach ( $hours as $line ) {
	if ( '' !== trim( $line ) ) {
		echo '<p>' . esc_html( $line ) . '</p>';
	}
}
```

### Mapa / coordenadas (solo si el tema las necesita)

```php
$lat = CIDEAPPS_GP['location']['latitude'] ?? '';
$lng = CIDEAPPS_GP['location']['longitude'] ?? '';
```

La **dirección mostrada al usuario** debe salir de `business_address`, no del geocoding en tiempo real.

---

## Post meta — Testimonials

**Post type:** `testimonials`  
**Archivo de plantilla típico:** `single-testimonials.php` o loop en archive.

| Meta key | Tipo | Admin |
|----------|------|-------|
| `testimonials_job` | string | Cargo / puesto |
| `testimonials_opinion` | string | Opinión (textarea) |
| `testimonials_image` | string (URL) | Imagen |
| `testimonials_image_id` | int | ID de attachment |

```php
$post_id = get_the_ID();

$job = get_post_meta( $post_id, 'testimonials_job', true );
$opinion = get_post_meta( $post_id, 'testimonials_opinion', true );
$image_id = (int) get_post_meta( $post_id, 'testimonials_image_id', true );

if ( $job ) {
	echo '<span class="testimonial-job">' . esc_html( $job ) . '</span>';
}

if ( $opinion ) {
	echo '<blockquote>' . esc_html( $opinion ) . '</blockquote>';
}

if ( $image_id ) {
	echo wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => 'testimonial-photo' ) );
}
```

---

## Post meta — Services

**Post type:** `services`  
**Taxonomía:** `service_category`

| Meta key | Tipo | Origen |
|----------|------|--------|
| `service_pricing` | string | Plugin |
| `service_slogan` | string | Plugin |
| `cwt_services_image` | string (URL) | Plugin (antes en tema; migrado) |
| `cwt_services_image_id` | int | Plugin |
| `cwt_services_excerpt` | string | Plugin |

Además, WordPress aporta **título**, **editor**, **imagen destacada** (`thumbnail`).

```php
$post_id = get_the_ID();

$pricing = get_post_meta( $post_id, 'service_pricing', true );
$slogan  = get_post_meta( $post_id, 'service_slogan', true );
$icon_id = (int) get_post_meta( $post_id, 'cwt_services_image_id', true );
$excerpt = get_post_meta( $post_id, 'cwt_services_excerpt', true );

if ( $pricing ) {
	echo '<p class="service-price">' . esc_html( $pricing ) . '</p>';
}

if ( $icon_id ) {
	echo wp_get_attachment_image( $icon_id, array( 100, 100 ), false, array( 'class' => 'service-icon' ) );
}

if ( $excerpt ) {
	echo '<p class="service-excerpt">' . esc_html( $excerpt ) . '</p>';
}
```

### Bloques (Gutenberg)

Si usas `cideapps-gt-blocks` u otro bloque, las mismas claves aplican: `cwt_services_image_id`, `cwt_services_excerpt`.

---

## Term meta — service_category

**Taxonomía:** `service_category`

| Meta key | Tipo |
|----------|------|
| `service_taxonomy_image` | string (URL) |
| `service_taxonomy_image_id` | int |

```php
$term_id = get_queried_object_id(); // en archive de taxonomía

$image_id = (int) get_term_meta( $term_id, 'service_taxonomy_image_id', true );

if ( $image_id ) {
	echo wp_get_attachment_image( $image_id, 'medium' );
}
```

En un loop de términos:

```php
foreach ( $terms as $term ) {
	$image_id = (int) get_term_meta( $term->term_id, 'service_taxonomy_image_id', true );
	// ...
}
```

---

## Campos tipo imagen (file)

El plugin guarda **dos meta keys** por cada imagen:

| Sufijo | Contenido |
|--------|-----------|
| `{nombre}` | URL del archivo |
| `{nombre}_id` | ID en la biblioteca de medios |

**Buena práctica en el tema:** usar siempre `{nombre}_id` cuando exista.

```php
$logo_id = ! empty( CIDEAPPS_GP['menu_logo_id'] )
	? (int) CIDEAPPS_GP['menu_logo_id']
	: 0;

if ( $logo_id ) {
	echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'site-logo' ) );
} elseif ( ! empty( CIDEAPPS_GP['menu_logo'] ) ) {
	// Fallback legacy: solo URL
	printf(
		'<img src="%s" alt="%s" class="site-logo" />',
		esc_url( CIDEAPPS_GP['menu_logo'] ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}
```

---

## REST API

Los CPT `testimonials` y `services` tienen `show_in_rest => true` y meta registrada con `show_in_rest`.

**Ejemplo — servicio:**

```
GET /wp-json/wp/v2/services/{id}
```

Respuesta (fragmento):

```json
{
  "id": 123,
  "title": { "rendered": "..." },
  "meta": {
    "service_pricing": "200",
    "service_slogan": "...",
    "cwt_services_image": "https://...",
    "cwt_services_image_id": 2032,
    "cwt_services_excerpt": "..."
  }
}
```

**Término:**

```
GET /wp-json/wp/v2/service_category/{id}
```

`meta.service_taxonomy_image` y `meta.service_taxonomy_image_id` incluidos.

Las opciones `cagp_theme_options` **no** están en REST por defecto; en headless usar `get_option()` vía endpoint propio o ampliar el plugin.

---

## Ejemplos en plantillas

### Helper opcional en el tema

Centraliza lectura y valores por defecto:

```php
/**
 * Get a theme option from Cideapps GP.
 *
 * @param string $key     Option array key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function cagp_get_theme_option( $key, $default = '' ) {
	if ( ! defined( 'CIDEAPPS_GP' ) || ! is_array( CIDEAPPS_GP ) ) {
		return $default;
	}
	return CIDEAPPS_GP[ $key ] ?? $default;
}
```

### Comprobar que el plugin está activo

```php
if ( ! function_exists( 'cideapps_gp_run_plugin' ) && ! defined( 'CIDEAPPS_GP' ) ) {
	return;
}
```

O comprobar post types:

```php
if ( ! post_type_exists( 'services' ) ) {
	return;
}
```

---

## Seguridad y rendimiento

1. **Nunca** imprimir meta sin escapar.
2. **No** guardar de nuevo en el tema lo que ya guarda el plugin (evitar duplicar lógica de negocio).
3. **Cache:** para opciones, `get_option` ya usa caché de objeto; evitar llamadas repetidas en el mismo request definiendo `CIDEAPPS_GP` una vez.
4. **Google Maps:** la API key solo en admin; el mapa público del sitio debe usar tu propia implementación en el tema si la necesitas.
5. **Desinstalación:** por defecto el plugin **no borra** datos al desinstalar (ver `uninstall.php`). Los temas pueden seguir esperando meta keys tras reinstalar.

---

## Depuración

| Problema | Qué revisar |
|----------|-------------|
| Meta vacío en frontend | ¿Post publicado? ¿Clave exacta? ¿Plugin activo? |
| Imagen no sale | ¿Existe `{key}_id`? ¿Attachment sigue en medios? |
| REST sin `meta` | CPT debe soportar `custom-fields` (ya configurado en el plugin) |
| Opciones no cargan | `get_option( 'cagp_theme_options' )` — ¿array vacío? configurar en admin |

Consulta meta en base de datos:

```sql
SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = 123;
```

---

## Extender el plugin

Para **nuevos campos** en un proyecto:

1. Añadir definición en la clase de campos correspondiente (`Cideapps_Gp_*_Fields` o `get_fields()` de options).
2. Registrar la clave en `Cideapps_Gp_Meta_Registry` (posts/terms) o en el sanitize de options.
3. Documentar la clave en esta guía y en el CHANGELOG.
4. Consumir en el tema con `get_post_meta` / `get_option` como el resto.

Documentación técnica de migración interna: [`migracion-cmb2-metaboxes-nativos.md`](migracion-cmb2-metaboxes-nativos.md).

---

## Referencia rápida (cheat sheet)

```
Opciones:     get_option( 'cagp_theme_options' )
              get_option( 'cagp_settings_options' )  // solo admin

Testimonial:  testimonials_job, testimonials_opinion,
              testimonials_image_id

Service:      service_pricing, service_slogan,
              cwt_services_image_id, cwt_services_excerpt

Term:         service_taxonomy_image_id  (taxonomy: service_category)

Imagen:       wp_get_attachment_image( (int) $id, $size )
```

---

*Cideapps GP — documentación para desarrolladores de temas. Mantener este archivo actualizado cuando se añadan meta keys nuevas.*
