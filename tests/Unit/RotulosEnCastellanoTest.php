<?php
/**
 * Los rótulos del tema se traducen, también los que viven en plantillas HTML (theme#11).
 *
 * DEFECTO QUE FIJA ESTA PRUEBA (medido en la portada de la demo, 15-16/10/2026): el contenido estaba en
 * castellano pero los rótulos del tema no — «Latest posts», «Want to be part of the change?», «Join as a
 * member…», «Write to us», «Explore», «Get involved», «News», «Community»—. La causa es que el texto
 * estaba **escrito a mano dentro de plantillas HTML** (`templates/*.html`, `parts/*.html`), y el HTML de
 * una plantilla **no pasa por gettext**: por eso había catálogo y se seguía sirviendo el inglés.
 *
 * La forma de arreglarlo en un tema FSE es la que ya usa el tema para todo lo demás: una **ficha**
 * (`{label_…}`) que se resuelve en PHP, donde sí hay `__()`. Esta prueba vigila las tres piezas:
 *
 *   1. ninguna plantilla, parte ni patrón sirve un rótulo inglés escrito a mano;
 *   2. cada ficha usada existe en el mapa de reemplazos **y es traducible** (con `__( … , 'convoca' )`);
 *   3. el catálogo `es_ES` trae su traducción (si no, la ficha resolvería igualmente al inglés).
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class RotulosEnCastellanoTest extends TestCase {

	/** Rótulos que se sirvieron en inglés. Si aparece otro, entra aquí. */
	private const ROTULOS = array(
		'Latest posts',
		'Want to be part of the change?',
		'Join as a member, volunteer, or just come and meet us. Every action counts.',
		'Write to us',
		'Explore',
		'Get involved',
		'News',
		'Community',
	);

	/** Fichas que los sustituyen. */
	private const FICHAS = array(
		'label_latest_posts',
		'label_write_us',
		'label_explore',
		'label_involved',
		'label_news',
		'label_community',
	);

	private function raiz(): string {
		return dirname( __DIR__, 2 );
	}

	/** Ficheros de plantilla, parte o patrón del tema. */
	private function ficherosDePlantilla(): array {
		$salida = array();
		foreach ( array( 'templates', 'parts', 'patterns' ) as $carpeta ) {
			foreach ( (array) glob( $this->raiz() . '/' . $carpeta . '/*.{html,php}', GLOB_BRACE ) as $f ) {
				$salida[] = $f;
			}
		}
		return $salida;
	}

	/**
	 * 1 · Ningún rótulo inglés visible escrito a mano.
	 *
	 * Se busca en posición de TEXTO (`>Rótulo<` o `>Rótulo `): un `{news_url}` o un nombre de clase no son
	 * texto visible, y buscarlos como cadena suelta daría falsos positivos.
	 */
	public function test_ninguna_plantilla_sirve_un_rotulo_escrito_a_mano(): void {
		$patron = '/>\s*(' . implode( '|', array_map( 'preg_quote', self::ROTULOS ) ) . ')/';

		foreach ( $this->ficherosDePlantilla() as $f ) {
			$contenido = (string) file_get_contents( $f );
			$relativo  = str_replace( $this->raiz(), '', $f );

			$this->assertDoesNotMatchRegularExpression(
				$patron,
				$contenido,
				$relativo . ' sirve un rótulo en inglés: en HTML no hay gettext, tiene que ser una ficha {label_…}'
			);
		}
	}

	/** 2 · Cada ficha usada existe en el mapa de reemplazos y pasa por gettext. */
	public function test_cada_ficha_de_rotulo_existe_y_es_traducible(): void {
		$functions = (string) file_get_contents( $this->raiz() . '/functions.php' );

		foreach ( self::FICHAS as $ficha ) {
			// La clave del mapa lleva llaves: `'{label_x}' => __( … )`. Buscar `'label_x'` sin ellas no
			// encontraba nada y la prueba caía por su propio patrón (medido al estrenarla).
			$this->assertMatchesRegularExpression(
				"/'\\{" . preg_quote( $ficha, '/' ) . "\\}'\\s*=>\\s*__\\(/",
				$functions,
				'La ficha {' . $ficha . '} tiene que existir en el mapa de reemplazos y resolverse con __()'
			);
		}
	}

	/** 3 · Y las fichas se usan de verdad en las plantillas (si no, sobran). */
	public function test_las_fichas_de_rotulo_se_usan_en_las_plantillas(): void {
		$usadas = '';
		foreach ( $this->ficherosDePlantilla() as $f ) {
			$usadas .= (string) file_get_contents( $f );
		}

		foreach ( self::FICHAS as $ficha ) {
			$this->assertStringContainsString( '{' . $ficha . '}', $usadas, 'La ficha {' . $ficha . '} no la usa nadie' );
		}
	}

	/** 4 · Y el catálogo castellano las traduce (si no, la ficha resolvería al inglés). */
	public function test_el_catalogo_traduce_los_rotulos(): void {
		$po = (string) file_get_contents( $this->raiz() . '/languages/es_ES.po' );

		// Las entradas largas van ENVUELTAS (`msgid ""` + líneas de continuación), así que primero se
		// desenvuelven: buscar `msgid "frase larga"` en una línea no encuentra nada (fallo real de la
		// primera versión de esta prueba).
		$plano = (string) preg_replace( '/"\s*\n"/', '', $po );

		foreach ( self::ROTULOS as $rotulo ) {
			$this->assertMatchesRegularExpression(
				'/msgid "' . preg_quote( $rotulo, '/' ) . '"\s*\nmsgstr "([^"]+)"/',
				$plano,
				'El catálogo es_ES no traduce «' . $rotulo . '»: la ficha lo serviría en inglés'
			);
		}
	}
}
