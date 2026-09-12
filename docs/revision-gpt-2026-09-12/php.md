No veo vulnerabilidades críticas de inyección SQL ni un bypass evidente de permisos. El guardado del metabox usa nonce y `current_user_can( 'edit_post' )`, y las salidas administrativas principales están escapadas. Sí hay varios problemas relevantes fuera del alcance de PHPStan/PHPCS.

### [ALTO] El filtro global de bloques ejecuta shortcodes y sustituye valores sin conocer el contexto HTML

- **Fichero:** `functions.php`, función `convoca_theme_render_block()`, filtro `render_block`.
- **Que pasa:** Se aplica a todos los bloques renderizados:

  ```php
  add_filter( 'render_block', 'convoca_theme_render_block', 10, 2 );
  ```

  y al final se ejecuta:

  ```php
  if ( strpos( $block_content, '[' ) !== false ) {
      $block_content = do_shortcode( $block_content );
  }
  ```

  Además, los valores procedentes de filtros se inyectan mediante `str_replace()` directamente en HTML:

  ```php
  $block_content = str_replace(
      array_keys( $replacements ),
      array_values( $replacements ),
      $block_content
  );
  ```

- **Por que importa:** Un placeholder puede estar dentro de texto, un atributo HTML, una URL, un atributo `aria-label`, CSS inline, etc. No se puede aplicar el mismo escapado a todos esos contextos. Por ejemplo, `{cta_url}` o `{community_url}` pueden acabar directamente en `href`, mientras que `{cta_label}` acaba en texto.

  Además, `do_shortcode()` globaliza la ejecución de shortcodes en todo el HTML renderizado. Esto puede ejecutar shortcodes de plugins en bloques donde no deberían ejecutarse, provocar consultas o efectos secundarios repetidos y convertir una funcionalidad de compatibilidad en una superficie de ejecución demasiado amplia.

- **Como se arregla:** No aplicar reemplazos al HTML final sin conocer el contexto. Alternativas:

  1. Resolver los valores al construir el bloque o mediante un bloque dinámico.
  2. Mantener placeholders exclusivamente en nodos de texto y sustituirlos con un parser de HTML.
  3. Para URLs, usar `esc_url()` antes de construir el atributo; para texto, `esc_html()`; para atributos, `esc_attr()`.
  4. Limitar `do_shortcode()` a bloques o patrones concretos, no a todo `render_block`.
  5. Si el objetivo es compatibilidad con patrones no sincronizados, procesar únicamente los bloques que contienen explícitamente esos placeholders.

### [ALTO] El JSON-LD genera fechas incorrectas o fechas de 1970 con entradas inválidas

- **Fichero:** `functions.php`, función `convoca_event_schema()`.
- **Que pasa:** Se acepta cualquier texto como fecha:

  ```php
  $start_date = convoca_get_event_meta( $post_id, '_convoca_event_start_date' );
  ```

  y después:

  ```php
  $iso_start = ! empty( $start_date )
      ? gmdate( 'c', strtotime( $start_date ) )
      : get_the_date( 'c' );
  ```

  Si `strtotime()` falla, devuelve `false`; `gmdate()` puede acabar generando una fecha equivalente a Unix epoch, por ejemplo `1970-01-01T00:00:00+00:00`.

  También se interpreta el valor de `datetime-local` usando la zona horaria del servidor y después se etiqueta como UTC mediante `gmdate()`. La hora del evento puede desplazarse varias horas.

- **Por que importa:** El marcado de eventos puede ser rechazado por Google o mostrar fechas incorrectas en buscadores. Un administrador que introduzca un valor no válido puede producir datos estructurados falsos sin recibir ningún aviso.

- **Como se arregla:** Validar estrictamente la fecha antes de generar el schema:

  ```php
  $timestamp = strtotime( $start_date );

  if ( false === $timestamp ) {
      return;
  }
  ```

  Para `datetime-local`, interpretar el valor en la zona horaria configurada en WordPress usando `wp_timezone()` y `DateTimeImmutable`, y serializar después mediante `setTimezone( new DateTimeZone( 'UTC' ) )` o conservando correctamente el offset.

  También conviene validar que la fecha final no sea anterior a la inicial.

### [ALTO] El guardado del metabox puede ejecutarse sobre revisiones y no valida el tipo de contenido

- **Fichero:** `functions.php`, función `convoca_event_meta_save()`, hook `save_post`.
- **Que pasa:** El callback se engancha a todos los tipos de contenido:

  ```php
  add_action( 'save_post', 'convoca_event_meta_save' );
  ```

  pero solo comprueba nonce, autosave y capacidad. No descarta revisiones ni comprueba que `$post_id` sea una entrada normal:

  ```php
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
  }
  ```

