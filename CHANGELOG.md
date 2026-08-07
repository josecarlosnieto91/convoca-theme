# Changelog - Convoca Theme

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
- **Fix:** Eliminadas todas las referencias a Biodevas, avisos de privacidad GDPR, nombre de sitio dinámico
- **Limpieza:** Eliminados archivos temporales gitignore
- **Infra:** uninstall.php con keep-data, traducciones .pot, hardening de seguridad

## 2.6.1
- **Refactor:** Renombrados todos los prefijos `biodevas_*` → `convoca_*` (shortcodes, funciones, hooks, textdomains, patrones)
- **Refactor:** Renombrados namespaces CSS/JS `bdv-` → `conv-`
- **Refactor:** Renombrados prefijos en opciones y metadatos
- **Fix:** Hook `biodevas_dark_mode_inline_init` → `convoca_dark_mode_inline_init`
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
