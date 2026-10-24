<?php
/**
 * Los tres filtros del tema que tocan el HTML ya renderizado.
 *
 * El más serio: `convoca_theme_render_block` resuelve shortcodes dentro de bloques y lleva una guarda de reentrada. Sin
 * ella, un patrón que se contiene a sí mismo giraba hasta agotar `max_execution_time`: 500 y un worker ocupado dos
 * minutos al 100 %, que con el pool lleno tumbaba el sitio. Eso se prueba por efecto: la segunda entrada no vuelve a
 * resolver.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Filtros de renderizado.
 */
final class FiltrosRenderTest extends TestCase {

	/**
	 * Previos.
	 *
	 * @var array
	 */
	private array $previo = array();

	/**
	 * Prepara el caso.
	 */
	protected function setUp(): void {
		$this->previo = array(
			'shortcode' => $GLOBALS['convoca_test_shortcodes'] ?? null,
			'filtros'   => $GLOBALS['convoca_test_filtros'] ?? null,
		);

		// El tema registra sus filtros al cargarse: sin esto, la prueba no tiene nada que invocar. `require_once`
		// garantiza que se registran una sola vez en el proceso.
		if ( ! function_exists( 'convoca_theme_render_block' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}
	}

	/**
	 * Limpia.
	 */
	protected function tearDown(): void {
		unset( $GLOBALS['convoca_test_resoluciones'], $GLOBALS['convoca_test_reentrar'] );
		if ( null === $this->previo['shortcode'] ) {
			unset( $GLOBALS['convoca_test_shortcodes'] );
		} else {
			$GLOBALS['convoca_test_shortcodes'] = $this->previo['shortcode'];
		}
	}

	/**
	 * El filtro del enlace «leer más», que WordPress rellena con el título.
	 *
	 * @return callable
	 */
	private function filtro_leer_mas(): callable {
		foreach ( $GLOBALS['convoca_test_filtros']['render_block_core/read-more'] as $callback ) {
			return $callback;
		}
		$this->fail( 'el tema no registra el filtro de read-more' );
	}

	/**
	 * Sin el enlace del tema, el contenido pasa intacto.
	 */
	public function test_leer_mas_ajeno_pasa_intacto(): void {
		$html = '<p>Hola</p>';

		$this->assertSame( $html, ( $this->filtro_leer_mas() )( $html, array() ) );
	}

	/**
	 * Con el enlace del tema, el texto es **solo** la etiqueta: WordPress añadía el título detrás.
	 */
	public function test_leer_mas_quita_el_titulo(): void {
		$bloque = '<a class="wp-block-read-more" href="/x">Más información : Excursión</a>';

		$salida = ( $this->filtro_leer_mas() )( $bloque, array( 'attrs' => array( 'content' => 'Ver la actividad' ) ) );

		$this->assertStringContainsString( 'Ver la actividad', $salida );
		$this->assertStringNotContainsString( 'Excursión', $salida, 'el título pegado detrás no pinta nada' );
	}

	/**
	 * Sin etiqueta configurada, una por defecto; y el HTML de la etiqueta no se cuela.
	 */
	public function test_leer_mas_etiqueta_por_defecto_y_escapada(): void {
		$bloque = '<a class="wp-block-read-more" href="/x">Lo que sea</a>';
		$sin    = ( $this->filtro_leer_mas() )( $bloque, array() );
		$malo   = ( $this->filtro_leer_mas() )( $bloque, array( 'attrs' => array( 'content' => '<script>x</script>' ) ) );

		$this->assertStringContainsString( 'More information', $sin );
		$this->assertStringNotContainsString( '<script>', $malo, 'la etiqueta se escapa' );
	}

	/**
	 * Un bloque que no está en la lista pasa intacto, aunque lleve corchetes.
	 */
	public function test_bloque_ajeno_no_se_toca(): void {
		$html = '<p>[convoca_algo]</p>';

		$this->assertSame(
			$html,
			convoca_theme_render_block( $html, array( 'blockName' => 'core/paragraph' ) )
		);
	}

	/**
	 * Un bloque listado **sin** corchetes tampoco: no hay shortcode que resolver.
	 */
	public function test_bloque_listado_sin_shortcode(): void {
		$html = '<div>sin corchetes</div>';

		$this->assertSame(
			$html,
			convoca_theme_render_block( $html, array( 'blockName' => 'core/html' ) )
		);
	}

	/**
	 * Un bloque listado con corchetes resuelve sus shortcodes.
	 */
	public function test_bloque_listado_con_shortcode(): void {
		$GLOBALS['convoca_test_shortcodes'] = true;

		$salida = convoca_theme_render_block( '[convoca_algo]', array( 'blockName' => 'core/html' ) );

		$this->assertSame( 'RESUELTO', $salida );
	}

	/**
	 * La guarda de reentrada: si el shortcode vuelve a renderizar el mismo bloque, **no** se resuelve otra vez.
	 *
	 * Sin esta guarda la petición se quedaba girando hasta el 500.
	 */
	public function test_la_guarda_corta_la_reentrada(): void {
		$GLOBALS['convoca_test_shortcodes']   = true;
		$GLOBALS['convoca_test_reentrar']     = true;
		$GLOBALS['convoca_test_resoluciones'] = 0;

		$salida = convoca_theme_render_block( '[convoca_algo]', array( 'blockName' => 'core/block' ) );

		$this->assertSame( 'RESUELTO', $salida, 'la primera entrada resuelve' );
		$this->assertSame( 1, $GLOBALS['convoca_test_resoluciones'] ?? 0, 'la segunda entrada NO vuelve a resolver: la guarda aguanta' );
		unset( $GLOBALS['convoca_test_reentrar'], $GLOBALS['convoca_test_resoluciones'] );
	}

	/**
	 * La etiqueta del botón de modo oscuro se traduce.
	 */
	public function test_etiqueta_modo_oscuro(): void {
		$sin = '<button>Modo</button>';
		$con = '<button aria-label="Switch color mode">Modo</button>';

		$this->assertSame( $sin, convoca_theme_etiqueta_modo_oscuro( $sin ), 'sin la etiqueta no se toca nada' );
		$salida = convoca_theme_etiqueta_modo_oscuro( $con );

		$this->assertStringContainsString( 'aria-label="', $salida, 'la etiqueta sigue ahí' );
		// El arnés no traduce (`esc_attr__` devuelve la misma cadena), así que lo que se fija es el camino: la marca se
		// sustituye por la cadena **traducible**, no que el texto cambie hoy.
		$this->assertSame( $salida, convoca_theme_etiqueta_modo_oscuro( $salida ), 'pasar dos veces no estropea nada' );
	}
}
