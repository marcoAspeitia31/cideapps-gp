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

## Checklist de validación manual — Fases 0 y 1

### Testimonials — campos nativos

- [ ] Ir a **Testimonials → Añadir nuevo** (o editar uno existente).
- [ ] Verificar metabox **Testimonial details** con campos Job y Opinion.
- [ ] Verificar metabox **Testimonial image** (CMB2) sigue visible debajo o en otra posición.
- [ ] Guardar Job y Opinion; recargar la página y confirmar que los valores persisten.
- [ ] En base de datos (`wp_postmeta`), confirmar keys `testimonials_job` y `testimonials_opinion` sin cambiar nombre.
- [ ] Si existían datos previos de CMB2, confirmar que se muestran correctamente en los campos nativos.
- [ ] Probar guardado rápido / autosave: no debe corromper meta ni mostrar errores.
- [ ] Usuario sin permiso de edición no debe poder guardar (probar con rol inferior si aplica).

### Services — metabox nativo completo

- [ ] Ir a **Services → Añadir nuevo** (o editar uno existente).
- [ ] Verificar metabox **Services pricing and generic details** con Pricing ($) y Slogan.
- [ ] Confirmar que **no** aparece metabox duplicado de CMB2 para servicios.
- [ ] Guardar valores; recargar y verificar persistencia.
- [ ] En `wp_postmeta`, confirmar keys `service_pricing` y `service_slogan`.
- [ ] Si había pricing con formato previo de CMB2 (ej. con `$`), verificar que el valor se muestra y guarda sin pérdida.

### REST API (opcional)

- [ ] Con usuario autenticado y Application Passwords o editor logueado, consultar `/wp-json/wp/v2/testimonials/{id}` y verificar meta `testimonials_job`, `testimonials_opinion`.
- [ ] Consultar `/wp-json/wp/v2/services/{id}` y verificar meta `service_pricing`, `service_slogan`.

### Regresión general

- [ ] Plugin activa/desactiva sin errores PHP.
- [ ] CPTs y taxonomías siguen funcionando.
- [ ] Página **Theme options** (CMB2) sigue operativa sin cambios.
- [ ] Frontend/tema que lea meta existente sigue mostrando datos correctamente.

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

*Última actualización: Fases 0 y 1 completadas. Corrección i18n WP 6.7+ (textdomain en `init`, traducciones lazy en metaboxes).*
