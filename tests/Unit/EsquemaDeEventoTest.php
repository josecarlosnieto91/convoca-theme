<?php
/**
 * El schema de evento de la entrada: cuándo se publica y con qué datos.
 *
 * Lo que se pinta aquí acaba en Google, así que las dos decisiones que importan son:
 *
 *   1. **no se publica un schema inventado** — una fecha imposible, un precio que no es número o un
 *      evento sin fecha se descartan en vez de salir con un dato falso;
 *   2. **la fecha se interpreta en la zona del sitio y se serializa en UTC** — antes se interpretaba en
 *      la del servidor y se etiquetaba como UTC, con lo que la hora del evento salía desplazada (una
 *      actividad de las 18:30 se anunciaba a las 16:30 o a las 20:30 según el servidor).
 *
 * La región y el país no se dan por sabidos: los declara el sitio con sus filtros, y sin ellos el
 * schema no los lleva.
 *
 * @package Convoca\Theme\Tests
 */

namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Esquema JSON-LD de evento.
 */
final class EsquemaDeEventoTest extends TestCase {

	/**
	 * Carga el tema y deja el contexto en blanco.
	 */
	protected function setUp(): void {
		if ( ! function_exists( 'convoca_event_schema' ) ) {
			require_once dirname( __DIR__, 2 ) . '/functions.php';
		}
		$GLOBALS['convoca_test_post_id']           = 42;
		$GLOBALS['convoca_test_is_single']         = true;
		$GLOBALS['convoca_test_is_search']         = false;
		$GLOBALS['convoca_test_categorias_post']   = array();
		$GLOBALS['convoca_test_contenido']         = 'Una salida de campo con el centro.';
		$GLOBALS['convoca_test_fecha_publicacion'] = '2026-10-01T09:00:00+00:00';
		$GLOBALS['convoca_test_imagen_destacada']  = '';
		$GLOBALS['convoca_test_titulos']           = array( 42 => 'Ruta de las setas' );
	}

	/**
	 * Quita lo que ha puesto la prueba.
	 */
	protected function tearDown(): void {
		unset(
			$GLOBALS['convoca_test_post_id'],
			$GLOBALS['convoca_test_is_single'],
			$GLOBALS['convoca_test_categorias_post'],
			$GLOBALS['convoca_test_contenido'],
			$GLOBALS['convoca_test_fecha_publicacion'],
			$GLOBALS['convoca_test_imagen_destacada'],
			$GLOBALS['convoca_test_titulos'],
			$GLOBALS['convoca_test_event_meta'],
			$GLOBALS['convoca_test_filtros']['convoca_theme_event_region'],
			$GLOBALS['convoca_test_filtros']['convoca_theme_event_country']
		);
	}

	/**
	 * Sin Convoca Core activo no hay dato de evento: el tema no se lo inventa.
	 *
	 * Va el primero a propósito: PHP no sabe retirar una función declarada, así que el caso «sin
	 * Core» solo se puede recorrer antes de cargar su doble.
	 */
	public function test_sin_core_no_hay_dato_de_evento(): void {
		$this->assertFalse(
			function_exists( '\Convoca\Core\event_meta' ),
			'El doble de Core no puede estar cargado todavía'
		);
		$this->assertSame( '', convoca_get_event_meta( 42, '_convoca_event_start_date' ) );
	}

	/**
	 * Con Core activo, la meta la sirve Core (el tema no la lee por su cuenta).
	 */
	public function test_con_core_la_meta_la_sirve_core(): void {
		require_once dirname( __DIR__ ) . '/StubCoreEventMeta.php';
		$GLOBALS['convoca_test_event_meta'][42] = array( '_convoca_event_start_date' => '2026-10-15T18:30' );

		$this->assertSame(
			'2026-10-15T18:30',
			convoca_get_event_meta( 42, '_convoca_event_start_date' ),
			'El dato tiene que venir de Core, que es su dueño'
		);
	}

	/**
	 * La fecha del campo `datetime-local` se interpreta en la zona del sitio y se publica en UTC.
	 */
	public function test_la_fecha_se_interpreta_en_la_zona_del_sitio(): void {
		// Verano en Madrid (UTC+2) y `datetime-local`, que viene SIN zona.
		$this->assertSame( '2026-10-15T16:30:00+00:00', convoca_theme_iso_datetime( '2026-10-15T18:30' ) );
		// Invierno (UTC+1): el mismo texto vale una hora más en UTC.
		$this->assertSame( '2026-01-15T17:30:00+00:00', convoca_theme_iso_datetime( '2026-01-15T18:30' ) );
		// El mismo valor con segundos y con espacio en lugar de la `T` del campo.
		$this->assertSame( '2026-07-01T08:00:00+00:00', convoca_theme_iso_datetime( '2026-07-01T10:00:00' ) );
		$this->assertSame( '2026-07-01T08:00:00+00:00', convoca_theme_iso_datetime( '2026-07-01 10:00' ) );
	}

