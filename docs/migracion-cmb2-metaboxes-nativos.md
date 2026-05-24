# Migración CMB2 → Metaboxes nativos de WordPress

Documento de referencia para la migración gradual del plugin **Cideapps GP** desde CMB2 hacia el core de WordPress, con vista a publicación en el repositorio oficial de plugins.

---

## Estado actual

| Fase | Estado | Descripción |
|------|--------|-------------|
| **Fase 0** | ✅ Completada | Infraestructura base (`Metabox_Base`, `Meta_Registry`) |
| **Fase 1** | ✅ Completada | Campos de texto en testimoniales y servicios |
| Fase 2 | Pendiente | Campos de archivo/imagen |
| Fase 3 | Pendiente | Term meta de taxonomía `service_category` |
| Fase 4 | Pendiente | Páginas de opciones (`cagp_theme_options`, `cagp_settings_options`) |
| Fase 5 | Pendiente | Campo de mapa (Google Maps) nativo |
| Fase 6 | Pendiente | Registro completo de meta (term meta, options REST) |
| Fase 7 | Pendiente | Eliminación de CMB2 y `cmb_field_map` |
| Fase 8 | Pendiente | Preparación WordPress.org (`readme.txt`, PHPCS, etc.) |

---

## Principio rector

**Misma clave, mismo formato, misma API de lectura.**

Los temas y cualquier código que use `get_post_meta()`, `get_term_meta()` o `get_option()` debe seguir funcionando sin cambios. No se eliminan meta keys existentes; solo se cambia la capa de administración y guardado.

---

## Inventario de meta keys (referencia)

### Post meta — CPT `testimonials`

| Meta key | Tipo | Fase | Estado admin |
|----------|------|------|--------------|
| `testimonials_job` | text | 1 | Nativo |
| `testimonials_opinion` | textarea | 1 | Nativo |
| `testimonials_image` | file (URL) | 2 | CMB2 |
| `testimonials_image_id` | integer (auxiliar CMB2) | 2 | CMB2 |

### Post meta — CPT `services`

| Meta key | Tipo | Fase | Estado admin |
|----------|------|------|--------------|
| `service_pricing` | text_money | 1 | Nativo |
| `service_slogan` | text | 1 | Nativo |

### Term meta — taxonomía `service_category`

| Meta key | Tipo | Fase | Estado admin |
|----------|------|------|--------------|
| `service_taxonomy_image` | file (URL) | 3 | CMB2 |
| `service_taxonomy_image_id` | integer | 3 | CMB2 |

### Options — `cagp_theme_options`

Branding, contacto, redes sociales, mapa, etc. Ver `class-cideapps-gp-options-menu-page-fields.php`. **Fase 4.**

### Options — `cagp_settings_options`

| Option key | Tipo | Fase |
|------------|------|------|
| `google_api_key` | text (password) | 4 |

---

## Arquitectura introducida (Fase 0)

### `includes/meta/class-cideapps-gp-metabox-base.php`

Clase abstracta que centraliza:

- Registro con `add_meta_box()` y `save_post_{post_type}`
- Nonce, comprobación de capabilities, guards de autosave/revisiones
- Renderizado de tipos: `text`, `textarea`, `text_money`
- Compatibilidad con block editor (`__block_editor_compatible_meta_box`)

### `includes/meta/class-cideapps-gp-meta-registry.php`

Registra meta keys con `register_post_meta()`:

- `show_in_rest => true` (equivalente al `show_in_rest` de CMB2)
- `auth_callback` con `current_user_can( 'edit_post', $object_id )`
- Sanitización por campo

---

## Actividades realizadas — Fases 0 y 1

### Fase 0 — Infraestructura

1. Creado directorio `includes/meta/` con clases base.
2. Creado `Cideapps_Gp_Metabox_Base` para metaboxes reutilizables.
3. Creado `Cideapps_Gp_Meta_Registry` con registro de meta de testimoniales y servicios.
4. Integrado en `includes/class-cideapps-gp.php`:
   - Carga de clases meta antes de custom-fields.
   - Hook `init` (prioridad 20) para `Meta_Registry::register()`.
