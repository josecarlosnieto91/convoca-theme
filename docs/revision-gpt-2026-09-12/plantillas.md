### [ALTO] Los marcadores `{...}` no son una sustitución nativa de WordPress

- **Fichero:** `parts/header.html`, `parts/footer.html`, `parts/cta.html`, `parts/hero.html`, `parts/sidebar.html` y otros.
- **Que pasa:** Se usan valores como `{cta_url}`, `{membership_url}`, `{site_title}`, `{contact_email}`, `{year}`, `{site_name}` y `{membership_label}` directamente en el marcado de bloques:

  ```html
  <a href="{membership_url}">{membership_label}</a>
  ```

  WordPress no interpreta esos tokens en plantillas FSE. Si no existe un filtro adicional que reemplace explícitamente cada token antes de renderizar, se mostrarán literalmente al usuario y también acabarán literalmente en atributos `href` y `mailto`.
- **Por que importa:** Puede producir enlaces inválidos, texto visible con llaves y atributos no válidos. Además, cualquier sustitución implementada por un plugin debe escapar según el contexto: `esc_url()` para URLs y `esc_html()` para texto.
- **Como se arregla:** Usar bloques dinámicos o bloques registrados por el plugin para estos datos. Si se mantiene el sistema de tokens, debe existir una capa de renderizado documentada que:
  - reemplace todos los tokens;
  - omita el bloque si la URL no existe;
  - aplique escape contextual;
  - no deje tokens sin resolver.

  No basta con declararlo en un comentario de la plantilla.

---

### [ALTO] Se ha convertido el tema genérico en una implementación específica de un sitio

- **Fichero:** Principalmente `parts/footer.html`, `parts/sidebar.html`, `templates/front-page.html`, `templates/404.html`, `templates/home.html`, `templates/index.html` y `templates/search.html`.
- **Que pasa:** Hay contenido, rutas, imágenes, teléfono y estructura propios de una instalación concreta:

  ```html
  <a href="/la-escuela/">La Escuela</a>
  <a href="/voluntariado/">Voluntariado</a>
  <a href="tel:+34621366648">621 366 648</a>
  ```

  y:

  ```html
  <!-- wp:image {"id":11104,...} /-->
  <img src="/wp-content/uploads/2026/04/Recogida-Voluntaria-08.jpg" ...>
  ```

  También hay textos como “Asociación socioambiental asturiana”, “La Escuela”, “¡Conciencia!” y “Todas nuestras actividades son gratuitas y abiertas”.
- **Por que importa:** En otra instalación el tema mostrará contenido falso, enlaces rotos o imágenes inexistentes. Además, contradice el requisito de que la marca, textos y enlaces los aporte la instalación.
- **Como se arregla:** Sustituir el contenido específico por:
  - bloques de sitio (`core/site-title`, `core/site-tagline`, navegación, logo);
  - bloques dinámicos proporcionados por el plugin del sitio;
  - patrones no sincronizados editables;
  - tokens correctamente procesados por una implementación segura, si ese mecanismo es realmente necesario.

  Las rutas absolutas o relativas específicas y los IDs de medios no deben formar parte del tema genérico.

---

### [ALTO] La consulta de actividades contiene bloques dinámicos dentro de `core/html`

- **Fichero:** `templates/archive-actividad.html`, aproximadamente dentro del `wp:html` de cada tarjeta.
- **Que pasa:** Se colocan bloques dinámicos dentro de un bloque HTML:

  ```html
  <!-- wp:html -->
  <p ...>
      <!-- wp:convoca-common/post-meta-field {"metaField":"_convoca_fecha_inicio","type":"date"} /-->
  </p>
  ...
  <!-- /wp:html -->
  ```

  `core/html` trata su contenido como HTML arbitrario; los comentarios de bloques anidados no se ejecutan como bloques independientes. Por tanto, `convoca-common/post-meta-field` puede terminar saliendo como comentario HTML o no renderizarse.
- **Por que importa:** Las tarjetas pueden mostrar las fechas, ubicación, precio y plazas vacías, o incluso el marcado de bloque sin procesar.
- **Como se arregla:** No envolver bloques dinámicos dentro de `wp:html`. Usar grupos y párrafos normales:

  ```html
  <!-- wp:paragraph {"className":"convoca-card-meta-date"} -->
  <p class="convoca-card-meta-date">
      <!-- wp:convoca-common/post-meta-field {"metaField":"_convoca_fecha_inicio","type":"date"} /-->
  </p>
  <!-- /wp:paragraph -->
  ```

  Si se necesita HTML complejo, registrar un bloque dinámico propio que genere todo el marcado en PHP y escape sus valores.

