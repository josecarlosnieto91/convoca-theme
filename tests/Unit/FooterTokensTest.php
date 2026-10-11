<?php
/**
 * Tests del render de bloques del tema: los tokens del pie.
 *
 * El defecto que cubre: el pie del tema pone el telefono como
 *   <a href="{phone_url}">{phone_label}</a>
 * y, si la instalacion no configura el telefono, el token no tiene valor. La
 * limpieza de tokens sin resolver solo miraba <li> y botones, asi que en el pie
 * (que va dentro de un <p>) quedaba «{phone_label}» a la vista del visitante.
 */
namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class FooterTokensTest extends TestCase {

	private function render( string $html ): string {
		require_once dirname( __DIR__, 2 ) . '/functions.php';
		$block = array( 'blockName' => 'core/paragraph' );
		return \convoca_theme_render_block( $html, $block );
	}

	public function test_un_token_sin_valor_no_deja_llaves_a_la_vista(): void {
		$pie = '<p style="font-size:0.94rem"><strong>Write to us</strong><br>'
			. '<a href="mailto:demo@example.com">demo@example.com</a><br>'
			. '<a href="{phone_url}">{phone_label}</a></p>';

		$salida = $this->render( $pie );

		$this->assertDoesNotMatchRegularExpression(
			'/\{[a-z0-9_]+\}/i',
			$salida,
			'Un token sin resolver no puede quedar visible en el HTML'
		);
		$this->assertStringNotContainsString( '{phone_label}', $salida );
		// El enlace del correo, que SI tiene valor, debe seguir ahi.
		$this->assertStringContainsString( 'mailto:demo@example.com', $salida );
	}

	public function test_un_enlace_suelto_con_token_se_retira_entero(): void {
		$pie    = '<p>Escribenos<br><a href="{phone_url}">{phone_label}</a></p>';
		$salida = $this->render( $pie );
		$this->assertStringNotContainsString( '<a', $salida, 'El enlace sin destino no se muestra' );
		$this->assertStringContainsString( 'Escribenos', $salida, 'El texto que no es enlace se conserva' );
	}

	public function test_un_token_con_valor_se_sustituye(): void {
		add_filter(
			'convoca_theme_footer_replacements',
			static function ( $r ) {
				$r['{phone_url}']   = 'tel:+34600000000';
				$r['{phone_label}'] = '600 00 00 00';
				return $r;
			}
		);
		$pie    = '<p><a href="{phone_url}">{phone_label}</a></p>';
		$salida = $this->render( $pie );
		$this->assertStringContainsString( '600 00 00 00', $salida );
		$this->assertStringContainsString( 'tel:+34600000000', $salida );
	}

	public function test_los_tokens_de_enlace_del_pie_no_se_rompen(): void {
		// Los enlaces que SI se configuran ({about_url}...) siguen funcionando.
		$pie    = '<ul><li><a href="{about_url}">{about_label}</a></li></ul>';
		$salida = $this->render( $pie );
		$this->assertStringNotContainsString( '{about', $salida, 'Sin valor, el enlace tambien se retira' );
	}
}
