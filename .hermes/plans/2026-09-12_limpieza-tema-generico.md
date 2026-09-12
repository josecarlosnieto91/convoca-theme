# Limpiar el tema para que sea genérico y vendible

**Motivo:** JC quiere un tema vendible y usable, no uno atado a esta instalación. La revisión
de ChatGPT encontró contenido de sitio dentro del tema y varias ataduras a la base de datos.

**Punto de retorno:** rama `fusion-tema-unico`, commit `cf987ee`. Todo lo de aquí va en commits
pequeños encima, así que cualquier fase se revierte con `git revert`.

## Lo que hay que sacar (inventario medido)

33 apariciones de contenido de instalación, en 10 ficheros:

| fichero | qué lleva |
|---|---|
| `parts/footer.html` | 3 rutas, un teléfono, una frase, `/contacto/` |
| `parts/sidebar.html` | 1 ruta, una frase, «Educación Ambiental» |
| `patterns/cta.php`, `hero.php`, `hero-imagen.php`, `proximas-actividades.php`, `cards-3.php` | rutas y frases |
| `templates/404.html`, `search.html` | 1 ruta cada uno |
| `templates/front-page.html` | 3 referencias `wp:block` a IDs de esta base de datos, 3 IDs de medios, 5 rutas, frases |

## Arquitectura de la limpieza

La regla: **el tema presenta, el sitio aporta los datos.**

1. **Pie y lateral** → ya existe el mecanismo de tokens (`{...}`) que el sitio rellena por
   filtros. Se sustituyen rutas, teléfono y frase por tokens nuevos, y el sitio da los valores.
2. **La portada** → el tema se queda con una portada genérica de muestra. La portada real de
   esta web se muda a la instalación como plantilla del sitio (`wp_template` en la base de
   datos), que es donde WordPress guarda lo que edita el usuario. Así el tema no depende de IDs
   ajenos y la web no cambia ni un píxel.
3. **Los patrones del tema** → se quedan neutros (sin rutas de este sitio). Los patrones reales
   ya viven en el sitio (los cuatro sincronizados).
4. **Plantillas muertas** → fuera. `archive-actividad`, `single-actividad`, `page-actividad` y
   `page-proyecto` dependen de un tipo de contenido que en esta web no se usa (1 entrada y sin
   archivo público). Borrarlas elimina de paso dos de los hallazgos de la revisión. Un fichero
   que se puede borrar no se rompe.

## Fases

**Fase 1 — Arreglos de código de la revisión (sin decisiones, cortos)**
- `convoca_event_schema()`: validar la fecha antes de generar el schema y tratar la zona horaria.
- `convoca_event_meta_save()`: descartar revisiones y autoguardados, comprobar el tipo.
- `convoca_theme_render_block()`: escape por contexto en las sustituciones.
- Botones de modo oscuro: `aria-pressed`.
- `patterns/stats.php`: validar la estructura antes de recorrerla.
- Verificación: PHPStan, PHPCS, receta de la demo.

**Fase 2 — Plantillas muertas**
- Borrar las cuatro plantillas que dependen del tipo `actividad`.
- Verificación: la receta de la demo (el verificador de plantillas cuenta bloques) y el render.

**Fase 3 — Contenido fuera del tema**
- Tokens nuevos en el pie y el lateral; el sitio da los valores en su plugin.
- Patrones del tema neutros.
- La portada real, a la instalación como plantilla del sitio; portada genérica en el tema.
- Verificación: comparación de capturas antes/después de la portada (deben salir iguales) y la
  receta completa.

**Fase 4 — Cierre para venta**
- README del tema con lo que ofrece y cómo se configura (sin datos de esta web).
- CHANGELOG al día.
- Theme Check, PHPStan, PHPCS y la receta, en verde.
- Barrido final: cero apariciones de contenido de instalación.

## Riesgos

- **Que la web cambie de aspecto.** Se controla con capturas comparadas antes/después de cada
  fase que toque la portada o el pie.
- **Que la portada del sitio se pierda al moverla a la instalación.** Se guarda su contenido en
  el repo (`patterns-del-sitio/`) antes de tocar nada.
- **Que el tema deje de servir para esta web.** No: el sitio sigue aportando todo por filtros,
  que es justo el mecanismo que ya usa.