---

### [ALTO] Las referencias `wp:block` hacen que la portada dependa de IDs de una instalación concreta

- **Fichero:** `templates/front-page.html`, aproximadamente al principio y antes del footer.

  ```html
  <!-- wp:block {"ref":11968} /-->
  ...
  <!-- wp:block {"ref":11970} /-->
  <!-- wp:block {"ref":11969} /-->
  ```
- **Que pasa:** `ref` apunta a bloques reutilizables/patrones sincronizados almacenados en la base de datos, no a contenido incluido en el tema.
- **Por que importa:** Esos IDs no son portables entre instalaciones. En otra base de datos pueden no existir o apuntar a contenido distinto. Además, los patrones sincronizados no son un mecanismo para ejecutar PHP ni shortcodes; cualquier expectativa de que esos bloques ejecuten `[convoca_*]` o lógica PHP no se cumplirá.
- **Como se arregla:** Incluir patrones no sincronizados en el tema, usar bloques dinámicos registrados por el plugin o sustituir las referencias por bloques estándar editables. Si se quiere contenido administrable, debe crearse durante la activación/importación del sitio, no mediante IDs codificados en el tema.

---

### [ALTO] El botón “Ver detalles” del archivo de actividades nunca enlaza con la actividad

- **Fichero:** `templates/archive-actividad.html`, botón dentro de `wp:post-template`.

  ```html
  <a ... href="#" class="wp-block-post-link">Ver detalles</a>
  ```
- **Que pasa:** El enlace siempre apunta a `#`. Además, el HTML generado contiene dos atributos `class`:

  ```html
  <a class="wp-block-button__link ..." href="#" class="wp-block-post-link">
  ```
- **Por que importa:** Todas las tarjetas llevan al inicio de la página en lugar del permalink de la actividad. El segundo `class` tampoco es una serialización válida fiable.
- **Como se arregla:** Usar un bloque dinámico de enlace al contenido, por ejemplo un `core:read-more`, `core:post-title` enlazado o un bloque `core:button` cuya URL sea generada por un bloque propio. No debe escribirse manualmente `href="#"`.

---

### [MEDIO] La portada contradice su propia lógica de consulta y paginación

- **Fichero:** `templates/front-page.html`, primera consulta y consulta de “Historias”.

  ```html
  <!-- wp:query {"query":{"perPage":6,...,"inherit":false}} -->
  ```

  y:

  ```html
  <!-- wp:query {"query":{"perPage":4,"pages":1,...,"inherit":false}} -->
  ```
- **Que pasa:** Los comentarios indican que la consulta hereda la consulta principal y que `/page/2/` mostrará más contenido, pero ambas consultas declaran `inherit:false`. La consulta de historias además fija `pages:1` y contiene paginación.
- **Por que importa:** La portada no sigue la consulta principal del contexto y la consulta de historias puede quedar limitada a una sola página, haciendo que la paginación no tenga sentido. El comportamiento descrito en los comentarios no coincide con el JSON.
- **Como se arregla:** Decidir explícitamente el comportamiento:
  - para heredar la consulta principal, usar `inherit:true` y no fijar argumentos incompatibles;
  - para una sección independiente, mantener `inherit:false`, pero no afirmar que sigue la paginación principal;
  - eliminar `pages:1` si se pretende paginar esa consulta;
  - comprobar el comportamiento real de `WP_Query` del bloque en la URL de portada paginada.

---

### [MEDIO] La tarjeta reutilizable tiene una imagen y contenido vacíos

- **Fichero:** `parts/card-actividad.html` y `parts/card-proyecto.html`.

  ```html
  <img src="" alt="" />
  ```

  y:

  ```html
  <p class="convoca-tag"></p>
  <h3 class="wp-block-heading"></h3>
  <p ...></p>
  <a ...></a>
  ```
- **Que pasa:** Son partes de plantilla completas, pero no contienen bloques dinámicos ni mecanismos que rellenen la imagen, título, descripción, URL o etiqueta.
- **Por que importa:** Si se insertan como partes, producirán tarjetas vacías y un `src=""`, que puede provocar una petición innecesaria a la URL actual y una imagen rota. El `alt` vacío solo sería correcto para una imagen puramente decorativa, no para una imagen de tarjeta de contenido.
- **Como se arregla:** Convertirlas en una composición de bloques dinámicos (`post-featured-image`, `post-title`, `post-excerpt`, `read-more`) dentro de un `post-template`, o documentarlas claramente como plantillas incompletas que no se deben renderizar directamente.

