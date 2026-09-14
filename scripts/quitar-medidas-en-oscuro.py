#!/usr/bin/env python3
"""Quita de las reglas de modo oscuro las declaraciones que cambian MEDIDAS.

Deja intacto todo lo que sea color. Escribe el fichero y saca el resumen de lo quitado,
para poder revisarlo (y revertirlo con git si algo se descompone).

Uso: python3 quitar-medidas-en-oscuro.py style.css
"""
import re
import sys

RUTA = sys.argv[1] if len(sys.argv) > 1 else "style.css"
s = open(RUTA, encoding="utf-8").read()
original = s

# Declaraciones de medida que se quitan. Se comparan por nombre de propiedad.
QUITAR = ("padding", "margin", "border-radius", "font-size", "line-height", "letter-spacing",
          "font-weight", "font-family", "text-transform", "gap", "flex-basis",
          "min-width", "min-height", "max-width", "max-height", "width", "height")
# Se conservan a propósito: no mueven nada por sí solas, o son el cambio de icono del botón.
CONSERVAR = ("position", "inset", "top", "left", "right", "bottom", "display", "padding-block")
ESTADOS = (":hover", ":focus", ":focus-visible", ":active", ":focus-within")

salida = []
quitadas = []
i = 0
while True:
    j = s.find("{", i)
    if j < 0:
        salida.append(s[i:])
        break
    k = s.find("}", j)
    if k < 0:
        salida.append(s[i:])
        break
    cabecera = s[i:j]
    cuerpo = s[j + 1:k]
    selector = " ".join(cabecera.split()).split("{")[-1]
    es_oscuro = bool(re.search(r"\.dark-mode(?![-\w])", selector)) and "dark-mode-toggle" not in selector
    es_estado = any(e in selector for e in ESTADOS)

    if es_oscuro and not es_estado and cuerpo.strip():
        nuevas = []
        for linea in cuerpo.split("\n"):
            decl = linea.strip().rstrip(";")
            if ":" not in decl or decl.startswith("/*") or decl.startswith("*"):
                nuevas.append(linea)
                continue
            prop = decl.split(":")[0].strip().lower()
            if any(prop == c or prop.startswith(c + "-") for c in CONSERVAR):
                nuevas.append(linea)
                continue
            if any(prop == q or prop.startswith(q + "-") for q in QUITAR):
                quitadas.append((selector[:60], decl[:70]))
                continue
            nuevas.append(linea)
        cuerpo = "\n".join(nuevas)
        # limpiar líneas en blanco de más
        cuerpo = re.sub(r"\n{3,}", "\n\n", cuerpo)

    salida.append(cabecera + "{" + cuerpo + "}")
    i = k + 1

nuevo = "".join(salida)
open(RUTA, "w", encoding="utf-8").write(nuevo)

print("  declaraciones de medida quitadas: " + str(len(quitadas)))
for sel, decl in quitadas:
    print("    " + sel.ljust(52) + " | " + decl)
sin = re.sub(r"/\*.*?\*/", "", nuevo, flags=re.S)
print("")
print("  llaves: " + ("OK" if sin.count("{") == sin.count("}") else "DESCUADRADO"))
print("  bytes: " + str(len(original)) + " -> " + str(len(nuevo)))
