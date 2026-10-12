# Comentarios de las plantillas

Estos comentarios explicaban las plantillas del tema y **se estaban sirviendo en el
HTML publico** de cada pagina, a cada visitante: pesan, desvelan la implementacion
interna y hacen que un barrido de tokens sin resolver de falsos positivos.

Se sacan del HTML y se guardan aqui, que es donde se leen. Los delimitadores de
bloque (`<!-- wp:... -->`) NO se tocan: son la plantilla, no documentacion.

## `parts/cta.html`

```
    CTA genérico: destino y etiqueta de cada botón vienen de convoca_theme_site_links().
        Si un enlace no está configurado, su botón no se muestra.
```

## `parts/footer.html`

```
    Pie: información ordenada y sin pared de enlaces.
        - Marca + contacto directo.
        - Navegación a las secciones principales.
        - Participación (los caminos para implicarse).
        - Legal + Creative Commons.
        Los tokens ({about_url}, {privacy_url}...) los resuelve el theme padre con los
        enlaces reales que declara el theme hijo; las redes las sirve el shortcode de redes de Convoca Core.
```

## `parts/header.html`

```
    Cabecera: UNA sola pieza de navegación.
        - Franja superior (carbón): menú "Superior" real + botón de modo + CTA.
        - Fila principal: marca + navegación principal (menú "Principal" real).
        Nada de rutas inventadas: los enlaces salen de los menús de WordPress, y el
        CTA de los filtros del theme hijo ({cta_url} / {cta_label}).
    
        Buscador: un único cuadro (abajo, ancho completo) que abren los dos botones de
        lupa — el de la franja en escritorio y el de la fila en móvil, igual que el
        botón de modo oscuro. El estado lo lleva la clase `--abierto`, no el atributo
        `hidden`: el editor de bloques reescribe el HTML al guardar y se lo comería.
```

## `parts/hero.html`

```
    Hero genérico: titulares y llamadas a la acción salen de datos del sitio y
        de los filtros del theme. Sin textos ni rutas de otra instalación:
          {site_name}   → nombre del sitio
          {site_tagline} → descripción corta (Ajustes → General)
          {activities_url} / {about_url} → enlaces del sitio (convoca_theme_site_links)
```

## `parts/sidebar.html`

```
    Barra lateral derecha: reproduce los widgets que hoy salen en todas las
        páginas del sitio (buscador, tarjetas y La Escuela,
        y los bloques de Recursos y Enlaces), con su contenido real.
```

## `templates/404.html`

```
    Página no encontrada: útil, con el lenguaje visual de la casa y salidas reales.
```

## `templates/archive.html`

```
    Archivo (categorías, etiquetas, fechas, autor): pensado para crecer a
         cientos de entradas sin degradarse. Cabecera editorial + rejilla de tarjetas
         y el buscador en un segundo plano, no dominando la página.
```

## `templates/home.html`

```
    Listado de entradas (home del blog). Mismo lenguaje editorial que el archivo.
```

## `templates/index.html`

```
    Índice del blog (fallback editorial).
```

## `templates/page-full-width.html`

```
    Página a ancho completo: para páginas cuyo contenido necesita todo el ancho
         disponible (cuadros, tablas, textos destacados) en lugar de la columna de lectura.
```

## `templates/page.html`

```
    Página (institucionales y de actividad): título, imagen destacada si la hay
         y el contenido real de la página intacto. Sin barra lateral dominante y con
         anchura de lectura cómoda.
```

## `templates/search.html`

```
    Resultados de búsqueda, con el mismo lenguaje editorial que el archivo.
```

## `templates/single.html`

```
    Entrada individual: título, metadatos, imagen grande, contenido con anchura
         cómoda de lectura, acciones y contenido relacionado. Sin barras laterales.
         El contenido existente se renderiza intacto con `post-content`, así que los
         shortcodes y bloques de plugins siguen funcionando.
```
