### [MEDIO] Los controles de formulario pierden el indicador de foco visible
- **Fichero:** `style.css`, reglas de formularios (`.convoca-form ...:focus` y `.dark-mode input:focus`)
- **Qué pasa:** Las reglas aplican `outline: none` a todos los controles cuando reciben foco:

```css
.convoca-form input:focus,
.convoca-form textarea:focus,
.convoca-form select:focus,
.wp-block-search__input:focus {
    outline: none;
}
```

y en modo oscuro:

```css
.dark-mode input:focus,
.dark-mode textarea:focus,
.dark-mode select:focus {
    outline: none;
}
```

Aunque existe un `:focus-visible` global, estas reglas tienen mayor especificidad y afectan también al foco por teclado. La sombra usada como sustituto es muy tenue, especialmente en modo oscuro:

```css
box-shadow: 0 0 0 4px rgba(255, 171, 0, 0.03) !important;
```

- **Por qué importa:** Las personas que navegan con teclado pueden perder la referencia visual del control activo. El anillo oscuro de `0.03` de opacidad puede ser prácticamente imperceptible y no proporciona un indicador WCAG fiable.
- **Cómo se arregla:** No eliminar el `outline` general. Aplicar el estilo únicamente a `:focus-visible` y usar un anillo con contraste suficiente:

```css
.convoca-form input:focus-visible,
.convoca-form textarea:focus-visible,
.convoca-form select:focus-visible,
.wp-block-search__input:focus-visible {
    outline: 3px solid var(--wp--preset--color--amarillo);
    outline-offset: 2px;
    box-shadow: none;
}
```

Si se quiere mantener una sombra, debe ser claramente visible y probarse sobre fondos claros y oscuros.

---

### [MEDIO] Los enlaces de submenú en modo oscuro tienen contraste insuficiente
- **Fichero:** `style.css`, sección `23. JERARQUÍA CLARA EN LOS SUBMENÚS DEL DESPLEGABLE`
- **Qué pasa:** Los enlaces de los submenús abiertos reciben siempre este color:

```css
.wp-block-navigation__responsive-container.is-menu-open
.wp-block-navigation__submenu-container
.wp-block-navigation-item__content {
    color: #59616a !important;
}
```

En modo oscuro, el panel tiene como fondo:

```css
.dark-mode .wp-block-navigation__responsive-container.is-menu-open {
    background: var(--dark-bg) !important;
}
```

Por tanto, el texto gris `#59616a` se utiliza sobre un fondo muy oscuro (`#1A0B16`). Además, el `!important` de esta regla gana al color claro declarado anteriormente para los enlaces del menú oscuro.

- **Por qué importa:** Los elementos secundarios del menú pueden quedar con contraste insuficiente en modo oscuro, especialmente en tamaños de texto normales. El problema afecta a la navegación principal, no a un elemento decorativo.
- **Cómo se arregla:** Separar los estilos claro y oscuro:

```css
.wp-block-navigation__responsive-container.is-menu-open
.wp-block-navigation__submenu-container
.wp-block-navigation-item__content {
    color: #59616a;
}

.dark-mode .wp-block-navigation__responsive-container.is-menu-open
.wp-block-navigation__submenu-container
.wp-block-navigation-item__content {
    color: var(--dark-text-secondary) !important;
}
```

También hay que comprobar el contraste del elemento abierto:

```css
color: #b05500 !important;
background-color: #fff2e0 !important;
```

porque ese color debe validarse sobre ese fondo, no solo sobre fondos claros genéricos.

---

### [MEDIO] El `overflow: hidden` posterior vuelve a recortar el desplegable de idioma
- **Fichero:** `style.css`, bloque `@media (max-width: 1199px)` del header
- **Qué pasa:** Primero se intenta permitir que el desplegable de idioma salga de la fila:

```css
.site-header > .wp-block-group > .wp-block-group.is-layout-flex {
    overflow: visible !important;
}
```

Pero más adelante, dentro del mismo breakpoint, se vuelve a declarar para el mismo selector:

```css
.site-header > .wp-block-group > .wp-block-group.is-layout-flex {
    ...
    overflow: hidden !important;
}
```

La segunda declaración aparece después y tiene la misma especificidad, por lo que gana. El comentario que indica que el desplegable debe “escapar del header” deja de ser cierto.

- **Por qué importa:** Un dropdown posicionado fuera de la fila puede quedar recortado, especialmente en móvil o cuando el menú está cerca del borde derecho. Es un fallo dependiente del contenido y puede no aparecer en todas las instalaciones.
- **Cómo se arregla:** No usar `overflow: hidden` en el contenedor que contiene el dropdown. Si se necesita recortar algún elemento concreto, aplicarlo a ese elemento, no a la fila:

```css
.site-header > .wp-block-group > .wp-block-group.is-layout-flex {
    overflow: visible !important;
}
```

También conviene comprobar si el recorte original procede realmente de otro contenedor del marcado.

---

