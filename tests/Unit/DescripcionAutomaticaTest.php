<?php
/**
 * Descripción automática de las páginas (theme#22).
 *
 * Yoast solo emite `meta description` si el editor la escribió o si hay extracto: las páginas y los
 * archivos se quedaban sin ninguna. Lo que se prueba aquí es el contrato del relleno: **no pisar** lo
 * que ya venga y no inventar texto cuando no hay nada que contar.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Relleno de la descripción.
 */
final class DescripcionAutomaticaTest extends TestCase {

	/**
	 * Carga el tema y los dobles.
	 */
	protected function setUp(): void {
		if ( ! function_exists( 'convoca_descripcion_automatica' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}

		require_once dirname( __DIR__ ) . '/StubSeo.php';

		$GLOBALS['convoca_test_singular']     = false;
		$GLOBALS['convoca_test_archivo_tipo'] = false;
		$GLOBALS['convoca_test_tax']          = false;
		$GLOBALS['convoca_test_categoria']    = false;
		$GLOBALS['convoca_test_etiqueta']     = false;
		$GLOBALS['convoca_test_objeto']       = null;
		$GLOBALS['convoca_test_archivo_desc'] = '';
	}

	/**
	 * Lo que ya trae el SEO no se toca.
	 */
	public function test_no_pisa_la_descripcion_del_seo(): void {
		$this->assertSame( 'La que escribió el editor', convoca_descripcion_automatica( 'La que escribió el editor' ) );
	}

	/**
	 * En una entrada sin extracto se usa el contenido, sin HTML ni shortcodes.
	 */
	public function test_usa_el_contenido_limpio(): void {
		$GLOBALS['convoca_test_singular'] = true;
		$GLOBALS['convoca_test_objeto']   = new \WP_Post(
			array(
				'ID'           => 7,
				'post_excerpt' => '',
				'post_content' => '<p>Curso de <strong>educación ambiental</strong> en Oviedo.</p>[galeria id="3"]',
			)
		);

		$descripcion = convoca_descripcion_automatica( '' );

		$this->assertSame( 'Curso de educación ambiental en Oviedo.', $descripcion );
	}

	/**
	 * Si hay extracto, manda el extracto.
	 */
	public function test_prefiere_el_extracto(): void {
		$GLOBALS['convoca_test_singular'] = true;
		$GLOBALS['convoca_test_objeto']   = new \WP_Post(
			array(
				'post_excerpt' => 'Resumen corto.',
				'post_content' => 'Contenido largo que no debería usarse.',
			)
		);

		$this->assertSame( 'Resumen corto.', convoca_descripcion_automatica( '' ) );
	}

	/**
	 * Se recorta a 155 caracteres con puntos suspensivos.
	 */
	public function test_recorta_largo(): void {
		$GLOBALS['convoca_test_singular'] = true;
		$GLOBALS['convoca_test_objeto']   = new \WP_Post(
			array(
				'post_content' => str_repeat( 'palabra ', 60 ),
			)
		);

		$descripcion = convoca_descripcion_automatica( '' );

		$this->assertSame( 156, mb_strlen( $descripcion ) );
		$this->assertStringEndsWith( '…', $descripcion );
	}

	/**
	 * Un archivo sin descripción propia cae en la del sitio.
	 */
	public function test_archivo_sin_descripcion_usa_la_del_sitio(): void {
		$GLOBALS['convoca_test_archivo_tipo'] = true;

		$descripcion = convoca_descripcion_automatica( '' );

		$this->assertNotSame( '', $descripcion );
		$this->assertSame( get_bloginfo( 'description' ), $descripcion );
	}

	/**
	 * Un archivo con descripción propia la usa.
	 */
	public function test_archivo_usa_su_descripcion(): void {
		$GLOBALS['convoca_test_archivo_tipo'] = true;
		$GLOBALS['convoca_test_archivo_desc'] = 'Cursos y actividades formativas.';

		$this->assertSame( 'Cursos y actividades formativas.', convoca_descripcion_automatica( '' ) );
	}

	/**
	 * Sin nada que contar cae en la descripción del sitio: nunca deja la meta vacía.
	 */
	public function test_sin_datos_usa_la_descripcion_del_sitio(): void {
		$GLOBALS['convoca_test_singular'] = true;
		$GLOBALS['convoca_test_objeto']   = new \WP_Post( array( 'post_content' => '' ) );

		$this->assertSame( get_bloginfo( 'description' ), convoca_descripcion_automatica( '' ) );
	}
}
