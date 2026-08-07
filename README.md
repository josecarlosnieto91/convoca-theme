# Convoca Theme

Tema (FSE) para el ecosistema Convoca.

## Requirements

- WordPress 6.4+
- PHP 8.0+

## Main Features

- Diseño responsive con Full Site Editing
- Theme 100% presentacional (sin lógica de negocio: los shortcodes/CPTs/REST viven en los plugins)
- Modo claro/oscuro automático y manual
- Tipografía: Outfit (títulos) + Lato (cuerpo)
- Full Site Editing con theme.json
- 15+ block patterns
- WCAG 2.2 AA

## Shortcode del tema

El theme registra únicamente un shortcode de interfaz:

### `[convoca_dark_mode_toggle]`

Botón o enlace que alterna entre modo claro y oscuro en el frontend. El cambio
se persiste en localStorage y respeta la preferencia del sistema
(prefers-color-scheme) como valor inicial.

> **Nota:** los shortcodes de negocio (`[convoca_mi_area]`, `[convoca_renovar]`,
> `[convoca_inscripcion_page]`, `[convoca_calendario]`, `[convoca_pago]`,
> `[convoca_assistant]`, etc.) pertenecen a los plugins y funcionan con cualquier
> tema activo. Consulta el manual de cada plugin.


## 📖 Documentación

La documentación completa (manual de usuario, API REST, hooks, instalación) vive en la wiki:

👉 **[Convoca Theme](https://docs.getconvoca.app/plugins/convoca-theme/)**

## Dependencies

WordPress 6.4+, PHP 8.0+

## Version

2.7.0

## Changelog

### 2.7.0
- Refactor: eliminada lógica de negocio (shortcodes de actividad, hooks, JSON-LD) — migrada a plugins
- Fix: selector de idioma con aria-label y dropdown completo
- Fix: menú móvil full-width sin duplicados
- Fix: contraste de metadatos en modo oscuro (`.convoca-card-meta`)

### 2.5.1
- docs: add MANUAL_USUARIO.md with FSE + shortcodes guide

### 2.5.0
- Added dark mode toggle shortcode
- Added inscripcion_actual shortcode
- Updated color palette with Sunset & Earth
- Performance: Google Fonts with preload + swap

### 2.4.0
- Full Site Editing with theme.json
- Block patterns (15+)
- WCAG 2.1 AA accessibility improvements

### 2.3.0
- Responsive mobile menu
- Sticky header with scroll effects
- Card and pricing components
