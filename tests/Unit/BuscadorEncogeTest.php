<?php
/**
 * El buscador de la cabecera encoge a 390 px (issue #5).
 *
 * Medido: a 390 px el interior del buscador medía **358** px dentro de una caja de **342** (390 menos los 24 px
 * de márgenes a cada lado). La causa no era un ancho mal puesto: en un contenedor flex `nowrap`, un hijo **no
 * encoge por debajo de su contenido** salvo que se le diga `min-width: 0`. La cadena entera —formulario, caja
 * interior y campo— tiene que poder encoger, o el último eslabón estira a todos los demás.
 *
 * Lo que esta prueba puede demostrar es que la regla sigue ahí; que el navegador la respete se mide en la
 * página, y está en la evidencia del issue.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class BuscadorEncogeTest extends TestCase {

	private function css(): string {
		return (string) file_get_contents( dirname( __DIR__, 2 ) . '/style.css' );
	}

	private function regla( string $selector ): string {
		$css = $this->css();
		$pos = strpos( $css, $selector . ' {' );
		$this->assertNotFalse( $pos, "No se encuentra la regla $selector" );

		return substr( $css, $pos, (int) strpos( $css, '}', $pos ) - $pos );
	}

	public function test_la_cadena_entera_puede_encoger(): void {
		$this->assertStringContainsString( 'min-width: 0', $this->regla( '.convoca-buscador-cabecera__interior .wp-block-search' ), 'El formulario, que es el hijo del flex' );
		$this->assertStringContainsString( 'min-width: 0', $this->regla( '.convoca-buscador-cabecera .wp-block-search__inside-wrapper' ), 'La caja interior' );
		$this->assertStringContainsString( 'min-width: 0', $this->regla( '.convoca-buscador-cabecera .wp-block-search__input' ), 'Y el campo, que es el que pone el ancho mínimo' );
	}

	public function test_el_campo_ocupa_lo_que_le_dejan(): void {
		$this->assertStringContainsString( 'width: 100%', $this->regla( '.convoca-buscador-cabecera .wp-block-search__input' ) );
	}
}
