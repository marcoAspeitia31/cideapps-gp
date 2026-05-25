# Cideapps GP

Plugin genérico para iniciar el desarrollo de proyectos en WordPress.  
Incluye **Custom Post Types (CPT)**, **taxonomías personalizadas**, **metaboxes nativos** y **opciones de tema** (Settings API) para centralizar información de negocio.

Este plugin está basado en la plantilla generada con [WordPress Plugin Boilerplate (wppb.me)](http://wppb.me/).

---

## Características principales

- **Metaboxes nativos de WordPress**  
  Campos personalizados en CPTs y taxonomías (`Cideapps_Gp_Metabox_Base`, `Meta_Registry`).

- **Custom Post Types incluidos**  
  - **Services** (`services`)  
  - **Testimonials** (`testimonials`)  

- **Taxonomías personalizadas**  
  - `service_category` (asociada al CPT `services`).  

- **Opciones de tema**  
  - `cagp_theme_options` — branding, contacto, mapa, redes sociales.  
  - `cagp_settings_options` — API key de Google Maps.

- **REST API**  
  Meta de posts y términos registrada con `register_post_meta` / `register_term_meta` y expuesta en JSON (requiere soporte `custom-fields` en los CPT).

- **Internacionalización (i18n)**  
  Archivos `.po` y `.mo` en `/languages` para traducciones.

- **Estructura clara**  
  Separación de lógica en carpetas `admin`, `public`, `includes/meta` siguiendo buenas prácticas.

---

## Estructura del plugin

```
cideapps-gp/
├── admin/
│   ├── custom-fields/      # Metaboxes y páginas de opciones
│   ├── custom-post-types/  # CPTs (services, testimonials)
│   ├── custom-taxonomies/  # Taxonomías personalizadas
│   ├── css/ js/ img/
│
├── public/
│   ├── css/ js/ partials/
│
├── includes/
│   ├── meta/               # Infraestructura nativa (metaboxes, options, registry)
│   ├── class-cideapps-gp.php
│   └── ...
│
├── docs/                   # Documentación de migración CMB2 → nativo
├── languages/
├── cideapps-gp.php
├── uninstall.php
├── LICENSE.txt
└── README.md
```

---

## Instalación

1. Clona o copia el plugin en `wp-content/plugins/`:
   ```bash
   git clone https://github.com/marcoAspeitia31/cideapps-gp.git
   ```
2. Activa **Cideapps GP** en **Plugins**.
3. Configura:
   - CPTs Services y Testimonials.  
   - Categorías en `service_category`.  
   - **Theme options** y **Settings** en el menú del plugin.

---

## Requisitos

- WordPress 6.0 o superior (probado con REST y metaboxes nativos en WP 7.x)  
- PHP 7.4+  
- Servicio externo opcional: **Google Maps** (geocoding y mapa en Theme options)

---

## Desarrollo

Extensión recomendada:

- Nuevos CPTs en `/admin/custom-post-types/`  
- Nuevas taxonomías en `/admin/custom-taxonomies/`  
- Campos en metaboxes: extender `Cideapps_Gp_Metabox_Base` y registrar claves en `Cideapps_Gp_Meta_Registry`  
- Opciones: extender `Cideapps_Gp_Options_Page_Base`

Documentación de migración desde CMB2: [`docs/migracion-cmb2-metaboxes-nativos.md`](docs/migracion-cmb2-metaboxes-nativos.md).

---

## Traducciones

- Español (`es`, `es_MX`, `es_ES`)  
- Plantilla `cideapps-gp.pot`

---

## Licencia

**GPL-2.0+** — ver [LICENSE.txt](./LICENSE.txt).

---

## Autor

**Marco Aspeitia**  
- GitHub: [@marcoAspeitia31](https://github.com/marcoAspeitia31)
