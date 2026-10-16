## [2.9.36] — 2026-10-16

### Añadido
- **Patrón «Inscripción para el anuncio» (`convoca/inscripcion-anuncio`), para las entradas de anuncio
  (theme#10).** El patrón hermano (`convoca/inscripcion-actividad`) está pensado para la ficha de la
  actividad: trae el shortcode **dinámico** `[convoca_inscripcion_actual]`, que solo resuelve donde se conoce
  la actividad actual. Puesto en una entrada de anuncio, los shortcodes desaparecen sin decir nada y el
  visitante ve un cuadro con título, lista vacía y sin formulario (medido en la demo).
  El nuevo trae el **bloque** del formulario con la actividad que se le indique, y como el bloque avisa
  cuando no tiene actividad, no deja el hueco en blanco.

### Notas
- Prueba nueva (`PatronAnuncioTest`): el patrón trae el bloque con su actividad y **no** el shortcode
  dinámico, que es justo lo que no resuelve en un anuncio.

