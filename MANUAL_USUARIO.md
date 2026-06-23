# MANUAL_USUARIO.md — Convoca Theme v2.5.0

> Tema hijo FSE (Full Site Editing) para la Asociación Convoca.

## 1. Introducción

Convoca Theme es un tema de bloques moderno con soporte para edición completa del sitio (FSE). Incluye paleta de colores accesible, tipografía optimizada, modo oscuro, y shortcodes para integración con los plugins Convoca.

**Integración con biodevas.org / lugg.biodevas.org:** El theme puede coexistir con otros temas. Sus shortcodes son independientes del tema activo. Si se usa como tema principal, reemplaza la apariencia actual.

## 2. Características

- 🎨 **Full Site Editing** — edita cabecera, pie, plantillas desde el editor de bloques
- 🌓 **Modo oscuro** — automático (prefiere sistema) o manual
- ♿ **WCAG 2.1 AA** — contraste, foco visible, navegación por teclado
- 🔤 **Tipografía** — Outfit (títulos) + Lato (cuerpo), cargadas desde Google Fonts con preload
- 📱 **Responsive** — menú móvil full-screen, header sticky
- 🧩 **15+ Block Patterns** — secciones predefinidas para hero, cards, pricing, CTA

## 3. Shortcodes del tema

### `[convoca_mi_perfil]`

Muestra el perfil del socio logueado: nombre, email, estado de membresía, inscripciones activas y horas de voluntariado. Si no ha iniciado sesión, muestra formulario de login.

### `[convoca_inscripcion_actual]`

Detecta automáticamente la actividad actual y muestra el formulario de inscripción. Ideal para usar en la plantilla de actividad. Atributo opcional `id` para especificar una actividad concreta.

### `[convoca_actividad_meta field="ubicacion"]`

Muestra un metadato de la actividad actual. Campos: `fecha_inicio`, `fecha_fin`, `ubicacion`, `plazas_totales`, `plazas_disponibles`, `precio_socio`, `precio_general`.

### `[convoca_dark_mode_toggle]`

Botón para alternar modo claro/oscuro. Persiste en localStorage.

### `[convoca_verificar_socio]`

Formulario de verificación pública de membresía.

### `[convoca_verificar_certificado]`

Formulario de verificación pública de certificados de voluntariado (código `VOL-AAAA-XXXXX`).

## 4. Block Patterns incluidos

| Pattern | Uso |
|---------|-----|
| Hero con CTA | Página de inicio |
| Grid de actividades | Listado de actividades |
| Tarjeta de socio | Perfil o llamada a alta |
| Pricing table | Página de membresías |
| Stats counter | Dashboard o impacto |
| Timeline | Historia o logros |
| Contacto | Página de contacto |

## 5. Personalización

Al ser un tema FSE, toda la personalización se hace desde **Apariencia → Editor**:

- **Plantillas**: Edita front-page, single, archive, 404
- **Partes de plantilla**: Header, footer
- **Estilos**: Modifica colores, tipografía, espaciado desde el panel de estilos

### Reset de plantillas

Si las plantillas personalizadas en la base de datos causan problemas, visita:

```
/wp-admin/?convoca_reset_templates=1&_wpnonce=XXXXX
```

Esto restaura las plantillas a los archivos del tema. Solo puede hacerse una vez por hora.

## 6. Compatibilidad con otros temas

Si biodevas.org o lugg.biodevas.org usan otros temas (Astra, Elementor), los shortcodes de Convoca Theme funcionan igualmente:

- Añade `[convoca_mi_perfil]` en cualquier página o widget
- Añade `[convoca_verificar_socio]` en una página pública
- El modo oscuro y los block patterns solo funcionan con Convoca Theme activo