5. Estilos mínimos en `admin/css/cideapps-gp-admin.css` para campos nativos.

### Fase 1 — Campos de texto

1. **`Cideapps_Gp_Testimonial_Fields`** refactorizado:
   - Extiende `Cideapps_Gp_Metabox_Base`.
   - Metabox nativo **Testimonial details**: `testimonials_job`, `testimonials_opinion`.
   - CMB2 reducido a metabox separado **Testimonial image** (`testimonial_image_metabox`) solo para `testimonials_image`.

2. **`Cideapps_Gp_Service_Fields`** migrado por completo a nativo:
   - Metabox **Services pricing and generic details**: `service_pricing`, `service_slogan`.
   - Eliminado hook `cmb2_init` para servicios.

3. Hooks actualizados en `class-cideapps-gp.php`:
   - Testimoniales: `register()` + `cmb2_init` → `testimonial_image_metabox`.
   - Servicios: solo `register()`.

### Archivos tocados

```
includes/meta/class-cideapps-gp-metabox-base.php   (nuevo)
includes/meta/class-cideapps-gp-meta-registry.php  (nuevo)
includes/meta/index.php                            (nuevo)
includes/class-cideapps-gp.php                     (modificado)
admin/custom-fields/class-cideapps-gp-testimonial-fields.php (modificado)
admin/custom-fields/class-cideapps-gp-service-fields.php     (modificado)
admin/css/cideapps-gp-admin.css                    (modificado)
docs/migracion-cmb2-metaboxes-nativos.md           (este archivo)
```

---

## Plan de fases restantes

### Fase 2 — Campos de archivo/imagen

- Migrar `testimonials_image` (+ `_id`) con `wp_enqueue_media()`.
- Eliminar metabox CMB2 de imagen en testimoniales.
- Preparar helper reutilizable `render_file_field()` en la clase base.

### Fase 3 — Term meta

- Hooks `{taxonomy}_add_form_fields`, `{taxonomy}_edit_form_fields`, `created_`, `edited_`.
- `register_term_meta( 'service_category', ... )`.
- Preservar `service_taxonomy_image` y `service_taxonomy_image_id`.

### Fase 4 — Options pages

- Settings API para `cagp_theme_options` y `cagp_settings_options`.
- Mantener un solo array serializado por option key.
- Repeatables, WYSIWYG, URLs.
- Refactorizar `geocode_business_address()` con `wp_remote_get()`.

### Fase 5 — Mapa nativo

- Desacoplar `cmb_field_map`; reutilizar JS/CSS adaptando markup.
- Guardar `location` como `['latitude' => ..., 'longitude' => ...]` dentro de `cagp_theme_options`.

### Fase 6 — Registro formal completo

- `register_term_meta` para taxonomía.
- Revisar exposición REST de options si el tema lo requiere.

### Fase 7 — Retirar CMB2

- Eliminar `includes/cmb2/`, `includes/cmb_field_map/`, `includes/cmb2-functions.php`.
- Quitar hooks `cmb2_init` / `cmb2_admin_init` restantes.
- Actualizar README y descripción del plugin.

### Fase 8 — WordPress.org

- `readme.txt`, PHPCS (WordPress Coding Standards).
- `uninstall.php` documentado.
- Declarar servicio externo Google Maps.
- Revisar encolado condicional de assets.

---

## Orden sugerido de PRs/commits

```
✅ 1. Infraestructura base (Meta Registry + Metabox abstract)
✅ 2. Testimonials — text fields
✅ 3. Services — text fields
4. Testimonials — image field
5. Service taxonomy — image field
6. Options — Settings API (campos simples)
7. Options — file fields + wysiwyg + repeatables
8. Options — mapa nativo + geocoding con wp_remote_get
9. register_term_meta
10. Eliminar CMB2 + cmb_field_map
11. readme.txt + ajustes WordPress.org
```

