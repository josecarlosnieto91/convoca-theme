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
if ( ! function_exists( 'is_search' ) ) {
	function is_search() {
		return (bool) ( $GLOBALS['convoca_test_is_search'] ?? false ); } }
if ( ! function_exists( 'get_search_query' ) ) {
	function get_search_query( $escaped = true ) {
		unset( $escaped );
		return (string) ( $GLOBALS['convoca_test_search_query'] ?? '' ); } }
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $h, $c, $p = 10, $a = 1 ) {
		// Registro real: sin él no se puede comprobar que un gancho quedó puesto.
		$GLOBALS['convoca_test_acciones'][ $h ][ (int) $p ][] = $c;
		return true; } }
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $h, $c, $p = 10, $a = 1 ) {
		// Registro real (no un simple true): los tests de tokens necesitan
		// comprobar que un valor configurado SI se sustituye.
		$GLOBALS['convoca_test_filtros'][ $h ][] = $c;
		return true; } }
if ( ! function_exists( 'add_theme_support' ) ) {
	function add_theme_support( $f, ...$a ) {
		$GLOBALS['convoca_test_theme_support'][ $f ] = $a[0] ?? true;
		return true; } }
if ( ! function_exists( 'register_block_style' ) ) {
	function register_block_style( $n, $a ) {
		$GLOBALS['convoca_test_estilos'][ $n ][] = $a['name'] ?? '';
		return true; } }
if ( ! function_exists( 'register_block_pattern' ) ) {
	function register_block_pattern( $n, $a ) {
		return true; } }
if ( ! function_exists( 'register_block_pattern_category' ) ) {
	function register_block_pattern_category( $n, $a ) {
		$GLOBALS['convoca_test_categorias'][ $n ] = $a;
		return true; } }
if ( ! function_exists( 'load_theme_textdomain' ) ) {
	function load_theme_textdomain( $d, $p ) {} }
if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		return ABSPATH; } }
if ( ! function_exists( 'get_template_directory_uri' ) ) {
	function get_template_directory_uri() {
		return 'https://example.com/wp-content/themes/convoca'; } }
// La función de sellado llama también a las del tema hijo. Faltaban: sin ellas, el caso
// «asset del tema» no se podía ni probar (el tema es su propia plantilla en el repo).
if ( ! function_exists( 'get_stylesheet_directory' ) ) {
	function get_stylesheet_directory() {
		return ABSPATH; } }
if ( ! function_exists( 'get_stylesheet_directory_uri' ) ) {
	function get_stylesheet_directory_uri() {
		return 'https://example.com/wp-content/themes/convoca'; } }
if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $h, $s = '', $d = [], $v = '', $m = 'all' ) {
		$GLOBALS['convoca_test_estilos_encolados'][ $h ] = compact( 's', 'd', 'v', 'm' ); } }
if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $h, $s = '', $d = [], $v = '', $i = false ) {
		$GLOBALS['convoca_test_scripts_encolados'][ $h ] = compact( 's', 'd', 'v', 'i' ); } }
if ( ! function_exists( 'wp_register_style' ) ) {
	function wp_register_style( $h, $s, $d = [], $v = '', $m = 'all' ) {
		return true; } }
if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode( $t, $c ) {
		return true; } }
if ( ! function_exists( 'is_admin' ) ) {
	// Controlable: el restablecimiento de plantillas solo actúa en el escritorio, así que la
	// prueba tiene que poder ponerse en ese contexto (y en el otro).
	function is_admin() {
		return (bool) ( $GLOBALS['convoca_test_is_admin'] ?? false ); } }
