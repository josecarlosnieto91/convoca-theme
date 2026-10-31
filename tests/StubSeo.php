<?php
/**
 * Dobles para la descripción automática (theme#22).
 *
 * Solo se cargan desde su test: el resto de la suite no necesita un objeto consultado ni un
 * archivo de taxonomía.
 *
 * @package Convoca\Theme\Tests
 */

if ( ! function_exists( 'get_queried_object' ) ) {
	/** Devuelve el objeto consultado del escenario. */
	function get_queried_object() {
		return $GLOBALS['convoca_test_objeto'] ?? null;
	}
}

if ( ! function_exists( 'has_excerpt' ) ) {
	/** ¿La entrada tiene extracto? */
	function has_excerpt( $post = null ) {
		return is_object( $post ) && '' !== trim( (string) $post->post_excerpt );
	}
}

if ( ! function_exists( 'get_the_excerpt' ) ) {
	/** Devuelve el extracto de la entrada. */
	function get_the_excerpt( $post = null ) {
		return is_object( $post ) ? (string) $post->post_excerpt : '';
	}
}

if ( ! function_exists( 'strip_shortcodes' ) ) {
	/** Quita los shortcodes del texto. */
	function strip_shortcodes( $texto ) {
		return preg_replace( '/\[[^\]]*\]/', '', (string) $texto );
	}
}

if ( ! function_exists( 'wp_html_excerpt' ) ) {
	/** Recorta un texto a la longitud pedida. */
	function wp_html_excerpt( $texto, $longitud, $sufijo = '' ) {
		$texto = (string) $texto;

		if ( mb_strlen( $texto ) <= $longitud ) {
			return $texto;
		}

		return mb_substr( $texto, 0, $longitud ) . $sufijo;
	}
}

if ( ! function_exists( 'is_tax' ) ) {
	/** ¿Es un archivo de taxonomía? */
	function is_tax( $taxonomia = '', $termino = '' ) {
		return (bool) ( $GLOBALS['convoca_test_tax'] ?? false );
	}
}

if ( ! function_exists( 'is_category' ) ) {
	/** ¿Es una categoría? */
	function is_category( $categoria = '' ) {
		return (bool) ( $GLOBALS['convoca_test_categoria'] ?? false );
	}
}

if ( ! function_exists( 'is_tag' ) ) {
	/** ¿Es una etiqueta? */
	function is_tag( $etiqueta = '' ) {
		return (bool) ( $GLOBALS['convoca_test_etiqueta'] ?? false );
	}
}

if ( ! function_exists( 'get_the_archive_description' ) ) {
	/** Devuelve la descripción del archivo. */
	function get_the_archive_description() {
		return (string) ( $GLOBALS['convoca_test_archivo_desc'] ?? '' );
	}
}
