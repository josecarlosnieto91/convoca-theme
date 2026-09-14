# Changelog - Convoca Theme

## 2.9.10 (2026-09-14)

Los menús desplegables eran ilegibles en modo oscuro.

- **Desplegables del menú en modo oscuro.** El panel del desplegable toma su fondo de la
  paleta base (blanco), pero los ítems heredaban el color claro del modo oscuro: texto
  claro sobre blanco, 1,1:1 de contraste, imposible de leer. Ahora el panel usa la
  superficie oscura del tema, los ítems el texto claro y el ámbar marca el elemento bajo
  el ratón y el de la página actual. Contraste medido: 15,6:1 (antes 1,1:1). En modo claro
  no cambia nada (verificado: 17,8:1, fondo blanco).

## 2.8.1 (2026-09-13)

Tres correcciones pedidas al revisar la web en el navegador, dos de ellas del modo oscuro.

- **Las entradas se veían más estrechas en modo oscuro que en claro.** Las tarjetas de las
  rejillas recibían un relleno de 2.5rem por lado que en modo claro no tienen: con dos
  columnas, 80 px de relleno dejan el texto sin sitio. Ahora esas tarjetas van sin relleno en
  los dos modos, como estaban en claro.
- **Textos ilegibles en la sección «Participa» en modo oscuro.** Esa sección no tenía ninguna
  regla de modo oscuro: se quedaba sin su fondo naranja pero con la tinta carbón de claro, así
  que titular, enlaces y textos de apoyo quedaban invisibles. Ahora usa la superficie oscura
  del tema con texto claro, y los botones mantienen su naranja. Contrastes medidos: 17,57:1 en
  los textos, 9,28:1 en los de apoyo y 7,84:1 en los botones (el mínimo es 4,5:1).
- **El grupo del CTA se libra de la regla que hace transparentes los grupos en modo oscuro**,
  para que la sección conserve su superficie propia.

## 2.8.0 (2026-09-12)

**The theme is now generic and sellable.** No data from any particular installation is left
inside it: texts, links, phone numbers and figures arrive through filters.

- **Removed:** content and paths from the site the theme was built for, in 10 files (footer,
  sidebar, five patterns, three templates). Everything now comes from installation filters.
- **Changed:** the front page, footer and sidebar shipped with the theme are generic samples.
  The templates a site edits itself live in its own database, as WordPress intends.
- **Removed:** three templates bound to a post type the theme does not register
  (`archive-actividad`, `single-actividad`, `page-actividad`): unverifiable and dead weight.
- **Fixed:** event JSON-LD. Dates are validated before publishing and read in the site's
  timezone; an unreadable date no longer becomes 1970.
- **Fixed:** the event meta box no longer runs on revisions, autosaves or other post types.
- **Fixed:** the block filter escapes values according to where they land (URL, text or
  allowed HTML), and a placeholder with no value is removed with its link instead of being
  printed.
- **Fixed:** community figures are validated at the source, so a filtered structure cannot
  break the pattern.
- **Fixed:** the dark mode toggle exposes `aria-pressed` from the first paint, not only after
  being used.
- **Fixed:** `{site_title}` in `parts/hero.html` was never declared and would have printed
  literally. Now uses `{site_name}`.
- **Added:** `contact` and `featured` as link keys so the theme's own parts reference nothing
  outside the theme.
- **Accessibility:** the site logo, icon and custom logo are no longer rounded.
- **Performance:** no `transition: all` left; the browser only watches the properties that
  change.
- **i18n:** source strings in English, text domain `convoca`, `.pot` included. The price card
  badge moved out of the CSS into a translatable attribute.
- **Docs:** README rewritten for buyers: what it offers, how to configure it, every filter and
  every placeholder, with examples. Version header fixed (`convoca-theme` → `convoca`).
- **Repo:** the build folder is no longer versioned.

## 2.7.0 (2026-08-07)
- **Refactor:** Eliminada toda la lógica de negocio (shortcodes de actividad, hooks de datos, JSON-LD) — migrada a los plugins. El theme queda 100% presentacional
- **Fix:** Selector de idioma con aria-label y dropdown completo (sin cortes)
- **Fix:** Menú móvil full-width sin duplicados (nav con links explícitos)
- **Fix:** Contraste de metadatos en modo oscuro (`.convoca-card-meta`)
- **A11y:** aria-label en el toggle de idioma

