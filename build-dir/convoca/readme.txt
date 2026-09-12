=== Convoca Theme ===
Contributors: josecarlosnietoramos
Tags: FSE, full-site-editing, dark-mode, blocks, patterns, asociaciones
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 2.7.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convoca Theme is distributed under the terms of the GNU GPL v2 or later.

Copyright (C) 2026 Jose Carlos Nieto Ramos.

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version. This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details. You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

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