---

## Checklist de testeo por fases

Usa esta sección para validar cada fase de la migración **antes** de continuar con la siguiente. Marca el estado de cada prueba y anota observaciones; luego compárteme el resumen (qué pasó ✅ y qué falló ❌).

### Cómo usar este checklist

| Símbolo | Significado |
|---------|-------------|
| ⬜ | Pendiente de probar |
| ✅ | Funciona correctamente |
| ❌ | Falla o comportamiento incorrecto |
| ⏭️ | No aplica en mi entorno |
| 🔶 | Parcial / con observaciones |

**Convención para reportar:** copia la tabla de la fase que probaste y reemplaza los estados. Ejemplo:

```
Fase 1 — T-1.3 ✅ | T-1.5 ❌ (el valor no persiste al recargar)
```

**Entorno sugerido:** sitio local `cideapps.local`, `WP_DEBUG` activo, plugin activo, usuario administrador salvo que la prueba indique otro rol.

---

### Fase 0 — Infraestructura (completada en código)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-0.1 | Activar el plugin sin errores PHP ni Notices en admin | ⬜ | |
| T-0.2 | Activar el plugin sin errores PHP ni Notices en REST (`/wp-json/wp/v2/`) | ⬜ | Ya confirmado: sin Notice de textdomain |
| T-0.3 | Desactivar y reactivar el plugin sin errores | ⬜ | |
| T-0.4 | Verificar que existen CPT **Testimonials** y **Services** en el menú admin | ⬜ | |
| T-0.5 | Verificar taxonomía **service_category** en Services | ⬜ | |
| T-0.6 | Listar testimonial vía REST: `GET /wp-json/wp/v2/testimonials` responde JSON válido | ⬜ | Ya confirmado |
| T-0.7 | Listar servicios vía REST: `GET /wp-json/wp/v2/services` responde JSON válido | ⬜ | |

---

### Fase 1 — Campos de texto (completada en código) ← **Probar ahora**

#### Admin — Testimonials

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.1 | Ir a **Testimonials → Añadir nuevo** (o editar uno existente) | ⬜ | |
| T-1.2 | Aparece metabox nativo **Testimonial details** con campos **Job** y **Opinion** | ⬜ | |
| T-1.3 | Aparece metabox CMB2 **Testimonial image** (solo imagen, separado del nativo) | ⬜ | |
| T-1.4 | Guardar Job y Opinion → recargar → los valores persisten | ⬜ | |
| T-1.5 | Editar un testimonial **con datos previos** de CMB2 → Job y Opinion se muestran correctamente | ⬜ | |
| T-1.6 | Subir/guardar imagen en metabox CMB2 → sigue funcionando (no regresión Fase 1) | ⬜ | |
| T-1.7 | Autosave / guardado rápido no borra ni corrompe meta de Job/Opinion | ⬜ | |
| T-1.8 | Block editor (si lo usas): metabox nativo visible y usable | ⬜ | ⏭️ si usas editor clásico |

#### Admin — Services

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.9 | Ir a **Services → Añadir nuevo** (o editar uno existente) | ⬜ | |
| T-1.10 | Aparece metabox nativo **Services pricing and generic details** con **Pricing ($)** y **Slogan** | ⬜ | |
| T-1.11 | **No** aparece metabox duplicado de CMB2 para servicios | ⬜ | |
| T-1.12 | Guardar Pricing y Slogan → recargar → los valores persisten | ⬜ | |
| T-1.13 | Servicio con pricing previo de CMB2 (ej. con `$`) se muestra y guarda sin pérdida | ⬜ | |
| T-1.14 | Crear servicio con título, contenido y thumbnail → todo guarda bien junto al metabox | ⬜ | |

#### Base de datos

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.15 | En `wp_postmeta`, keys `testimonials_job` y `testimonials_opinion` con el nombre exacto | ⬜ | |
| T-1.16 | En `wp_postmeta`, keys `service_pricing` y `service_slogan` con el nombre exacto | ⬜ | |
| T-1.17 | No se crearon meta keys duplicadas con prefijo distinto | ⬜ | |