if ( ! function_exists( 'current_user_can' ) ) {
	// Por defecto concede; si la prueba escribe en convoca_test_caps, manda lo que diga ella.
	function current_user_can( $c, ...$a ) {
		if ( isset( $GLOBALS['convoca_test_caps'][ $c ] ) ) {
			return (bool) $GLOBALS['convoca_test_caps'][ $c ]; }
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
if ( ! function_exists( 'has_site_icon' ) ) {
	// El tema emite su favicon SOLO si el sitio no tiene icono propio (Ajustes →
	// Identidad del sitio). Las pruebas controlan la respuesta con esta bandera.
	function has_site_icon() {
		return ! empty( $GLOBALS['_test_has_site_icon'] );
	}
}
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
		// Sin el interruptor, el shortcode se devuelve tal cual (lo que esperan las pruebas que ya había).
		if ( empty( $GLOBALS['convoca_test_shortcodes'] ) ) {
			return $t;
		}
		$GLOBALS['convoca_test_resoluciones'] = ( $GLOBALS['convoca_test_resoluciones'] ?? 0 ) + 1;
		// Reentrada: el shortcode vuelve a renderizar el mismo bloque, como el patrón que se contiene a sí mismo.
		if ( ! empty( $GLOBALS['convoca_test_reentrar'] ) ) {
			$GLOBALS['convoca_test_reentrar'] = false;
			convoca_theme_render_block( $t, array( 'blockName' => 'core/block' ) );
		}
		return 'RESUELTO'; } }
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
			array(
				'language_code' => 'es',
				'native_name'   => 'Español',
				'url'           => 'https://example.com/',
				'active'        => true,
			),
			array(
				'language_code' => 'en',
				'native_name'   => 'English',
				'url'           => 'https://example.com/en/',
				'active'        => false,
			),
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
if ( ! function_exists( 'content_url' ) ) {
	function content_url( $ruta = '' ) {
		return 'https://example.com/wp-content' . $ruta; } }
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
// Las versiones que IMPRIMEN: los patrones de bloque están llenos de `esc_html_e()` (31 llamadas)
// y sin ellas no se podía ni incluir un patrón en una prueba.
if ( ! function_exists( 'esc_html_e' ) ) {
	function esc_html_e( $t, $d = 'default' ) {
		echo esc_html( $t ); } }
if ( ! function_exists( 'esc_attr_e' ) ) {
	function esc_attr_e( $t, $d = 'default' ) {
		echo esc_attr( $t ); } }
// ── Dobles que faltaban para poder recorrer el arranque del tema ──────────────
if ( ! function_exists( 'add_editor_style' ) ) {
	function add_editor_style( $f ) {
		$GLOBALS['convoca_test_editor_style'][] = $f; } }
if ( ! function_exists( 'remove_action' ) ) {
	function remove_action( $h, $c, $p = 10 ) {
		$GLOBALS['convoca_test_quitados'][] = array( $h, $c, (int) $p );
		return true; } }
if ( ! function_exists( 'add_image_size' ) ) {
	function add_image_size( $n, $w = 0, $h = 0, $crop = false ) {
		$GLOBALS['convoca_test_tamanos'][ $n ] = array( $w, $h, $crop ); } }
if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $objeto, $datos ) {
		$GLOBALS['convoca_test_localizados'][ $objeto ] = $datos;
		return true; } }
if ( ! function_exists( 'add_theme_page' ) ) {
	function add_theme_page( $titulo, $menu, $cap, $slug, $cb = '' ) {
		$GLOBALS['convoca_test_paginas_tema'][ $slug ] = compact( 'titulo', 'menu', 'cap', 'cb' );
		return 'hook-' . $slug; } }
if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $ruta = '' ) {
		return 'https://example.com/wp-admin/' . ltrim( (string) $ruta, '/' ); } }
if ( ! function_exists( 'remove_meta_box' ) ) {
	function remove_meta_box( $id, $pantalla, $contexto ) {
		$GLOBALS['convoca_test_metaboxes_quitados'][] = array( $id, $pantalla, $contexto );
		return true; } }
if ( ! function_exists( 'get_stylesheet_uri' ) ) {
	function get_stylesheet_uri() {
		return 'https://example.com/wp-content/themes/convoca/style.css'; } }
if ( ! function_exists( 'esc_js' ) ) {
	function esc_js( $t ) {
		return str_replace( "'", '&#039;', (string) $t ); } }
if ( ! function_exists( 'wp_nonce_url' ) ) {
	function wp_nonce_url( $url, $accion = -1, $nombre = '_wpnonce' ) {
		return $url . ( str_contains( (string) $url, '?' ) ? '&' : '?' ) . $nombre . '=nonce'; } }
if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $u ) {
		return (string) $u; } }
if ( ! function_exists( 'get_locale' ) ) {
	function get_locale() {
		return $GLOBALS['convoca_test_locale'] ?? 'es_ES'; } }
if ( ! function_exists( 'wp_parse_args' ) ) {
	function wp_parse_args( $args, $defaults = array() ) {
		return array_merge( $defaults, (array) $args ); } }
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $clave, $defecto = false ) {
		return $GLOBALS['convoca_test_opciones'][ $clave ] ?? $defecto; } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $t ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- este doble ES wp_strip_all_tags.
		return trim( strip_tags( (string) $t ) ); } }