	/**
	 * Una fecha escrita de otra forma legible se acepta; una imposible o vacía, no.
	 */
	public function test_fechas_ilegibles_o_vacias_no_se_publican(): void {
		$this->assertNull( convoca_theme_iso_datetime( '' ), 'Sin fecha no hay dato' );
		$this->assertNull( convoca_theme_iso_datetime( '   ' ), 'Espacios en blanco tampoco son fecha' );
		$this->assertNull( convoca_theme_iso_datetime( 'el jueves por la tarde' ), 'Un texto que no es fecha no se interpreta' );
		$this->assertSame(
			'2026-10-15T16:30:00+00:00',
			convoca_theme_iso_datetime( '15 October 2026 18:30' ),
			'Otra forma legible se acepta (se lee en la zona del sitio)'
		);
	}

	/**
	 * Fuera de la entrada no se pinta schema, y una entrada que no es evento tampoco.
	 *
	 * Son las dos guardas del tema:
	 *   - `is_single()`: un evento con todos sus datos no puede publicar schema en la portada ni en los
	 *     archivos, donde el JSON-LD iría repetido o hablaría de otra entrada;
	 *   - la marca de evento: una fecha suelta en la meta (resto de una actividad desmarcada) no
	 *     convierte la entrada en evento.
	 */
	public function test_sin_entrada_o_sin_evento_no_se_pinta_nada(): void {
		$GLOBALS['convoca_test_event_meta'][42] = array(
			'_convoca_has_event'        => '1',
			'_convoca_event_start_date' => '2026-10-15T18:30',
		);

		$GLOBALS['convoca_test_is_single'] = false;
		$this->assertSame( '', $this->schema(), 'Fuera de la entrada no hay schema, aunque la entrada sea un evento completo' );

		// En la entrada, con la fecha puesta pero SIN la marca de evento y fuera de las categorías de
		// actividad: no es un evento, así que no hay schema.
		$GLOBALS['convoca_test_is_single']      = true;
		$GLOBALS['convoca_test_event_meta'][42] = array( '_convoca_event_start_date' => '2026-10-15T18:30' );
		$this->assertSame( '', $this->schema(), 'Una fecha suelta no convierte la entrada en evento' );

		unset( $GLOBALS['convoca_test_event_meta'][42] );
		$this->assertSame( '', $this->schema(), 'Una entrada sin evento no publica schema' );
	}

	/**
	 * Una fecha imposible no se publica: nada de datos estructurados con un dato falso.
	 */
	public function test_una_fecha_invalida_no_publica_schema(): void {
		$GLOBALS['convoca_test_event_meta'][42] = array(
			'_convoca_has_event'        => '1',
			'_convoca_event_start_date' => 'sin fecha',
		);

		$this->assertSame( '', $this->schema() );
	}

	/**
	 * El caso mínimo: la entrada está en una categoría de actividad y hay fecha.
	 *
	 * Sin precio declarado el schema sale gratuito, no con un precio inventado; y sin región ni país
	 * declarados, no lleva dirección.
	 *
	 * @return void
	 */
	public function test_el_esquema_minimo_de_una_actividad(): void {
		$GLOBALS['convoca_test_categorias_post'] = array( 'local' );
		$GLOBALS['convoca_test_event_meta'][42]  = array( '_convoca_event_start_date' => '2026-10-15T18:30' );

		$data = $this->schema_json( $this->schema() );

		$this->assertSame( 'Event', $data['@type'] );
		$this->assertSame( 'Ruta de las setas', $data['name'] );
		$this->assertSame( '2026-10-15T16:30:00+00:00', $data['startDate'], 'La hora va en UTC, no en la del servidor' );
		$this->assertSame( '0', $data['offers']['price'], 'Sin precio declarado el evento es gratuito' );
		$this->assertSame( 'EUR', $data['offers']['priceCurrency'] );
		$this->assertSame( 'Convoca', $data['location']['name'], 'Sin dirección, el lugar es el sitio' );
		$this->assertArrayNotHasKey( 'address', $data['location'], 'Sin región ni país no hay dirección' );
		$this->assertArrayNotHasKey( 'endDate', $data, 'Sin hora de fin no se inventa una' );
		$this->assertSame( 'https://schema.org/EventScheduled', $data['eventStatus'] );
	}