### [MEDIO] El CSS introduce texto visible no traducible
- **Fichero:** `style.css`, reglas de tarjetas de precio y listas
- **Qué pasa:** Hay textos generados directamente desde CSS:

```css
.convoca-price-card.featured::before {
    content: 'Popular';
}
```

y también:

```css
.price-features li::before {
    content: '✓ ';
}
```

- **Por qué importa:** `Popular` es contenido visible del tema, pero no pasa por la API de traducciones ni por el dominio `convoca-theme`. En una instalación en español, francés u otro idioma aparecerá siempre en inglés. Además, el texto generado con `content` no es una solución adecuada para contenido semántico.
- **Cómo se arregla:** Mover el texto a un nodo HTML generado por el patrón o por el contenido del sitio, de forma que pueda traducirse. Para el icono de marca de las características, usar un elemento decorativo separado o un pseudo-elemento sin texto:

```css
.price-features li::before {
    content: "";
    /* dibujar el indicador o usar un SVG decorativo */
}
```

El tema es genérico, pero esta cadena sí está incrustada en el tema y no procede de la instalación.

---

### [BAJO] Las imágenes reciben un radio global que también afecta a logos e imágenes no decorativas
- **Fichero:** `style.css`, regla global de imágenes
- **Qué pasa:** Se aplica a todas las imágenes del sitio:

```css
img {
    border-radius: var(--wp--custom--radius--small);
    max-width: 100%;
    height: auto;
}
```

Después se necesitan múltiples excepciones:

```css
.convoca-card img,
.is-style-card > .wp-block-post-featured-image img {
    border-radius: 0;
}
```

Pero no hay excepciones generales para logos, avatares, iconos rasterizados, imágenes dentro de tablas o imágenes cuyo contenido necesite esquinas cuadradas.

- **Por qué importa:** La regla altera globalmente el contenido aportado por el sitio y puede producir resultados visuales incorrectos en bloques que no son tarjetas. En particular, puede redondear logos, capturas, diagramas o imágenes de contenido que deberían conservar su geometría.
- **Cómo se arregla:** Limitar el radio a los componentes que lo necesitan:

```css
.convoca-card img,
.convoca-hero__foto img,
.convoca-single__imagen img,
.is-style-elevated img {
    border-radius: var(--wp--custom--radius--small);
}
```

Y dejar las imágenes genéricas sin una presentación específica del tema.

---

### [BAJO] El uso de `transition: all` y `backdrop-filter` puede ser costoso en listados largos
- **Fichero:** `style.css`, reglas de tarjetas en modo oscuro
- **Qué pasa:** Cada entrada de un listado puede recibir simultáneamente:

```css
.dark-mode .wp-block-post {
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    transition: all 0.5s ease;
}
```

y en hover:

```css
.dark-mode .wp-block-post:hover {
    background: var(--dark-card-hover) !important;
}
```

También se aplica el mismo enfoque a `.convoca-card` e `.is-style-card`.

- **Por qué importa:** `backdrop-filter` fuerza composición y puede resultar costoso en móviles o en consultas con muchas entradas. `transition: all` puede animar propiedades no deseadas y dificulta controlar el coste de repintado.
- **Cómo se arregla:** Animar únicamente las propiedades necesarias y reservar el desenfoque para componentes pequeños o destacados:

```css
.dark-mode .wp-block-post {
    transition:
        transform 0.25s ease,
        background-color 0.25s ease,
        box-shadow 0.25s ease;
}
```

Si los listados pueden contener muchas entradas, usar un fondo opaco o aplicar `backdrop-filter` solo a la cabecera, hero y tarjetas destacadas.

---

### [DUDA] El estado de submenú depende de una clase personalizada no verificable en este fichero
- **Fichero:** `style.css`, selectores `.convoca-sub-abierto`
- **Qué pasa:** La apertura del submenú depende de que JavaScript añada y retire correctamente esta clase:

```css
.convoca-sub-abierto > .wp-block-navigation__submenu-container {
    display: block !important;
}
```

La CSS fuerza todos los submenús a `display: none !important` y solo la clase personalizada los abre.
- **Por qué importa:** Si la clase no se sincroniza con el estado real de WordPress, el submenú puede quedar permanentemente cerrado, abierto sin corresponder al estado accesible, o no actualizar `aria-expanded`.
- **Cómo se arregla:** Falta revisar el JavaScript que gestiona `.convoca-sub-abierto`. Debe sincronizar como mínimo:
  - la clase visual;
  - `aria-expanded`;
  - el foco;
  - la apertura/cierre al pulsar el botón del submenú.

No lo clasifico como fallo confirmado porque ese código no está incluido.

No detecto problemas de seguridad de salida, nonces o saneado en esta hoja de estilos: esos riesgos pertenecerían al PHP o al JavaScript que genera el marcado. Sí hay una cantidad importante de reglas duplicadas y parches con `!important`, pero los problemas confirmables son los conflictos concretos anteriores, especialmente el `overflow` del dropdown y los estados de foco/contraste.