- **Por que importa:** El metabox solo se muestra en `post`, pero el callback puede recibir revisiones u otros objetos si se presenta un nonce válido. Puede guardar metadatos en una revisión o en un tipo de contenido no previsto. Además, al editar una entrada con una revisión automática, el comportamiento puede ser inconsistente.

- **Como se arregla:**

  ```php
  if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
      return;
  }

  if ( 'post' !== get_post_type( $post_id ) ) {
      return;
  }
  ```

  También conviene validar explícitamente los campos `datetime-local` antes de guardarlos, en lugar de limitarse a `sanitize_text_field()`.

### [MEDIO] Se incrustan datos y decisiones específicas de una instalación en un tema declarado genérico

- **Fichero:** `functions.php`, especialmente `convoca_help_page_html()`, `convoca_event_schema()` y `convoca_critical_css()`.
- **Que pasa:** Hay contenido específico incrustado en el tema:

  ```php
  'name' => 'Asturias'
  ```

  y:

  ```php
  'addressRegion' => 'Asturias',
  'addressCountry' => 'ES',
  ```

  También se muestra:

  ```php
  'Este es un theme FSE ... optimizado para la Asociación Convoca...'
  ```

  y se fijan colores, tipografías y presets directamente en CSS:

  ```css
  --wp--preset--color--naranja: #ff8700;
  --wp--preset--color--blanco: #ffffff;
  --wp--preset--color--antracita: #1a1a1a;
  ```

- **Por que importa:** La instalación debe proporcionar la marca, textos, enlaces y paleta. En otra instalación, el schema puede atribuir todos los eventos a Asturias y España, y el panel de ayuda presenta una identidad que no corresponde. El CSS inline puede imponerse o competir con `theme.json` y con los Estilos Globales.

- **Como se arregla:** Mover región, país, organización, textos de ayuda, colores y tipografías a:

  - `theme.json` y Estilos Globales para diseño.
  - filtros bien documentados o configuración del sitio para datos de organización y ubicación.
  - un plugin del sitio para la ayuda específica de la instalación.

  Si el tema debe ser genérico, el schema no debería inventar `Asturias`, `ES`, precio `0` ni modalidad presencial por defecto.

### [MEDIO] El schema de evento se genera para entradas que quizá no sean eventos

- **Fichero:** `functions.php`, función `convoca_event_schema()`.
- **Que pasa:** Basta con que la entrada pertenezca a las categorías `actividades` o `local`:

  ```php
  foreach ( array( 'actividades', 'local' ) as $cat_slug ) {
      if ( has_category( $cat_slug ) ) {
          $in_event_cat = true;
      }
  }
  ```

  para producir un `Event`, aunque no se haya marcado como evento ni tenga fechas.

- **Por que importa:** Una categoría editorial no garantiza que el contenido sea un evento. El resultado puede ser marcado estructurado incorrecto, especialmente al usar la fecha de publicación como fecha del evento:

  ```php
  : get_the_date( 'c' );
  ```

- **Como se arregla:** Generar `Event` únicamente cuando exista una marca explícita y una fecha válida. Si se necesita compatibilidad con datos históricos, hacer esa regla configurable mediante un filtro y exigir al menos `startDate` válido.

### [MEDIO] El selector de idioma se añade a todos los menús y bloques de navegación

- **Fichero:** `functions.php`, funciones `convoca_theme_language_switcher()` y `convoca_theme_lang_switcher_block()`.
- **Que pasa:** El filtro de menús clásicos ignora `$args`:

  ```php
  return $items . convoca_theme_lang_switcher_html();
  ```

  Por tanto, añade el selector a cada menú clásico, incluidos menú superior, sociales, pie, etc.

  En FSE, el filtro añade el selector a cualquier `core/navigation`, sin comprobar ubicación, contexto o si ya se ha añadido.

- **Por que importa:** Puede haber varios selectores en la misma página, incluso en navegación del pie o navegación móvil duplicada. En una página con varios bloques de navegación, el resultado es inconsistente y visualmente incorrecto.

- **Como se arregla:** Limitar la inserción a una ubicación o bloque concreto. Para menús clásicos, comprobar `$args->theme_location`. Para FSE, usar un bloque o patrón específico, o verificar atributos/contexto antes de inyectar el HTML.

### [MEDIO] El HTML del selector FSE puede ser inválido y su accesibilidad es incompleta

