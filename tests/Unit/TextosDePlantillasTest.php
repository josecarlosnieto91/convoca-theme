<?php
/**
 * Los textos de las plantillas del tema pasan por traducción (docs#7).
 *
 * MEDIDO en la demo: en una web en castellano se leían «Latest», «Error 404», «Keep reading», «Privacy policy»,
 * «LOCATION», «SPOTS»… Las plantillas y las partes son marcado de bloque y **no ejecutan PHP**, así que un
 * rótulo escrito dentro no pasa por ninguna traducción: se ve tal cual, en inglés.
 *
 * El tema ya resolvía así el botón de modo oscuro (#7); esto lo generaliza y, sobre todo, deja un guardián: si
 * alguien vuelve a escribir un rótulo a mano en una plantilla, esta prueba lo dice —con el texto delante— en
 * vez de enterarse quien lee la web.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class TextosDePlantillasTest extends TestCase {

	private function raiz(): string {
		return dirname( __DIR__, 2 );
	}

	/** El filtro está enganchado y solo toca el contenido de las etiquetas, nunca un atributo. */
	public function test_el_filtro_no_toca_atributos(): void {
		$fuente = (string) file_get_contents( $this->raiz() . '/functions.php' );

		$this->assertStringContainsString(
			"add_filter( 'render_block', 'convoca_theme_traduce_textos_de_plantillas'",
			$fuente,
			'El filtro tiene que estar enganchado'
		);
		$this->assertStringContainsString( "preg_quote( \$original, '/' )", $fuente, 'El texto se busca literal, no como expresión regular' );
		$this->assertStringContainsString(
			"'/>\\s*'",
			$fuente,
			'Solo se sustituye el texto que es TODO el contenido de una etiqueta: un `class` o un `href` no se traducen'
		);
	}

	/** Cada rótulo escrito a mano en una plantilla está declarado. */
	public function test_no_queda_ningun_rotulo_sin_declarar(): void {
		$fuente = (string) file_get_contents( $this->raiz() . '/functions.php' );

		preg_match( '/function convoca_theme_textos_de_plantillas\(\): array \{\s*return array\((.*?)\);\s*\}/s', $fuente, $m );
		$this->assertNotEmpty( $m, 'No he encontrado la lista de textos declarada' );

		preg_match_all( "/'((?:[^'\\\\]|\\\\.)+)'\\s*=>/", $m[1], $declarados );
		$declarados = array_map( 'trim', $declarados[1] );

		$sin_declarar = array();
		foreach ( array( 'templates', 'parts' ) as $carpeta ) {
			foreach ( glob( $this->raiz() . '/' . $carpeta . '/*' ) as $fichero ) {
				foreach ( explode( "\n", (string) file_get_contents( $fichero ) ) as $n => $linea ) {
					if ( preg_match( '/__\(|_e\(|esc_html_e|esc_attr_e|sprintf\(|printf\(/', $linea ) ) {
						continue;
					// Y los textos que viven en ATRIBUTOS de bloque (`content`, `label`, `placeholder`, `buttonText`,
					// `linkLabel`): «View more» y la paginación no se pintan entre etiquetas, así que el patrón de
					// arriba no los veía y salían en inglés (theme#13).
					if ( preg_match_all( '/"(?:content|label|placeholder|buttonText|linkLabel|text)"\s*:\s*"([^"]{2,200})"/', $linea, $attrs ) ) {
						foreach ( $attrs[1] as $texto ) {
							if ( ! in_array( $texto, $declarados, true ) ) {
								$sin_declarar[] = basename( $fichero ) . ':' . ( $n + 1 ) . ' atributo «' . $texto . '»';
							}
						}
					}

					}
					if ( preg_match_all( '/>\s*([A-Za-z][^<>{}]{2,200}?)\s*</', $linea, $textos ) ) {
						foreach ( $textos[1] as $texto ) {
							$texto = trim( $texto );
							if ( preg_match( '/^[\s\d.,:;|\/\\\\-]*$/', $texto ) ) {
								continue;
							}
							if ( ! in_array( $texto, $declarados, true ) ) {
								$sin_declarar[] = basename( $fichero ) . ':' . ( $n + 1 ) . ' «' . $texto . '»';
							}
						}
					}
				}
			}
		}

		$this->assertSame(
			array(),
			$sin_declarar,
			"Hay rótulos escritos a mano que no pasan por traducción (docs#7):\n  " . implode( "\n  ", $sin_declarar )
		);
	}

	/**
	 * Y el catálogo castellano no deja nada sin traducir, que es el criterio del informe.
	 *
	 * Se recorren las entradas uniendo antes las líneas de continuación: una entrada puede estar partida en
	 * varias (`msgid ""` + `"…"`), y una búsqueda de línea suelta da falsos avisos.
	 */
	public function test_el_catalogo_no_tiene_nada_sin_traducir(): void {
		$po     = (string) file_get_contents( $this->raiz() . '/languages/es_ES.po' );
		$lineas = array();

		foreach ( explode( "\n", $po ) as $linea ) {
			$limpia = trim( $linea );
			if ( '' === $limpia || str_starts_with( $limpia, '#' ) ) {
				continue;
			}
			$ultima = $lineas ? $lineas[ count( $lineas ) - 1 ] : '';
			if ( str_starts_with( $limpia, '"' ) && preg_match( '/^(msgid|msgstr)\s/', trim( $ultima ) ) ) {
				$lineas[ count( $lineas ) - 1 ] .= ' ' . $limpia;
				continue;
			}
			$lineas[] = $limpia;
		}

		$sin_traducir = array();
		foreach ( $lineas as $i => $linea ) {
			if ( preg_match( '/^msgid\s+"(.+)"$/', $linea, $m ) && isset( $lineas[ $i + 1 ] ) && 'msgstr ""' === $lineas[ $i + 1 ] ) {
				$sin_traducir[] = $m[1];
			}
		}

		$this->assertSame(
			array(),
			$sin_traducir,
			"Quedan cadenas sin traducir en es_ES.po (docs#7: el criterio es cero):\n  " . implode( "\n  ", $sin_traducir )
		);
	}
}
