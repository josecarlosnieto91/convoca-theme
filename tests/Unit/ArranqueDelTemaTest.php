<?php

/**
 * El arranque del tema: soportes, patrones, estilos de bloque, ajustes de rendimiento,
 * encolado de assets y las dos pantallas/ganchos que ya existían sin cubrir.
 *
 * `functions.php` estaba al 34,8 % con 660 statements sin cubrir: casi todo el arranque. No es
 * adorno —de aquí salen el favicon que evita un 404 por carga, el `defer` de los scripts, la
 * versión por fecha de la hoja de estilos (sin la cual el navegador sirve CSS viejo) y el
 * restablecimiento de plantillas del editor—, así que se prueba la decisión, no el HTML.
 *
 * El fichero se carga en `setUp()`: cargarlo al nivel del fichero de prueba lo ejecuta al
 * descubrir las clases y su código de nivel superior no se cuenta.
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class ArranqueDelTemaTest extends TestCase {

	protected function setUp(): void {
		// `functions.php` se incluye UNA vez por proceso: sus ganchos quedan registrados en
		// $GLOBALS y no se vuelven a declarar en cada prueba. Por eso aquí solo se limpia lo que
		// cambia durante la prueba; borrar los ganchos dejaría a las siguientes sin nada que mirar.
		if ( ! function_exists( 'convoca_setup' ) ) {
			$GLOBALS['convoca_test_acciones']   = [];
			$GLOBALS['convoca_test_filtros']    = [];
			$GLOBALS['convoca_test_categorias'] = [];
			$GLOBALS['convoca_test_estilos']    = [];
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}

		$GLOBALS['convoca_test_theme_support']      = [];
		$GLOBALS['convoca_test_estilos_encolados']  = [];
		$GLOBALS['convoca_test_scripts_encolados']  = [];
		$GLOBALS['convoca_test_tamanos']            = [];
		$GLOBALS['convoca_test_localizados']        = [];
		$GLOBALS['convoca_test_paginas_tema']       = [];
		$GLOBALS['convoca_test_metaboxes_quitados'] = [];
		$GLOBALS['convoca_test_quitados']           = [];
		$GLOBALS['convoca_test_opciones']           = [];
		$GLOBALS['convoca_test_nonce']              = 1;
		$GLOBALS['convoca_test_ajax']               = false;
		$GLOBALS['convoca_test_is_admin']           = true;
		$GLOBALS['convoca_test_caps']               = [];
		$GLOBALS['convoca_test_ids']                = [];
		$GLOBALS['convoca_test_borrados']           = [];
		$GLOBALS['convoca_test_posts']              = [];
		$GLOBALS['convoca_test_miniatura']          = 0;
		unset( $GLOBALS['convoca_test_editor_style'], $_GET );
	}

	protected function tearDown(): void {
		unset(
			$_GET,
			$GLOBALS['convoca_test_theme_support'],
			$GLOBALS['convoca_test_estilos'],
			$GLOBALS['convoca_test_estilos_encolados'],
			$GLOBALS['convoca_test_scripts_encolados'],
			$GLOBALS['convoca_test_tamanos'],
			$GLOBALS['convoca_test_localizados'],
			$GLOBALS['convoca_test_paginas_tema'],
			$GLOBALS['convoca_test_metaboxes_quitados'],
			$GLOBALS['convoca_test_quitados'],
			$GLOBALS['convoca_test_opciones'],
			$GLOBALS['convoca_test_nonce'],
			$GLOBALS['convoca_test_ajax'],
			$GLOBALS['convoca_test_is_admin'],
			$GLOBALS['convoca_test_caps'],
			$GLOBALS['convoca_test_ids'],
			$GLOBALS['convoca_test_borrados'],
			$GLOBALS['convoca_test_miniatura'],
			$GLOBALS['convoca_test_editor_style']
		);
	}

	/**
	 * Recoge lo que imprime una función del tema.
	 *
	 * @param callable $funcion Función a ejecutar.
	 * @return string
	 */
	private function salida( callable $funcion ): string {
		ob_start();
		$funcion();

		return (string) ob_get_clean();
	}

	/**
	 * La primera función registrada en un gancho.
	 *
	 * @param string $gancho Nombre del gancho.
	 * @param int    $prioridad Prioridad.
	 * @return callable|null
	 */
	private function enGancho( string $gancho, int $prioridad = 10 ) {
		return $GLOBALS['convoca_test_acciones'][ $gancho ][ $prioridad ][0] ?? null;
	}

	/**
	 * El ÚLTIMO aviso registrado en `admin_notices` (los avisos se acumulan entre pruebas).
	 *
	 * @return callable|null
	 */
	private function ultimoAviso() {
		$avisos = $GLOBALS['convoca_test_acciones']['admin_notices'][10] ?? [];

		return $avisos ? $avisos[ array_key_last( $avisos ) ] : null;
	}

	// ── el gancho de restablecer plantillas ───────────────────────────────────────────

	public function test_el_restablecimiento_de_plantillas_esta_enganchado(): void {
		$this->assertNotNull( $this->enGancho( 'admin_init' ), 'sin gancho, el botón de la ayuda no hace nada' );
	}

	public function test_sin_el_parametro_no_se_borra_nada(): void {
		$_GET = [];

		( $this->enGancho( 'admin_init' ) )();

		$this->assertSame( [], $GLOBALS['convoca_test_borrados'] );
	}

	public function test_sin_permiso_no_se_borra_nada(): void {
		$_GET = [
			'convoca_reset_templates' => '1',
			'_wpnonce' => 'x',
		];
		// Con identificadores en juego: si la guarda se cayera, se borrarían de verdad.
		$GLOBALS['convoca_test_ids'] = [ 11, 12 ];
		$this->marcarPermiso( false );

		( $this->enGancho( 'admin_init' ) )();

		$this->assertSame( [], $GLOBALS['convoca_test_borrados'], 'borrar plantillas es de administración' );
	}

	public function test_un_nonce_invalido_no_borra_nada(): void {
		$_GET                          = [
			'convoca_reset_templates' => '1',
			'_wpnonce' => 'x',
		];
		$GLOBALS['convoca_test_nonce'] = false;
		$GLOBALS['convoca_test_ids']   = [ 11, 12 ];

		( $this->enGancho( 'admin_init' ) )();

		$this->assertSame( [], $GLOBALS['convoca_test_borrados'] );
	}

	public function test_una_peticion_ajax_no_borra_nada(): void {
		$_GET                         = [
			'convoca_reset_templates' => '1',
			'_wpnonce' => 'x',
		];
		$GLOBALS['convoca_test_ajax'] = true;
		$GLOBALS['convoca_test_ids']  = [ 11, 12 ];

		( $this->enGancho( 'admin_init' ) )();

		$this->assertSame( [], $GLOBALS['convoca_test_borrados'], 'el borrado se pide desde una pantalla, no por ajax' );
	}

	public function test_no_se_puede_restablecer_dos_veces_en_la_misma_hora(): void {
		$_GET = [
			'convoca_reset_templates' => '1',
			'_wpnonce' => 'x',
		];
		$GLOBALS['convoca_test_opciones']['convoca_templates_reset_time'] = time();

		$salida = $this->salida( $this->enGancho( 'admin_init' ) );

		$this->assertSame( [], $GLOBALS['convoca_test_borrados'], 'la hora de espera es la que impide borrar en bucle' );
		$aviso = $this->ultimoAviso();
		$this->assertNotNull( $aviso );
		ob_start();
		$aviso();
		$html = (string) ob_get_clean();
		$this->assertStringContainsString( 'once per hour', $html );
		$this->assertSame( '', $salida );
	}

	public function test_restablecer_borra_las_plantillas_del_tema_y_avisa(): void {
		$_GET                        = [
			'convoca_reset_templates' => '1',
			'_wpnonce' => 'x',
		];
		$GLOBALS['convoca_test_ids'] = [ 11, 12 ];

		( $this->enGancho( 'admin_init' ) )();

		// Dos tipos (plantillas y partes de plantilla) por dos identificadores: cuatro borrados.
		$this->assertCount( 4, $GLOBALS['convoca_test_borrados'], 'se borran de verdad, no se papeliquean' );
		$this->assertSame( [ 11, true ], $GLOBALS['convoca_test_borrados'][0], 'y con borrado forzado: una papelera no restablece nada' );
		$this->assertArrayHasKey( 'wp_template', $GLOBALS['convoca_test_posts'] );
		$this->assertArrayHasKey( 'wp_template_part', $GLOBALS['convoca_test_posts'] );
		$this->assertArrayHasKey( 'convoca_templates_reset_time', $GLOBALS['convoca_test_opciones'] );

		$aviso = $this->ultimoAviso();
		$this->assertNotNull( $aviso, 'hay que decir cuántas plantillas se han restablecido' );
		ob_start();
		$aviso();
		$html = (string) ob_get_clean();
		$this->assertStringContainsString( 'Reset 4 templates', $html );
	}

	// ── soportes del tema ────────────────────────────────────────────────────────────

	public function test_el_tema_declara_sus_soportes(): void {
		convoca_setup();

		$soportes = $GLOBALS['convoca_test_theme_support'];

		$this->assertArrayHasKey( 'wp-block-styles', $soportes );
		$this->assertArrayHasKey( 'editor-styles', $soportes );
		$this->assertArrayHasKey( 'responsive-embeds', $soportes );
		$this->assertArrayHasKey( 'post-thumbnails', $soportes );
		$this->assertSame( 80, $soportes['custom-logo']['height'], 'el logo de la cabecera mide 80 de alto' );
		$this->assertContains( 'search-form', $soportes['html5'], 'sin html5 el buscador sale con marcado viejo' );
		$this->assertContains( 'style.css', $GLOBALS['convoca_test_editor_style'] );
	}

	// ── patrones y estilos de bloque ─────────────────────────────────────────────────

	public function test_las_categorias_de_patrones_son_las_del_tema(): void {
		convoca_register_pattern_categories();

		$this->assertArrayHasKey( 'convoca', $GLOBALS['convoca_test_categorias'] );
		$this->assertArrayHasKey( 'convoca-layout', $GLOBALS['convoca_test_categorias'] );
	}

	public function test_los_estilos_de_bloque_se_registran_todos(): void {
		convoca_register_block_styles();

		$estilos = $GLOBALS['convoca_test_estilos'];

		$this->assertContains( 'lead', $estilos['core/paragraph'] );
		$this->assertContains( 'card', $estilos['core/group'] );
		$this->assertContains( 'coordinator', $estilos['core/group'] );
		$this->assertContains( 'glass', $estilos['core/group'] );
		$this->assertContains( 'topographic', $estilos['core/cover'] );
		$this->assertContains( 'convoca', $estilos['core/table'] );
		$this->assertContains( 'secondary', $estilos['core/button'] );
		$this->assertContains( 'elevated', $estilos['core/image'] );
	}

	// ── rendimiento y cabecera ───────────────────────────────────────────────────────

	public function test_los_resource_hints_solo_ponen_las_fuentes_al_preconectar(): void {
		$urls = convoca_resource_hints( [], 'preconnect' );

		$this->assertContains( 'https://fonts.googleapis.com', $urls );
		$this->assertContains(
			[
				'href' => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			],
			$urls
		);

		$this->assertSame( [], convoca_resource_hints( [], 'prefetch' ), 'prefetch no necesita preconectar nada' );
	}

	public function test_la_hoja_de_fuentes_se_carga_sin_bloquear(): void {
		// Etiqueta de ENTRADA de la prueba (lo que WordPress genera), no un estilo que se encole aquí.
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- es la entrada que se le pasa al filtro.
		$tag = "<link rel='stylesheet' href='https://fonts.googleapis.com/css2' />";

		$resultado = convoca_style_loader_tag( $tag, 'convoca-google-fonts' );

		$this->assertStringContainsString( "rel='preload'", $resultado );
		$this->assertStringContainsString( 'onload', $resultado, 'el preload tiene que convertirse en hoja al cargar' );
		$this->assertStringContainsString( '<noscript>', $resultado, 'sin JS, las fuentes siguen cargando' );
	}

	public function test_otra_hoja_no_se_toca(): void {
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- etiqueta de entrada, no un encolado.
		$tag = "<link rel='stylesheet' href='x.css' />";

		$this->assertSame( $tag, convoca_style_loader_tag( $tag, 'otra-hoja' ) );
	}

	public function test_el_css_critico_sale_en_la_cabecera(): void {
		$html = $this->salida( 'convoca_critical_css' );

		$this->assertStringContainsString( 'convoca-critical-css', $html );
		$this->assertStringContainsString( '.site-header', $html, 'la cabecera pegajosa es lo que evita el salto al cargar' );
		$this->assertStringContainsString( 'overflow-x: hidden', $html );
	}

	public function test_sin_icono_de_sitio_el_tema_declara_el_suyo(): void {
		$html = $this->salida( 'convoca_theme_favicon' );

		$this->assertStringContainsString( 'rel="icon"', $html, 'sin esto el navegador pide /favicon.ico y recibe un 404' );
		$this->assertStringContainsString( 'favicon.png', $html );
	}

	public function test_con_icono_de_sitio_no_se_pisa(): void {
		$GLOBALS['_test_has_site_icon'] = true;

		$html = $this->salida( 'convoca_theme_favicon' );

		$this->assertSame( '', $html, 'el icono que ha puesto quien administra manda' );
		unset( $GLOBALS['_test_has_site_icon'] );
	}

	public function test_los_emojis_de_wordpress_se_retiran(): void {
		convoca_performance_tweaks();

		$quitados = array_column( $GLOBALS['convoca_test_quitados'], 1 );

		$this->assertContains( 'print_emoji_detection_script', $quitados );
		$this->assertContains( 'print_emoji_styles', $quitados );
		$this->assertCount( 4, $quitados, 'cuatro enganches de emoji: portada y escritorio' );
	}

	public function test_las_imagenes_se_cargan_en_diferido_salvo_la_del_lcp(): void {
		$filtro = $GLOBALS['convoca_test_filtros']['wp_get_attachment_image_attributes'][0];

		$normal = $filtro( [ 'alt' => 'x' ], null, 'full' );
		$this->assertSame( 'lazy', $normal['loading'] );

		$lcp = $filtro( [ 'fetchpriority' => 'high' ], null, 'full' );
		$this->assertArrayNotHasKey( 'loading', $lcp, 'la imagen principal no se retrasa' );
	}

	public function test_los_tamanos_de_tarjeta_y_portada_se_declaran(): void {
		convoca_image_sizes();

		$this->assertSame( [ 600, 400, true ], $GLOBALS['convoca_test_tamanos']['convoca-card'] );
		$this->assertSame( [ 1600, 900, true ], $GLOBALS['convoca_test_tamanos']['convoca-hero'] );
	}

	// ── encolado ─────────────────────────────────────────────────────────────────────

	public function test_las_fuentes_y_la_hoja_del_tema_se_encolan(): void {
		convoca_theme_scripts();

		$estilos = $GLOBALS['convoca_test_estilos_encolados'];

		$this->assertArrayHasKey( 'convoca-google-fonts', $estilos );
		$this->assertArrayHasKey( 'convoca-theme-style', $estilos );
		$this->assertSame( [ 'convoca-google-fonts' ], $estilos['convoca-theme-style']['d'], 'la hoja del tema va detrás de las fuentes' );
		$esperado = file_exists( ABSPATH . 'style.css' ) ? (string) (int) filemtime( ABSPATH . 'style.css' ) : '';
		$this->assertSame(
			$esperado,
			(string) $estilos['convoca-theme-style']['v'],
			'la versión de la hoja es la fecha del fichero: con la versión del tema, el navegador sirve CSS viejo'
		);
	}

	public function test_el_editor_carga_las_mismas_fuentes(): void {
		convoca_editor_fonts();

		$this->assertArrayHasKey( 'convoca-editor-google-fonts', $GLOBALS['convoca_test_estilos_encolados'] );
	}

	public function test_los_scripts_van_en_diferido_y_traducen_sus_etiquetas(): void {
		convoca_enqueue_scripts();

		$scripts = $GLOBALS['convoca_test_scripts_encolados'];

		$this->assertSame( 'defer', $scripts['convoca']['i']['strategy'] );
		$this->assertTrue( $scripts['convoca']['i']['in_footer'] );
		$this->assertArrayHasKey( 'convoca-dark-mode', $scripts );
		$this->assertArrayHasKey( 'convocaModoOscuro', $GLOBALS['convoca_test_localizados'], 'las etiquetas del botón son texto traducible, no cadenas en el JS' );
		$this->assertSame( 'Switch to dark mode', $GLOBALS['convoca_test_localizados']['convocaModoOscuro']['etiquetaOscuro'] );
	}

	public function test_el_modo_oscuro_se_aplica_antes_de_pintar(): void {
		$html = $this->salida( 'convoca_dark_mode_inline_init' );

		$this->assertStringContainsString( 'convoca-theme-mode', $html );
		$this->assertStringContainsString( 'classList.add', $html );
		$this->assertStringContainsString( 'try', $html, 'un localStorage bloqueado no puede romper la página' );
	}

	// ── pantalla de ayuda y escritorio ───────────────────────────────────────────────

	public function test_la_ayuda_se_registra_tras_una_capacidad(): void {
		convoca_admin_menu();

		$pagina = $GLOBALS['convoca_test_paginas_tema']['convoca-help'];

		$this->assertSame( 'edit_theme_options', $pagina['cap'], 'la ayuda es para quien administra, no para cualquiera' );
	}

	public function test_la_pantalla_de_ayuda_se_pinta(): void {
		$html = $this->salida( 'convoca_help_page_html' );

		$this->assertStringContainsString( 'Convoca', $html );
		$this->assertStringContainsString( 'site-editor.php', $html, 'el enlace al editor del sitio es lo primero que se busca' );
		$this->assertStringContainsString( 'convoca_reset_templates', $html );
		$this->assertStringContainsString( 'convoca/hero', $html, 'y la lista de patrones disponibles' );
	}

	public function test_el_aviso_de_novedades_de_wordPress_se_quita(): void {
		$gancho = $this->enGancho( 'wp_dashboard_setup', 999 );

		$this->assertNotNull( $gancho, 'el widget de novedades falla sin internet' );

		$gancho();

		$this->assertContains( [ 'dashboard_primary', 'dashboard', 'side' ], $GLOBALS['convoca_test_metaboxes_quitados'] );
	}

	/**
	 * Cambia lo que responde `current_user_can()` en este arnés.
	 *
	 * @param bool $puede Permiso concedido.
	 */
	private function marcarPermiso( bool $puede ): void {
		$GLOBALS['convoca_test_caps']['manage_options'] = $puede;
	}
}
