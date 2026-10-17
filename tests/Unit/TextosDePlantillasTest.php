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
					// Los textos que viven en ATRIBUTOS de bloque (`content`, `label`, `placeholder`, `buttonText`,
					// `linkLabel`): «View more» y la paginación no se pintan entre etiquetas, así que el patrón de
					// más abajo no los veía y salían en inglés (theme#13).
					if ( preg_match_all( '/"(?:content|label|placeholder|buttonText|linkLabel|text)"\s*:\s*"([^"]{2,200})"/', $linea, $attrs ) ) {
						foreach ( $attrs[1] as $texto ) {
							// Un atributo con el mismo nombre lleva también COLORES y medidas
							// (`{"color":{"text":"#777777"}}`, `rgba(...)`, `var:preset|...`): no son
							// texto y no se traducen. Y estos ya están en castellano en la plantilla.
							if ( preg_match( '/^(#|rgba?\(|var[:(]|[0-9.]+(px|rem|em|%|vh|vw)$)/', $texto ) ) {
								continue;
							}
							if ( in_array( $texto, array( 'Buscar en la web', 'Buscar en la web…', 'Buscar' ), true ) ) {
								continue;
							}
							if ( ! in_array( $texto, $declarados, true ) ) {
								$sin_declarar[] = basename( $fichero ) . ':' . ( $n + 1 ) . ' atributo «' . $texto . '»';
							}
						}
					}

					if ( preg_match( '/__\(|_e\(|esc_html_e|esc_attr_e|sprintf\(|printf\(/', $linea ) ) {
						continue;
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

	/**
	 * La invariante de verdad (theme#14): un texto inglés escrito en un atributo de bloque no se traduce
	 * solo, así que tiene que estar **en el mapa del tema** (que es lo que el filtro de render usa para
	 * sustituirlo) **y traducido en el catálogo**. Medido: «View more» estaba traducido en el `.po` y no
	 * en el mapa —o al revés— y el navegador recibía el inglés igual.
	 */
	public function test_cada_texto_ingles_de_plantilla_esta_en_el_mapa_y_traducido(): void {
		$funciones = (string) file_get_contents( dirname( __DIR__, 2 ) . '/functions.php' );

		// El mapa del tema: 'texto original' => __( 'texto original', 'convoca' ).
		preg_match_all( "/^\t\t'((?:[^'\\\\]|\\\\.)+)'\s*=> __\(/m", $funciones, $m );
		$mapa = array_flip( $m[1] );

		// El catálogo: msgid => msgstr (sólo las traducidas de verdad).
		$po         = (string) file_get_contents( dirname( __DIR__, 2 ) . '/languages/es_ES.po' );
		$traducidas = array();
		preg_match_all( '/msgid "((?:[^"\\\\]|\\\\.)*)"\nmsgstr "((?:[^"\\\\]|\\\\.)*)"/', $po, $mm, PREG_SET_ORDER );
		foreach ( $mm as $par ) {
			if ( '' !== $par[2] && $par[2] !== $par[1] ) {
				$traducidas[ stripslashes( $par[1] ) ] = true;
			}
		}

		$problemas = array();
		$atributos = array( 'label', 'placeholder', 'buttonText', 'content' );

		foreach ( array( 'templates', 'parts' ) as $carpeta ) {
			foreach ( glob( $this->raiz() . '/' . $carpeta . '/*' ) as $fichero ) {
				$contenido = (string) file_get_contents( $fichero );
				// cada comentario de bloque: <!-- wp:x {json} /-->.
				preg_match_all( '/<!-- wp:[a-z0-9\/-]+ (\{.*?\}) \/-->/', $contenido, $bloques );
				foreach ( $bloques[1] as $json ) {
					$attrs = json_decode( $json, true );
					if ( ! is_array( $attrs ) ) {
						continue;
					}
					foreach ( $atributos as $clave ) {
						if ( ! isset( $attrs[ $clave ] ) || ! is_string( $attrs[ $clave ] ) ) {
							continue;
						}
						$texto = $attrs[ $clave ];

						// Un texto que ya está en castellano en la plantilla es válido (no necesita nada).
						// Lo que se vigila es el que *parece inglés*: ese no lo traduce WordPress solo,
						// porque un atributo de bloque no pasa por `__()`.
						if ( ! preg_match( '/\b(search|view|more|previous|next|site|here|error|latest|reading|keep|entries|read)\b/i', $texto ) ) {
							continue;
						}

						if ( ! isset( $mapa[ $texto ] ) ) {
							$problemas[] = basename( $fichero ) . ': "' . $texto . '" parece inglés y no está en el mapa del tema';
						} elseif ( ! isset( $traducidas[ $texto ] ) ) {
							$problemas[] = basename( $fichero ) . ': "' . $texto . '" está en el mapa pero sin traducir en el .po';
						}
					}
				}
			}
		}

		$this->assertSame( array(), $problemas, "Textos de atributo que saldrían en inglés:\n  " . implode( "\n  ", array_unique( $problemas ) ) );
	}
}
