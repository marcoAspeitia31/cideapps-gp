# Migración CMB2 → Metaboxes nativos de WordPress

Documento de referencia para la migración gradual del plugin **Cideapps GP** desde CMB2 hacia el core de WordPress, con vista a publicación en el repositorio oficial de plugins.

---

## Estado actual

| Fase | Estado | Descripción |
|------|--------|-------------|
| **Fase 0** | ✅ Completada | Infraestructura base (`Metabox_Base`, `Meta_Registry`) |
| **Fase 1** | ✅ Completada y validada | Campos de texto en testimoniales y servicios |
| **Fase 2** | ✅ Completada y validada | Campo de imagen en testimoniales |
| **Fase 3** | ✅ Completada y validada | Term meta de taxonomía `service_category` |
| **Fase 4** | ✅ Completada y validada | Páginas de opciones (Settings API) |
| **Fase 5** | ✅ Completada y validada | Mapa Google nativo en Theme options |
| **Fase 6** | ✅ Completada y validada | REST: meta de posts y términos (T-6.2, T-6.3) |
| **Fase 7** | ✅ Completada | CMB2 y `cmb_field_map` eliminados del repositorio |
| **Fase 7.1** | ✅ Completada | UI Theme options: tabs + avisos de guardado |
| **Fase 7.2** | ✅ Completada | Geocoding AJAX al soltar marker |
| **Fase 7.3** | ✅ Completada | Preview file clicable + preselección Media Library |
| **Fase 8** | ✅ Completada | Documentación temas + `readme.txt` + `uninstall.php` |

**CMB2:** eliminado del repositorio (Fase 7). El plugin usa solo metaboxes nativos y Settings API.

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
| `testimonials_image` | file (URL) | 2 | Nativo |
| `testimonials_image_id` | integer (auxiliar) | 2 | Nativo |

### Post meta — CPT `services`

| Meta key | Tipo | Fase | Estado admin |
|----------|------|------|--------------|
| `service_pricing` | text_money | 1 | Nativo (`Cideapps_Gp_Service_Fields`) |
| `service_slogan` | text | 1 | Nativo (`Cideapps_Gp_Service_Fields`) |
| `cwt_services_image` | file (URL) | 1.4 | Nativo (`Cideapps_Gp_Service_Additional_Fields`) — migrado desde tema |
| `cwt_services_image_id` | integer (auxiliar) | 1.4 | Nativo |
| `cwt_services_excerpt` | textarea | 1.4 | Nativo |

> **Origen `cwt_*`:** antes en `cideapps-wp-theme/inc/custom-fields.php` (CMB2). Centralizado en el plugin para no depender de CMB2 en el tema. Los bloques `cideapps-gt-blocks` siguen usando las mismas claves.

### Term meta — taxonomía `service_category`

| Meta key | Tipo | Fase | Estado admin |
|----------|------|------|--------------|
| `service_taxonomy_image` | file (URL) | 3 | Nativo |
| `service_taxonomy_image_id` | integer (auxiliar) | 3 | Nativo |

### Options — `cagp_theme_options`

Branding, contacto, redes sociales, mapa, etc. Ver `admin/custom-fields/class-cideapps-gp-theme-options-page.php`.

### Options — `cagp_settings_options`

| Option key | Tipo | Fase |
|------------|------|------|
| `google_api_key` | text (password) | 4 |

---

## Metaboxes esperados por pantalla (referencia rápida)

Útil para corroborar que “no falta nada” tras la migración.

| Pantalla | Qué debe verse | Qué NO debe verse |
|----------|----------------|-------------------|
| **Testimonials → editar** | 1 metabox nativo **Testimonial details** (imagen, job, opinion) | Metabox CMB2 de imagen separado |
| **Services → editar** | 2 metaboxes nativos: **Services pricing and generic details** + **Additional information** (icono, excerpt) + core WP (título, editor, imagen destacada) | Metabox CMB2 del tema o plugin; duplicados |
| **Services → Categories → editar término** | Campo nativo **Main image** en el formulario del término | Metabox CMB2 en taxonomía |
| **Theme options** | Formulario Settings API nativo (tabla de campos) | Pantalla CMB2 antigua |
| **Theme options → Settings** | Submenú con API key de Google Maps | — |

