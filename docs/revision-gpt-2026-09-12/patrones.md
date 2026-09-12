### [ALTO] El patrón de contacto deja literalmente placeholders sin sustituir

- **Fichero:** `patterns/contact-banner.php:47-66`
- **Qué pasa:** El patrón contiene valores como:

  ```html
  <a href="mailto:{contact_email}">{contact_email}</a>
  ```

  y:

  ```json
  {"url":"https://www.instagram.com/{social_handle}"}
  ```

  WordPress no sustituye automáticamente `{contact_email}`, `{volunteer_email}` ni `{social_handle}`. Además, los bloques `core/social-link` conservarán esas URLs literales.
- **Por qué importa:** El usuario puede insertar el patrón y publicar enlaces rotos que apuntan a `mailto:{contact_email}` o a perfiles inexistentes. Un patrón sincronizado tampoco ejecutará PHP que pudiera reemplazar esos valores.
- **Cómo se arregla:** Convertirlos en campos editables claramente marcados por el usuario, dejar los enlaces sin configurar o proporcionar un bloque dinámico/plugin que resuelva esos datos. No usar placeholders con apariencia de datos reales en atributos `href` y `url`.

### [ALTO] Los patrones no son realmente genéricos: contienen contenido, marca implícita y URLs fijas

- **Ficheros:** principalmente `patterns/cards-3.php`, `cards-grid.php`, `contact-banner.php`, `cta.php`, `hero.php`, `hero-imagen.php`, `inscripcion-actividad.php`, `proximas-actividades.php`
- **Qué pasa:** Hay textos de contenido y llamadas a la acción incrustados, por ejemplo:

  ```html
  Nuestras áreas de acción
  Educación Ambiental
  Conservación
  Acción Comunitaria
  ```

  ```html
  href="/actividades/"
  href="/quienes-somos/"
  href="/alta-socios/"
  href="/voluntariado/"
  ```

  También se fijan colores concretos como `#f8f6f2`, `#555555`, `rgba(255,255,255,0.85)` y se introducen nombres de presets concretos como `naranja`, `violeta`, `blanco` y `amarillo`.
- **Por qué importa:** En un tema deliberadamente genérico, el patrón se publica con textos, enlaces y supuestos de marca de otra instalación. Las URLs relativas absolutas fallan si las páginas tienen otros slugs, si el sitio está instalado en un subdirectorio o si la instalación usa otro idioma. Los textos tampoco proceden de la instalación ni pueden adaptarse correctamente mediante los ajustes del sitio.
- **Cómo se arregla:** Entregar patrones realmente editables y neutros: campos de texto vacíos solo cuando sea imprescindible, instrucciones de edición o bloques dinámicos configurables. Para enlaces, usar enlaces que el usuario pueda seleccionar desde el editor, patrones de navegación o bloques proporcionados por el plugin del sitio. Evitar fijar colores fuera de los presets del tema, especialmente los valores `#555555`, `#666666`, `#777777`, `#888888` y `#f8f6f2`.

### [MEDIO] “Próximas actividades” no filtra actividades futuras

- **Fichero:** `patterns/proximas-actividades.php:48`
- **Qué pasa:** La consulta se describe como de próximas actividades, pero solo contiene:

  ```json
  "postType":"actividad",
  "order":"asc",
  "orderBy":"meta_value",
  "metaKey":"_convoca_fecha_inicio"
  ```

  No existe una `meta_query` que compare `_convoca_fecha_inicio` con la fecha actual.
- **Por qué importa:** Las actividades pasadas seguirán apareciendo en el listado mientras tengan un valor de ese metadato. El resultado contradice el título y la descripción del patrón.
- **Cómo se arregla:** Añadir una consulta de metadatos que filtre por fechas futuras, con un formato de fecha comparable y un tipo explícito, por ejemplo `DATE` o `DATETIME`, según el formato usado por el plugin. La lógica debe residir preferentemente en un bloque dinámico o en el plugin que registra el CPT, no en el tema.

### [MEDIO] Ordenación de fechas dependiente del formato almacenado

- **Fichero:** `patterns/proximas-actividades.php:48`
- **Qué pasa:** Se usa `orderBy: "meta_value"` sin declarar el tipo del metadato. WordPress puede ordenar el valor lexicográficamente, no como fecha.
- **Por qué importa:** Si `_convoca_fecha_inicio` no está almacenado en un formato ISO ordenable directamente, por ejemplo `YYYY-MM-DD` o `YYYY-MM-DD HH:MM:SS`, el orden puede ser incorrecto. El patrón tampoco documenta ese contrato.
- **Cómo se arregla:** Garantizar en el plugin un formato ISO uniforme y usar `meta_type: "DATE"` o `"DATETIME"` en la consulta, según corresponda. La comparación y ordenación de fechas debería centralizarse en el bloque/plugin que conoce el modelo de datos.

### [MEDIO] `stats.php` confía en una estructura no validada y puede producir errores en PHP 8.4

- **Fichero:** `patterns/stats.php:32-36, 48-52`
- **Qué pasa:** El código asume que `convoca_theme_get_stats()` siempre devuelve un array no vacío de arrays con las claves `value` y `label`:

  ```php
  $convoca_cols = count( $convoca_stats );
  ```

  y:

  ```php
  $convoca_stat['value']
  $convoca_stat['label']
  ```

  El propio comentario permite que un filtro externo sobrescriba los datos.
