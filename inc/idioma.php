<?php
/**
 * Selector de idioma y enlaces del sitio.
 *
 * Nueve funciones que se llamaban entre ellas y nada más. Salen de `functions.php` —2.127 líneas y 66
 * funciones en un solo fichero— sin cambiar una línea: la batería del tema (169 pruebas) las cubre.
 *
 * @package Convoca
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enlaces del sitio usados por el pie y las llamadas a la acción.
 *
 * Vacíos por defecto: cada instalación (o su theme hijo) declara sus URLs
 * reales. Un enlace sin URL no se pinta, así el theme no arrastra rutas de
 * otra instalación.
 *
 * @since 2.8.0
 * @return array<string,string> clave => URL absoluta.
 */
function convoca_theme_get_site_links(): array {
	// Cacheado por petición: el pie, el menú y los CTA piden estos enlaces en CADA
	// bloque renderizado. Resolverlos cuesta una consulta por enlace (get_page_by_path)
	// más otra por permalink, así que sin caché una página normal hacía miles de
	// consultas y cualquier bucle de render se llevaba por delante el worker de PHP.
	static $links = null;
	if ( null !== $links ) {
		return $links;
	}

	$links = apply_filters(
		'convoca_theme_site_links',
		array(
			'about'        => '',
			'transparency' => '',
			'projects'     => '',
			'alliances'    => '',
			'activities'   => '',
			'membership'   => '',
			'volunteer'    => '',
			'donations'    => '',
			'centro'       => '',
			'privacy'      => '',
			'cookies'      => '',
			'legal'        => '',
			'contact'      => '',
			'featured'     => '',
			'news'         => '',
			'library'      => '',
			'links'        => '',
		)
	);

	return $links;
}

/**
 * Etiquetas de los enlaces del sitio.
 *
 * @since 2.8.0
 * @return array<string,string> clave => texto visible.
 */
function convoca_theme_get_link_labels(): array {
	// Igual que los enlaces: cacheado por petición (se pide en cada bloque).
	static $labels = null;
	if ( null !== $labels ) {
		return $labels;
	}

	$labels = apply_filters(
		'convoca_theme_link_labels',
		array(
			'about'        => __( 'Who we are', 'convoca' ),
			'transparency' => __( 'Transparency', 'convoca' ),
			'projects'     => __( 'Projects', 'convoca' ),
			'alliances'    => __( 'Alliances', 'convoca' ),
			'activities'   => __( 'Activities', 'convoca' ),
			'membership'   => __( 'Become a member', 'convoca' ),
			'volunteer'    => __( 'Volunteering', 'convoca' ),
			'donations'    => __( 'Donations', 'convoca' ),
			'centro'       => __( 'Community centre', 'convoca' ),
			'privacy'      => __( 'Privacy', 'convoca' ),
			'cookies'      => __( 'Cookies', 'convoca' ),
			'legal'        => __( 'Legal notice', 'convoca' ),
		)
	);

	return $labels;
}

/**
 * URL del CTA de cabecera. Sin URL, el botón no se muestra.
 *
 * @since 2.8.0
 */
function convoca_theme_get_cta_url(): string {
	$url = (string) apply_filters( 'convoca_theme_cta_url', '' );
	if ( '' === $url ) {
		$links = convoca_theme_get_site_links();
		$url   = $links['membership'] ?? '';
	}
	return $url;
}

/**
 * Etiqueta del CTA de cabecera.
 *
 * @since 2.8.0
 */
function convoca_theme_get_cta_label(): string {
	return (string) apply_filters( 'convoca_theme_cta_label', __( 'Join us', 'convoca' ) );
}

/**
 * Language Switcher — añade selector de idioma al menú si hay más de un idioma activo.
 *
 * Compatible con WPML (icl_get_languages) y Polylang (pll_the_languages).
 * Se muestra solo cuando hay ≥2 idiomas activos.
 *
 * Soporta dos vías:
 * 1. Menús clásicos → wp_nav_menu_items (añade <li> al final).
 * 2. Themes FSE → render_block (inyecta el selector dentro del <nav> del bloque
 *    de navegación, antes del cierre).
 */
