<?php
/**
 * La cabecera de la entrada no recorta los carteles.
 *
 * El defecto: `width:100%` + `max-height:40rem` + `object-fit:cover` sobre una
 * destacada vertical enseñaba solo la franja central del cartel (el 34 %), y con
 * ella desaparecían el título y el pie. La clase la pone el filtro de atributos
 * de la imagen, y solo para la destacada de la entrada que se está viendo.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Cabecera de entrada: orientación, modo guardado y alcance del filtro.
 */
final class CabeceraDeEntradaTest extends TestCase {

	/**
	 * Carga el tema una sola vez y deja el escenario limpio.
	 */
	protected function setUp(): void {
		if ( ! function_exists( 'convoca_cabecera_modo' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}

		$GLOBALS['convoca_test_singular']        = true;
		$GLOBALS['convoca_test_post_id']         = 77;
		$GLOBALS['convoca_test_miniatura']       = 88;
		$GLOBALS['convoca_test_meta']            = array();
		$GLOBALS['convoca_test_attachment_meta'] = array();
	}

	/**
	 * Limpia el escenario.
	 */
	protected function tearDown(): void {
		unset(
			$GLOBALS['convoca_test_singular'],
			$GLOBALS['convoca_test_post_id'],
			$GLOBALS['convoca_test_miniatura'],
			$GLOBALS['convoca_test_meta'],
			$GLOBALS['convoca_test_attachment_meta'],
			$GLOBALS['convoca_test_nonce'],
			$_POST
		);
	}

	/**
	 * El filtro que el tema registra para los atributos de la imagen.
	 *
	 * @return callable
	 */
	private function filtro(): callable {
		$callbacks = $GLOBALS['convoca_test_filtros']['wp_get_attachment_image_attributes'] ?? array();
		// El tema registra más de un filtro en este gancho (lazy loading va antes):
		// el de la cabecera es el último, y hay que probar ese, no el primero.
		$callback = end( $callbacks );
		if ( is_callable( $callback ) ) {
			return $callback;
		}

		$this->fail( 'el tema no registra el filtro de atributos de la imagen' );
	}

	/**
	 * Metadatos de un adjunto.
	 *
	 * @param int $ancho  Ancho.
	 * @param int $alto   Alto.
	 */
	private function sembrar_adjunto( int $ancho, int $alto ): void {
		$GLOBALS['convoca_test_attachment_meta'][88] = array(
			'width'  => $ancho,
			'height' => $alto,
		);
	}

	/**
	 * Atributos de la imagen con la clase del tema si toca.
	 *
	 * @param int $adjunto Id del adjunto que se pinta.
	 * @return string
	 */
	private function clase( int $adjunto = 88 ): string {
		$attr = ( $this->filtro() )( array( 'class' => 'wp-post-image' ), (object) array( 'ID' => $adjunto ) );

		return (string) ( $attr['class'] ?? '' );
	}

	/**
	 * Un cartel vertical se muestra entero.
	 */
	public function test_la_destacada_vertical_lleva_la_clase_de_cabecera_entera(): void {
		$this->sembrar_adjunto( 1080, 1350 );

		$this->assertStringContainsString( 'convoca-cabecera-vertical', $this->clase() );
		$this->assertStringContainsString( 'wp-post-image', $this->clase(), 'no se pierden las clases que ya traía' );
	}

	/**
	 * Una foto apaisada se queda a sangre: recortarla no quita información.
	 */
	public function test_la_destacada_apaisada_no_lleva_la_clase(): void {
		$this->sembrar_adjunto( 1200, 675 );

		$this->assertStringNotContainsString( 'convoca-cabecera-vertical', $this->clase() );
	}

	/**
	 * La decisión guardada manda sobre la orientación, en los dos sentidos.
	 */
	public function test_el_modo_guardado_manda_sobre_la_orientacion(): void {
		$this->sembrar_adjunto( 1080, 1350 );
		$GLOBALS['convoca_test_meta'][77]['_convoca_cabecera_imagen'] = 'sangre';
		$this->assertStringNotContainsString( 'convoca-cabecera-vertical', $this->clase(), '«sangre» recorta aunque sea vertical' );

		$this->sembrar_adjunto( 1200, 675 );
		$GLOBALS['convoca_test_meta'][77]['_convoca_cabecera_imagen'] = 'entera';
		$this->assertStringContainsString( 'convoca-cabecera-vertical', $this->clase(), '«entera» no recorta aunque sea apaisada' );
	}

	/**
	 * La clase no se cuela en la misma imagen pintada en una tarjeta relacionada.
	 */
	public function test_otro_adjunto_no_recibe_la_clase(): void {
		$this->sembrar_adjunto( 1080, 1350 );
		$GLOBALS['convoca_test_attachment_meta'][99] = array(
			'width'  => 1080,
			'height' => 1350,
		);

		$this->assertStringNotContainsString( 'convoca-cabecera-vertical', $this->clase( 99 ) );
	}

	/**
	 * Fuera de la vista de una entrada no hay cabecera que arreglar.
	 */
	public function test_fuera_de_una_entrada_no_toca_nada(): void {
		$this->sembrar_adjunto( 1080, 1350 );
		$GLOBALS['convoca_test_singular'] = false;

		$this->assertStringNotContainsString( 'convoca-cabecera-vertical', $this->clase() );
	}

	/**
	 * Sin metadatos del adjunto no se inventa la orientación.
	 */
	public function test_sin_metadatos_no_se_marca(): void {
		$this->assertStringNotContainsString( 'convoca-cabecera-vertical', $this->clase() );
	}

	/**
	 * La caja lateral se registra en el editor de entradas.
	 */
	public function test_la_caja_de_cabecera_se_registra(): void {
		$GLOBALS['convoca_test_metaboxes'] = array();

		foreach ( $GLOBALS['convoca_test_acciones']['add_meta_boxes_post'][10] ?? array() as $callback ) {
			$callback();
		}

		$this->assertArrayHasKey( 'convoca_cabecera_imagen', $GLOBALS['convoca_test_metaboxes'] );
		$this->assertSame( 'convoca_cabecera_metabox', $GLOBALS['convoca_test_metaboxes']['convoca_cabecera_imagen']['cb'] );
	}

	/**
	 * Guardar la elección la escribe; con un nonce inválido no se toca nada.
	 */
	public function test_guardar_la_eleccion_respeta_el_nonce(): void {
		$guardar = null;
		foreach ( $GLOBALS['convoca_test_acciones']['save_post_post'][10] ?? array() as $callback ) {
			$guardar = $callback;
		}
		$this->assertIsCallable( $guardar );

		$_POST = array(
			'convoca_cabecera_nonce'  => 'valido',
			'convoca_cabecera_imagen' => 'entera',
		);
		$guardar( 77 );
		$this->assertSame( 'entera', $GLOBALS['convoca_test_meta'][77]['_convoca_cabecera_imagen'] );

		$GLOBALS['convoca_test_nonce']    = false;
		$_POST['convoca_cabecera_imagen'] = 'sangre';
		$guardar( 77 );
		$this->assertSame( 'entera', $GLOBALS['convoca_test_meta'][77]['_convoca_cabecera_imagen'], 'un nonce inválido no cambia la elección' );
	}
}
