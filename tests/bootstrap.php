<?php
/**
 * Bootstrap for Convoca Theme unit tests.
 */
// Load composer autoload.
$autoload = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

// Define ABSPATH for theme files.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' ); }
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true ); }

// Mock WP functions needed by theme.
if ( ! function_exists( '__' ) ) {
	function __( $t, $d = 'default' ) {
		return $t; } }
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $t ) {
		return htmlspecialchars( $t, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_attr__' ) ) {
	function esc_attr__( $t, $d = 'default' ) {
		return esc_attr( $t ); } }
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $t, $d = 'default' ) {
		return esc_html( $t ); } }
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $t ) {
		return htmlspecialchars( $t, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $h, $c, $p = 10, $a = 1 ) {
		return true; } }
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $h, $c, $p = 10, $a = 1 ) {
		// Registro real (no un simple true): los tests de tokens necesitan
		// comprobar que un valor configurado SI se sustituye.
		$GLOBALS['convoca_test_filtros'][ $h ][] = $c;
		return true; } }
if ( ! function_exists( 'add_theme_support' ) ) {
	function add_theme_support( $f, ...$a ) {
		return true; } }
if ( ! function_exists( 'register_block_style' ) ) {
	function register_block_style( $n, $a ) {
		return true; } }
if ( ! function_exists( 'register_block_pattern' ) ) {
	function register_block_pattern( $n, $a ) {
		return true; } }
if ( ! function_exists( 'register_block_pattern_category' ) ) {
	function register_block_pattern_category( $n, $a ) {
		return true; } }
if ( ! function_exists( 'load_theme_textdomain' ) ) {
	function load_theme_textdomain( $d, $p ) {} }
if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		return ABSPATH; } }
if ( ! function_exists( 'get_template_directory_uri' ) ) {
	function get_template_directory_uri() {
		return 'https://example.com/wp-content/themes/convoca'; } }
if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $h, $s = '', $d = [], $v = '', $m = 'all' ) {} }
if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $h, $s = '', $d = [], $v = '', $i = false ) {} }
if ( ! function_exists( 'wp_register_style' ) ) {
	function wp_register_style( $h, $s, $d = [], $v = '', $m = 'all' ) {
		return true; } }
if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode( $t, $c ) {
		return true; } }
if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return false; } }
if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $c, ...$a ) {
		return true; } }
if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $s = '' ) {
		return 'Convoca'; } }
if ( ! function_exists( 'wp_get_theme' ) ) {
	function wp_get_theme() {
		return new class() {
			public function get( $k ) {
				return '1.0'; }
			public function get_template() {
				return 'convoca'; }
			public function exists() {
				return true; }
		};
	}
}
if ( ! function_exists( 'get_theme_file_path' ) ) {
	function get_theme_file_path( $f = '' ) {
		return dirname( __DIR__ ) . ( $f ? '/' . $f : '' ); } }
if ( ! function_exists( 'get_theme_file_uri' ) ) {
	function get_theme_file_uri( $f = '' ) {
		return 'https://example.com/wp-content/themes/convoca/' . $f; } }
// Mocks que necesitan los filtros de render de bloques (tokens del pie).
if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $tag, $value ) {
		foreach ( $GLOBALS['convoca_test_filtros'][ $tag ] ?? array() as $cb ) {
			$value = $cb( $value );
		}
		return $value; } }
if ( ! function_exists( 'home_url' ) ) {
	function home_url( $p = '' ) {
		return 'https://example.com' . $p; } }
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $u ) {
		return $u; } }
if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $t ) {
		return $t; } }
if ( ! function_exists( 'do_shortcode' ) ) {
	function do_shortcode( $t ) {
		return $t; } }
if ( ! function_exists( 'has_filter' ) ) {
	function has_filter( $tag, $cb = false ) {
		$hay = ! empty( $GLOBALS['convoca_test_filtros'][ $tag ] );
		return $hay; } }
// Mocks del selector de idioma. VAN AQUÍ (fichero sin namespace): functions.php es
// del namespace global y no vería una función declarada dentro del namespace de un
// test. `icl_get_languages` solo lo consulta el selector (bloques core/navigation).
if ( ! function_exists( 'wp_unique_id' ) ) {
	function wp_unique_id( $prefix = '' ) {
		static $n = 0;
		return $prefix . ( ++$n ); } }
if ( ! function_exists( 'icl_get_languages' ) ) {
	function icl_get_languages( $args = '' ) {
		return array(
			array( 'language_code' => 'es', 'native_name' => 'Español', 'url' => 'https://example.com/', 'active' => true ),
			array( 'language_code' => 'en', 'native_name' => 'English', 'url' => 'https://example.com/en/', 'active' => false ),
		); } }
if ( ! function_exists( 'get_page_by_path' ) ) {
	function get_page_by_path( $p ) {
		return null; } }
if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $p = 0 ) {
		return 'https://example.com/'; } }
// Mocks para el sellado de assets por fecha de fichero (convoca_fse_remove_version).
if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', sys_get_temp_dir() . '/convoca-test-wp-contents' ); }
if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', 'https://example.com/wp-content' ); }
if ( ! function_exists( 'remove_query_arg' ) ) {
	function remove_query_arg( $clave, $url = '' ) {
		$partes = explode( '?', $url, 2 );
		if ( ! isset( $partes[1] ) ) {
			return $url; }
		parse_str( $partes[1], $q );
		unset( $q[ $clave ] );
		return $q ? $partes[0] . '?' . http_build_query( $q ) : $partes[0]; } }
if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( $clave, $valor, $url = '' ) {
		$partes = explode( '?', $url, 2 );
		parse_str( $partes[1] ?? '', $q );
		$q[ $clave ] = $valor;
		return $partes[0] . '?' . http_build_query( $q ); } }
date_default_timezone_set( 'Europe/Madrid' );