	/**
	 * Con todos los datos: dirección, precio, fin, región, país e imagen.
	 */
	public function test_el_esquema_completo_con_direccion_precio_y_fin(): void {
		$GLOBALS['convoca_test_categorias_post']  = array();
		$GLOBALS['convoca_test_event_meta'][42]   = array(
			'_convoca_has_event'        => '1',
			'_convoca_event_start_date' => '2026-01-15 18:30',
			'_convoca_event_end_date'   => '2026-01-15T20:00',
			'_convoca_event_address'    => 'Aula de la naturaleza, Asturias',
			'_convoca_event_price'      => '12.50',
		);
		$GLOBALS['convoca_test_imagen_destacada'] = 'https://example.com/setas.jpg';

		add_filter(
			'convoca_theme_event_region',
			static function () {
				return 'Asturias';
			}
		);
		add_filter(
			'convoca_theme_event_country',
			static function () {
				return 'ES';
			}
		);

		$data = $this->schema_json( $this->schema() );

		$this->assertSame( 'Aula de la naturaleza, Asturias', $data['location']['name'] );
		$this->assertSame( 'Asturias', $data['location']['address']['addressRegion'], 'La región la declara el sitio' );
		$this->assertSame( 'ES', $data['location']['address']['addressCountry'], 'El país lo declara el sitio' );
		$this->assertSame( '12.50', $data['offers']['price'] );
		$this->assertSame( '2026-01-15T19:00:00+00:00', $data['endDate'], 'La hora de fin también va en UTC' );
		$this->assertSame( 'https://example.com/setas.jpg', $data['image'] );
		$this->assertSame( 'https://example.com/', $data['url'] );
		$this->assertSame( '2026-10-01T09:00:00+00:00', $data['offers']['validFrom'], 'La oferta es válida desde la publicación' );
		$this->assertSame( 'Convoca', $data['organizer']['name'] );
	}

	/**
	 * Un precio que no es un número no se publica: sale como gratuito.
	 */
	public function test_un_precio_no_numerico_no_se_publica(): void {
		$GLOBALS['convoca_test_categorias_post'] = array( 'actividades' );
		$GLOBALS['convoca_test_event_meta'][42]  = array(
			'_convoca_event_start_date' => '2026-10-15T18:30',
			'_convoca_event_price'      => 'consulta',
		);

		$data = $this->schema_json( $this->schema() );

		$this->assertSame( '0', $data['offers']['price'] );
	}

	/**
	 * La descripción se recorta: el schema no lleva la entrada entera.
	 */
	public function test_la_descripcion_se_recorta_a_treinta_y_cinco_palabras(): void {
		$GLOBALS['convoca_test_categorias_post'] = array( 'actividades' );
		$GLOBALS['convoca_test_event_meta'][42]  = array( '_convoca_event_start_date' => '2026-10-15T18:30' );
		$GLOBALS['convoca_test_contenido']       = '<p>' . implode( ' ', array_fill( 0, 60, 'palabra' ) ) . '</p>';

		$data = $this->schema_json( $this->schema() );

		$this->assertStringNotContainsString( '<p>', $data['description'], 'El HTML del contenido no entra en el schema' );
		$this->assertCount( 35, explode( ' ', $data['description'] ), 'La descripción se recorta a 35 palabras' );
	}

	/**
	 * Recoge lo que imprime el schema.
	 *
	 * @return string
	 */
	private function schema(): string {
		ob_start();
		convoca_event_schema();

		return (string) ob_get_clean();
	}

	/**
	 * El JSON del schema, ya decodificado.
	 *
	 * @param string $salida Salida del schema.
	 * @return array<string, mixed>
	 */
	private function schema_json( string $salida ): array {
		$this->assertMatchesRegularExpression(
			'~<script type="application/ld\+json">\s*(.+?)\s*</script>~s',
			$salida,
			'Tiene que salir un bloque JSON-LD'
		);
		preg_match( '~<script type="application/ld\+json">\s*(.+?)\s*</script>~s', $salida, $m );
		$data = json_decode( $m[1], true );
		$this->assertIsArray( $data, 'El JSON-LD tiene que ser JSON válido' );

		return $data;
	}
}
