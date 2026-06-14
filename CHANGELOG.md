# Changelog - Convoca Theme

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