#### REST API

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.18 | `GET /wp-json/wp/v2/testimonials/{id}` incluye meta `testimonials_job` y `testimonials_opinion` | ⬜ | |
| T-1.19 | `GET /wp-json/wp/v2/services/{id}` incluye meta `service_pricing` y `service_slogan` | ⬜ | |
| T-1.20 | Editar meta vía REST (POST/PATCH autenticado) actualiza el valor en admin | ⬜ | Opcional |
| T-1.21 | Peticiones REST sin Notice/Warning de PHP en la respuesta | ⬜ | Ya confirmado textdomain |

#### Regresión (no debe romperse en Fase 1)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.22 | **Theme options** (CMB2) abre y guarda sin errores | ⬜ | |
| T-1.23 | **Settings** (submenú, API key Google) sigue operativo | ⬜ | |
| T-1.24 | Editar término en **service_category** → metabox CMB2 de imagen sigue ahí | ⬜ | |
| T-1.25 | Frontend/tema muestra testimoniales y servicios con meta existente | ⬜ | |
| T-1.26 | Sin Notice `_load_textdomain_just_in_time` en admin ni REST | ⬜ | Ya confirmado |

---

### Fase 2 — Campos de archivo/imagen (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-2.1 | Metabox nativo de imagen en testimoniales reemplaza al CMB2 | ⬜ | |
| T-2.2 | Selector de medios (`wp_enqueue_media`) abre la biblioteca de WordPress | ⬜ | |
| T-2.3 | Guardar imagen persiste `testimonials_image` (URL) y `testimonials_image_id` (ID) | ⬜ | |
| T-2.4 | Testimonial con imagen previa de CMB2 muestra la imagen en el campo nativo | ⬜ | |
| T-2.5 | Eliminar imagen desde admin borra/actualiza URL e ID correctamente | ⬜ | |
| T-2.6 | REST expone `testimonials_image` y `testimonials_image_id` | ⬜ | |
| T-2.7 | Ya no existe metabox CMB2 **Testimonial image** | ⬜ | |

---

### Fase 3 — Term meta `service_category` (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-3.1 | Al **crear** término: campo de imagen nativo visible | ⬜ | |
| T-3.2 | Al **editar** término: campo de imagen nativo visible con valor previo | ⬜ | |
| T-3.3 | Guardar persiste `service_taxonomy_image` y `service_taxonomy_image_id` | ⬜ | |
| T-3.4 | Término con imagen previa de CMB2 muestra datos correctamente | ⬜ | |
| T-3.5 | Ya no existe metabox CMB2 en taxonomía | ⬜ | |
| T-3.6 | REST de términos expone meta de imagen (si aplica en tu setup) | ⬜ | |

---

### Fase 4 — Páginas de opciones (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-4.1 | Menú **Theme options** abre con Settings API nativo | ⬜ | |
| T-4.2 | Submenú **Settings** (API key) funciona | ⬜ | |
| T-4.3 | Guardar branding (logos, about) persiste en `cagp_theme_options` | ⬜ | |
| T-4.4 | Campos repeatable (teléfonos, horarios) guardan array como antes | ⬜ | |
| T-4.5 | WYSIWYG **About Business** guarda HTML correctamente | ⬜ | |
| T-4.6 | URLs de redes sociales validan y guardan | ⬜ | |
| T-4.7 | `get_option('cagp_theme_options')` retorna misma estructura que con CMB2 | ⬜ | |
| T-4.8 | Opciones previas de CMB2 se muestran en campos nativos sin pérdida | ⬜ | |

---

### Fase 5 — Mapa nativo (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-5.1 | Mapa carga con API key de `cagp_settings_options` | ⬜ | |
| T-5.2 | Arrastrar marcador guarda `location.latitude` y `location.longitude` | ⬜ | |
| T-5.3 | Al guardar Theme options, geocoding actualiza `business_address` | ⬜ | |
| T-5.4 | Ubicación previa de CMB2 se muestra en el mapa nativo | ⬜ | |
| T-5.5 | Sin dependencia de `cmb_field_map` | ⬜ | |

