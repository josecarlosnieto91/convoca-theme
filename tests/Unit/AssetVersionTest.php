<?php
/**
 * Quién sella el `?ver=` de los assets (convoca_fse_remove_version).
 *
 * REPARTO FIJO (decisión del 16/10/2026, `convoca-core#19`; razonado en docs/sello-de-assets.md):
 *   - ficheros del TEMA        -> fecha de modificación del fichero;
 *   - plugins del ECOSISTEMA   -> la versión del plugin, sin tocar;
 *   - núcleo y plugins ajenos  -> sin versión (endurecimiento heredado).
 *
 * Cada extremo tiene un incidente detrás, y por eso los dos están cubiertos:
 *   · quitarles el `?ver=` a los plugins dejaba al navegador sirviendo la copia vieja tras
 *     actualizar — un arreglo desplegado podía no llegar nunca al usuario;
 *   · sellar por fecha los assets de un plugin TAPA su versión real, y la comprobación de la
 *     batería mide justamente esa versión (`2.3.35`, `2.5.24`, `0.2.17`).
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class AssetVersionTest extends TestCase {

	/** URL y ruta de un asset de prueba dentro de wp-content. */
	private function asset( string $relativo ): array {
		$disco = WP_CONTENT_DIR . '/' . $relativo;
		if ( ! is_dir( dirname( $disco ) ) ) {
			mkdir( dirname( $disco ), 0777, true );
		}
		if ( ! file_exists( $disco ) ) {
			file_put_contents( $disco, "/* prueba */\n" );
		}
		// content_url(), no la constante WP_CONTENT_URL: PHPStan la rechaza y pide
		// la función (que además respeta WP_CONTENT_URL si el sitio la redefine).
		return array( content_url( '/' . $relativo ), $disco );
	}

	private function sella( string $url ): string {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		return \convoca_fse_remove_version( $url );
	}

	/**
	 * 1) El tema sí se sella por fecha. Este caso no se podía probar antes: la función llama a
	 * get_stylesheet_directory() y el bootstrap no lo tenía definido.
	 */
	public function test_una_hoja_del_tema_se_sella_con_la_fecha_del_fichero(): void {
		$salida = $this->sella( get_template_directory_uri() . '/style.css?ver=2.9.34' );

		$this->assertStringContainsString(
			'ver=' . filemtime( ABSPATH . 'style.css' ),
			$salida,
			'El fichero del tema debe sellarse con su fecha: es lo que rompe la caché del navegador'
		);
		$this->assertStringNotContainsString( 'ver=2.9.34', $salida );
	}

	/** La fecha del tema tiene que moverse cuando el fichero cambia (es su razón de ser). */
	public function test_la_fecha_del_tema_cambia_cuando_cambia_el_fichero(): void {
		$disco = ABSPATH . 'style.css';
		$url   = get_template_directory_uri() . '/style.css';

		$antes = $this->sella( $url );

		// +60 sobre el mtime ACTUAL, no sobre time(): con time() el test fallaba al
		// repetirse dentro del mismo segundo (mismo valor, misma URL, falso fallo).
		touch( $disco, filemtime( $disco ) + 60 );
		clearstatcache( true, $disco );
		$despues = $this->sella( $url );

		$this->assertNotSame( $antes, $despues, 'Si el fichero cambia, la URL tiene que cambiar' );
	}

	/** 2) Los assets de los plugins del ecosistema CONSERVAN su versión (decisión del 16/10). */
	public function test_un_asset_de_plugin_del_ecosistema_conserva_la_version_del_plugin(): void {
		list( $url ) = $this->asset( 'plugins/convoca-members/public/assets/mi-area.css' );
		$entrada     = $url . '?ver=2.8.79';

		$this->assertSame(
			$entrada,
			$this->sella( $entrada ),
			'La versión del plugin es la que se sube en cada release: no se puede tapar con una fecha'
		);
	}

	/** Y no la tapa la fecha ni aunque el fichero acabe de cambiar. */
	public function test_la_fecha_del_fichero_no_tapa_la_version_del_plugin(): void {
		list( $url, $disco ) = $this->asset( 'plugins/convoca-core/assets/css/convoca-common.css' );
		$entrada             = $url . '?ver=2.3.35';

		touch( $disco, filemtime( $disco ) + 120 );
		clearstatcache( true, $disco );
		$salida = $this->sella( $entrada );

		$this->assertStringContainsString( 'ver=2.3.35', $salida );
		$this->assertStringNotContainsString( 'ver=' . filemtime( $disco ), $salida );
	}

	/** 3) El objetivo original sigue cumplido: la versión del núcleo no se publica. */
	public function test_la_version_del_core_de_wordpress_se_sigue_ocultando(): void {
		$salida = $this->sella( 'https://example.com/wp-includes/js/wp-embed.min.js?ver=6.9' );

		$this->assertStringNotContainsString( 'ver=', $salida );
		$this->assertStringContainsString( 'wp-embed.min.js', $salida );
	}

	/** Y la de un plugin ajeno tampoco. */
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
