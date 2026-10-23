<?php
/**
 * El título de búsqueda, las pistas de recursos y la carga de las fuentes.
 *
 * `functions.php` tiene 1.012 statements y era lo único con cuerpo del tema. Estas tres funciones se
 * eligen porque cada una tiene una consecuencia que se ve: el título que sale en Google, el preconnect
 * que ahorra medio segundo, y las fuentes que se cargan sin bloquear la página.
 *
 * @package Convoca
 */

namespace Convoca\Theme\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Título de búsqueda, resource hints y preload de fuentes.
 */
final class BusquedaYFuentesTest extends TestCase {

	/**
	 * Cada prueba parte de una página que no es una búsqueda.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$GLOBALS['convoca_test_is_search']    = false;
		$GLOBALS['convoca_test_search_query'] = '';
	}

	/**
	 * Y no deja restos.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		$GLOBALS['convoca_test_is_search']    = false;
		$GLOBALS['convoca_test_search_query'] = '';
	}

	// ── título ──────────────────────────────────────────────────────────────────────────

	/**
	 * Fuera de una búsqueda, el título se deja como está.
	 *
	 * @return void
	 */
	public function test_fuera_de_la_busqueda_el_titulo_no_se_toca(): void {
		$this->assertSame( 'Un título cualquiera', \convoca_theme_titulo_de_busqueda( 'Un título cualquiera' ) );
	}

	/**
	 * Una búsqueda vacía no deja «Resultados para «»».
	 *
	 * @return void
	 */
	public function test_una_busqueda_vacia_no_deja_un_titulo_roto(): void {
		$GLOBALS['convoca_test_is_search']    = true;
		$GLOBALS['convoca_test_search_query'] = '   ';

		$this->assertSame( 'Buscar en la web', \convoca_theme_titulo_de_busqueda( 'x' ) );
	}

	/**
	 * Un término normal sale entre comillas angulares.
	 *
	 * @return void
	 */
	public function test_un_termino_sale_en_el_titulo(): void {
		$GLOBALS['convoca_test_is_search']    = true;
		$GLOBALS['convoca_test_search_query'] = 'turnos de voluntariado';

		$this->assertSame( 'Resultados para «turnos de voluntariado»', \convoca_theme_titulo_de_busqueda( 'x' ) );
	}

	// ── pistas de recursos ─────────────────────────────────────────────────────────────

	/**
	 * En preconnect se añaden los dos orígenes de fuentes.
	 *
	 * @return void
	 */
	public function test_preconnect_anade_los_origenes_de_fuentes(): void {
		$urls = \convoca_resource_hints( array(), 'preconnect' );

		$this->assertCount( 2, $urls );
		$this->assertSame( 'https://fonts.gstatic.com', $urls[0]['href'] );
		$this->assertSame( 'anonymous', $urls[0]['crossorigin'], 'gstatic necesita crossorigin o el preconnect no sirve' );
		$this->assertSame( 'https://fonts.googleapis.com', $urls[1] );
	}

	/**
	 * En dns-prefetch también.
	 *
	 * @return void
	 */
	public function test_dns_prefetch_tambien_los_anade(): void {
		$this->assertCount( 2, \convoca_resource_hints( array(), 'dns-prefetch' ) );
	}

	/**
	 * En cualquier otra relación no se toca nada.
	 *
	 * @return void
	 */
	public function test_otra_relacion_no_se_toca(): void {
		$urls = \convoca_resource_hints( array( 'https://example.org' ), 'preload' );

		$this->assertSame( array( 'https://example.org' ), $urls, 'no se le cuelgan fuentes a un preload' );
	}

	/**
	 * Y lo que ya venía se conserva.
	 *
	 * @return void
	 */
	public function test_lo_que_ya_venia_se_conserva(): void {
		$urls = \convoca_resource_hints( array( 'https://example.org' ), 'preconnect' );

		$this->assertSame( 'https://example.org', $urls[0] );
		$this->assertCount( 3, $urls );
	}

	// ── carga de fuentes ───────────────────────────────────────────────────────────────

	/**
	 * El handle de las fuentes se convierte en preload.
	 *
	 * @return void
	 */
	public function test_las_fuentes_se_cargan_como_preload(): void {
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- es una cadena de prueba, no un estilo del tema.
		$tag = "<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Lato'>";

		$salida = \convoca_style_loader_tag( $tag, 'convoca-google-fonts' );

		$this->assertStringContainsString( "rel='preload'", $salida, 'sin preload, las fuentes bloquean el render' );
		$this->assertStringContainsString( "as='style'", $salida );
		$this->assertStringContainsString( 'onload=', $salida, 'el preload tiene que convertirse en hoja al cargar' );
	}

	/**
	 * Y se deja un `<noscript>` para quien no ejecuta JavaScript.
	 *
	 * @return void
	 */
	public function test_las_fuentes_dejan_un_noscript(): void {
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- es una cadena de prueba, no un estilo del tema.
		$salida = \convoca_style_loader_tag( "<link rel='stylesheet' href='x'>", 'convoca-google-fonts' );

		$this->assertStringContainsString( '<noscript>', $salida );
		$this->assertStringContainsString( 'family=Lato', $salida, 'el noscript tiene que traer la fuente, no un hueco' );
	}

	/**
	 * Cualquier otro handle se devuelve intacto.
	 *
	 * @return void
	 */
	public function test_otro_handle_se_devuelve_intacto(): void {
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- es una cadena de prueba, no un estilo del tema.
		$tag = "<link rel='stylesheet' href='https://example.org/a.css'>";

		$this->assertSame( $tag, \convoca_style_loader_tag( $tag, 'convoca-main' ), 'no se toca lo que no es nuestro' );
	}
}