- **Fichero:** `functions.php`, `convoca_theme_lang_switcher_block()` y `convoca_theme_lang_switcher_html()`.
- **Que pasa:** El selector devuelve un `<li>`:

  ```php
  $html  = '<li class="menu-item ...">';
  ```

  pero se inserta justo antes de `</nav>`:

  ```php
  $pos = $m[1][1];
  return substr( $block_content, 0, $pos ) . $switcher . substr( $block_content, $pos );
  ```

  En la estructura habitual de `core/navigation`, los elementos de menú deben estar dentro del `<ul>`, no ser hijos directos de `<nav>`.

  Además, el botón usa `aria-expanded` y `aria-controls`, pero no gestiona Escape, foco, navegación por teclado ni estado de ocultación accesible del `<ul>`.

- **Por que importa:** Puede producir HTML no válido y una experiencia deficiente para usuarios de teclado o lectores de pantalla. El desplegable también se posiciona con `position: fixed`, pero no se reposiciona al cambiar el foco ni al abrirse fuera de la ventana.

- **Como se arregla:** Insertar el `<li>` dentro del `<ul>` correcto o implementar el selector como un bloque propio. Añadir:

  - `aria-haspopup="true"`;
  - gestión de Escape;
  - devolución del foco al botón;
  - cierre al perder foco o hacer clic fuera;
  - estado accesible coherente del panel;
  - soporte de navegación con teclado.

### [MEDIO] Suposiciones no comprobadas sobre las respuestas de WPML y Polylang

- **Fichero:** `functions.php`, función `convoca_theme_lang_languages()`.
- **Que pasa:** Se accede directamente a índices devueltos por plugins externos:

  ```php
  'code'       => $lang['language_code'],
  'name'       => $lang['native_name'] ? $lang['native_name'] : $lang['translated_name'],
  'url'        => $lang['url'],
  ```

  y:

  ```php
  'code'       => $lang['slug'],
  'name'       => $lang['name'],
  'url'        => $lang['url'],
  ```

- **Por que importa:** Cambios de versión, configuraciones incompletas o respuestas parciales pueden generar avisos, valores nulos o HTML incompleto. En PHP moderno, una respuesta no compatible puede terminar causando errores de tipo posteriores.

- **Como se arregla:** Comprobar `is_array( $lang )`, usar `isset()`/`??`, validar que código, nombre y URL sean escalares y descartar idiomas sin URL válida antes de construir el HTML.

### [MEDIO] Consulta costosa en cada ejecución de `wp_list_pages()`

- **Fichero:** `functions.php`, filtro `wp_list_pages_excludes`.
- **Que pasa:** Cada llamada al filtro ejecuta una consulta sin límite:

  ```php
  $pages = get_posts(
      array(
          'post_type'   => 'page',
          'post_status' => 'publish',
          'numberposts' => -1,
      )
  );
  ```

- **Por que importa:** `wp_list_pages()` puede ejecutarse varias veces en una página, especialmente en temas FSE con varias partes. Se cargan todos los objetos `WP_Post` aunque solo se necesitan IDs, y se excluyen por patrón cualquier página cuyo slug termine en `-2`, aunque sea una página legítima no relacionada con traducciones.

- **Como se arregla:** Usar `fields => 'ids'`, limitar la consulta y cachear el resultado con un transitorio o caché de objeto. Preferiblemente, usar la API del plugin de traducción activo en vez de inferir traducciones por el sufijo del slug.

### [MEDIO] El seguimiento de Google Forms se añade desde el tema sin consentimiento

- **Fichero:** `functions.php`, callback anónimo en `wp_head`, prioridad `99`.
- **Que pasa:** El tema registra eventos de Google Analytics para enlaces externos:

  ```js
  gtag("event", "form_click", {
      form_url: el.href,
      page_location: window.location.href
  });
  ```

  No hay comprobación de consentimiento ni integración con una plataforma de consentimiento. Además, el código solo se instala si `gtag` existe en el momento de ejecutar el script; si Google Analytics se carga después, no se registrará ningún listener.

- **Por que importa:** Es lógica de analítica y privacidad propia de un sitio, no del tema genérico. Puede enviar URL de formularios y de páginas a terceros antes de que el usuario haya consentido. También es una implementación frágil por la dependencia del orden de carga.

- **Como se arregla:** Moverlo a un plugin del sitio o a la integración de analítica. Ejecutarlo solo después del consentimiento correspondiente y cargarlo mediante un script encolado con una dependencia o mecanismo de inicialización fiable.

### [BAJO] La transformación del texto de `core/read-more` usa una cadena de reemplazo insegura

- **Fichero:** `functions.php`, filtro `render_block_core/read-more`.
- **Que pasa:** Se usa el texto escapado como parte de la cadena de reemplazo de `preg_replace()`:

  ```php
  return preg_replace(
      '#(<a[^>]*wp-block-read-more[^>]*>).*?(</a>)#s',
      '$1' . esc_html( $label ) . '$2',
      $block_content,
      1
  );
  ```

  Si el texto contiene secuencias especiales de reemplazo como `$1` o `$2`, puede ser interpretado por `preg_replace()` como una referencia, no como texto literal.

