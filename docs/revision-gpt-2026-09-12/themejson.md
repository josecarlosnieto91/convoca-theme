### [ALTO] Contraste insuficiente en los encabezados y estados de foco
- **Fichero:** `theme.json`, `styles.elements.h1`–`h4`, `styles.elements.link:focus`
- **Que pasa:** Los encabezados usan `#ff8700` mediante `var(--wp--preset--color--naranja)` sobre el fondo base `#ffffff` (`hueso`). El contraste aproximado es de 2,4:1, inferior al mínimo WCAG AA incluso para texto grande (3:1). El foco de los enlaces también usa naranja tanto para el texto como para el `outline`.
- **Por que importa:** Los títulos pueden resultar difíciles de leer y el indicador de foco puede no distinguirse suficientemente, especialmente para usuarios con baja visión. Esto contradice la afirmación de cumplimiento WCAG 2.2 AA del `readme.txt`.
- **Como se arregla:** Usar un color más oscuro para los encabezados —por ejemplo `naranja-oscuro`— o reservar el naranja para fondos y elementos decorativos. Para el foco, usar un color con al menos 3:1 de contraste respecto al fondo adyacente, por ejemplo `carbon`, `antracita` o un color de foco específico. Verificar también todas las combinaciones con fondos oscuros y estados personalizados del editor.

### [MEDIO] Se usan valores `medium` como si fueran referencias a presets
- **Fichero:** `theme.json`, varias ubicaciones:
  - `settings.blocks.core/button.typography.fontSize`
  - `settings.blocks.core/navigation.typography.fontSize`
  - `styles.elements.button.typography.fontSize`
  - `styles.blocks.core/query-pagination.typography.fontSize`
  - `styles.blocks.core/post-excerpt.typography.fontSize`
  - `styles.blocks.core/search.typography.fontSize`
  - `styles.blocks.core/comments.typography.fontSize`
  - `styles.blocks.core/table.typography.fontSize`
- **Que pasa:** El preset se ha definido con el slug `medium`, pero en los estilos se utiliza `"medium"` en vez de `"var:preset|font-size|medium"`. En CSS, `medium` es además una palabra clave válida de `font-size`, por lo que puede terminar generando el tamaño CSS estándar del navegador, no el preset de `1.0625rem`.
- **Por que importa:** Esos bloques pueden no utilizar el tamaño tipográfico definido por el tema y pueden comportarse de forma distinta según el navegador. En las rutas bajo `settings.blocks`, el valor tampoco representa la forma habitual de activar un preset y puede ser ignorado o interpretado incorrectamente.
- **Como se arregla:** En `styles`, sustituir los valores por:
  ```json
  "fontSize": "var:preset|font-size|medium"
  ```
  En `settings.blocks`, usar la configuración booleana apropiada —por ejemplo `"fontSize": true` o `false`, según el objetivo— y dejar la aplicación del tamaño en `styles.blocks`.

### [MEDIO] El tema declara fuentes pero no las carga
- **Fichero:** `theme.json`, `settings.typography.fontFamilies`
- **Que pasa:** Las familias `Lato` y `Outfit` se declaran con `fontFace: []`, sin archivos locales ni reglas `@font-face`. Tampoco aparece en el material proporcionado ninguna carga de Google Fonts.
- **Por que importa:** En instalaciones que no tengan esas fuentes disponibles, el navegador utilizará las fuentes de reserva. La tipografía descrita en el `readme.txt` —“Outfit + Lato con Google Fonts”— no queda garantizada.
- **Como se arregla:** Elegir una de estas opciones:
  - incluir las fuentes legalmente en el tema y declarar sus archivos mediante `fontFace`;
  - cargarlas desde un plugin o desde la instalación, documentando esa dependencia;
  - eliminar los nombres de fuentes no disponibles y usar una pila de fuentes del sistema.
  
  Si se usan fuentes remotas, conviene valorar privacidad, consentimiento y rendimiento, además de no depender de un servicio externo para que el tema sea visualmente correcto.

### [MEDIO] La paleta y varios colores están fijados por el tema pese a que la instalación debe aportarla
- **Fichero:** `theme.json`, `settings.color.palette`, `styles.color`, `styles.elements` y varios estilos de bloque
- **Que pasa:** El archivo define una paleta completa con nombres y colores concretos, y además utiliza colores rígidos fuera de presets, por ejemplo:
  ```json
  "text": "#1A1D24"
  ```
  y:
  ```json
  "background": "#e67300"
  ```
  También se referencian directamente variables como:
  ```json
  "var(--wp--preset--color--naranja)"
  ```
- **Por que importa:** Si el plugin o los Estilos Globales de la instalación sustituyen la paleta, cambian sus slugs o eliminan esos presets, partes del diseño pueden quedar con variables CSS inexistentes o conservar colores impuestos por el tema. Esto contradice el carácter genérico indicado: la instalación debería poder aportar la identidad visual sin tener que luchar contra valores de presentación codificados en el tema.
- **Como se arregla:** Definir únicamente defaults neutrales en el tema o usar una capa semántica estable que la instalación pueda sobrescribir. Evitar colores hexadecimales directos en los estilos y centralizar los valores en presets que estén garantizados. Si la paleta debe ser completamente responsabilidad del sitio, no incluir esta paleta fija en el tema y documentar los slugs contractuales que el plugin debe registrar.

### [BAJO] Presets duplicados o prácticamente equivalentes en la interfaz
- **Fichero:** `theme.json`, `settings.color.palette`, `settings.color.gradients` y `settings.shadow.presets`
- **Que pasa:** Hay varios presets con valores idénticos o redundantes:
  - `violeta` y `granate` usan el mismo color.
  - `blanco` y `hueso` usan `#ffffff`.
  - `amarillo` y `ambar` usan `#ffab00`.
  - `stats-dark` y `cta-dark` usan el mismo degradado.
  - Existen dos conceptos de sombra elevados (`elevated` y `elevada`) con nombres y valores distintos, además de `card` y `tarjeta`.
- **Por que importa:** El editor muestra opciones indistinguibles o difíciles de distinguir. También aumenta el riesgo de que patrones y estilos utilicen slugs distintos para el mismo concepto, dificultando futuras modificaciones de la identidad visual.
- **Como se arregla:** Eliminar duplicados o darles una finalidad visual inequívoca. Si son aliases deliberados por compatibilidad con patrones existentes, documentarlo y mantener una única convención para los nuevos archivos.

### [BAJO] El requisito mínimo declarado no es coherente con `theme.json` versión 3
- **Fichero:** `theme.json` y `readme.txt`
- **Que pasa:** El tema declara `"version": 3`, mientras que el `readme.txt` indica `Requires at least: 6.4`. La versión 3 de `theme.json` requiere soporte de versiones de WordPress más recientes que las primeras versiones compatibles con 6.4.
- **Por que importa:** En una instalación 6.4 el tema puede ignorar propiedades, producir estilos incompletos o no procesar correctamente algunas capacidades del archivo.
- **Como se arregla:** Si el tema solo soporta WordPress 6.6 o superior, elevar `Requires at least`. Si se mantiene 6.4 como requisito, usar una versión de `theme.json` compatible y probar explícitamente presets, estilos por bloque y plantillas en esa versión.

No se observan problemas de escapado, nonces, capacidades, consultas SQL, URLs no confiables ni hooks en esta parte: `theme.json` es un archivo declarativo y no ejecuta código. Tampoco se aprecia un problema funcional en las declaraciones de `templateParts` o `customTemplates` por sí mismas, aunque los nombres de los tipos `actividad` y las plantillas asociadas crean una dependencia de presentación con el plugin que registra ese CPT.