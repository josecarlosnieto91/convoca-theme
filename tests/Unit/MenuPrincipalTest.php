<?php
/**
 * Menú principal por defecto (theme#32).
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Cubre la creación del menú y el ref del bloque de navegación.
 */
final class MenuPrincipalTest extends TestCase {

	/**
	 * Estado previo de las opciones.
	 *
	 * @var array
	 */
	private array $opciones = array();

	/**
	 * Prepara el escenario.
	 */
	protected function setUp(): void {
		$this->opciones = $GLOBALS['convoca_test_opciones'] ?? array();

		$GLOBALS['convoca_test_opciones']       = array();
		$GLOBALS['convoca_test_menu_locations'] = array();
		$GLOBALS['convoca_test_menu_creado']    = '';
		$GLOBALS['convoca_test_menu_items']     = array();
		$GLOBALS['convoca_test_theme_mods']     = array();
	}

	/**
	 * Restaura el escenario.
	 */
	protected function tearDown(): void {
		$GLOBALS['convoca_test_opciones'] = $this->opciones;
		unset(
			$GLOBALS['convoca_test_menu_locations'],
			$GLOBALS['convoca_test_menu_creado'],
			$GLOBALS['convoca_test_menu_items'],
			$GLOBALS['convoca_test_theme_mods']
		);
	}

	/**
	 * Sin menú asignado se crea uno y se asigna a primary.
	 */
	public function test_crea_y_asigna_el_menu_principal(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		\convoca_theme_crear_menu_principal();

		$this->assertSame( 'Convoca — Principal', $GLOBALS['convoca_test_menu_creado'] );
		$this->assertSame( 77, $GLOBALS['convoca_test_theme_mods']['nav_menu_locations']['primary'] );
		$this->assertSame( 1, get_option( 'convoca_theme_menu_principal_creado' ) );
	}

	/**
	 * Si ya hay menú, no se crea otro.
	 */
	public function test_no_duplica_si_ya_hay_menu(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$GLOBALS['convoca_test_menu_locations'] = array( 'primary' => 12 );
		\convoca_theme_crear_menu_principal();

		$this->assertSame( '', $GLOBALS['convoca_test_menu_creado'] );
		$this->assertSame( 1, get_option( 'convoca_theme_menu_principal_creado' ) );
	}

	/**
	 * El bloque de navegación recibe el ref del menú principal.
	 */
	public function test_el_bloque_de_navegacion_recibe_el_ref(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$GLOBALS['convoca_test_menu_locations'] = array( 'primary' => 34 );

		$bloque = \convoca_theme_nav_ref(
			array(
				'blockName' => 'core/navigation',
				'attrs'     => array(),
			)
		);

		$this->assertSame( 34, $bloque['attrs']['ref'] );
	}

	/**
	 * Un ref explícito no se pisa.
	 */
	public function test_respeta_un_ref_explicito(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$GLOBALS['convoca_test_menu_locations'] = array( 'primary' => 34 );

		$bloque = \convoca_theme_nav_ref(
			array(
				'blockName' => 'core/navigation',
				'attrs'     => array( 'ref' => 99 ),
			)
		);

		$this->assertSame( 99, $bloque['attrs']['ref'] );
	}
}
