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
| **Fase 6** | ⏸️ Pendiente (gate) | Revisión REST + confirmación metaboxes Services |
| Fase 7 | Pendiente | Eliminar carpetas `includes/cmb2/` y `cmb_field_map/` del repo |
| Fase 8 | Pendiente | Preparación WordPress.org (`readme.txt`, PHPCS, etc.) |

**CMB2 en runtime:** ya no se carga (sin hooks `cmb2_init` / `cmb2_admin_init`). Las carpetas en `includes/cmb2/` y `includes/cmb_field_map/` siguen en disco hasta la Fase 7.

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

## Gate antes de Fase 6 — Acciones a confirmar contigo

Marca mentalmente o en el chat:

1. **Services (C-S1 a C-S5):** confirmar que el comportamiento actual es el esperado o indicar qué campo falta.
2. **¿Fase 6 es necesaria para tu tema?** La meta de posts ya usa `register_post_meta()` pero puede no aparecer en JSON REST sin configuración extra (como viste en T-1.18/T-1.19). Fase 6 = decidir si exponer meta/options en REST.
3. **¿Proceder Fase 7?** Eliminar carpetas CMB2 del disco (recomendado tras confirmar Services).
4. **¿Proceder Fase 8?** Preparación WordPress.org.

**Recomendación:** con T-4 y T-5 validadas, puedes hacer Fase 7 (limpieza) en paralelo a Fase 6 si no necesitas REST de inmediato.

---

## Resultados de testeo consolidados

| Fase | Resultado | Notas |
|------|-----------|-------|
| 0–1 | ✅ Validado | T-1.8 y T-1.20 N/A; REST meta posts no visible (esperado) |
| 2 | ✅ Validado | T-2.6 N/A (REST) |
| 3 | ✅ Validado | T-3.1 a T-3.6 |
| 4 | ✅ Validado | T-4.1 a T-4.8 |
| 5 | ✅ Validado | T-5.1 a T-5.5 |
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

- `register_post_meta()` — `testimonials`, `services`
- `register_term_meta()` — `service_category` (ya hecho en Fase 3)

---

## Fases restantes (resumen)

### Fase 6 — REST y registro formal (opcional)

**Ya implementado en código (parcial):**

- `register_post_meta` y `register_term_meta` con `show_in_rest => true`

**Pendiente de decidir / validar:**

- Hacer visible la meta en respuestas REST de posts (p. ej. `testimonials`, `services`)
- Exponer `cagp_theme_options` / `cagp_settings_options` en REST si el tema lo requiere
- Probar permisos REST (T-6.3)

### Fase 7 — Limpieza del repositorio

- Eliminar `includes/cmb2/`, `includes/cmb_field_map/`, `includes/cmb2-functions.php` (si aún existen)
- Verificar que no queden referencias en el código

### Fase 8 — WordPress.org

- `readme.txt`, PHPCS, Plugin Check, documentar Google Maps en readme

---

## Orden sugerido de commits

```
✅ 1–8. Migración funcional (Fases 0–5)
⏸️ 9. Gate: corroboración Services (C-S1–C-S5)
10. Fase 6 — REST (si aplica)
11. Fase 7 — Eliminar carpetas CMB2 del repo
12. Fase 8 — WordPress.org
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
| T-1.18 a T-1.19 | REST posts meta | ⏭️ | No visible sin CMB2; esperado |
| T-1.20 | REST PATCH | ⏭️ | |
| T-1.21 a T-1.26 | Regresión / i18n | ✅ | |

---

### Fase 2 — Imagen testimoniales

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-2.1 a T-2.5, T-2.7 | Imagen nativa testimoniales | ✅ | |
| T-2.6 | REST imagen | ⏭️ | |

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

### Corroboración Services (antes Fase 6)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| C-S1 | Metabox pricing + slogan visible | ⬜ | Confirmar contigo |
| C-S2 | Sin metabox CMB2 en services | ⬜ | |
| C-S3 | Imagen en post: no existía en plugin | ⬜ | |
| C-S4 | Imagen en término categoría | ⬜ | |
| C-S5 | ¿Añadir campos nuevos al post? | ⬜ | Decisión producto |

---

### Fase 6 — REST (pendiente)

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-6.1 | `register_term_meta` activo | ✅ | Hecho en Fase 3 |
| T-6.2 | Meta posts/términos visible en REST | ⬜ | Opcional |
| T-6.3 | Permisos REST | ⬜ | Opcional |

---

### Fase 7 — Eliminar CMB2 del disco

| ID | Prueba | Estado | Notas |
|----|--------|--------|-------|
| T-7.1 a T-7.4 | Limpieza carpetas | ⬜ | |

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
| REST sin meta visible | `register_post_meta` activo; exposición REST puede requerir trabajo extra en Fase 6 |
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

*Última actualización: Fases 4–5 validadas (T-4.1–T-5.5). Gate pre-Fase 6 documentado para Services. Fases 1–3 validadas.*