---

## Corroboración Services — metaboxes del plugin

En **Services → Añadir/Editar** deben verse **dos** metaboxes del plugin:

| Metabox | Clase | Campos |
|---------|-------|--------|
| Services pricing and generic details | `Cideapps_Gp_Service_Fields` | `service_pricing`, `service_slogan` |
| Additional information | `Cideapps_Gp_Service_Additional_Fields` | `cwt_services_image` (+ `_id`), `cwt_services_excerpt` |

El tema **no** debe registrar `cmb2_init` para servicios (`inc/custom-fields.php` vacío o sin require).

Imagen de **categoría** (no del post): taxonomía `service_category` → `service_taxonomy_image`.

### Checklist Services (post-migración tema → plugin)

| ID | Prueba | Estado |
|----|--------|--------|
| C-S1 | Metabox pricing + slogan | ⬜ |
| C-S2 | Metabox **Additional information** (icono + excerpt) | ⬜ |
| C-S3 | Datos previos `cwt_services_*` visibles al editar | ⬜ |
| C-S4 | Sin CMB2 en tema (`custom-fields.php` no cargado) | ⬜ |
| C-S5 | Bloques `services-grid` muestran icono y excerpt | ⬜ |

### Acciones posibles según tu confirmación

| Si confirmas… | Acción recomendada |
|---------------|-------------------|
| **C-S1–C-S4 OK** y no necesitas más campos en el post | ✅ Continuar con **Fase 6** (REST) y luego **Fase 7** (borrar carpetas CMB2) |
| Falta un metabox que **sí existía** con CMB2 y no está en la tabla anterior | Reportar ID del campo / captura → lo tratamos como bug de migración |
| Quieres **imagen u otros campos** en el post `services` | Planificar como **mejora nueva** (nueva fase o extensión de `Cideapps_Gp_Service_Fields`) — no bloquea Fase 6 si no es requisito |
| Confusión con **testimonials** (que sí tienen imagen en el mismo metabox) | Solo documentar; comportamiento distinto por diseño |

---

## Fase 7.1 — UI Theme options (tabs y avisos)

- Pestañas automáticas por campos `type => title` en Theme options (Branding, Contact, Social Media).
- Avisos verde/rojo vía `settings_errors()` al guardar.
- Archivos: `cagp-options-admin.css`, `cagp-options-tabs.js`, cambios en `Cideapps_Gp_Options_Page_Base`.

## Fase 7.3 — Campos file: preview clicable (estilo CMB2)

- Clic en la miniatura abre `wp.media` con el attachment actual preseleccionado.
- Título del modal = label del campo (`data-frame-title`).
- Botón del frame: “Use this file”. Aplica a posts, options y term meta.

## Fase 7.2 — Geocoding al soltar marker (AJAX)

- **Ya no** se geocodifica al guardar `cagp_theme_options` (`pre_update_option` eliminado).
- Al **soltar** el marker (`dragend`, debounce 500 ms): `wp_ajax_cagp_reverse_geocode` actualiza `business_address`.
- Al **guardar**: se persiste el texto del textarea tal como lo dejó el usuario.
- Avisos rojos/verdes junto al mapa vía AJAX (la API key no se expone al front).

## Publicación en WordPress.org (checklist opcional)