---

### Fase 6 — Registro formal de meta (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-6.1 | `register_term_meta` activo para `service_category` | ⬜ | |
| T-6.2 | Meta de posts/términos visible en REST con schema correcto | ⬜ | |
| T-6.3 | Usuario sin permisos no puede editar meta vía REST | ⬜ | |

---

### Fase 7 — Eliminación de CMB2 (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-7.1 | Carpetas `includes/cmb2/` y `includes/cmb_field_map/` eliminadas | ⬜ | |
| T-7.2 | Plugin activa sin errores de clases/funciones CMB2 faltantes | ⬜ | |
| T-7.3 | Todas las pantallas admin del plugin funcionan solo con metaboxes nativos | ⬜ | |
| T-7.4 | Tamaño del plugin reducido (sin librería embebida) | ⬜ | |

---

### Fase 8 — Preparación WordPress.org (pendiente implementación)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-8.1 | Existe `readme.txt` válido en la raíz del plugin | ⬜ | |
| T-8.2 | PHPCS (WordPress Coding Standards) sin errores críticos | ⬜ | |
| T-8.3 | `uninstall.php` documentado y probado | ⬜ | |
| T-8.4 | Servicio externo Google Maps declarado en readme | ⬜ | |
| T-8.5 | Assets (CSS/JS) solo se encolan en pantallas necesarias | ⬜ | |
| T-8.6 | Plugin Check (PCP) de WordPress.org pasa validaciones principales | ⬜ | |

---

### Plantilla para reportar resultados

Copia, completa y envíame en el chat:

```markdown
## Resultados de testeo — Fase X

**Entorno:** WP x.x | PHP x.x | Plugin v1.x.x | Fecha: YYYY-MM-DD

### Resumen
- ✅ OK: (IDs)
- ❌ Fallas: (IDs + descripción breve)
- ⏭️ N/A: (IDs)
- 🔶 Parcial: (IDs + observación)

### Detalle de fallas
| ID | Qué esperaba | Qué ocurrió |
|----|--------------|-------------|
| T-X.X | ... | ... |

### ¿Continuar a siguiente fase?
[ ] Sí, todo OK en fases probadas
[ ] No, hay fallas por corregir
```

---

### Qué debes probar ahora (resumen)

Con las **Fases 0 y 1** implementadas, prioriza en este orden:

1. **T-1.1 a T-1.14** — Admin de testimoniales y servicios (crear, editar, guardar, recargar).
2. **T-1.15 a T-1.17** — Meta keys en base de datos (opcional pero recomendado).
3. **T-1.18 a T-1.21** — REST API por ID de post concreto.
4. **T-1.22 a T-1.26** — Regresión: Theme options, taxonomía, frontend, sin notices.

Las fases **2 a 8** están listadas para cuando las implementemos; no hace falta probarlas aún.

---

## Riesgos conocidos

| Riesgo | Mitigación aplicada / pendiente |
|--------|----------------------------------|
| Formato distinto en `file` | Fase 2: guardar URL + `{key}_id` |
| Repeatables como string vs array | Fase 4: inspeccionar BD y replicar formato |
| `text_money` con símbolo `$` | UI muestra `$` decorativo; valor guardado con `sanitize_text_field` |
| Doble guardado CMB2 + nativo | Servicios: CMB2 eliminado; Testimoniales: campos separados por metabox |
| Block editor | Metabox nativo declarado compatible |

---

## Referencias

- [register_post_meta()](https://developer.wordpress.org/reference/functions/register_post_meta/)
- [add_meta_box()](https://developer.wordpress.org/reference/functions/add_meta_box/)
- [Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [Plugin developer guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)

---

*Última actualización: Fases 0 y 1 completadas. Checklist de testeo por fases añadido. Corrección i18n WP 6.7+.*