---

### [MEDIO] El bloque de estilo del precio tiene el atributo `color` en una ubicación incorrecta

- **Fichero:** `parts/activity-meta.html`, párrafo de “PRECIO”.

  ```json
  {
    "style": {
      "typography": {
        "fontSize": "1rem",
        "fontWeight": "700",
        "color": {
          "text": "var:preset|color--naranja"
        }
      }
    }
  }
  ```
- **Que pasa:** `color` no pertenece dentro de `typography`; debe ser hermano de `typography`. Además, la referencia de preset está escrita como `var:preset|color--naranja`, mientras que en el resto del archivo se usa la forma `var:preset|color|naranja`.
- **Por que importa:** El editor puede descartar el ajuste, no reconocerlo como color del bloque o generar CSS inconsistente. El HTML manual contiene un color correcto, pero el comentario JSON y el HTML pueden divergir al editar y guardar la plantilla.
- **Como se arregla:**

  ```json
  {
    "style": {
      "typography": {
        "fontSize": "1rem",
        "fontWeight": "700"
      },
      "color": {
        "text": "var:preset|color|naranja"
      }
    }
  }
  ```

---

### [MEDIO] Muchas cadenas y etiquetas visibles no están internacionalizadas

- **Fichero:** Todas las partes y plantillas; ejemplos en `parts/activity-meta.html`, `parts/sidebar.html`, `templates/404.html`, `templates/archive.html` y `templates/single.html`.
- **Que pasa:** Hay numerosas cadenas escritas directamente en español:

  ```html
  <p>UBICACIÓN</p>
  <h2>¿No encuentras algo?</h2>
  <p>Esta página no está (o se fue de ruta)</p>
  <a ...>Ver más</a>
  ```
- **Por que importa:** Las plantillas FSE pueden traducirse, pero estas cadenas no contienen contexto ni están pasando por una API de traducción del tema. Un sitio que use otro idioma no podrá traducirlas correctamente desde los mecanismos normales.
- **Como se arregla:** Para contenido fijo del tema, usar cadenas traducibles del dominio del tema mediante bloques registrados/renderizados por PHP, o proporcionar patrones traducibles correctamente. Para contenido editorial y de marca, moverlo al contenido del sitio o a patrones editables. No convertir textos específicos de una instalación en cadenas fijas del tema.

---

### [BAJO] Los botones de modo oscuro no exponen su estado accesible

- **Fichero:** `parts/header.html`, los dos botones `.dark-mode-toggle`.
- **Que pasa:** Los botones solo tienen:

  ```html
  aria-label="Cambiar modo de color"
  ```

  pero no exponen si el modo está activo mediante `aria-pressed`, ni actualizan una descripción del estado.
- **Por que importa:** Un usuario de lector de pantalla recibe una acción genérica, pero no sabe cuál es el estado actual ni si el clic ha surtido efecto. Hay además dos controles equivalentes en la cabecera.
- **Como se arregla:** Implementar un único control por estado responsive, o sincronizar ambos, y actualizar:

  ```html
  aria-pressed="true|false"
  ```

  junto con una etiqueta accesible que indique “Activar modo oscuro” o “Activar modo claro”, según corresponda. El JavaScript debe mantener ambos estados sincronizados.

---

### [BAJO] Dependencia externa y contenido legal específico incrustados en el footer

- **Fichero:** `parts/footer.html`.

  ```html
  <img src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" ...>
  ```

  y:

  ```html
  <a href="http://creativecommons.org/licenses/by-nc/4.0/" ...>
  ```
- **Que pasa:** El tema carga una imagen desde un dominio externo, usa HTTP en el enlace legal y fija una licencia concreta.
- **Por que importa:** Introduce una dependencia externa, posible bloqueo por políticas de contenido y una URL no segura que puede redirigir. Además, la licencia no necesariamente será la de todas las instalaciones.
- **Como se arregla:** Hacer que la licencia y su imagen sean configurables por el sitio, usar `https://` y evitar cargar recursos externos desde el tema si no son imprescindibles.

No observo en estas plantillas consultas SQL directas, recepción de datos de usuario ni operaciones que requieran nonces o comprobación de capacidades. Esas áreas deben revisarse en los bloques dinámicos, shortcodes y filtros que generan el contenido referenciado aquí, especialmente `convoca_*` y `convoca-common/post-meta-field`.