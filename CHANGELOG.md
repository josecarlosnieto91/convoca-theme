## [2.9.38] — 2026-10-16

### Arreglado
- **La frase de «no se ha encontrado nada» ya no se rompe cuando faltan los enlaces.** Traía dos enlaces dentro
  (`{activities_url}` y `{news_url}`) y, si el sitio no tiene esas URL configuradas, el tema los retira: medido
  en la demo, el visitante leía «Nothing was found for those terms. Try another word or check the  and .» — con
  el hueco en medio, en cualquier idioma. Ahora la frase se sostiene sola y se traduce entera.

