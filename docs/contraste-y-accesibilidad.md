# Contraste y accesibilidad — criterio del theme Convoca

Medido con **axe-core 4.10.2** (reglas `wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`).

## Criterio

El objetivo sigue siendo **WCAG 2.1 AA**. Pero **los colores de marca del tema no se
modifican para cuadrar el contraste**: si un color de la paleta se queda corto, se
acepta y se documenta aquí, con su medida. La paleta es una decisión de producto, no
un parámetro que se ajuste hasta que el medidor calle.

Esto es una decisión explícita de JC del **2026-10-12**, después de que yo cambiara el
tema para arreglar el contraste y el resultado no le gustara.

## Excepciones aceptadas (medidas, no supuestas)

| Caso | Colores | Medido | Mínimo AA |
|---|---|---|---|
| `.convoca-eyebrow` sobre fondo naranja (`convoca-participa`, `is-style-convoca-banda-naranja`) | `#b05500` sobre `#ff8700` | **2.1:1** | 4.5:1 |
| `h3` sobre fondo azul claro | `#b05500` sobre `#e3f2fd` | **4.44:1** | 4.5:1 |
| `h3` sobre fondo hueso beige | `#b05500` sobre `#f5f1e9` | **4.5:1** | 4.5:1 |
| Botón con colores elegidos en el editor | `#ffffff` sobre `#ff8700` (`has-blanco-color` + `has-naranja-background-color`) | **2.4:1** | 4.5:1 |

Los tres primeros son CSS del tema. El cuarto es **contenido**: los colores los eligió
quien editó la entrada, así que no se toca desde el tema.

## Lo que sí se corrigió (y no es contraste)

- El contraste de los **encabezados** venía del `theme.json`, y el CSS que llegaba al
  navegador podía ser el viejo por la caché de nginx (`fastcgi_cache`, 60 min). Al
  comprobar en producción hay que **purgar Redis y después nginx**, en ese orden.
- `.convoca-btn-primary` en el **panel de administración**: allí el CSS se carga sin
  el tema, así que las variables `--wp--preset--color-*` no existen y caía al naranja
  brillante con texto blanco (2.41:1). Se corrigió **solo el último fallback**, no la
  paleta.

## Cómo volver a medirlo

```
# la home y las internas, con axe-core 4.10.2 y recarga sin caché
# (Page.reload ignoreCache=True) o los resultados son del HTML cacheado
```
