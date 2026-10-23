<?php
/**
 * Doble de `\Convoca\Core\site_stats()` para el tema.
 *
 * El patrón `stats` pide las cifras reales del sitio al núcleo y **no pinta nada** si no las hay.
 * Sin este doble, la prueba del patrón se salía por esa guarda y quedaba sin comprobar lo que sí
 * importa: que con datos pinte la franja con su valor y su rótulo.
 */

namespace Convoca\Core;

if ( ! function_exists( 'Convoca\\Core\\site_stats' ) ) {
	/**
	 * Cifras del sitio para las pruebas.
	 *
	 * @return array<string, array{value: string, label: string}>
	 */
	function site_stats() {
		return $GLOBALS['convoca_test_stats'] ?? array();
	}
}
