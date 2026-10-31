<?php
/**
 * Dobles para la descripción automática (theme#22).
 *
 * Van en su propio fichero y se cargan solo desde su test: el resto de la suite no necesita
 * un WP_Post ni un archivo de taxonomía.
 *
 * @package Convoca\Theme\Tests
 */

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Entrada mínima.
	 */
	class WP_Post {

		/** @var int */
		public $ID = 0;

		/** @var string */
		public $post_excerpt = '';

		/** @var string */
		public $post_content = '';

		/**
		 * Constructor.
		 *
		 * @param array<string,mixed> $datos Campos.
		 */
		public function __construct( array $datos = array() ) {
			foreach ( $datos as $clave => $valor ) {
				$this->$clave = $valor;
			}
		}
	}
}

if ( ! function_exists( 'get_queried_object' ) ) {
	/**
	 * Objeto consultado.
	 *
	 * @return mixed
	 */
	function get_queried_object() {
		return $GLOBALS['convoca_test_objeto'] ?? null;
	}
}

if ( ! function_exists( 'has_excerpt' ) ) {
	/**
	 * ¿Tiene extracto?
	 *
	 * @param mixed $post Entrada.
	 * @return bool
	 */
	function has_excerpt( $post = null ) {
		return is_object( $post ) && '' !== trim( (string) $post->post_excerpt );
	}
}

if ( ! function_exists( 'get_the_excerpt' ) ) {
	/**
	 * Extracto.
	 *
	 * @param mixed $post Entrada.
	 * @return string
	 */
	function get_the_excerpt( $post = null ) {
		return is_object( $post ) ? (string) $post->post_excerpt : '';
	}
}

if ( ! function_exists( 'strip_shortcodes' ) ) {
	/**
	 * Quita shortcodes.
	 *
	 * @param string $texto Texto.
	 * @return string
	 */
	function strip_shortcodes( $texto ) {
		return preg_replace( '/\[[^\]]*\]/', '', (string) $texto );
	}
}

if ( ! function_exists( 'wp_html_excerpt' ) ) {
	/**
	 * Recorta texto.
	 *
	 * @param string $texto     Texto.
	 * @param int    $longitud  Longitud.
	 * @param string $sufijo    Sufijo.
	 * @return string
	 */
	function wp_html_excerpt( $texto, $longitud, $sufijo = '' ) {
		$texto = (string) $texto;

		if ( mb_strlen( $texto ) <= $longitud ) {
			return $texto;
		}

		return mb_substr( $texto, 0, $longitud ) . $sufijo;
	}
}

if ( ! function_exists( 'is_tax' ) ) {
	/**
	 * ¿Archivo de taxonomía?
	 *
	 * @return bool
	 */
	function is_tax( $taxonomia = '', $termino = '' ) {
		return (bool) ( $GLOBALS['convoca_test_tax'] ?? false );
	}
}

if ( ! function_exists( 'is_category' ) ) {
	/**
	 * ¿Categoría?
	 *
	 * @return bool
	 */
	function is_category( $categoria = '' ) {
		return (bool) ( $GLOBALS['convoca_test_categoria'] ?? false );
	}
}

if ( ! function_exists( 'is_tag' ) ) {
	/**
	 * ¿Etiqueta?
	 *
	 * @return bool
	 */
	function is_tag( $etiqueta = '' ) {
		return (bool) ( $GLOBALS['convoca_test_etiqueta'] ?? false );
	}
}

if ( ! function_exists( 'get_the_archive_description' ) ) {
	/**
	 * Descripción del archivo.
	 *
	 * @return string
	 */
	function get_the_archive_description() {
		return (string) ( $GLOBALS['convoca_test_archivo_desc'] ?? '' );
	}
}
