# Atajos de verificación: los mismos comandos y la misma memoria que el CI.
# `make check` es la puerta de la casa: si pasa aquí, pasa allí.
.PHONY: test lint analyse check

# Pruebas unitarias.
test:
	vendor/bin/phpunit -c phpunit.xml.dist

# Solo el estándar de código.
lint:
	vendor/bin/phpcs

# Análisis estático. Con el límite de memoria por defecto (128M) PHPStan
# revienta con un «process crashed» que se lee como un error del código.
analyse:
	vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress --memory-limit=2G

# Lo que exige el CI, en el mismo orden.
check: lint analyse test
