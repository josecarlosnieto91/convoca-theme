<?php
/**
 * Sellado de assets por fecha de fichero (convoca_fse_remove_version).
 *
 * DEFECTO QUE FIJA ESTA PRUEBA (vivido midiendo la demo):
 *
 * La función sellaba con `filemtime` las hojas del PROPIO tema —y su comentario
 * explica por qué: «sin esto la URL del CSS no cambia nunca, el navegador sirve una
 * copia vieja y se acaba midiendo y diagnosticando sobre un estado que no es el del
 * fichero»—, pero a los assets de los PLUGINS les QUITABA el `?ver=`.
 *
 * Resultado real: `mi-area.css` corregido en disco seguía llegando viejo al
 * navegador, así que un arreglo desplegado podía no llegar nunca al usuario.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class AssetVersionTest extends TestCase {

	/** URL y ruta de un asset de prueba dentro de wp-content. */
	private function asset( string $relativo ): array {
		$disco = WP_CONTENT_DIR . '/' . $relativo;
		@mkdir( dirname( $disco ), 0777, true );
		if ( ! file_exists( $disco ) ) {
			file_put_contents( $disco, "/* prueba */\n" );
		}
		return array( WP_CONTENT_URL . '/' . $relativo, $disco );
	}

	private function sella( string $url ): string {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		return \convoca_fse_remove_version( $url );
	}

	public function test_un_asset_de_plugin_del_ecosistema_se_sella_con_la_fecha(): void {
		list( $url, $disco ) = $this->asset( 'plugins/convoca-members/public/assets/mi-area.css' );

		$salida = $this->sella( $url . '?ver=2.8.28' );

		$this->assertStringContainsString(
			'ver=' . filemtime( $disco ),
			$salida,
			'El asset del plugin debe sellarse con la fecha del fichero, no quedarse sin versión'
		);
		$this->assertStringNotContainsString( 'ver=2.8.28', $salida );
	}

	public function test_la_fecha_cambia_cuando_cambia_el_fichero(): void {
		list( $url, $disco ) = $this->asset( 'plugins/convoca-core/assets/css/convoca-common.css' );

		$antes = $this->sella( $url );

		// +60 sobre el mtime ACTUAL, no sobre time(): con time() el test fallaba al
		// repetirse dentro del mismo segundo (mismo valor, misma URL, falso fallo).
		touch( $disco, filemtime( $disco ) + 60 );
		clearstatcache( true, $disco );
		$despues = $this->sella( $url );

		$this->assertNotSame( $antes, $despues, 'Si el fichero cambia, la URL tiene que cambiar' );
	}

	public function test_la_version_del_core_de_wordpress_se_sigue_ocultando(): void {
		// El objetivo original de la función era no publicar la versión de WP.
		$salida = $this->sella( 'https://example.com/wp-includes/js/wp-embed.min.js?ver=6.9' );
		$this->assertStringNotContainsString( 'ver=', $salida );
		$this->assertStringContainsString( 'wp-embed.min.js', $salida );
	}

	public function test_un_asset_de_plugin_ajeno_no_se_toca_mas_alla_de_quitar_la_version(): void {
		$salida = $this->sella( 'https://example.com/wp-content/plugins/otro-plugin/x.css?ver=1.2' );
		$this->assertStringNotContainsString( 'ver=', $salida );
	}

	public function test_no_revienta_con_valores_que_no_son_cadena(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$this->assertNull( \convoca_fse_remove_version( null ) );
		$this->assertSame( '', \convoca_fse_remove_version( '' ) );
		$this->assertSame( array(), \convoca_fse_remove_version( array() ) );
	}
}
