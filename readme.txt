=== Convoca Theme ===
Contributors: josecarlosnietoramos
Tags: FSE, full-site-editing, dark-mode, blocks, patterns, asociaciones
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 2.7.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tema FSE con modo oscuro y block patterns para el ecosistema Convoca.

== Description ==

Tema de bloques moderno con Full Site Editing (FSE). Incluye modo oscuro nativo, 15+ block patterns y estilos de bloque personalizados. El tema es 100% presentacional: la funcionalidad (shortcodes, CPTs, REST) vive en los plugins de Convoca.

* Full Site Editing — edita cabecera, pie y plantillas desde el editor de bloques
* Modo oscuro nativo con toggle manual y persistencia
* WCAG 2.2 AA — contraste, foco visible, navegación por teclado
* Tipografía Outfit + Lato con Google Fonts
* 15+ Block Patterns: hero, cards, pricing, CTA, estadísticas, contacto
* 8 estilos de bloque personalizados
* Shortcode de interfaz: [convoca_dark_mode_toggle]
* Los shortcodes de negocio ([convoca_mi_area], [convoca_inscripcion_page], [convoca_calendario], [convoca_pago]…) pertenecen a los plugins y funcionan con cualquier tema

== Installation ==

1. Sube la carpeta `convoca-theme` a `/wp-content/themes/`
2. Activa el tema desde Apariencia > Temas

== Changelog ==

= 2.7.0 =
* Refactor: eliminada lógica de negocio del theme (migrada a plugins)
* Fix: selector de idioma, menú móvil, contraste dark

= 2.6.5 =
* Nuevo: Tests de estructura del tema — 10 tests, 19 aserciones
* Mejora: Compatibilidad con WordPress 7.0 y PHP 8.5
