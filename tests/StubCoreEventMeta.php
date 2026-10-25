<?php
/**
 * Doble de `\Convoca\Core\event_meta()` para el tema.
 *
 * La meta del evento la lee y la escribe Convoca Core (`includes/event-meta.php`); el tema solo
 * delega. Este doble se carga **a petición** desde la prueba del esquema, porque el tema tiene una
 * rama para «sin Core activo» que solo se puede recorrer mientras la función no exista (PHP no sabe
 * retirar una función declarada).
 */

namespace Convoca\Core;

if ( ! function_exists( 'Convoca\\Core\\event_meta' ) ) {
	/**
	 * Meta de evento para las pruebas.
	 *
	 * @param int    $post_id  Entrada.
	 * @param string $meta_key Clave de la meta.
	 * @return string
	 */
	function event_meta( $post_id, $meta_key ) {
		return (string) ( $GLOBALS['convoca_test_event_meta'][ (int) $post_id ][ $meta_key ] ?? '' );
	}
}
