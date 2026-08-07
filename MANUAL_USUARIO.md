# MANUAL_USUARIO.md — Convoca Theme v2.7.0

> Tema FSE (Full Site Editing) para el ecosistema Convoca.

## 1. Introducción

Convoca Theme es un tema de bloques moderno con soporte para edición completa del sitio (FSE). Incluye paleta de colores accesible (naranja #ff8700 como primario), tipografía optimizada (Outfit + Lato), modo oscuro nativo y patrones de bloque para integrar el contenido de los plugins Convoca.

**Arquitectura (v2.7.0):** el theme es **100% presentacional**. No registra shortcodes de negocio, CPTs, endpoints REST ni hooks de datos — toda la funcionalidad vive en los plugins (enroll, members, shifts, gateway, assistant). Cambiar de tema no pierde ninguna funcionalidad.

**Integración en cualquier sitio:** los shortcodes que uses en páginas (`[convoca_mi_area]`, `[convoca_inscripcion_page]`, etc.) son de los **plugins**, no del theme, y funcionan con cualquier tema activo. Convoca Theme solo aporta la capa visual.

## 2. Características

- 🎨 **Full Site Editing** — edita cabecera, pie, plantillas desde el editor de bloques
- 🌓 **Modo oscuro** — automático (prefiere sistema), con toggle manual y persistencia en localStorage. Incluye init inline en `<head>` para evitar FOUC (flash de contenido sin estilo)
- ♿ **WCAG 2.2 AA** — contraste (enlaces #cc6e00 ratio 4.6:1), foco visible, navegación por teclado
- 🔤 **Tipografía** — Outfit (títulos) + Lato (cuerpo), cargadas desde Google Fonts con preload + preconnect + fallback noscript
- 📱 **Responsive** — menú móvil full-screen con overlay, header sticky con backdrop-filter, diseño mobile-first
- 🧩 **15+ Block Patterns** — secciones predefinidas para hero, cards, pricing, CTA, estadísticas
- 🎨 **Estilos de bloque personalizados** — Tarjeta, Cristal Esmerilado, Overlay Topográfico, Tabla Convoca, Botón Secundario, Imagen Elevada, Caja Coordinador
- ⚡ **Rendimiento** — CSS crítico inline, emoji scripts eliminados, lazy loading nativo, defer en JS
- 📍 **Footer dinámico** — Año actualizado automáticamente vía JavaScript, enlaces a privacidad, cookies y aviso legal
- 🏗️ **Sin lógica de negocio** — los datos de actividad/membresía se consumen de los plugins vía sus shortcodes y bloques

## 3. Shortcode del tema

El theme registra un **único** shortcode, puramente de interfaz:

### `[convoca_dark_mode_toggle]`

Botón con iconos SVG (sol/luna) para alternar modo claro/oscuro. Persiste en localStorage. Incluido por defecto en el header del tema.

> **Nota:** los shortcodes de negocio (`[convoca_mi_area]`, `[convoca_mi_perfil]`, `[convoca_renovar]`, `[convoca_inscripcion_page]`, `[convoca_calendario]`, `[convoca_pago]`, `[convoca_assistant]`, etc.) pertenecen a los **plugins** correspondientes. Consulta el manual de cada plugin.

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

Los shortcodes de los **plugins** (`[convoca_mi_area]`, `[convoca_renovar]`, `[convoca_inscripcion_page]`, `[convoca_calendario]`, `[convoca_pago]`, etc.) funcionan con cualquier tema activo, incluido Convoca Theme.

- El modo oscuro y los block patterns solo funcionan con Convoca Theme activo
- Los patrones de actividades (`proximas-actividades.php`) usan los datos de los plugins vía bloques dinámicos

## 9. Problemas comunes

| Problema | Solución |
|----------|----------|
| **El modo oscuro no persiste** | Verifica que localStorage está habilitado en el navegador |
| **Las plantillas no se visualizan** | Usa el botón "Reiniciar Plantillas" en Apariencia → Ayuda Convoca |
| **Las actividades no aparecen** | Asegúrate de que Convoca Enroll está activo y hay actividades publicadas |
| **Footer muestra año incorrecto** | El año se genera dinámicamente con JavaScript; si JS está desactivado, se muestra el año actual del servidor |
| **Google Fonts no cargan** | Verifica que `wp_remote_get` funciona; las fuentes tienen fallback a system fonts |
| **Un shortcode no funciona** | Confirma que el plugin correspondiente está activo: `[convoca_mi_area]` → Members, `[convoca_calendario]` → Shifts, `[convoca_pago]` → Gateway |
