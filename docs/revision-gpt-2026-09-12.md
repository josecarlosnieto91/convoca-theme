# Revisión del tema por ChatGPT — y su verificación

Tema revisado: **Convoca** (tema de bloques FSE). Fecha: 2026-09-12.

## Cómo se hizo

Se envió el tema **completo** a `gpt-5.6-luna` en cinco áreas (PHP, plantillas y partes,
patrones, CSS, theme.json), con el contexto de que es un tema de bloques **genérico** donde la
instalación aporta textos, enlaces y paleta, y con el aviso de que PHPStan, PHPCS y Theme Check
ya pasan, así que el formato no interesa. 132 000 tokens en total.

**49 hallazgos**: 11 altos, 24 medios, 12 bajos, 2 dudas.

Este documento es el resultado de **verificar cada hallazgo alto contra el código real**, con la
evidencia. Un revisor automático acierta y también se equivoca: de 11 altos, **8 son ciertos, 2
son parciales y 1 es falso en la práctica**.

## Los altos, uno por uno

### 1. VERDADERO · La portada depende de IDs de esta instalación
`templates/front-page.html` referencia los patrones del sitio por ID de la base de datos:

```
<!-- wp:block {"ref":11968} /-->   <!-- wp:block {"ref":11970} /-->   <!-- wp:block {"ref":11969} /-->
```

Esos IDs son entradas `wp_block` de esta instalación. En otra instalación, o si se borran esos
patrones, la portada pierde esas secciones. **Viene de mi paso de llevar los patrones al sitio.**
Arreglo: que el tema no dependa de IDs ajenos (patrones de tema o contenido editable).

### 2. VERDADERO · El tema lleva contenido de una instalación concreta
Diez ficheros con rutas, un teléfono y frases propias del sitio:

| fichero | qué lleva |
|---|---|
| `parts/footer.html` | `/actividades/`, `/voluntariado/`, `/la-escuela/`, `tel:+34…`, «socioambiental asturiana» |
| `parts/sidebar.html` | `/la-escuela/`, «socioambiental asturiana», «Educación Ambiental» |
| `templates/front-page.html` | `/actividades/`, `/voluntariado/`, `/la-escuela/`, «Educación Ambiental» |
| `templates/404.html`, `templates/search.html` | `/actividades/` |
| `patterns/hero.php`, `hero-imagen.php`, `proximas-actividades.php` | `/actividades/`, `/quienes-somos/` |
| `patterns/cta.php` | `/voluntariado/` |
| `patterns/cards-3.php` | «Educación Ambiental» |

Contradice el criterio de tema genérico. **Es decisión tuya**: o se limpia, o aceptamos que el
tema sea «el tema de esta web». Nota: el barrido anterior buscaba la palabra de marca y dio 0;
esto es otra cosa: contenido y rutas.

### 3. VERDADERO · Bloques dinámicos dentro de `core/html`, con efecto real
`templates/archive-actividad.html` mete `convoca-common/post-meta-field` dentro de un bloque
`core/html`. Dentro de `core/html` los comentarios de bloque **no se ejecutan**: medido en el
archivo, `convoca-card-meta-date`, `convoca-card__datos` y `convoca-plazas-total` aparecen **0
veces**. Las tarjetas pierden sus datos. Latente hoy (esa plantilla no es alcanzable: el tipo
`actividad` no tiene archivo público), pero rota.

### 4. VERDADERO · El botón «Ver detalles» no lleva a la actividad
```
<a class="wp-block-button__link …" href="#" class="wp-block-post-link">Ver detalles</a>
```
Dos problemas: `href="#"` (no va a ninguna parte) y **el atributo `class` duplicado** (HTML
inválido, el segundo se ignora).

### 5. VERDADERO · Fechas del schema de eventos
`convoca_event_schema()` usa `strtotime()` y `gmdate()` sin comprobar si `strtotime()` devuelve
`false`: una fecha mal escrita por un administrador se publica como **1970-01-01** y Google
puede rechazar el marcado. Además, un valor `datetime-local` se interpreta en la zona del
servidor y se etiqueta como UTC, así que la hora puede salir desplazada.

### 6. VERDADERO · El guardado del metabox no descarta revisiones
`convoca_event_meta_save()` comprueba nonce, autosave y capacidad —bien— pero **no** comprueba
`wp_is_post_revision()`, `wp_is_post_autosave()` ni el tipo de contenido, y el gancho
`save_post` es global. Puede escribir metadatos en una revisión o en un tipo no previsto.

### 7. VERDADERO · El filtro global de bloques sustituye sin conocer el contexto
`convoca_theme_render_block()` (gancho `render_block`, 3 297 caracteres) hace `str_replace` de
los tokens y ejecuta `do_shortcode` sobre el HTML ya renderizado, **sin escape por contexto**
(no usa `esc_url`, `esc_attr` ni `wp_kses`). Funciona porque los valores salen de filtros
propios, pero un valor con comillas rompería el atributo.

### 8. PARCIAL · Los tokens `{…}` no son nativos de WordPress
Cierto que no son nativos, pero **sí se sustituyen**: hay capa de renderizado y está
documentada. El riesgo real es otro y lo dejo apuntado: un token **sin valor** se imprime
literal (fue justo el fallo del pie, que ya se arregló al cargar el plugin de sitio).

### 9. PARCIAL · Contraste insuficiente
El theme.json tiene 8 declaraciones que usan naranja o amarillo como color de texto
(`.price-amount`, `.convoca-stats .stat-value`…), y sobre blanco darían 1.9–3.3:1. **Medido en
vivo, los elementos que sí se pintan cumplen todos**: cifra 5.86, botón 7.38 (texto oscuro
sobre naranja), «Ver más» 5.07, títulos 17.79, menú 17.79. Queda como riesgo latente en
componentes que hoy no se usan.

### 10. FALSO · «El patrón de contacto deja placeholders sin sustituir»
De 15 patrones del tema, solo `contact-banner.php` usa tokens, y el tema **sí** los sustituye en
el render. Es un aviso razonable sobre el mecanismo, no un fallo existente.

### 11. PARCIAL · Los patrones no son genéricos
Mismo caso que el punto 2: es contenido de sitio en el tema, no un fallo de código.

## Lo que merece la pena de los medios y bajos

- `patterns/stats.php` confía en la estructura de los datos sin validarla (puede dar error en PHP 8.4).
- Varios patrones generan enlaces interactivos vacíos.
- `transition: all` y `backdrop-filter` en listados largos (rendimiento).
- Radio global en imágenes que también afecta a logos.
- Cadenas visibles sin internacionalizar en CSS y plantillas.
- Botones de modo oscuro sin estado accesible (`aria-pressed`).

## Propuesta de orden de trabajo

1. `wp:block` de la portada (punto 1): rompe en cualquier instalación que no sea esta.
2. Botón «Ver detalles» y `core/html` del archivo de actividad (puntos 3 y 4): arreglo corto.
3. Fechas del schema y guardado del metabox (puntos 5 y 6): arreglo corto, evita datos falsos.
4. Contenido de sitio en el tema (punto 2): **decisión tuya** antes de tocar nada.
5. Escape por contexto en el filtro de bloques (punto 7): endurecimiento, se puede planificar.