- **Por qué importa:** Un filtro que devuelva un escalar, un array con una entrada incompleta o un valor con estructura distinta puede provocar un `TypeError`, avisos de clave inexistente o una salida incompleta. En PHP 8.4 estos contratos incorrectos no deben darse por tolerados.
- **Cómo se arregla:** Validar el resultado antes de usarlo:

  ```php
  if ( ! is_array( $convoca_stats ) ) {
      return;
  }

  $convoca_stats = array_filter(
      $convoca_stats,
      static function ( $stat ) {
          return is_array( $stat )
              && array_key_exists( 'value', $stat )
              && array_key_exists( 'label', $stat );
      }
  );
  ```

  Después de filtrar, volver a comprobar que el array no está vacío antes de calcular el porcentaje. También conviene documentar el contrato del filtro y normalizar los valores.

### [MEDIO] Los patrones con dependencias de plugin no tienen degradación funcional

- **Ficheros:** `patterns/inscripcion-actividad.php`, `patterns/proximas-actividades.php`
- **Qué pasa:** Se incluyen directamente bloques y shortcodes que no pertenecen al núcleo ni al tema:

  ```html
  <!-- wp:convoca-common/post-meta-field ... /-->
  [convoca_actividad_meta field="fecha_inicio"]
  [convoca_inscripcion_actual]
  ```

- **Por qué importa:** Si el plugin correspondiente no está activo, el editor mostrará bloques inválidos y el visitante verá contenido vacío o el shortcode sin procesar. Además, el tema queda acoplado a un modelo de datos y a nombres de shortcodes concretos.
- **Cómo se arregla:** Mover estos patrones al plugin que registra el CPT y sus bloques/shortcodes, o declarar claramente que son patrones opcionales proporcionados por el plugin. Para un tema genérico, ofrecer una variante equivalente basada solo en bloques core, sin suponer que existe `actividad`.

### [MEDIO] Uso de shortcode en un patrón que puede quedar sincronizado

- **Fichero:** `patterns/inscripcion-actividad.php:50`
- **Qué pasa:** El patrón contiene un bloque `core/shortcode`:

  ```html
  <!-- wp:shortcode -->
  [convoca_inscripcion_actual]
  <!-- /wp:shortcode -->
  ```

  El PHP del fichero de patrón no se ejecuta para generar contenido cuando el patrón se guarda o utiliza como patrón sincronizado. Tampoco se expande el shortcode durante la inserción del patrón.
- **Por qué importa:** No se obtiene un formulario generado en el momento de insertar el patrón. Si la instalación trata ese contenido como patrón sincronizado, la expectativa de que el patrón “detecte la actividad actual” puede no cumplirse en la fase de generación. El resultado depende de que el bloque de shortcode se renderice posteriormente y de que el plugin siga activo.
- **Cómo se arregla:** Para contenido dinámico dependiente del contexto, usar un bloque dinámico registrado por el plugin, con `render_callback`, o mantener este patrón fuera de la sincronización y documentar la dependencia. No presentar un patrón sincronizado como si ejecutara PHP al insertarse.

### [MEDIO] Varios patrones generan enlaces interactivos vacíos

- **Ficheros:** `patterns/cards-grid.php`, `cta-centered.php`, `hero-gradient.php`, `pricing-grid.php`
- **Qué pasa:** Hay botones como:

  ```html
  <a class="wp-block-button__link ..."></a>
  ```

  sin texto ni `href`, además de encabezados y párrafos completamente vacíos.
- **Por qué importa:** Si el usuario inserta y publica el patrón sin completar todos los campos, se generan controles sin nombre, enlaces que no llevan a ninguna parte y una estructura poco usable para lectores de pantalla. En el caso del botón outline, el contenido vacío puede ser difícil de localizar incluso visualmente.
- **Cómo se arregla:** Proporcionar contenido de ejemplo claramente editable y enlaces de ejemplo configurables, o no renderizar el botón hasta que tenga texto y URL mediante un bloque dinámico. Si se mantienen campos vacíos, añadir una instrucción visible en el editor, no confiar en que el usuario detectará todos los huecos.

### [BAJO] Emojis usados como contenido visual no están marcados como decorativos

- **Fichero:** `patterns/cards-3.php:32, 49, 66`
- **Qué pasa:** Los emojis se colocan como texto dentro de párrafos:

  ```html
  <p class="has-text-align-center">🌿</p>
  ```

- **Por qué importa:** Los lectores de pantalla pueden anunciar el nombre del emoji como contenido adicional o poco útil. Además, el emoji puede variar visualmente según sistema operativo y no aporta una alternativa consistente.
- **Cómo se arregla:** Si son decorativos, ocultarlos a tecnologías de asistencia, por ejemplo mediante un elemento con `aria-hidden="true"`, o sustituirlos por un icono con una alternativa textual adecuada si transmiten información.

### [BAJO] Los iconos Dashicons no están declarados como decorativos

- **Fichero:** `patterns/proximas-actividades.php:70, 76`
- **Qué pasa:** Se usan:

  ```html
  <span class="dashicons dashicons-calendar-alt"></span>
  <span class="dashicons dashicons-location"></span>
  ```

  sin `aria-hidden="true"` ni texto alternativo asociado.
- **Por qué importa:** Si el icono se interpreta como contenido o si la fuente no carga, la información depende de una representación visual no accesible. Además, el patrón no garantiza por sí mismo que Dashicons esté disponible en el frontend.
- **Cómo se arregla:** Marcar los iconos como decorativos (`aria-hidden="true"`) y conservar la información en texto, por ejemplo “Fecha:” y “Ubicación:”. No depender del icono para transmitir el significado.

No observo en estos patrones consultas SQL directas, recepción de datos por formularios, atributos generados con entrada de usuario ni un problema de escapado en `stats.php`: los valores dinámicos de ese fichero sí se imprimen con `esc_html()` y el porcentaje con `esc_attr()`.