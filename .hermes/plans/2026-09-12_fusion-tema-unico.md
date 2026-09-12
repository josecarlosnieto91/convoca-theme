# Fusión del tema: un solo `convoca-theme`

> **Para Hermes:** ejecutar fase a fase con `subagent-driven-development` no; este
> trabajo lo lleva Mika directamente porque necesita la demo viva y medición real.
> Cada fase termina con verificación y commit. No se pasa a la siguiente sin verde.

**Objetivo:** que `convoca-theme` sea el único tema, con todo el diseño que se hizo
en el hijo `biodevas-fse` dentro y sin nomenclatura de Biodevas en su código.

**Contexto de partida (medido el 2026-09-12):**

| | `convoca-theme` | `biodevas-fse` (hijo) |
|---|---|---|
| plantillas | 14 | 8 (sobrescriben 8 del padre) |
| partes | 8 | 3 (sobrescriben 3 del padre) |
| patrones | 15 | 4 (editoriales) |
| `style.css` | 1.686 líneas | 2.385 líneas |
| `functions.php` | 1.049 líneas, 62 funciones, 18 hooks | 903 líneas, 10 funciones, 31 hooks |
| `theme.json` | 531 líneas | 651 líneas |
| clases `bio-*` | 0 | 922 |
| variables `--bio-*` | 0 | 204 |
| funciones `biodevas_*` | 0 | 59 |
| metas `_biodevas_*` | 0 | 21 (solo en `functions.php`) |

Sin colisiones de funciones entre ambos (0 de 62+10). **5 hooks compartidos** que hay
que fusionar a mano: `after_setup_theme`, `init`, `wp_enqueue_scripts`, `wp_head` y el
filtro `convoca_theme_stats`. Hoy se cargan las DOS hojas de estilo.

**Decisiones de JC (2026-09-12):**

1. Prefijo: `convoca-` (el theme ya usa 44 clases así: `.convoca-card`, `.convoca-badge`).
2. Los 5 colores de la identidad viven en **Global Styles del sitio**; el theme queda neutro.
3. Las plantillas y partes solo del padre (producto) se **adaptan** al diseño nuevo.
4. Los 4 patrones editoriales de Biodevas pasan **al sitio** como patrones sincronizados.
5. Las metas `_biodevas_*` se **migran también en producción** (Orion y Andromeda).

**Puntos de retorno creados:** `pre-fusion-convoca` (6aa2f5f), `pre-fusion-child`
(215e03c), `pre-fusion-demo` (802e49b).

---

## Fase 0 — Red de seguridad

- [ ] Ramas: `fusion-tema-unico` en `convoca-theme` y en `biodevas-fse`.
- [ ] Empujar los tres tags al remoto (`git push origin --tags`).
- [ ] Anotar el estado de partida: `hermes verify` (8 fases) y las 42 capturas.
      **Comando:** `cd ~/repos/biodevas-demo && hermes verify --json > /tmp/fusion-antes.json`
- [ ] Referencia visual "antes": `bash scripts/shots-web.sh` y guardar en
      `reference/fusion-antes/` (la comparación final se hace contra esto).

**Verificación:** el JSON de la receta con `ok: true` y las 42 capturas guardadas.

## Fase 1 — Plantillas y partes

Se sustituyen las 8 plantillas y 3 partes del padre por las del hijo (que hoy ganan) y
se renombran sus clases dentro.

**Ficheros (hijo → padre):**
- `templates/{404,archive,front-page,home,index,page,search,single}.html`
- `parts/{footer,header,sidebar}.html`

**Pasos:**
1. Copiar cada fichero del hijo sobre el del padre.
2. Renombrar dentro: `bio-` → `convoca-`, `biodevas-` → `convoca-`.
3. Comprobar que no queda ninguna clase `bio-` sin renombrar:
   `grep -rn '\bbio-[a-z]' convoca-theme/templates convoca-theme/parts` → debe salir vacío.
4. Los 4 patrones editoriales del hijo (`hero-portada`, `participa`, `quienes-somos`,
   `tres-caminos`) salen del theme y se crean en la demo como patrones sincronizados
   (`wp_block`) con WP-CLI, conservando su contenido literal.

**Verificación:**
- `bash scripts/verify-templates.sh` (parser de WordPress, 11 plantillas + patrones).
- `python3 scripts/verify-render.py` (12 casos sobre HTTP real).

## Fase 2 — `style.css` fusionada

