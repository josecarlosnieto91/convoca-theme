<?php
/**
 * Guardas de estructura y accesibilidad de las plantillas.
 *
 * Tres defectos medidos con axe en un sitio real (04/11/2026) que ningún test
 * miraba porque viven en HTML de plantilla, no en PHP:
 *
 * - `region`: la cabecera de archivo iba antes de `<main>` y el CTA después de
 *   `</main>`, así que ese contenido quedaba fuera de todo landmark;
 * - `landmark-unique`: dos `<nav>` sin nombre accesible (barra superior y
 *   navegación principal);
 * - tablas anchas que arrastran la página entera en móvil.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Landmarks, nombres accesibles y tablas.
 */
final class AccesibilidadDePlantillasTest extends TestCase {

	/**
	 * Plantillas del tema.
	 *
	 * @return array<string,string>
	 */
	private function plantillas(): array {
		$plantillas = array();
		foreach ( glob( dirname( __DIR__, 2 ) . '/templates/*.html' ) as $ruta ) {
			$plantillas[ basename( $ruta ) ] = (string) file_get_contents( $ruta );
		}

		return $plantillas;
	}

	/**
	 * Lo que queda fuera de `<main>` y de los `<aside>` (landmarks propios).
	 *
	 * @param string $html Plantilla.
	 * @return string
	 */
	private function fuera_de_landmarks( string $html ): string {
		$sin = (string) preg_replace( '/<!--.*?-->/s', '', $html );
		$sin = (string) preg_replace( '/<main\b[^>]*>.*?<\/main>/s', '', $sin );
		$sin = (string) preg_replace( '/<aside\b[^>]*>.*?<\/aside>/s', '', $sin );

		return trim( $sin );
	}

	/**
	 * Toda plantilla deja su contenido dentro de un landmark.
	 */
	public function test_ninguna_plantilla_deja_contenido_fuera_de_los_landmarks(): void {
		$plantillas = $this->plantillas();
		$this->assertNotEmpty( $plantillas );

		foreach ( $plantillas as $nombre => $html ) {
			$this->assertSame(
				'',
				$this->fuera_de_landmarks( $html ),
				"«{$nombre}» tiene contenido fuera de <main>: axe lo marca como «region»"
			);
		}
	}

	/**
	 * Cada plantilla tiene un solo `<main>`.
	 */
	public function test_cada_plantilla_tiene_un_solo_main(): void {
		foreach ( $this->plantillas() as $nombre => $html ) {
			$this->assertSame( 1, substr_count( $html, '<main' ), "«{$nombre}» tiene más de un <main>" );
			$this->assertSame( 1, substr_count( $html, '</main>' ), "«{$nombre}» cierra el <main> más de una vez" );
		}
	}

	/**
	 * Las dos navegaciones del header tienen nombre accesible y distinto.
	 */
	public function test_las_dos_navegaciones_tienen_nombre_accesible(): void {
		$header = (string) file_get_contents( dirname( __DIR__, 2 ) . '/parts/header.html' );

		$this->assertStringContainsString( '"ariaLabel":"Navegación principal"', $header, 'la navegación principal necesita aria-label' );
		$this->assertMatchesRegularExpression(
			'/\[convoca_menu[^\]]*label="Enlaces rápidos"/',
			$header,
			'la barra superior necesita su propia etiqueta'
		);
	}

	/**
	 * Las tablas anchas se desplazan dentro de su figura, no con la página.
	 *
	 * La regla del body que ya existía (`overflow-x:hidden`) no cuenta como
	 * arreglo: no evita el scroll, lo esconde. Lo que se exige aquí es el
	 * contenedor con scroll propio.
	 */
	public function test_las_tablas_anchas_no_arrastran_la_pagina(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/style.css' );

		$this->assertMatchesRegularExpression(
			'/\.wp-block-table\s*\{[^}]*overflow-x:\s*auto/s',
			$css,
			'falta el contenedor con scroll para la tabla ancha'
		);
	}

	/**
	 * Ningún titular del contenido se oculta con `display:none`: lo que no se
	 * pinta no existe para quien lee, ni para el lector de pantalla, y deja
	 * saltos de nivel (`heading-order`).
	 */
	public function test_ningun_titular_del_contenido_se_oculta(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/style.css' );
		// Los comentarios citan la regla vieja: sin quitarlos, el test se pondría
		// rojo por la documentación y no por el CSS.
		$css = (string) preg_replace( '/\/\*.*?\*\//s', '', $css );

		$this->assertDoesNotMatchRegularExpression(
			'/\.wp-block-heading:first-child\s*\{[^}]*display:\s*none/s',
			$css,
			'no se esconde el primer titular del contenido'
		);
	}

	/**
	 * La cabecera vertical tiene su regla propia y no se recorta.
	 */
	public function test_la_cabecera_vertical_no_se_recorta(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/style.css' );

		$this->assertMatchesRegularExpression(
			'/\.convoca-single__imagen img\.convoca-cabecera-vertical\s*\{[^}]*max-height:\s*none/s',
			$css,
			'la cabecera vertical necesita max-height:none'
		);
	}
}
