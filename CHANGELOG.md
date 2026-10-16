## [2.9.40] — 2026-10-16

### Arreglado
- **El contrato de textos tenía un bloque muerto.** El bloque que revisa los textos que viven en los
  **atributos** de bloque (contenido, etiqueta, marcador…) había quedado detrás de un `continue;`, así que
  no se ejecutaba nunca: el contrato parecía vigilando y no vigilaba. Lo cazó el PHPCS del CI, no el propio
  contrato. Movido delante de la guarda y comprobado sembrando un infractor.
- Con el bloque vivo, los hallazgos son casi todos falsos positivos (un mismo nombre de atributo lleva
  colores, medidas y `var:preset|…`), ahora saltados; y uno real: la barra lateral decía `Type here` sin
  traducir.

## [2.9.39] — 2026-10-16

### Arreglado
- **Lo que quedaba en inglés** (theme#13):
  - La frase del enlace en el 404 («The link may be misspelled or the content may have moved…») y el subtítulo de
    la portada. No estaban traducidos **ni declarados**: el contrato que vigila los rótulos solo miraba textos de
    hasta 70 caracteres, así que una frase de 95 se le colaba.
  - «View more», «← Previous» y «Next →» vivían en **atributos** de bloque (`content`), no entre etiquetas, y el
    contrato solo miraba `>texto<`: el contrato tiene ahora los dos ojos, y por eso encontró también el subtítulo.
- **El título de una búsqueda sin resultados** decía «Página no encontrada». WordPress marca esas búsquedas como
  404 —no hay nada que enseñar, y es coherente—, pero el título de la página es el de los resultados con su
  buscador delante: ahora dice «Resultados para «X»», que es lo que de verdad es.

