# Biodevas Theme

Tema hijo (FSE) para la Asociación Biodevas.

## Requirements

- WordPress 6.4+
- PHP 8.0+

## Main Features

- Diseño responsive con Full Site Editing
- Shortcodes de perfil de socio y frontend
- Plantillas de email
- Integración con plugins Biodevas
- Full Site Editing con theme.json
- Modo claro/oscuro automático y manual
- Tipografía: Outfit (títulos) + Lato (cuerpo)

## Shortcodes del Tema

### `[biodevas_mi_perfil]`

Muestra el perfil del socio o voluntario logueado: nombre, email, estado de
membresía, inscripciones activas y horas de voluntariado. Si el usuario no ha
iniciado sesión, muestra un formulario de login (email + código de acceso).

### `[biodevas_inscripcion_actual]`

Detecta automáticamente la página de actividad actual (por el slug o ID del
post) e incrusta el formulario de inscripción. Ideal para usar en la plantilla
de una actividad (single-actividad.php). Acepta el atributo opcional `id` para
especificar una actividad concreta.

### `[biodevas_actividad_meta field="ubicacion"]`

Muestra un metadato específico de la actividad actual. Campos disponibles:
`fecha_inicio`, `fecha_fin`, `ubicacion`, `plazas_totales`, `plazas_disponibles`,
`precio_socio`, `precio_general`. Requiere estar en una página de actividad.

### `[biodevas_dark_mode_toggle]`

Botón o enlace que alterna entre modo claro y oscuro en el frontend. El cambio
se persiste en localStorage y respeta la preferencia del sistema
(prefers-color-scheme) como valor inicial.

### `[biodevas_verificar_socio]`

Página pública de verificación de membresía. El usuario introduce el código
del socio (recibido por email) y el sistema muestra el estado actual de su
membresía (activo, pendiente, expirado, etc.) sin necesidad de iniciar sesión.

### `[biodevas_verificar_certificado]`

Página pública de verificación de certificados de voluntariado. Introduce el
ID único del certificado (formato `VOL-AAAA-XXXXX`) y el sistema muestra:
nombre del voluntario, horas totales, fecha de emisión y estado del
certificado (válido o revocado).

## Dependencies

WordPress 6.4+, PHP 8.0+

## Version

2.5.0

## Changelog

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
