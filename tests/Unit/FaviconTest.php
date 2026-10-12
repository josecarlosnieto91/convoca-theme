<?php
/**
 * El tema declara el favicon cuando el sitio no tiene uno propio.
 *
 * DEFECTO QUE FIJA ESTA PRUEBA (medido en la demo): el tema traía `favicon.png` pero no
 * declaraba `rel="icon"` en ninguna parte. Un sitio sin icono configurado no declaraba
 * ninguno, así que el navegador pedía `/favicon.ico` en CADA carga y recibía un 404.
 *
 * Y al revés: si el sitio SÍ tiene icono (Ajustes → Identidad del sitio), el del tema no
 * puede pisarlo.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class FaviconTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$GLOBALS['_test_has_site_icon'] = false;
	}

	private function salida(): string {
		ob_start();
		\convoca_theme_favicon();
		return (string) ob_get_clean();
	}

	public function test_declara_el_icono_si_el_sitio_no_tiene(): void {
		$html = $this->salida();
		$this->assertStringContainsString( 'rel="icon"', $html );
		$this->assertStringContainsString( 'favicon.png', $html );
	}

	public function test_no_pisa_el_icono_del_sitio(): void {
		$GLOBALS['_test_has_site_icon'] = true;
		$this->assertSame( '', $this->salida(), 'si el sitio tiene icono propio, el del tema no debe salir' );
	}
}
