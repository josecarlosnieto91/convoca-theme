<?php
/**
 * El selector de idioma del tema: dónde se cuelga y con qué etiqueta.
 *
 * DEFECTO QUE FIJA ESTA PRUEBA (medido con axe en la demo):
 *
 * El selector se inyectaba justo antes de `</nav>`, o sea DESPUÉS del `</ul>` del
 * menú. Resultado: un `<li>` sin lista que lo contenga —
 *
 *     <li class="menu-item ... convoca-lang-switcher convoca-lang-switcher--dropdown">
 *
 * axe lo marca («List item does not have a <ul> parent») y además es HTML
 * inválido. Un `<li>` solo vale dentro de `<ul>`/`<ol>`.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class LangSwitcherTest extends TestCase {

	private function render( string $html ): string {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		return \convoca_theme_lang_switcher_block( $html, array( 'blockName' => 'core/navigation' ) );
	}

	public function test_por_defecto_el_selector_es_un_li(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$html = \convoca_theme_lang_switcher_html();
		$this->assertStringStartsWith( '<li', ltrim( $html ) );
		$this->assertStringEndsWith( '</li>', rtrim( $html ) );
	}

	public function test_se_puede_pedir_como_div(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$html = \convoca_theme_lang_switcher_html( 'div' );
		$this->assertStringStartsWith( '<div', ltrim( $html ) );
		$this->assertStringEndsWith( '</div>', rtrim( $html ) );
		// El contenedor exterior NO es un <li>. Los items del desplegable sí lo
		// son, y van dentro de su propio <ul>: eso es válido y no se toca.
		$this->assertStringNotContainsString( '<li class="menu-item', $html );
	}

	public function test_el_selector_entra_dentro_de_la_lista_no_despues(): void {
		$bloque = '<nav class="wp-block-navigation"><ul class="wp-block-navigation__container">'
			. '<li class="wp-block-navigation-item"><a href="/">Inicio</a></li>'
			. '</ul></nav>';

		$salida = $this->render( $bloque );

		// El <li> del selector tiene que quedar ANTES del cierre de la lista.
		$pos_ul  = strpos( $salida, '</ul>' );
		$pos_sel = strpos( $salida, 'convoca-lang-switcher--dropdown' );
		$this->assertNotFalse( $pos_sel, 'El selector sigue apareciendo' );
		$this->assertLessThan( $pos_ul, $pos_sel, 'El selector debe ir DENTRO del <ul>, no después' );
	}

	public function test_sin_lista_no_se_cuela_un_li_suelto(): void {
		// Un bloque de navegación sin <ul>: el selector no puede ser un <li>.
		$bloque = '<nav class="wp-block-navigation"><p>Sin lista</p></nav>';
		$salida = $this->render( $bloque );

		$this->assertStringNotContainsString( '<li class="menu-item', $salida, 'Un <li> suelto no es válido' );
		$this->assertMatchesRegularExpression( '/<div class="menu-item[^"]*convoca-lang-switcher/', $salida );
	}

	public function test_sin_nav_devuelve_el_div_y_sigue_siendo_html_valido(): void {
		$bloque = '<div class="wp-block-navigation"><p>algo</p></div>';
		$salida = $this->render( $bloque );
		$this->assertStringNotContainsString( '<li class="menu-item', $salida );
	}
}