function convoca_theme_lang_languages(): array {
	$languages = array();

	if ( function_exists( 'icl_get_languages' ) ) {
		$raw = icl_get_languages( 'skip_missing=0&orderby=code' );
		if ( ! empty( $raw ) ) {
			foreach ( $raw as $lang ) {
				$languages[] = array(
					'code'       => $lang['language_code'],
					'name'       => $lang['native_name'] ? $lang['native_name'] : $lang['translated_name'],
					'url'        => $lang['url'],
					'is_current' => ! empty( $lang['active'] ),
				);
			}
		}
	} elseif ( function_exists( 'pll_the_languages' ) ) {
		// Polylang fallback.
		$pll = pll_the_languages(
			array(
				'raw'          => 1,
				'hide_current' => 0,
			)
		);
		if ( is_array( $pll ) ) {
			foreach ( $pll as $lang ) {
				$languages[] = array(
					'code'       => $lang['slug'],
					'name'       => $lang['name'],
					'url'        => $lang['url'],
					'is_current' => ! empty( $lang['current_lang'] ),
				);
			}
		}
	}

	return $languages;
}

/**
 * Devuelve el HTML del selector de idioma, o cadena vacía si sólo hay un idioma.
 *
 * @param string $tag Etiqueta contenedora de cada idioma: 'li' (por defecto) o 'div'
 *                    cuando el llamador pinta fuera de una lista.
 * @return string HTML del selector.
 */
function convoca_theme_lang_switcher_html( string $tag = 'li' ): string {
	$languages = convoca_theme_lang_languages();
	if ( count( $languages ) < 2 ) {
		return '';
	}
	// El contenedor es un <li> cuando el selector va DENTRO de una lista (menu
	// clasico). Cuando el bloque lo deja fuera del <ul>, un <li> no es valido:
	// axe lo marca como «List item does not have a <ul> parent» — asi que ahi se
	// pide un <div>. El CSS va por las clases `convoca-lang-switcher*`, no por el tag.
	$tag = ( 'div' === $tag ) ? 'div' : 'li';

	$current_code = 'EN';
	foreach ( $languages as $lang ) {
		if ( ! empty( $lang['is_current'] ) ) {
			$current_code = strtoupper( $lang['code'] );
			break;
		}
	}

	$id = 'convoca-lang-' . wp_unique_id();

	$html  = '<' . $tag . ' class="menu-item menu-item-type-custom menu-item-object-custom convoca-lang-switcher convoca-lang-switcher--dropdown">';
	$html .= '<button type="button" class="convoca-lang-switcher__toggle" aria-label="' . esc_attr__( 'Change language', 'convoca' ) . '" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '">';
	$html .= '<span class="convoca-lang-switcher__globe" aria-hidden="true">🌐</span>';
	$html .= '<span class="convoca-lang-switcher__code">' . esc_html( $current_code ) . '</span>';
	$html .= '<span class="convoca-lang-switcher__caret" aria-hidden="true">▾</span>';
	$html .= '</button>';
	$html .= '<ul class="convoca-lang-switcher__dropdown" id="' . esc_attr( $id ) . '">';
	foreach ( $languages as $lang ) {
		$cls   = $lang['is_current'] ? 'convoca-lang-switcher__link is-active' : 'convoca-lang-switcher__link';
		$html .= '<li class="convoca-lang-switcher__item">';
		$html .= '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( $lang['url'] ) . '" hreflang="' . esc_attr( $lang['code'] ) . '" lang="' . esc_attr( $lang['code'] ) . '">' . esc_html( $lang['name'] ) . '</a>';
		$html .= '</li>';
	}
	$html .= '</ul></' . $tag . '>';

	return $html;
}

/**
 * Añade el selector de idioma al final del menú clásico.
 *
 * @param string $items HTML de los elementos del menú.
 * @param object $args  Objeto con los argumentos del menú.
 * @return string Elementos del menú, con el selector añadido.
 */
function convoca_theme_language_switcher( $items, $args ) {
	if ( ! function_exists( 'icl_get_languages' ) && ! function_exists( 'pll_the_languages' ) ) {
		return $items;
	}

	return $items . convoca_theme_lang_switcher_html();
}

/**
 * FSE: inyecta el selector en los bloques de navegación (core/navigation).
 *
 * @param string $block_content Contenido HTML del bloque de navegación.
 * @param array  $block         Bloque completo (nombre, atributos...).
 * @return string Contenido con el selector añadido.
 */
