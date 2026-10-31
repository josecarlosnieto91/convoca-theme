<?php
/**
 * El título del archivo no imprime el `<span>` del core (theme#18).
 *
 * WordPress envuelve el nombre de la categoría en un `<span>`; al imprimirse escapado sale como
 * texto literal y su ancho empuja la página en móvil.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Título de archivo limpio.
 */
final class TituloDeArchivoTest extends TestCase {

	/**
	 * Carga el tema.
	 */
	protected function setUp(): void {
		if ( ! function_exists( 'convoca_titulo_de_archivo' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}

		$GLOBALS['convoca_test_archivo_tipo'] = false;
	}

	/**
	 * El título de una categoría llega sin etiquetas.
	 */
	public function test_categoria_sin_span(): void {
		$titulo = convoca_titulo_de_archivo( 'Categoría: <span>Actividades</span>' );

		$this->assertSame( 'Categoría: Actividades', $titulo );
		$this->assertStringNotContainsString( '<span>', $titulo );
	}

	/**
	 * Una etiqueta tampoco deja etiquetas HTML.
	 */
	public function test_etiqueta_sin_span(): void {
		$titulo = convoca_titulo_de_archivo( 'Etiqueta: <span>Noticias</span>' );

		$this->assertSame( 'Etiqueta: Noticias', $titulo );
	}

	/**
	 * En el archivo de un tipo de contenido manda el nombre del tipo.
	 */
	public function test_archivo_de_tipo_usa_su_nombre(): void {
		$GLOBALS['convoca_test_archivo_tipo'] = true;

		$titulo = convoca_titulo_de_archivo( 'Archivos: <span>Actividad</span>' );

		$this->assertStringNotContainsString( '<span>', $titulo );
	}

	/**
	 * El prefijo del core se queda vacío.
	 */
	public function test_sin_prefijo(): void {
		$this->assertSame( '', convoca_sin_prefijo_de_archivo() );
	}
}
