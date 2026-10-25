<?php
/**
 * La traducción de los rótulos que viven en plantillas: el mapa y sus dos filtros.
 *
 * Las plantillas y las partes son marcado de bloque y **no ejecutan PHP**, así que un rótulo escrito
 * dentro no pasa por gettext: se sirve tal cual. El tema lo resuelve con un mapa (theme#13/#14) y dos
 * filtros de render. Aquí se prueba la **decisión**, no el HTML:
 *
 *   - el filtro de contenido sustituye SOLO cuando el texto es todo el contenido de una etiqueta, nunca
 *     dentro de un atributo (`class`, `href`): hacerlo rompería el diseño o los enlaces;
 *   - la traducción entra escapada (un `$` o una barra invertida en el catálogo no puede romper el
 *     `preg_replace` ni colarse como referencia);
 *   - los rótulos que WordPress pinta **dentro de atributos** (`label`, `placeholder`, `buttonText`,
 *     `content`) se resuelven aparte, con el mismo mapa, porque el primer filtro no los ve.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Mapa y filtros de traducción de plantillas.
 */
final class TraduccionDePlantillasTest extends TestCase {

	/**
	 * Carga el tema y deja limpio el catálogo de la prueba.
	 */
	protected function setUp(): void {
		if ( ! function_exists( 'convoca_theme_textos_de_plantillas' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}
		$GLOBALS['convoca_test_traducciones'] = array();
	}

	/**
	 * Quita lo que ha puesto la prueba.
	 */
	protected function tearDown(): void {
		unset( $GLOBALS['convoca_test_traducciones'] );
	}

	/**
	 * El mapa es la fuente única: trae los rótulos medidos en inglés y, sin catálogo, no cambia nada.
	 */
	public function test_el_mapa_trae_los_rotulos_medidos_en_ingles(): void {
		$mapa = convoca_theme_textos_de_plantillas();

		foreach ( array( 'Latest', 'Error 404', 'Keep reading', 'Privacy policy', 'LOCATION', 'SPOTS', 'View more', 'Next →' ) as $rotulo ) {
			$this->assertArrayHasKey( $rotulo, $mapa, 'El rótulo «' . $rotulo . '» se sirvió en inglés y no está en el mapa' );
			$this->assertSame( $rotulo, $mapa[ $rotulo ], 'Sin catálogo cargado, el mapa devuelve el texto original' );
		}
		$this->assertGreaterThan( 30, count( $mapa ), 'El mapa tiene que cubrir todas las plantillas, no una muestra' );
	}

	/**
	 * Con el texto dentro de una etiqueta y catálogo cargado, se sirve la traducción.
	 */
	public function test_traduce_el_texto_que_es_todo_el_contenido_de_la_etiqueta(): void {
		$GLOBALS['convoca_test_traducciones'] = array( 'Latest' => 'Últimas' );

		$this->assertSame(
			'<h2 class="wp-block-heading">Últimas</h2>',
			convoca_theme_traduce_textos_de_plantillas( '<h2 class="wp-block-heading">Latest</h2>' ),
			'El rótulo dentro de la etiqueta tiene que salir traducido'
		);
		// Con espacios alrededor del texto, también (el marcado del editor los mete): se comen al sustituir.
		$this->assertSame(
			'<p>Últimas</p>',
			convoca_theme_traduce_textos_de_plantillas( '<p> Latest </p>' ),
			'Los espacios del marcado no impiden reconocer el rótulo'
		);
	}

	/**
	 * Un atributo NO se traduce: cambiar un `class` o un `href` rompería el diseño y los enlaces.
	 */
	public function test_no_toca_los_atributos(): void {
		$GLOBALS['convoca_test_traducciones'] = array( 'Latest' => 'Últimas' );

		$atributo = '<a href="/x" class="Latest" title="Latest">Latest</a>';
		$salida   = convoca_theme_traduce_textos_de_plantillas( $atributo );

		$this->assertStringContainsString( 'class="Latest"', $salida, 'La clase no se traduce' );
		$this->assertStringContainsString( 'title="Latest"', $salida, 'El atributo no se traduce' );
		$this->assertStringContainsString( '>Últimas</a>', $salida, 'El texto del enlace sí' );
	}

	/**
	 * Una traducción con `$` o barra invertida entra literal: no se interpreta como referencia.
	 */
	public function test_la_traduccion_entra_escapada(): void {
		$GLOBALS['convoca_test_traducciones'] = array( 'PRICE' => 'Precio \1 $1 \\ fin' );

		$salida = convoca_theme_traduce_textos_de_plantillas( '<span>PRICE</span>' );

		$this->assertStringContainsString( 'Precio \1 $1 \\ fin', $salida, 'El texto del catálogo se copia tal cual, sin interpretarse' );
	}

	/**
	 * Un contenido sin etiquetas no se toca (nada que sustituir entre `>` y `<`).
	 */
	public function test_sin_etiquetas_devuelve_el_contenido_intacto(): void {
		$GLOBALS['convoca_test_traducciones'] = array( 'Latest' => 'Últimas' );

		$this->assertSame( 'Latest', convoca_theme_traduce_textos_de_plantillas( 'Latest' ) );
	}

	/**
	 * Los rótulos que WordPress pinta en ATRIBUTOS: se traducen, y solo las claves que el tema declara.
	 */
	public function test_traduce_los_atributos_de_bloque_del_mapa(): void {
		$GLOBALS['convoca_test_traducciones'] = array(
			'Search the site' => 'Busca en la web',
			'View more'       => 'Ver más',
		);

		$bloque = convoca_theme_traduce_atributos_de_bloque(
			array(
				'blockName' => 'core/search',
				'attrs'     => array(
					'label'       => 'Search the site',
					'placeholder' => 'View more',
					'buttonText'  => 'Search the site',
					'content'     => 'View more',
					'className'   => 'View more',
				),
			)
		);

		$this->assertSame( 'Busca en la web', $bloque['attrs']['label'] );
		$this->assertSame( 'Ver más', $bloque['attrs']['placeholder'] );
		$this->assertSame( 'Busca en la web', $bloque['attrs']['buttonText'] );
		$this->assertSame( 'Ver más', $bloque['attrs']['content'] );
		$this->assertSame( 'View more', $bloque['attrs']['className'], 'Una clave que no es rótulo no se toca' );
	}

	/**
	 * Un bloque sin atributos, con atributos que no son mapa o con valores que no son texto pasa intacto.
	 */
	public function test_atributos_ajenos_pasan_intactos(): void {
		$sin_attrs = array( 'blockName' => 'core/paragraph' );
		$this->assertSame( $sin_attrs, convoca_theme_traduce_atributos_de_bloque( $sin_attrs ) );

		$no_mapa = array(
			'blockName' => 'core/paragraph',
			'attrs'     => array( 'content' => 'Un texto cualquiera' ),
		);
		$this->assertSame( $no_mapa, convoca_theme_traduce_atributos_de_bloque( $no_mapa ), 'Un texto que no está en el mapa no se inventa' );

		$no_texto = array(
			'blockName' => 'core/paragraph',
			'attrs'     => array( 'content' => array( 'Latest' ) ),
		);
		$this->assertSame( $no_texto, convoca_theme_traduce_atributos_de_bloque( $no_texto ), 'Un atributo que no es texto no se toca' );
	}

	/**
	 * Los dos filtros están enganchados donde WordPress los llama.
	 */
	public function test_los_dos_filtros_estan_enganchados(): void {
		$this->assertContains(
			'convoca_theme_traduce_textos_de_plantillas',
			$GLOBALS['convoca_test_filtros']['render_block'] ?? array(),
			'Sin el filtro de render, el marcado de las plantillas sale en inglés'
		);
		$this->assertContains(
			'convoca_theme_traduce_atributos_de_bloque',
			$GLOBALS['convoca_test_filtros']['render_block_data'] ?? array(),
			'Sin este filtro, los rótulos de atributo no se traducen nunca'
		);
	}
}