- [ ] Ejecutar [Plugin Check](https://wordpress.org/plugins/plugin-check/)
- [ ] PHPCS con WordPress Coding Standards
- [ ] Subir a SVN de WordPress.org / crear release en GitHub
- [ ] Bump de versión en `cideapps-gp.php` y `readme.txt` stable tag

---

## Resultados de testeo consolidados

| Fase | Resultado | Notas |
|------|-----------|-------|
| 0–1 | ✅ Validado | T-1.8 y T-1.20 N/A; REST meta posts no visible (esperado) |
| 2 | ✅ Validado | T-2.6 N/A (REST) |
| 3 | ✅ Validado | T-3.1 a T-3.6 |
| 4 | ✅ Validado | T-4.1 a T-4.8 |
| 5 | ✅ Validado | T-5.1 a T-5.5 |
| 6 | ✅ Validado | T-6.2, T-6.3 |
| 7 | ✅ Completada | CMB2 eliminado del disco |
| i18n | ✅ Corregido | Textdomain en `init`; sin Notice `_load_textdomain_just_in_time` |

---

## Arquitectura introducida

### `includes/meta/class-cideapps-gp-metabox-base.php`

- Metaboxes de post: `text`, `textarea`, `text_money`, `file`
- Compatible con block editor
- `Cideapps_Gp_File_Field_Assets` para biblioteca de medios

### `includes/meta/class-cideapps-gp-term-fields-base.php`

- Campos en taxonomías (`add` / `edit` term)
- Mismo formato file URL + `_id`

### Options (Fase 4–5)

- `Cideapps_Gp_Options_Page_Base` + Theme/Settings pages
- `cagp-options-map.js`, `cagp-options-repeatable.js`
- Geocoding: `pre_update_option_cagp_theme_options` + `wp_remote_get()`

### `includes/meta/class-cideapps-gp-meta-registry.php`

- `register_post_meta()` — `testimonials`, `services` (schema REST `view` + `edit`)
- `register_term_meta()` — `service_category`

### Fase 6 — REST (requerimiento clave)

WordPress solo incluye el objeto `meta` en el schema REST de un CPT si el post type declara soporte **`custom-fields`**. Sin eso, `register_post_meta( …, show_in_rest )` no basta.

| CPT / taxonomía | Soporte / registro | Objeto REST |
|-----------------|-------------------|-------------|
| `testimonials` | `custom-fields` + `register_post_meta` | `meta.testimonials_*` |
| `services` | `custom-fields` + `register_post_meta` | `meta.service_*`, `meta.cwt_services_*` |
| `service_category` | `register_term_meta` | `meta.service_taxonomy_*` |

**Options** (`cagp_theme_options`, `cagp_settings_options`): no expuestas en REST; el tema usa `get_option()`. Añadir endpoint propio solo si hace falta headless.

---

## Actividades realizadas — Fase 6

1. **`custom-fields`** añadido a `supports` de CPT `testimonials` y `services`.
2. **`Cideapps_Gp_Meta_Registry`**: `show_in_rest` con schema explícito (`type`, `context`, `default`).
3. Verificado en local: `GET /wp-json/wp/v2/services/{id}` devuelve objeto `meta` con todas las claves registradas.

### Archivos tocados (Fase 6)

```
admin/custom-post-types/class-cideapps-gp-services-post-type.php
admin/custom-post-types/class-cideapps-gp-testimonial-post-type.php
includes/meta/class-cideapps-gp-meta-registry.php
docs/migracion-cmb2-metaboxes-nativos.md
```

---

## Fases restantes (resumen)

### Fase 7 — Limpieza del repositorio ✅

- Eliminado `includes/cmb2/` (~229 archivos)
- Eliminado `includes/cmb_field_map/`
- Eliminado `includes/cmb2-functions.php`
- `README.md` actualizado (sin referencias a CMB2)

### Fase 8 — Documentación y WordPress.org ✅

- `docs/guia-desarrollo-temas.md` — guía para desarrolladores de temas (referencia pública de meta keys)
- `README.md` y `docs/README.md` actualizados
- `readme.txt` para el directorio de plugins (inglés, Google Maps declarado)
- `uninstall.php` documentado (datos preservados por defecto; filtro `cideapps_gp_uninstall_delete_data`)
- Pendiente antes de publicar en WordPress.org: Plugin Check, PHPCS, bump de versión si aplica

---

## Actividades realizadas — Fase 8

1. Guía profesional de consumo de meta/options para temas personalizados.
2. `readme.txt` con tags, FAQ, external services (Google Maps).
3. Política de desinstalación segura documentada en código y guías.

---

## Orden sugerido de commits

```
✅ 1–12. Migración funcional y documentación (Fases 0–8)
13. (Opcional) PHPCS + Plugin Check + tag de release
```

---

## Checklist de testeo por fases

### Cómo usar

| Símbolo | Significado |
|---------|-------------|
| ⬜ | Pendiente |
| ✅ | OK |
| ❌ | Falla |
| ⏭️ | N/A |
| 🔶 | Parcial |

---

### Fase 1 — Testimonials / Services

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-1.1 a T-1.7 | Testimonials admin | ✅ | |
| T-1.8 | Block editor | ⏭️ | |
| T-1.9 a T-1.14 | Services admin | ✅ | Ver sección corroboración C-S* |
| T-1.15 a T-1.17 | BD postmeta | ✅ | |
| T-1.18 a T-1.19 | REST posts meta | ✅ | Resuelto en Fase 6 (`custom-fields`) |
| T-1.20 | REST PATCH | ⏭️ | |
| T-1.21 a T-1.26 | Regresión / i18n | ✅ | |

---

### Fase 2 — Imagen testimoniales

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-2.1 a T-2.5, T-2.7 | Imagen nativa testimoniales | ✅ | |
| T-2.6 | REST imagen | ✅ | Fase 6 |

---

### Fase 3 — Taxonomía `service_category`

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-3.1 a T-3.6 | Term meta imagen | ✅ | |

---

### Fase 4 — Theme options / Settings

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-4.1 a T-4.8 | Options nativas | ✅ | |

---

### Fase 5 — Mapa

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-5.1 a T-5.5 | Mapa y geocoding | ✅ | |

---

### Corroboración Services (completada)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| C-S1 a C-S5 | Metaboxes + campos `cwt_*` en plugin | ✅ | Validado por usuario |

---

### Fase 6 — REST

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-6.1 | `register_term_meta` activo | ✅ | Fase 3 |
| T-6.2 | Meta posts visible en REST (`meta` en JSON) | ✅ | |
| T-6.3 | Meta términos visible en REST | ✅ | |
| T-6.4 | Editar meta vía REST | ⏭️ | Opcional |
| T-6.5 | Permisos REST PATCH | ⏭️ | Opcional |

---

### Fase 7 — Eliminar CMB2 del disco

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-7.1 | Carpetas `cmb2` y `cmb_field_map` eliminadas | ✅ | |
| T-7.2 | Plugin activa sin errores | ✅ | Verificado con WP-CLI |
| T-7.3 | Admin y REST operativos | ⬜ | Smoke test en tu entorno |
| T-7.4 | Tamaño del plugin reducido | ✅ | ~230 archivos menos |

---

### Fase 8 — WordPress.org

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-8.1 a T-8.6 | Publicación repo | ⬜ | |

---

## Plantilla para reportar (Fase 6 gate)

```markdown
## Corroboración pre-Fase 6

**Services (C-S1–C-S5):**
- C-S1: ✅ / ❌ — notas:
- C-S2: ✅
- C-S3: Confirmo que no necesito imagen en el post / Quiero añadir imagen al post
- C-S4: ✅
- C-S5: No añadir campos / Quiero añadir: _________

**Fase 6 REST:** ¿La necesito? Sí / No / Solo posts / Solo options

**Siguiente paso:** Fase 6 / Fase 7 / Ambas
```

---

## Riesgos conocidos

| Riesgo | Mitigación |
|--------|------------|
| Formato `file` | URL + `{key}_id` en post, term y options |
| Repeatables | Array en `cagp_theme_options` |
| REST sin meta en posts | CPT debe incluir `custom-fields` en `supports` (Fase 6) |
| Confundir imagen de categoría vs post servicio | Documentado en sección corroboración |
| Textdomain temprano | Getters lazy + `register()` en `init` |

---

## Referencias

- [register_post_meta()](https://developer.wordpress.org/reference/functions/register_post_meta/)
- [register_term_meta()](https://developer.wordpress.org/reference/functions/register_term_meta/)
- [add_meta_box()](https://developer.wordpress.org/reference/functions/add_meta_box/)
- [Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [Plugin developer guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)

---

*Última actualización: Fase 7 completada (CMB2 eliminado). Fases 0–6 validadas.*

---

## Actividades realizadas — Fase 7

1. Eliminación física de `includes/cmb2/`, `includes/cmb_field_map/` y `includes/cmb2-functions.php`.
2. Sin referencias PHP activas a CMB2 (ya retiradas en Fases 1–5).
3. `README.md` reescrito para describir arquitectura nativa.
4. Plugin verificado activo; REST `meta` en `services` sigue respondiendo.