**El punto delicado**: el hijo tiene reglas que existen SOLO para ganar al padre. Al
fusionar hay que arreglar la regla original, no arrastrar el parche. Conocidos:

| Parche en el hijo | Regla del padre que lo obliga | Qué hacer |
|---|---|---|
| `body .site-header .biodevas-fila { min-height: 88px !important }` | `.site-header .biodevas-fila` de la sección 20 | dejar UNA regla, sin `!important` |
| correcciones de `.wp-block-site-title { max-width: 40vw }` | regla del padre que recorta el titular | arreglar el padre en su origen |
| relleno de `#main-content` forzado | `has-global-padding` + `#main-content` del padre | una sola fuente de verdad |
| impresión pintada en el contenedor | `global-styles-inline-css` con `!important` | revisar si sigue haciendo falta |

**Pasos:**
1. Renombrar en el hijo: `bio-` → `convoca-`, `--bio-` → `--convoca-`.
2. Integrar las secciones del hijo (1..26) en el `style.css` del padre por bloques
   temáticos, no pegando al final: sistema visual, cabecera, portada, tarjetas,
   archivos, entrada, pies, responsive, desplegable móvil, impresión.
3. Eliminar los parches que solo existían por tener dos capas (tabla de arriba).
4. Deduplicar: el hijo tiene un bloque `.bio-dato-evento` repetido y varias reglas que
   ya hacía el padre.

**Verificación:**
- Toda clase usada en plantillas existe en el CSS:
  `grep -o 'convoca-[a-z0-9_-]*' -h templates/*.html parts/*.html | sort -u` contra
  las definidas en `style.css`.
- `python3 scripts/verify-assets.py` (la hoja del tema carga en https).

## Fase 3 — `functions.php` fusionada

**Pasos:**
1. Renombrar `biodevas_*` → `convoca_*` (59 apariciones) y `biodevas-` → `convoca-`.
2. Fusionar los 5 hooks compartidos revisando los dos lados, uno a uno.
3. **Doble lectura de las metas** (clave para la fase 6):
   leer primero `_convoca_event_start_date` y caer a `_biodevas_event_start_date`.
   Igual para `_end_date`, `_address` y `has_event`.
4. El metabox se queda, con la etiqueta en español neutro («Evento»), sin la palabra
   Biodevas.

**Verificación:**
- `php -l` de todos los PHP del theme.
- `composer run phpcs` y `composer run phpstan` **en verde**: el CI de
  `convoca-theme` es bloqueante y este código no ha pasado nunca por ahí.
- `python3 scripts/verify-peticiones.py` (emojis, destacada, fechas, marca).

## Fase 4 — `theme.json` y los colores

**Pasos:**
1. El `theme.json` del padre se queda con la paleta neutra (escalas de gris + un color
   de marca genérico), sin los 5 colores de Biodevas.
2. Los 5 colores (`#ff8700`, `#ffab00`, `#ff500a`, `#7d0032`, `#320028`) se exportan
   como **Global Styles del sitio** (`wp_global_styles` del tema): así la identidad es
   configuración, no código, y sobrevive a las actualizaciones del theme.
3. Documentar en el README del theme cómo se aplican (Ajustes → Estilos → Colores).
4. `--bio-*` del CSS pasa a leerse de los presets de Global Styles donde tenga sentido.

**Verificación:**
- La portada de la demo conserva los colores exactos: medir los 5 valores con el
  navegador antes y después y comparar.
- Contrastes calculados de nuevo (los pares texto/fondo que ya validamos).

## Fase 5 — La demo, con un solo tema

**Pasos:**
1. `docker-compose.yml`: quitar el montaje del hijo; `themes/convoca` desde
   `~/repos/convoca-theme` (4 referencias al hijo que hay que limpiar).
2. Activar el tema: `wp theme activate convoca`.
3. Quitar los 5 temas de arrastre (`biodevas-theme`, `bravada`, `bravada-child`,
   `twentytwentyfive`) — son inactivos, vienen del clon de producción.
4. Actualizar `README.md` (9 referencias) y `scripts/verify-assets.py` (1).
5. Arrancar y comprobar que todo sigue: las 8 fases de la receta + `verify-tarjetas.py`.

**Verificación (la puerta de aceptación):**
- `hermes verify` con las 8 fases en verde.
- `bash scripts/shots-web.sh` → 42 capturas a `reference/fusion-despues/`.
- Comparación **una a una** contra `reference/fusion-antes/`: mismas dimensiones, misma
  estructura, mismas secciones.