function convoca_theme_lang_switcher_block( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || 'core/navigation' !== $block['blockName'] ) {
		return $block_content;
	}
	$switcher = convoca_theme_lang_switcher_html();
	if ( '' === $switcher ) {
		return $block_content;
	}

	$cierre_nav = strpos( $block_content, '</nav>' );

	// El selector se colgaba justo antes de `</nav>`, es decir DESPUES del `</ul>`
	// del menu: un <li> suelto, sin lista que lo contenga. axe lo marca («List item
	// does not have a <ul> parent») y es HTML invalido. Va DENTRO de la ultima
	// lista del bloque, que es donde un <li> es valido.
	if ( false !== $cierre_nav ) {
		$antes_del_nav = substr( $block_content, 0, $cierre_nav );
		$cierre_ul     = strrpos( $antes_del_nav, '</ul>' );
		if ( false !== $cierre_ul ) {
			return substr_replace( $block_content, $switcher, $cierre_ul, 0 );
		}
		// Sin lista donde meterlo: se inyecta un <div> (nunca un <li> suelto).
		return substr_replace( $block_content, convoca_theme_lang_switcher_html( 'div' ), $cierre_nav, 0 );
	}

	return $block_content . convoca_theme_lang_switcher_html( 'div' );
}

/**
 * Estilos del selector de idioma (inline para no depender de assets compilados).
 */
function convoca_theme_lang_switcher_styles(): void {
	echo '<style>
    .convoca-lang-switcher { position:relative; display:inline-flex; align-items:center; }
    .convoca-lang-switcher__toggle { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border:1px solid rgba(255,135,0,0.25); border-radius:20px; background:rgba(255,135,0,0.08); cursor:pointer; font-size:0.75rem; font-weight:700; line-height:1; color:inherit; transition:background 0.2s ease, border-color 0.2s ease; }
    .convoca-lang-switcher__toggle:hover { background:rgba(255,135,0,0.16); border-color:rgba(255,135,0,0.45); }
    .convoca-lang-switcher__globe { font-size:0.8rem; }
    .convoca-lang-switcher__code { letter-spacing:0.04em; }
    .convoca-lang-switcher__caret { font-size:0.6rem; opacity:0.7; }
    .convoca-lang-switcher__dropdown { display:none; position:absolute; top:calc(100% + 6px); right:0; z-index:999; min-width:130px; margin:0; padding:6px; list-style:none; background:#1A0B16; border:1px solid rgba(255,135,0,0.18); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.45); }
    .convoca-lang-switcher--dropdown.is-open .convoca-lang-switcher__dropdown { display:block; }
    .convoca-lang-switcher__item { margin:0; padding:0; }
    .convoca-lang-switcher__link { display:block; padding:7px 12px; font-size:0.8rem; font-weight:600; text-decoration:none; opacity:0.75; border-radius:6px; white-space:nowrap; }
    .convoca-lang-switcher__link:hover { opacity:1; background:rgba(255,135,0,0.12); }
    .convoca-lang-switcher__link.is-active { opacity:1; color:#FF8700; }
    </style>
    <script>
    (function () {
        function positionDropdown(li) {
            var dd = li.querySelector(".convoca-lang-switcher__dropdown");
            var toggle = li.querySelector(".convoca-lang-switcher__toggle");
            if (!dd || !toggle) return;
            var tr = toggle.getBoundingClientRect();
            // Fixed positioning escapes the sticky header stacking context.
            dd.style.position = "fixed";
            dd.style.top = (tr.bottom + 6) + "px";
            dd.style.right = (window.innerWidth - tr.right) + "px";
            dd.style.left = "auto";
        }
        function closeAll(except) {
            document.querySelectorAll(".convoca-lang-switcher--dropdown.is-open").forEach(function (li) {
                if (li !== except) {
                    li.classList.remove("is-open");
                    var b = li.querySelector(".convoca-lang-switcher__toggle");
                    if (b) b.setAttribute("aria-expanded", "false");
                }
            });
        }
        document.addEventListener("click", function (e) {
            var toggle = e.target.closest(".convoca-lang-switcher__toggle");
            if (!toggle) {
                closeAll(null);
                return;
            }
            var li = toggle.closest(".convoca-lang-switcher--dropdown");
            var wasOpen = li.classList.contains("is-open");
            closeAll(li);
            var open = !wasOpen;
            li.classList.toggle("is-open", open);
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
            if (open) positionDropdown(li);
        });
        window.addEventListener("resize", function () {
            var open = document.querySelector(".convoca-lang-switcher--dropdown.is-open");
            if (open) positionDropdown(open);
        });
        window.addEventListener("scroll", function () {
            var open = document.querySelector(".convoca-lang-switcher--dropdown.is-open");
            if (open) positionDropdown(open);
        });
    })();
    </script>';
}
