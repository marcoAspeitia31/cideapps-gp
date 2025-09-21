# Cideapps GP

Plugin genérico para iniciar el desarrollo de proyectos en WordPress.  
Incluye integración con **CMB2**, creación de **Custom Post Types (CPT)**, **taxonomías personalizadas** y **opciones de tema** para centralizar información de negocio.  

Este plugin está basado en la plantilla generada con [WordPress Plugin Boilerplate (wppb.me)](http://wppb.me/).

---

## 🚀 Características principales

- **CMB2 integrado**  
  Campos personalizados y metaboxes listos para extender en CPTs y taxonomías.
  
- **Custom Post Types incluidos**  
  - **Services** (`services`)  
  - **Testimonials** (`testimonials`)  

- **Taxonomías personalizadas**  
  - `service_category` (asociada al CPT `services`).  

- **Opciones de tema**  
  Página en el admin para agregar información del negocio (por ejemplo, datos de contacto o branding).

- **Internacionalización (i18n)**  
  Archivos `.po` y `.mo` en `/languages` para traducciones.

- **Estructura clara**  
  Separación de lógica en carpetas `admin`, `public` e `includes` siguiendo buenas prácticas.

---

## 📂 Estructura del plugin

```
cideapps-gp/
├── admin/                 # Código específico para el área de administración
│   ├── custom-fields/      # Metaboxes con CMB2
│   ├── custom-post-types/  # CPTs (services, testimonials)
│   ├── custom-taxonomies/  # Taxonomías personalizadas
│   ├── css/ js/ img/       # Assets para el admin
│
├── public/                # Código para el frontend
│   ├── css/ js/ partials/
│
├── includes/              # Clases principales, loader, activador/desactivador
│   └── cmb2-functions.php # Funciones de integración con CMB2
│
├── languages/             # Archivos de traducción
├── cideapps-gp.php        # Bootstrap del plugin
├── uninstall.php          # Limpieza al desinstalar
├── LICENSE.txt
└── README.md
```

---

## ⚙️ Instalación

1. Descarga este repositorio o clónalo en tu carpeta `wp-content/plugins`:
   ```bash
   git clone https://github.com/marcoAspeitia31/cideapps-gp.git
   ```
2. Activa el plugin desde el panel de administración de WordPress:  
   **Plugins → Activar "Cideapps GP"**  
3. Una vez activo:
   - Revisa los CPTs "Services" y "Testimonials".  
   - Configura los metaboxes con CMB2.  
   - Agrega categorías en `service_category`.  
   - Ajusta las opciones de negocio desde el menú correspondiente.  

---

## 📦 Requisitos

- WordPress 6.0 o superior  
- PHP 7.4+  
- [CMB2](https://cmb2.io/) (ya integrado en el plugin)  

---

## 👨‍💻 Desarrollo

Este plugin está diseñado como base para proyectos personalizados.  
Puedes extenderlo agregando:

- Nuevos **CPTs** en `/admin/custom-post-types/`  
- Nuevas **taxonomías** en `/admin/custom-taxonomies/`  
- Campos adicionales con **CMB2** en `/admin/custom-fields/`  

---

## 🌍 Traducciones

El plugin incluye soporte de internacionalización:  

- Español (`es`, `es_MX`)  
- Plantilla base `cideapps-gp.pot` para generar otros idiomas  

---

## 📜 Licencia

Distribuido bajo la licencia **GPL-2.0+**.  
Consulta el archivo [LICENSE.txt](./LICENSE.txt) para más información.  

---

## ✨ Autor

**Marco Aspeitia**  
- GitHub: [@marcoAspeitia31](https://github.com/marcoAspeitia31)  
- Proyecto: [Cideapps](https://github.com/marcoAspeitia31/)  