- Crítica de visión externa (`scripts/critica-vision.py`) sobre la portada y el menú
  móvil: sin regresiones nuevas.
- Los números ya medidos deben seguir igual: fila de cabecera 88 px, logo 207×52 con
  18/19 px de aire a 390, flechas del desplegable alineadas a 372, 9 tarjetas con una
  etiqueta, portada sin fechas.

## Fase 6 — Migración de las metas en producción

**Orden obligatorio** (cada paso reversible por sí solo):

1. **Desplegar primero el tema con doble lectura** (fase 3). Sin esto, renombrar las
   metas deja 306 actividades sin fecha.
2. **Volcado de seguridad** de la base de datos de Orion antes de tocar nada:
   `wp db export` con fecha en el nombre + comprobar que el fichero existe y pesa lo
   que debe. (Además está el backup diario con retención de 14 días.)
3. **Renombrar** con un script idempotente:

   ```sql
   UPDATE wp_postmeta SET meta_key = '_convoca_event_start_date'
    WHERE meta_key = '_biodevas_event_start_date';
   ```

   (y las otras tres). Guardar el recuento ANTES y DESPUÉS: los números deben cuadrar.
4. **Verificar** que las fechas siguen saliendo: listar 5 actividades con su fecha de
   evento y comprobar que el valor no cambió.
5. Andromeda: es réplica de Orion por `sync-replica.sh`, así que hereda la migración.
   Comprobar que efectivamente la hereda y no mantener dos verdades.
6. **Quitar la lectura antigua** del tema (dejar solo `_convoca_*`) cuando producción
   lleve unos días estable.

**Riesgos:**
- Producción **no puede** llevar este tema todavía: el despliegue del FSE a
  biodevas.org es un proyecto aparte. Hasta que ocurra, la doble lectura se queda.
- Toda la fase 6 va con el OK explícito de JC en el momento de ejecutarla, no antes.

---

## Validación global (criterio de cierre)

1. `convoca-theme` es el único tema: 0 referencias a Biodevas en su código
   (`grep -ri biodevas convoca-theme --exclude-dir=.git` → solo la doble lectura de
   metas, que es explícita y documentada).
2. `hermes verify`: 8 fases en verde.
3. Las 42 capturas comparadas contra el antes: sin diferencias estructurales.
4. La demo se ve igual que antes de empezar (esa es la prueba de que la fusión no cambió
   nada de lo que ya habíamos acordado).
5. `biodevas-fse` archivado en `~/archive/projects/` con su README explicando que su
   contenido vive en `convoca-theme`.

## Cerrado con JC (2026-09-12)

1. **Las 6 plantillas de producto** (`single-actividad`, `archive-actividad`,
   `page-actividad`, `page-proyecto`, `page-landing`, `page-institucional`) y sus partes
   (`card-actividad`, `card-proyecto`, `activity-meta`, `cta`, `hero`) **se adaptan
   DESPUÉS**: primero la fusión, que se verifica sola, y el diseño se hace ya con un solo
   tema. Esta tanda cierra en la fase 5.
2. **La fase 6 se queda parada en el paso 1**: el tema con doble lectura de las metas, y
   la migración de la base de datos se ejecuta cuando toque desplegar el FSE a
   biodevas.org. No se toca Orion en esta tanda.

## Ficheros que van a cambiar

- `convoca-theme/style.css` — fusión (el grueso del trabajo)
- `convoca-theme/functions.php` — fusión de los 5 hooks + renombrado + doble lectura
- `convoca-theme/theme.json` — paleta neutra
- `convoca-theme/templates/*.html`, `convoca-theme/parts/*.html` — sustitución y renombrado
- `convoca-theme/patterns/*.php` — limpieza de los 4 editoriales
- `biodevas-demo/docker-compose.yml`, `README.md`, `scripts/verify-assets.py`
- `biodevas-demo/reference/fusion-antes/`, `reference/fusion-despues/` — capturas

## Comandos de verificación (los de siempre, no inventar otros)

```bash
cd ~/repos/biodevas-demo
hermes verify --json                      # 8 fases (receta del proyecto)
python3 scripts/verify-tarjetas.py        # etiquetas y fechas
python3 scripts/verify-render.py          # 12 casos sobre HTTP real
bash scripts/shots-web.sh                 # 42 capturas
python3 scripts/critica-vision.py         # crítica de visión externa
cd ~/repos/convoca-theme && composer run phpcs && composer run phpstan
```
