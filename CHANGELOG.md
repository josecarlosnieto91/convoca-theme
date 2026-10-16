## [2.9.37] — 2026-10-16

### Arreglado
- **Los rótulos escritos a mano en las plantillas ya se traducen (docs#7).** Las plantillas y las partes son
  marcado de bloque y **no ejecutan PHP**, así que un texto escrito dentro no pasaba por ninguna traducción: en
  una web en castellano se leían «Latest», «Error 404», «Keep reading», «Privacy policy», «LOCATION», «SPOTS»…
  El tema ya resolvía así la etiqueta del botón de modo oscuro (#7); esto lo generaliza a **32 rótulos**, con el
  mismo mecanismo (`render_block`), sustituyendo **solo el texto que es todo el contenido de una etiqueta** —
  nunca un atributo: cambiar un `class` o un `href` por una traducción rompería el diseño o los enlaces.
- **Catálogo castellano al día**: 229 mensajes traducidos y **cero sin traducir**. Los que quedaban a cero eran
  nombres propios, direcciones, fuentes y tallas (Convoca, Lato, XS/XL…), que se traducen a sí mismos: su
  traducción es la misma cadena, y así el recuento dice la verdad.

### Notas
- Tres pruebas nuevas (`TextosDePlantillasTest`): el filtro no toca atributos, **cada rótulo a mano tiene que
  estar declarado** (si alguien vuelve a escribir uno, la prueba lo dice con el texto delante) y el catálogo no
  puede dejar nada sin traducir, que es el criterio del informe.

