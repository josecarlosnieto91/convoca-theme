#!/usr/bin/env python3
"""
Comprueba que el auditor de modo oscuro caza lo que debe y no da falsos positivos,
y que el CSS del tema (y el que sirve producción) lo pasa.

Es una prueba puntual de esa herramienta, no una suite del tema:
    python3 scripts/verificar-auditor.py
"""
import glob
import os
import pathlib
import subprocess
import sys
import tempfile
import textwrap
import urllib.request

AQUI = pathlib.Path(__file__).resolve().parent
AUDITOR = AQUI / "auditar-modo-oscuro.py"
CSS = AQUI.parent / "style.css"
PRODUCCION = "https://biodevas.org/wp-content/themes/convoca/style.css"

CAZA = [
    ("forma corta border", ".dark-mode .x { border: 2px solid red; }"),
    ("border-left", ".dark-mode .x { border-left: 2px solid rgba(255,171,0,.15); }"),
    ("border-top", ".dark-mode .x { border-top: 1px solid #333; }"),
    ("border-width", ".dark-mode .x { border-width: 3px; }"),
    ("border-style", ".dark-mode .x { border-style: dashed; }"),
    ("relleno", ".dark-mode .x { padding: 9px; }"),
    ("radio", ".dark-mode .x { border-radius: 24px; }"),
    ("letra", ".dark-mode .x { font-size: 20px; }"),
    ("interlineado", ".dark-mode .x { line-height: 1.8; }"),
    ("el caso real que falló", ".dark-mode .wp-block-quote { border-left: 2px solid rgba(255,171,0,.15); }"),
]
QUIETO = [
    ("color de borde", ".dark-mode .x { border-left-color: rgba(255,171,0,.45); }"),
    ("border-color", ".dark-mode .x { border-color: transparent !important; }"),
    ("sombra interior", ".dark-mode .x { box-shadow: inset 0 0 0 1px #333; }"),
    ("color y fondo", ".dark-mode .x { color: #f4f6f8; background: #1c1f23; }"),
    ("outline", ".dark-mode .x:focus { outline: none; outline-offset: 2px; }"),
    ("transición", ".dark-mode .x { transition: color .3s ease; }"),
    ("botón del tema", ".dark-mode-toggle { width: 40px; height: 40px; border: none; }"),
    ("hover", ".dark-mode .x:hover { padding: 9px; }"),
    ("regla sin modo oscuro", ".x { border: 5px solid red; }"),
]
# Lo que deben haber dejado las correcciones en el CSS desplegado.
EFECTOS = {
    "la cita cambia solo de color": "border-left-color: rgba(255, 171, 0, 0.45)",
    "buscador: contenedor con línea interior": "inset 0 0 0 1px rgba(255, 135, 0, 0.12)",
    "buscador: campo": "border-color: transparent !important",
    "botón de contorno con sombra interior": "inset 0 0 0 1.5px rgba(255, 235, 220, 0.18)",
    "widgets con línea interior fusionada": "inset 0 0 0 1px var(--dark-border)",
}

fallos = []


def audita(css, esperado, nombre, etiqueta="  OK   "):
    with tempfile.NamedTemporaryFile("w", prefix="hermes-verify-", suffix=".css", delete=False, encoding="utf-8") as f:
        f.write(textwrap.dedent(css))
        ruta = f.name
    try:
        r = subprocess.run([sys.executable, str(AUDITOR), ruta], capture_output=True, text=True)
        ok = r.returncode == esperado and "RESULTADO" in (r.stdout + r.stderr)
        if not ok:
            fallos.append(nombre + " → salida " + str(r.returncode) + ", esperaba " + str(esperado))
        return ok
    finally:
        os.unlink(ruta)


def titulo(t):
    print("  ═══ " + t + " ═══")


def caso(nombre, css, esperado, etiqueta="  OK   "):
    print("    " + (etiqueta if audita(css, esperado, nombre) else "  FALLA") + "  " + nombre)


def contiene(texto, claves, prefijo=""):
    faltan = [n for n, a in claves.items() if a not in texto]
    for n in claves:
        print("    " + ("  OK   " if n not in faltan else "  FALLA") + "  " + prefijo + n)
    return not faltan


titulo("el auditor caza lo que debe (" + str(len(CAZA)) + " casos)")
for n, c in CAZA:
    caso(n, c, 1)
titulo("y no da falsos positivos (" + str(len(QUIETO)) + " casos)")
for n, c in QUIETO:
    caso(n, c, 0)

css = CSS.read_text(encoding="utf-8")
titulo("el CSS del tema")
caso("pasa el auditor", css, 0)
contiene(css, EFECTOS, "corregido: ")

titulo("lo que sirve producción")
try:
    servido = urllib.request.urlopen(PRODUCCION, timeout=30).read().decode("utf-8", "replace")
    version = servido.split("Version: ")[1].split("\n")[0] if "Version: " in servido else "?"
    caso("pasa el auditor · versión " + version, servido, 0)
    contiene(servido, EFECTOS, "desplegado: ")
except Exception as e:  # red caída, servidor caído…
    print("    AVISO  no se pudo comprobar producción: " + str(e))

titulo("higiene")
restos = [t for t in ("padding: 9px", "border-left: 1px solid red", "MARCA-ANCHO", "MARCA-ESTRECHO") if t in css]
llaves = css.count("{") - css.count("}")
for ok, txt in ((not restos, "sin restos de pruebas"), (llaves == 0, "llaves equilibradas (" + str(llaves) + ")")):
    print("    " + ("  OK   " if ok else "  FALLA") + "  " + txt)
if restos:
    fallos.append("restos de pruebas: " + ", ".join(restos))
if llaves:
    fallos.append("llaves desequilibradas")

sobrantes = glob.glob("/tmp/hermes-verify-*")
print("\n  temporales de esta prueba: " + (", ".join(sobrantes) if sobrantes else "ninguno"))
print("  RESULTADO: " + (str(len(fallos)) + " fallo(s)" if fallos else "todo correcto"))
for f in fallos:
    print("    · " + f[:150])
sys.exit(1 if fallos else 0)
