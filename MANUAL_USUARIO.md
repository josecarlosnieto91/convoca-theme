# MANUAL_USUARIO.md — Convoca Theme v2.6.5

> Tema hijo FSE (Full Site Editing) para la Asociación Convoca.

## 1. Introducción

Convoca Theme es un tema de bloques moderno con soporte para edición completa del sitio (FSE). Incluye paleta de colores accesible (naranja #ff8700 como primario), tipografía optimizada (Outfit + Lato), modo oscuro nativo, y shortcodes para integración con los plugins Convoca.

**Integración en cualquier sitio:** El theme puede coexistir con otros temas. Sus shortcodes son independientes del tema activo. Si se usa como tema principal, reemplaza la apariencia actual.

## 2. Características

- 🎨 **Full Site Editing** — edita cabecera, pie, plantillas desde el editor de bloques
- 🌓 **Modo oscuro** — automático (prefiere sistema), con toggle manual y persistencia en localStorage. Incluye init inline en `<head>` para evitar FOUC (flash de contenido sin estilo)
- ♿ **WCAG 2.2 AA** — contraste (enlaces #cc6e00 ratio 4.6:1), foco visible, navegación por teclado
- 🔤 **Tipografía** — Outfit (títulos) + Lato (cuerpo), cargadas desde Google Fonts con preload + preconnect + fallback noscript
- 📱 **Responsive** — menú móvil full-screen con overlay, header sticky con backdrop-filter, diseño mobile-first
- 🧩 **15+ Block Patterns** — secciones predefinidas para hero, cards, pricing, CTA, estadísticas
- 🎨 **Estilos de bloque personalizados** — Tarjeta, Cristal Esmerilado, Overlay Topográfico, Tabla Convoca, Botón Secundario, Imagen Elevada, Caja Coordinador
- 🔗 **Meta markers en plantillas FSE** — `%%FECHA_INICIO%%`, `%%LUGAR%%`, `%%PRECIO%%`, `%%PLAZAS%%` para mostrar datos de actividad en plantillas de archivo
- ⚡ **Rendimiento** — CSS crítico inline, emoji scripts eliminados, lazy loading nativo, defer en JS
- 📍 **Footer dinámico** — Año actualizado automáticamente vía JavaScript (`document.write`), enlaces a privacidad, cookies y aviso legal
- 🔍 **SEO** — Datos estructurados JSON-LD (Event Schema) para actividades

## 3. Shortcodes del tema

### `[convoca_mi_perfil]`

Muestra el perfil del socio logueado: nombre, email, estado de membresía, inscripciones activas y horas de voluntariado. Si no ha iniciado sesión, muestra formulario de login.

### `[convoca_inscripcion_actual]`

Detecta automáticamente la actividad actual y muestra el formulario de inscripción. Ideal para usar en la plantilla de actividad. Atributo opcional `id` para especificar una actividad concreta.

### `[convoca_actividad_meta field="ubicacion"]`

Muestra un metadato de la actividad actual. Campos disponibles: `fecha_inicio`, `fecha_fin`, `ubicacion`, `lugar`, `plazas_totales`, `plazas_disponibles`, `precio_socio`, `precio_general`, `precio`, `requires_payment`. Los campos `fecha_*` se formatean como fecha legible; `precio` muestra "Gratis" si es 0.

### `[convoca_dark_mode_toggle]`

Botón con iconos SVG (sol/luna) para alternar modo claro/oscuro. Persiste en localStorage. Incluido por defecto en el header del tema.

### `[convoca_calendario]`

Grid de actividades próximas (hasta 20) con tarjetas visuales que incluyen fecha, lugar, precio, plazas disponibles y enlace a detalle. Los datos se obtienen de `Convoca\Enroll\CPT_Actividad::get_upcoming()` si el plugin Convoca Enroll está activo.

### `[convoca_verificar_socio]`

Formulario de verificación pública de membresía.

### `[convoca_verificar_certificado]`

Formulario de verificación pública de certificados de voluntariado (código `VOL-AAAA-XXXXX`).

## 4. Block Patterns incluidos

| Pattern | Archivo | Uso |
|---------|---------|-----|
| Hero | `patterns/hero.php` | Cabecera principal con texto |
| Hero con gradiente | `patterns/hero-gradient.php` | Cabecera con fondo degradado |
| Hero con imagen | `patterns/hero-imagen.php` | Cabecera con imagen de fondo |
| Grid de actividades | `patterns/cards-grid.php` | Cuadrícula de actividades o noticias |
| Cards (3 columnas) | `patterns/cards-3.php` | Tres tarjetas en fila |
| Pricing table | `patterns/pricing-grid.php` | Página de membresías |
| Stats counter | `patterns/stats-bar.php` | Barra de estadísticas animada |
| Stats grid | `patterns/stats.php` | Estadísticas en cuadrícula |
| CTA | `patterns/cta.php` | Llamada a la acción |
| CTA centrado | `patterns/cta-centered.php` | CTA centrado con fondo |
| Contacto | `patterns/contact-banner.php` | Banner de contacto |
| Inscripción actividad | `patterns/inscripcion-actividad.php` | Formulario de inscripción integrado |
| Sección violeta | `patterns/section-violeta.php` | Sección con fondo violeta |
| Próximas actividades | `patterns/proximas-actividades.php` | Lista de próximas actividades |
| Tabla transparencia | `patterns/transparencia-table.php` | Tabla para página de transparencia |

## 5. Partes de plantilla (Template Parts)

| Parte | Archivo | Descripción |
|-------|---------|-------------|
| Header | `parts/header.html` | Cabecera con logo, navegación, dark mode toggle y botón "Asóciate" |
| Footer | `parts/footer.html` | Pie con 4 columnas (marca, enlaces, CTA, contacto), separador y copyright dinámico |
| Hero | `parts/hero.html` | Sección hero reutilizable |
| CTA | `parts/cta.html` | Llamada a la acción reutilizable |
| Sidebar | `parts/sidebar.html` | Barra lateral |
| Activity meta | `parts/activity-meta.html` | Metadatos de actividad (fecha, lugar, precio) |
| Card actividad | `parts/card-actividad.html` | Tarjeta individual de actividad |
| Card proyecto | `parts/card-proyecto.html` | Tarjeta individual de proyecto |

## 6. Plantillas (Templates)

| Plantilla | Archivo | Uso |
|-----------|---------|-----|
| Front Page | `templates/front-page.html` | Página de inicio |
| Home (Blog) | `templates/home.html` | Índice del blog |
| Single | `templates/single.html` | Entrada individual |
| Page | `templates/page.html` | Página genérica |
| Page Actividad | `templates/page-actividad.html` | Página de actividad con inscripción |
| Page Proyecto | `templates/page-proyecto.html` | Página de proyecto |
| Page Landing | `templates/page-landing.html` | Página de aterrizaje |
| Page Institucional | `templates/page-institucional.html` | Página institucional |
| Archive | `templates/archive.html` | Archivo genérico |
| Archive Actividad | `templates/archive-actividad.html` | Archivo de actividades (solo futuras) |
| Single Actividad | `templates/single-actividad.html` | Actividad individual |
| 404 | `templates/404.html` | Página no encontrada |
| Search | `templates/search.html` | Resultados de búsqueda |

## 7. Personalización

Al ser un tema FSE, toda la personalización se hace desde **Apariencia → Editor**:

- **Plantillas**: Edita front-page, single, archive, 404, etc.
- **Partes de plantilla**: Header, footer, sidebar
- **Estilos**: Modifica colores, tipografía, espaciado desde el panel de estilos

### Estilos de bloque registrados

El tema registra estos estilos de bloque personalizados:

| Bloque | Estilo | Descripción |
|--------|-------|-------------|
| Paragraph | `lead` | Texto destacado (lead) |
| Group | `card` | Tarjeta con bordes y sombra |
| Group | `coordinator` | Caja de coordinador |
| Group | `glass` | Efecto cristal esmerilado (frosted glass) |
| Cover | `topographic` | Overlay topográfico |
| Table | `convoca` | Tabla con estilo Convoca |
| Button | `secondary` | Botón secundario (outline sobre fondo oscuro) |
| Image | `elevated` | Imagen elevada con sombra y bordes redondeados |

### Reset de plantillas

Si las plantillas personalizadas en la base de datos causan problemas, puedes reiniciarlas desde **Apariencia → Ayuda Convoca** (botón "Reiniciar Plantillas a valores de fábrica"). También puedes usar:

```
/wp-admin/?convoca_reset_templates=1&_wpnonce=XXXXX
```

Esto restaura las plantillas a los archivos del tema. Solo puede hacerse una vez por hora.

## 8. Compatibilidad con otros temas

Si el sitio usa otro tema (Astra, Elementor, etc.), los shortcodes de Convoca Theme funcionan igualmente:

- Añade `[convoca_mi_perfil]` en cualquier página o widget
- Añade `[convoca_verificar_socio]` en una página pública
- El modo oscuro y los block patterns solo funcionan con Convoca Theme activo
- El shortcode `[convoca_calendario]` funciona independientemente del tema activo

## 9. Problemas comunes

| Problema | Solución |
|----------|----------|
| **El modo oscuro no persiste** | Verifica que localStorage está habilitado en el navegador |
| **Las plantillas no se visualizan** | Usa el botón "Reiniciar Plantillas" en Apariencia → Ayuda Convoca |
| **Las actividades no aparecen** | Asegúrate de que Convoca Enroll está activo y hay actividades publicadas |
| **Footer muestra año incorrecto** | El año se genera dinámicamente con JavaScript; si JS está desactivado, se muestra el año actual del servidor |
| **Google Fonts no cargan** | Verifica que `wp_remote_get` funciona; las fuentes tienen fallback a system fonts |