if ( ! function_exists( 'get_the_ID' ) ) {
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- el doble tiene que llamarse como la función real de WordPress (get_the_ID), no como WordPress Coding Standards querría.
	function get_the_ID() {
		return (int) ( $GLOBALS['convoca_test_post_id'] ?? 0 ); } }
if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $id, $clave = '', $unico = false ) {
		return $GLOBALS['convoca_test_meta'][ (int) $id ][ $clave ] ?? ( $unico ? '' : array() ); } }
if ( ! function_exists( 'get_post_type' ) ) {
	function get_post_type( $post = null ) {
		$id = is_object( $post ) ? (int) $post->ID : (int) $post;
		return $GLOBALS['convoca_test_tipos'][ $id ] ?? false; } }
if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $tipos = '' ) {
		return (bool) ( $GLOBALS['convoca_test_singular'] ?? false ); } }
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $d, $o = 0 ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- este doble ES wp_json_encode.
		return json_encode( $d, $o ); } }
if ( ! function_exists( 'get_queried_object_id' ) ) {
	function get_queried_object_id() {
		return (int) ( $GLOBALS['convoca_test_post_id'] ?? 0 ); } }
if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title( $post = null ) {
		$id = is_object( $post ) ? (int) $post->ID : (int) $post;
		return (string) ( $GLOBALS['convoca_test_titulos'][ $id ] ?? 'Actividad de ejemplo' ); } }
if ( ! function_exists( 'get_permalink_x' ) ) {
	function get_permalink_x() {} }
if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	function wp_get_attachment_image_url( $id, $tam = '' ) {
		return 'https://example.com/imagen-' . (int) $id . '.jpg'; } }
if ( ! function_exists( 'get_post_thumbnail_id' ) ) {
	function get_post_thumbnail_id( $post = null ) {
		return (int) ( $GLOBALS['convoca_test_miniatura'] ?? 0 ); } }
if ( ! function_exists( 'wp_kses' ) ) {
	function wp_kses( $t, $permitido ) {
		return $t; } }
if ( ! function_exists( 'absint' ) ) {
	function absint( $v ) {
		return abs( (int) $v ); } }
if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $v ) {
		return is_string( $v ) ? stripslashes( $v ) : $v; } }
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $v ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- el doble imita a WordPress, que aquí usa strip_tags().
		return trim( strip_tags( (string) $v ) ); } }

if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
	define( 'HOUR_IN_SECONDS', 3600 ); }
if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $accion = -1 ) {
		return $GLOBALS['convoca_test_nonce'] ?? 1; } }
if ( ! function_exists( 'wp_doing_ajax' ) ) {
	function wp_doing_ajax() {
		return (bool) ( $GLOBALS['convoca_test_ajax'] ?? false ); } }
if ( ! function_exists( 'get_stylesheet' ) ) {
	function get_stylesheet() {
		return 'convoca'; } }
if ( ! function_exists( 'get_posts' ) ) {
	function get_posts( $args = array() ) {
		$GLOBALS['convoca_test_posts'][ $args['post_type'] ?? '' ] = $args;
		return $GLOBALS['convoca_test_ids'] ?? array(); } }
if ( ! function_exists( 'wp_delete_post' ) ) {
	function wp_delete_post( $id, $force = false ) {
		$GLOBALS['convoca_test_borrados'][] = array( (int) $id, (bool) $force );
		return true; } }
if ( ! function_exists( 'update_option' ) ) {
	function update_option( $clave, $valor, $auto = null ) {
		$GLOBALS['convoca_test_opciones'][ $clave ] = $valor;
		return true; } }
if ( ! function_exists( 'wp_cache_get' ) ) {
	function wp_cache_get( $clave, $grupo = '' ) {
		return false; } }
if ( ! function_exists( 'wp_cache_set' ) ) {
	function wp_cache_set( $clave, $dato, $grupo = '', $ttl = 0 ) {
		return true; } }

date_default_timezone_set( 'Europe/Madrid' );

// El doble de \Convoca\Core\site_stats() vive en su propio fichero: un bloque `namespace`
// no puede convivir con el código sin espacio de nombres de este bootstrap.
require_once __DIR__ . '/StubCoreSiteStats.php';
