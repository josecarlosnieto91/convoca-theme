#!/usr/bin/env python3
"""Audita el modo oscuro del tema: reglas que cambian MEDIDAS o TIPOGRAFÍA.

El modo oscuro debe cambiar el color. Si cambia rellenos, bordes, redondeos, tamaños o
interlineados, al alternar de modo la página se mueve. Este guion saca todas las reglas
que lo hacen, para revisarlas de una vez en lugar de ir elemento por elemento.

Uso: python3 auditar-modo-oscuro.py [ruta/style.css]
"""
import re
import sys

RUTA = sys.argv[1] if len(sys.argv) > 1 else "style.css"
S = open(RUTA, encoding="utf-8").read()
S = re.sub(r"/\*.*?\*/", "", S, flags=re.S)

GEOMETRICAS = ("padding", "margin", "border-width", "border-radius", "width", "height",
               "min-width", "min-height", "max-width", "max-height", "gap", "flex-basis",
               "font-size", "line-height", "letter-spacing", "font-weight", "font-family",
               "text-transform", "box-sizing", "grid-template", "flex-direction", "align-items", "justify-content", "order")
# `border` a secas sí mueve (ocupa sitio); `border-color` no.
COLOR = ("border-color", "border-top-color", "border-right-color", "border-bottom-color",
         "border-left-color", "background", "color", "box-shadow", "outline-color", "fill",
         "stroke", "text-decoration-color", "caret-color", "border-image")
ESTADOS = (":hover", ":focus", ":focus-visible", ":active", ":focus-within")

reglas = []
i = 0
while True:
    j = S.find("{", i)
    if j < 0:
        break
    k = S.find("}", j)
    if k < 0:
        break
    selector = " ".join(S[i:j].split())
    cuerpo = S[j + 1:k]
    reglas.append((selector, cuerpo))
    i = k + 1


def es_modo_oscuro(selector):
    """`.dark-mode` como ancestro o en el propio elemento, pero no `.dark-mode-toggle`."""
    if not re.search(r"\.dark-mode(?![-\w])", selector):
        return False
    # Si TODOS los selectores de la lista son el propio boton, no es una regla de modo.
    partes = [p.strip() for p in selector.split(",")]
    return any(re.search(r"\.dark-mode(?![-\w])", p) and "dark-mode-toggle" not in p for p in partes)


problemas = 0
for selector, cuerpo in reglas:
    if not es_modo_oscuro(selector):
        continue
    if any(e in selector for e in ESTADOS):
        continue
    # el cambio de icono sol/luna del boton no mueve nada
    if "dark-mode-toggle" in selector and "display" in cuerpo and "font" not in cuerpo:
        continue
    malas = []
    for linea in cuerpo.split(";"):
        decl = linea.strip()
        if not decl or ":" not in decl:
            continue
        prop = decl.split(":")[0].strip().lower()
        if prop in COLOR or any(prop.startswith(c) for c in COLOR if c != "color"):
            continue
        if prop in ("transition", "transition-property", "transform", "box-shadow"):
            continue
        if any(prop == g or prop.startswith(g + "-") or prop == g for g in GEOMETRICAS):
            malas.append(decl)
    if malas:
        problemas += 1
        print("  " + selector[:90])
        for m in malas:
            print("       " + m[:84])
        print("")

print("  RESULTADO: " + (str(problemas) + " regla(s) de modo oscuro cambian medidas o tipografía"
                         if problemas else "ninguna regla de modo oscuro cambia las medidas"))
sys.exit(1 if problemas else 0)
