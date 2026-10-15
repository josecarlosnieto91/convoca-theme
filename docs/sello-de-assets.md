# El `?ver=` de los assets: quién sella y por qué

**Decidido el 16/10/2026 (`convoca-core#19`, punto A11 del plan de cierre).** Criterio de hecho: `?ver=2.3.35` en el
CSS común de los plugins, `?ver=2.5.24` en los de turnos, `?ver=0.2.17` en los del asistente y el CSS del **tema** con
**su fecha de fichero**.

## El reparto, en una frase

| Asset | Sello | Por qué |
|---|---|---|
| Ficheros del **tema** (`/themes/convoca/…`) | **fecha del fichero** | La versión del tema cambia poco y el fichero puede cambiar sin subirla: sin sello, el navegador sirve copia vieja y se acaba midiendo sobre un estado que no es el del fichero. |
| Assets de los **plugins del ecosistema** (`/plugins/convoca-…`) | **la versión del plugin** (su `?ver=`) | Esa versión **se sube en cada release y en cada despliegue**: ya cumple la función de sello. Sellar por fecha la tapaba, y la comprobación de la batería espera verla. |
| Núcleo de WordPress y plugins ajenos | **sin versión** | Es el endurecimiento heredado: no publicar la versión instalada. |

## La historia, porque el porqué es el que evita volver a cambiarlo

1. Al principio la función **quitaba** el `?ver=` a todo (ocultar la versión del núcleo). Efecto colateral real:
   un arreglo desplegado en un plugin podía **no llegar nunca** al navegador — el usuario seguía con la copia
   vieja en caché.
2. Se corrigió sellando por **fecha de fichero** los assets del tema… y de paso también los de los plugins.
3. El sello por fecha en los plugins tapaba la versión real: la medida de la batería decía «fecha» donde esperaba
   `2.3.35`, `2.5.24` o `0.2.17`. **Se acotó al tema** (esta spec), con el motivo anotado en el comentario del
   filtro.

Así que los dos extremos tienen un incidente detrás: **quitar el sello rompe las actualizaciones; sellar por fecha
lo que ya tiene versión esconde la versión**. De ahí el reparto por tipo de asset y no un criterio único.

## Dónde vive

`functions.php` → `convoca_fse_remove_version()`, enganchada a `style_loader_src` y `script_loader_src`.
Su prueba: `tests/Unit/AssetVersionTest.php` (un caso por fila de la tabla).
