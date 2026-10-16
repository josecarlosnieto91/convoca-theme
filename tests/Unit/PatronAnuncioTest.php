<?php
/**
 * El patrón del anuncio pinta el formulario de la actividad que se le indique (theme#10).
 *
 * MEDIDO (informe del issue, entrada de prueba en la demo): el patrón hermano
 * (`convoca/inscripcion-actividad`) trae el shortcode **dinámico** `[convoca_inscripcion_actual]`, que solo
 * resuelve donde se conoce la actividad actual. Puesto en una entrada de anuncio, los shortcodes
 * desaparecen sin decir nada y el visitante ve un cuadro con un título, una lista vacía y sin formulario.
 *
 * Este patrón usa el bloque, que sí sabe de qué actividad se le habla porque se le indica en el propio
 * bloque; y como el bloque del formulario avisa cuando no tiene actividad, no deja el hueco en blanco.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class PatronAnuncioTest extends TestCase {

	private function leer( string $rel ): string {
		return (string) file_get_contents( dirname( __DIR__, 2 ) . '/' . $rel );
	}

	/** El patrón existe, con su slug y su descripción. */
	public function test_el_patron_del_anuncio_existe(): void {
		$patron = $this->leer( 'patterns/inscripcion-anuncio.php' );

		$this->assertStringContainsString( 'Slug: convoca/inscripcion-anuncio', $patron );
		$this->assertStringContainsString( 'Title:', $patron );
		$this->assertStringContainsString( 'Description:', $patron );
		$this->assertStringContainsString( 'Categories: convoca', $patron );
	}

	/** Y trae el bloque del formulario, no el shortcode dinámico. */
	public function test_usa_el_bloque_con_actividad_explicita(): void {
		$patron = $this->leer( 'patterns/inscripcion-anuncio.php' );

		$this->assertStringContainsString(
			'wp:convoca-enroll/formulario-inscripcion',
			$patron,
			'El anuncio tiene que traer el bloque del formulario, que sabe de qué actividad se le habla'
		);
		$this->assertStringContainsString( '"actividadId"', $patron, 'Con su actividad declarada en el bloque' );

		$this->assertStringNotContainsString(
			'convoca_inscripcion_actual',
			$patron,
			'El shortcode dinámico NO resuelve en una entrada de anuncio: es justo el defecto del hermano'
		);
	}
}