## 2.6.5
- **Fix:** Footer año dinámico — reemplazado `{{year}}` estático por `document.write(new Date().getFullYear())` vía JavaScript
- **Docs:** Añadidos CODE_OF_CONDUCT.md, LICENSE (GPL v2+), SECURITY.md, SUPPORT.md, CONTRIBUTING.md
- **Docs:** Actualizado MANUAL_USUARIO.md con shortcodes y guías completas
- **Docs:** Actualizado readme.txt con stable tag 2.6.5 y changelog
- **Rendimiento:** Añadida configuración phpstan, mejoras de calidad de código

## 2.6.4
- **Fix:** Ajuste de padding de contenido en plantillas post/página (excluye CTA, featured image, spacers)
- **Fix:** CTA full-width con negative margin, padding de contenido específico
- **Rendimiento:** Cache bust por bump de versión
- **Mejoras:** Ajustes de theme para entorno de desarrollo

## 2.6.3
- **Fix:** Patrones de actividad usan meta keys `_conv_` (eran `_bde_`) — las cards de actividad se renderizan de nuevo
- **Fix:** Shortcode `actividad_meta` — maneja valor numérico 0, meta keys `conv_` con fallback `bde_`
- **Fix:** Restauradas cadenas de functions.php, corregido textdomain y rename de patrones
- **SEO:** Schema JSON-LD para actividades con plazas totales y precios

## 2.6.2
- **Seguridad:** Hardening de seguridad, auditorías de producción aplicadas
- **Fix:** Eliminadas todas las referencias del sitio anterior, avisos de privacidad GDPR, nombre de sitio dinámico
- **Limpieza:** Eliminados archivos temporales gitignore
- **Infra:** uninstall.php con keep-data, traducciones .pot, hardening de seguridad

## 2.6.1
- **Refactor:** Renombrados todos los prefijos antiguos → `convoca_*` (shortcodes, funciones, hooks, textdomains, patrones)
- **Refactor:** Renombrados namespaces CSS/JS `bdv-` → `conv-`
- **Refactor:** Renombrados prefijos en opciones y metadatos
- **Fix:** Hook del modo oscuro renombrado al prefijo del tema
- **Compatibilidad:** Actualizado composer.lock tras rename de paquetes

## 2.6.0
- **Rediseño visual:** Paleta naranja (#ff8700) como color primario dominante
- **Footer:** Fondo violeta (#320028) con borde decorativo gradiente naranja
- **WCAG 2.2 AA:** Enlaces en texto usan #cc6e00 (ratio 4.6:1 sobre blanco)
- **Headings:** h1, h2, h4 → naranja; h3 → violeta (#320028)
- **Botones:** border-radius 10px, hover #e67300
- **Sombras:** Tinte naranja en vez de violeta en cards
- **Gradientes:** sunset-hero centrado en naranja; nuevo hero-light
- **Tablas:** Cabecera con fondo naranja
- **Dark mode:** Naranja mantiene #ff8700 (contraste 7.5:1 sobre fondo oscuro)
- **Site title:** Color neutro #2d2d3a

## 2.5.0
- Seguridad: Google Fonts version extraída dinámicamente (wp_get_theme)
- Actualización: Documentación sincronizada (versión 2.5.0)

## 2.4.0
- **Branding:** Sincronización de paleta de colores (Lila/Naranja) para los componentes de `convoca-members` (Tarjetas de socio).
- **Consistencia:** Actualizada la lógica de previsualización de bloques para alinearse con las mejoras de estabilidad de los plugins del ecosistema.

## 2.3.0
- **Rendimiento:** Eliminado `@import` de Google Fonts en `style.css` — las fuentes ahora se cargan exclusivamente vía `wp_enqueue_style` con `preconnect` + `preload` + fallback `<noscript>`.
- **Fix:** Migrado `date()` → `wp_date()` en el filtro de archivo de actividades para consistencia de zona horaria.
- **Fix:** Versión del enqueue actualizada de 2.2.0 a 2.3.0.

## 2.2.0
- **Modo Oscuro Nativo**: Toggle (Luna/Sol) con persistencia en `localStorage` y detección de sistema.
- **Optimización Mobile**: Gestos de cierre en menú (swipe), áreas de toque aumentadas y header dinámico.
- **Core Web Vitals**: Precarga de fuentes, CSS crítico inline y carga diferida de JS.
- **SEO Avanzado**: Datos estructurados JSON-LD (Event Schema) para actividades.
- **Admin Help**: Nueva página en **Apariencia > Ayuda Convoca** con guías y estadísticas.
