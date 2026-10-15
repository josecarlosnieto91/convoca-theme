<?php
/**
 * El tema no lleva dentro datos de ningún cliente (issue #3).
 *
 * Criterio de hecho del issue: cero cadenas de cliente y cero rutas de infraestructura interna en el árbol. La
 * excepción documentada son los **alias de filtro publicados** (`{lugg_url}` y `convoca_theme_lugg_url`): son
 * API que un sitio puede estar usando, y quitarlos rompe compatibilidad; van marcados con
 * `convoca-hygiene-ignore` y su motivo.
 *
 * Las palabras prohibidas se componen aquí con concatenación a propósito: escribirlas enteras convertiría esta
 * prueba en la fuga que vigila.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class SinDatosDeClienteTest extends TestCase {

	/**
	 * Lo que no puede aparecer en el árbol.
	 *
	 * @return array<int, string>
	 */
	private function cadenas(): array {
		return array(
			'l' . 'ugg',
			'bio' . 'devas',
			'o' . 'rion',
			'andre' . 'meda',
			'sync-' . 'replica',
		);
	}

	/**
	 * Ficheros que se revisan: los que se publican y los que se leen.
	 *
	 * @return array<int, string>
	 */
	private function ficheros(): array {
		$raiz = dirname( __DIR__, 2 );
		$salida = array();
		foreach ( array( 'functions.php', 'style.css', 'CHANGELOG.md', 'README.md', '.distignore' ) as $f ) {
			if ( file_exists( $raiz . '/' . $f ) ) {
				$salida[] = $f;
			}
		}
		foreach ( glob( $raiz . '/scripts/*' ) as $script ) {
			if ( is_file( $script ) ) {
				$salida[] = 'scripts/' . basename( $script );
			}
		}

		return $salida;
	}

	public function test_ningun_fichero_nombra_a_un_cliente_ni_a_la_infraestructura(): void {
		$raiz      = dirname( __DIR__, 2 );
		$encontrado = array();

		foreach ( $this->ficheros() as $ruta ) {
			$contenido = (string) file_get_contents( $raiz . '/' . $ruta );
			foreach ( preg_split( '/\R/', $contenido ) as $numero => $linea ) {
				if ( false !== strpos( $linea, 'convoca-hygiene-ignore' ) ) {
					continue; // Excepción documentada: alias de filtro publicado.
				}
				foreach ( $this->cadenas() as $prohibida ) {
					if ( false !== stripos( $linea, $prohibida ) ) {
						$encontrado[] = $ruta . ':' . ( $numero + 1 );
					}
				}
			}
		}

		$this->assertSame( array(), array_values( array_unique( $encontrado ) ), 'El tema es genérico: los datos del sitio los pone el sitio' );
	}

	public function test_los_planes_internos_no_se_versionan(): void {
		$gitignore = (string) file_get_contents( dirname( __DIR__, 2 ) . '/.gitignore' );
		$this->assertStringContainsString( '.hermes/', $gitignore );
	}

	public function test_el_auditor_recibe_la_direccion_por_fuera(): void {
		$script = (string) file_get_contents( dirname( __DIR__, 2 ) . '/scripts/verificar-auditor.py' );
		$this->assertStringContainsString( 'CONVOCA_CSS_REMOTO', $script, 'La dirección se pasa por entorno o argumento' );
		$this->assertStringContainsString( 'sys.argv', $script );
	}
}
