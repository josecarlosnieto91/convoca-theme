<?php

/**
 * Los patrones de bloque del tema: que cada uno se pueda incluir y salga marcado.
 *
 * Los 16 ficheros de `patterns/` estaban al **0 %** (456 de los 1.411 statements del tema): no
 * había ni una prueba que los incluyera, así que un error de sintaxis o una llamada a una función
 * que no existe en un patrón solo se veía al insertarlo en el editor, ya en producción. Aquí se
 * incluye cada uno como lo hace WordPress al registrar el patrón y se comprueba que produce
 * marcado de bloque, no una página en blanco.
 *
 * El `include` va DENTRO de la prueba (no a nivel de fichero), que es lo que hace que pcov cuente
 * las líneas: cargarlo al descubrir las clases lo dejaría fuera de la medida.
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class PatronesDeBloqueTest extends TestCase {

	protected function setUp(): void {
		// El patrón `stats` pide las cifras al núcleo: con datos, la franja se pinta.
		$GLOBALS['convoca_test_stats'] = [
			'socios' => [
				'value' => '210',
				'label' => 'Socios',
			],
			'anyos' => [
				'value' => '12',
				'label' => 'Años',
			],
		];
	}

	protected function tearDown(): void {
		unset( $GLOBALS['convoca_test_stats'] );
	}
	/**
	 * Los ficheros de patrón del tema.
	 *
	 * @return array<string, array{0: string}>
	 */
	public static function patrones(): array {
		$directorio = dirname( __DIR__, 2 ) . '/patterns';
		$ficheros   = glob( $directorio . '/*.php' );
		if ( false === $ficheros ) {
			$ficheros = [];
		}
		sort( $ficheros );

		$casos = [];
		foreach ( $ficheros as $fichero ) {
			$casos[ basename( $fichero ) ] = [ $fichero ];
		}

		return $casos;
	}

	/**
	 * Incluye un patrón y devuelve lo que imprime.
	 *
	 * @param string $fichero Ruta del patrón.
	 * @return string
	 */
	private function render( string $fichero ): string {
		ob_start();
		include $fichero;

		return (string) ob_get_clean();
	}

	/**
	 * Cada patrón produce marcado de bloque.
	 *
	 * @dataProvider patrones
	 *
	 * @param string $fichero Patrón a incluir.
	 */
	public function test_un_patron_produce_marcado_de_bloque( string $fichero ): void {
		$html = $this->render( $fichero );

		$this->assertNotSame( '', trim( $html ), basename( $fichero ) . ': un patrón vacío no pinta nada en el editor' );
		$this->assertStringContainsString( '<!-- wp:', $html, basename( $fichero ) . ': sin comentario de bloque WordPress no lo reconoce como patrón' );
		$this->assertStringNotContainsString( '<?php', $html, basename( $fichero ) . ': se ha colado código sin ejecutar en la salida' );
	}

	/**
	 * Cada patrón produce marcado de bloque.
	 *
	 * @dataProvider patrones
	 *
	 * @param string $fichero Patrón a incluir.
	 */
	public function test_la_cabecera_del_patron_declara_su_slug( string $fichero ): void {
		$cabecera = (string) file_get_contents( $fichero );

		$this->assertMatchesRegularExpression( '/^\s*\*\s*Title:\s*\S/m', $cabecera, basename( $fichero ) . ': sin Title el patrón no se lista' );
		$this->assertMatchesRegularExpression( '/^\s*\*\s*Slug:\s*convoca\//m', $cabecera, basename( $fichero ) . ': todos los patrones del tema van bajo el espacio de nombres convoca/' );
	}

	/**
	 * Cada patrón produce marcado de bloque.
	 *
	 * @dataProvider patrones
	 *
	 * @param string $fichero Patrón a incluir.
	 */
	public function test_cada_bloque_que_se_abre_se_cierra( string $fichero ): void {
		$html = $this->render( $fichero );

		// Un comentario de bloque puede ser apertura (`<!-- wp:x {...} -->`), cierre
		// (`<!-- /wp:x -->`) o bloque autocerrado (`<!-- wp:x /-->`). Los autocerrados no
		// llevan pareja: contarlos como apertura daba un falso «bloque sin cerrar».
		preg_match_all( '/<!--\s*(\/?)wp:([a-z0-9\/-]+)([^>]*)-->/', $html, $bloques, PREG_SET_ORDER );

		$abiertos = [];
		$cerrados = [];
		foreach ( $bloques as $bloque ) {
			$nombre = $bloque[2];
			if ( '/' === $bloque[1] ) {
				$cerrados[ $nombre ] = ( $cerrados[ $nombre ] ?? 0 ) + 1;
				continue;
			}
			if ( str_contains( rtrim( $bloque[3] ), '/' ) ) {
				continue; // Bloque autocerrado: no lleva pareja.
			}
			$abiertos[ $nombre ] = ( $abiertos[ $nombre ] ?? 0 ) + 1;
		}
		ksort( $abiertos );
		ksort( $cerrados );

		$this->assertSame(
			$abiertos,
			$cerrados,
			basename( $fichero ) . ': un bloque sin cerrar rompe el editor («contenido inesperado»)'
		);
	}

	public function test_la_franja_de_cifras_pinta_los_datos_reales(): void {
		$html = $this->render( dirname( __DIR__, 2 ) . '/patterns/stats.php' );

		$this->assertStringContainsString( '210', $html );
		$this->assertStringContainsString( 'Socios', $html );
		$this->assertStringContainsString( '50%', $html, 'con dos cifras, cada columna ocupa la mitad' );
	}

	public function test_sin_cifras_la_franja_no_se_pinta(): void {
		$GLOBALS['convoca_test_stats'] = [];

		$html = $this->render( dirname( __DIR__, 2 ) . '/patterns/stats.php' );

		$this->assertSame( '', trim( $html ), 'una franja de cifras vacía es peor que ninguna franja' );
	}
}