- **Por que importa:** Puede alterar el texto mostrado o producir resultados inesperados con etiquetas personalizadas. No es, por sí solo, un bypass de seguridad porque el texto se escapa, pero sí un error de robustez.

- **Como se arregla:** Usar `preg_replace_callback()` y devolver la concatenación desde el callback, o modificar el nodo de texto mediante un parser HTML.

### [BAJO] El CSS crítico fija valores de diseño por encima de la configuración del sitio

- **Fichero:** `functions.php`, función `convoca_critical_css()`.
- **Que pasa:** Se imprimen variables y estilos globales directamente en `<head>`:

  ```css
  :root { --wp--preset--color--naranja: #ff8700; ... }
  body { ... font-family: 'Lato', sans-serif; ... }
  ```

- **Por que importa:** Los valores pueden entrar en conflicto con `theme.json`, los Estilos Globales y los estilos personalizados del usuario. Además, `overflow-x: hidden` puede ocultar contenido que desborde por un error de layout, impidiendo que el usuario pueda acceder a él.

- **Como se arregla:** Declarar presets y estilos en `theme.json`. Reservar el CSS crítico para reglas estrictamente necesarias para el primer render y utilizar variables ya generadas por WordPress, sin redefinirlas con valores de marca.

### [BAJO] Hay cadenas visibles sin internacionalizar

- **Fichero:** `functions.php`, metabox de eventos y avisos administrativos.
- **Que pasa:** Varias cadenas se imprimen directamente:

  ```php
  add_meta_box( 'convoca_event_meta', 'Evento', ... );
  ```

  ```php
  <label>Fecha y hora de inicio</label>
  ```

  ```php
  <p>Rellena estos campos solo si quieres que Google indexe...</p>
  ```

  y el aviso de rate limit contiene texto literal en español.

- **Por que importa:** Aunque el resto del fichero usa el dominio `convoca`, estas cadenas no aparecen en los catálogos de traducción y no se pueden traducir desde WordPress.

- **Como se arregla:** Envolver todas las cadenas visibles en `__()`, `esc_html__()` o equivalentes con el dominio `convoca`, incluyendo textos del metabox, avisos y cualquier texto visible en HTML generado.

### [BAJO] El HTML de la página de ayuda está estructuralmente desbalanceado

- **Fichero:** `functions.php`, función `convoca_help_page_html()`, sección “Guía de Plantillas y Páginas”.
- **Que pasa:** Después de cerrar el primer `.postbox`, se imprime el encabezado de la guía sin abrir otro `.postbox`:

  ```php
  </div>

  <h2 class="hndle">...</h2>
  <div class="inside">
  ```

  Después se cierran varios `div` que no corresponden claramente a los contenedores abiertos.

- **Por que importa:** El marcado del panel de administración puede quedar mal anidado, afectando al layout, estilos de WordPress y lectores de pantalla.

- **Como se arregla:** Envolver la guía en su propio:

  ```html
  <div class="postbox">
      <h2 class="hndle">...</h2>
      <div class="inside">...</div>
  </div>
  ```

  y revisar el número de cierres de `.postbox-container`, `#dashboard-widgets` y `#dashboard-widgets-wrap`.

### [DUDA] Uso de `data-wp-*` e Interactividad de WordPress

- **Fichero:** `functions.php`.
- **Que pasa:** En esta área no se utilizan directivas de la API de Interactividad (`data-wp-class--*`, `data-wp-context`, etc.). El selector de idioma y el acordeón implementan su propio estado mediante clases y `setAttribute()`.

- **Por que importa:** No es un fallo de la API en sí, pero si el tema pretende integrarse con bloques interactivos de WordPress 7.x, este JavaScript paralelo puede interferir con el estado que mantenga el bloque de navegación. No se puede confirmar el impacto sin ver el HTML final de las plantillas y los scripts de `assets/js/`.

- **Como se arregla:** Comprobar el DOM final de `core/navigation` y decidir una única fuente de estado. Si el bloque utiliza Interactividad, no modificar manualmente sus clases o atributos desde otro listener salvo que se haga mediante la API correspondiente.

**Aspectos revisados que no presentan un fallo evidente:** el reseteo de plantillas sí está protegido por capacidad y nonce; los campos del metabox se escapan al mostrarse; las URLs del selector de idioma se pasan por `esc_url()`; y el JSON-LD se serializa con `wp_json_encode()`.