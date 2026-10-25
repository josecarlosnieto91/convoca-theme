<?php
/**
 * El tema SIN Convoca Core activo: no se inventa el dato de evento.
 *
 * Este caso va en **proceso aparte** y no es capricho: PHP no sabe retirar una función declarada, así que una vez que
 * el doble de `\Convoca\Core\event_meta()` está cargado no hay manera de volver atrás. Antes vivía dentro de
 * `EsquemaDeEventoTest` con un comentario que pedía ser el primero — y eso convertía la suite en algo que solo pasa
 * en un orden (theme#15).
 *
 * Con `#[RunInSeparateProcess]` el caso arranca un PHP limpio, comprueba que la función NO existe y termina; los
 * demás ficheros pueden correr en el orden que quieran.
 *
 * @package Convoca\Theme
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * Sin Core.
 */
final class SinCoreTest extends TestCase {

	/**
	 * El tema no lee la meta por su cuenta: si Core no está, devuelve vacío.
	 */
	#[RunInSeparateProcess]
	public function test_sin_core_no_hay_dato_de_evento(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$this->assertFalse(
			function_exists( '\Convoca\Core\event_meta' ),
			'en este proceso el doble de Core no puede estar cargado'
		);

		$this->assertSame(
			'',
			convoca_get_event_meta( 42, '_convoca_event_start_date' ),
			'sin Core no hay dato: el tema no se lo inventa'
		);
	}
}
