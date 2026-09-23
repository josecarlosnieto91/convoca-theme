<?php
/**
 * Convoca Theme
 *
 * @package    Convoca\Theme
 * @subpackage Convoca-theme
 *
 * @copyright  Copyright (C) 2026 Jose Carlos Nieto Ramos
 * @license    GPL-2.0-or-later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

/**
 * Convoca Theme — Functions v2
 *
 * Minimal functions.php for FSE Block Theme.
 * Handles: theme support, block patterns, block styles, performance, accessibility.
 *
 * @package Convoca
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * One-time utility: Reset database-cached templates/template parts.
 *
 * WordPress stores customized templates in the database (wp_posts), which override
 * theme files. Visit: ?convoca_reset_templates=1 while logged in as admin to clear them.
 * Remove this block after running it once.
 */
/**
 * Safe utility: Reset database-cached templates/template parts.
 *
 * WordPress stores customized templates in the database which override theme files.
 * Only activates via explicit admin page with nonce and rate limit.
 */
add_action(
	'admin_init',
	function () {
		if (
		! empty( $_GET['convoca_reset_templates'] )
		&& current_user_can( 'manage_options' )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'convoca_reset_templates' )
		&& is_admin()
		&& wp_doing_ajax() === false
		) {
			// Rate limit: only once per hour.
			$reset_key  = 'convoca_templates_reset_time';
			$last_reset = get_option( $reset_key, 0 );
			if ( time() - $last_reset < HOUR_IN_SECONDS ) {
				add_action(
					'admin_notices',
					function () {
						echo '<div class="notice notice-warning"><p><strong>Convoca:</strong> Templates can only be reset once per hour. Please wait a few minutes.</p></div>';
					}
				);
				return;
			}

			$types   = [ 'wp_template', 'wp_template_part' ];
			$deleted = 0;
			foreach ( $types as $type ) {
				$posts = get_posts(
					[
						'post_type'      => $type,
						'posts_per_page' => -1,
						'post_status'    => 'any',
						'fields'         => 'ids',
						'tax_query'      => [
							[
								'taxonomy' => 'wp_theme',
								'field'    => 'slug',
								'terms'    => get_stylesheet(),
							],
						],
					]
				);
				foreach ( $posts as $id ) {
					if ( wp_delete_post( $id, true ) ) {
						$deleted++;
					}
				}
			}

			update_option( $reset_key, time() );
			add_action(
				'admin_notices',
				function () use ( $deleted ) {
					$aviso = sprintf(
						/* translators: %d: number of templates and template parts reset. */
						esc_html__( 'Reset %d templates and template parts. They will now be read directly from the theme files.', 'convoca' ),
						$deleted
					);
					echo wp_kses_post( '<div class="notice notice-success"><p><strong>Convoca:</strong> ' . $aviso . '</p></div>' );
				}
			);
		}
	}
);

/**
 * 1. Theme Setup
 */
function convoca_setup(): void {
	// WordPress recibe la RAIZ del tema y añade el /languages el solo. Pasandole ya
	// /languages buscaba en .../languages/languages/ y no cargaba nunca la traduccion.
	load_theme_textdomain( 'convoca', get_template_directory() . '/languages' );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		[
			'height'      => 80,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		]
	);
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]
	);
	add_theme_support( 'post-thumbnails' );

	// Load editor stylesheet.
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'convoca_setup' );

/**
 * 2. Register Block Pattern Categories
 */
function convoca_register_pattern_categories(): void {
	register_block_pattern_category(
		'convoca',
		[
			'label'       => __( 'Convoca', 'convoca' ),
			'description' => __( 'Convoca theme patterns.', 'convoca' ),
		]
	);
	register_block_pattern_category(
		'convoca-layout',
		[
			'label'       => __( 'Convoca — Layout', 'convoca' ),
			'description' => __( 'Full-page sections.', 'convoca' ),
		]
	);
}
add_action( 'init', 'convoca_register_pattern_categories' );

/**
 * 3. Register Custom Block Styles
 */
function convoca_register_block_styles(): void {
	// Paragraph: Lead style.
	register_block_style(
		'core/paragraph',
		[
			'name'  => 'lead',
			'label' => __( 'Lead', 'convoca' ),
		]
	);

	// Group: Card style.
	register_block_style(
		'core/group',
		[
			'name'  => 'card',
			'label' => __( 'Card', 'convoca' ),
		]
	);

	// Group: Coordinator box style.
	register_block_style(
		'core/group',
		[
			'name'  => 'coordinator',
			'label' => __( 'Coordinator box', 'convoca' ),
		]
	);

	// Group: Glass (frosted glass effect).
	register_block_style(
		'core/group',
		[
			'name'  => 'glass',
			'label' => __( 'Frosted glass', 'convoca' ),
		]
	);

	// Cover: Topographic overlay.
	register_block_style(
		'core/cover',
		[
			'name'  => 'topographic',
			'label' => __( 'Topographic overlay', 'convoca' ),
		]
	);

	// Table: Convoca styled table.
	register_block_style(
		'core/table',
		[
			'name'  => 'convoca',
			'label' => __( 'Convoca table', 'convoca' ),
		]
	);

	// Buttons: Secondary (outline on dark).
	register_block_style(
		'core/button',
		[
			'name'  => 'secondary',
			'label' => __( 'Secondary', 'convoca' ),
		]
	);

	// Image: Rounded + shadow.
	register_block_style(
		'core/image',
		[
			'name'  => 'elevated',
			'label' => __( 'Elevated', 'convoca' ),
		]
	);
}
add_action( 'init', 'convoca_register_block_styles' );

/**
 * 4. Resource Hints & Performance
 *
 * @param array  $urls     URLs de resource hints actuales.
 * @param string $relation Tipo de relación (preconnect, dns-prefetch...).
 * @return array URLs modificadas.
 */
function convoca_resource_hints( array $urls, string $relation ): array {
	if ( 'preconnect' === $relation || 'dns-prefetch' === $relation ) {
		$urls[] = [
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		];
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'convoca_resource_hints', 10, 2 );

/**
 * 5. Preload Google Fonts Stylesheet
 *
 * @param string $tag    Etiqueta <link> generada por WordPress.
 * @param string $handle Identificador del estilo.
 * @return string Etiqueta modificada.
 */
function convoca_style_loader_tag( string $tag, string $handle ): string {
	if ( 'convoca-google-fonts' === $handle ) {
		return str_replace( "rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag ) .
			// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- Fallback <noscript> del preload de fuentes; no se puede encolar con wp_enqueue_style().
			'<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Outfit:wght@400..900&display=swap"></noscript>';
	}
	return $tag;
}
add_filter( 'style_loader_tag', 'convoca_style_loader_tag', 10, 2 );

/**
 * 6. Inline Critical CSS (Header & Above the fold)
 */
function convoca_critical_css(): void {
	?>
	<style id="convoca-critical-css">
		:root { --wp--preset--color--naranja: #ff8700; --wp--preset--color--blanco: #ffffff; --wp--preset--color--antracita: #1a1a1a; --wp--preset--spacing--20: 1rem; --wp--custom--transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
		body { margin: 0; font-family: 'Lato', sans-serif; background: var(--wp--preset--color--blanco); color: var(--wp--preset--color--antracita); overflow-x: hidden; }
		.site-header { position: sticky; top: 0; z-index: 100; background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(16px) saturate(180%); border-bottom: 1px solid rgba(0, 0, 0, 0.06); transition: box-shadow var(--wp--custom--transition); padding: var(--wp--preset--spacing--20) 0; }
		.wp-block-group { box-sizing: border-box; }
		/* Solo la fila DIRECTA de la cabecera es contenedor: si se aplica a todos
			los grupos anidados, con un menú largo el grupo interior se queda en
			1200px y desborda la página (scroll horizontal). */
		.site-header > .wp-block-group { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--wp--preset--spacing--20); max-width: 1400px; margin: 0 auto; padding: 0 1.5rem; }
		.site-header .wp-block-navigation { min-width: 0; max-width: 100%; }
		.site-header .wp-block-buttons { flex-shrink: 0; }
		.wp-block-site-title { font-size: 1.6rem; font-weight: 700; margin: 0; font-family: 'Outfit', sans-serif; }
		.wp-block-site-title a { text-decoration: none; color: #2d2d3a; }
		.hero-topographic { min-height: 40vh; display: flex; align-items: center; justify-content: center; position: relative; background: #ff8700; color: #fff; text-align: center; }
	</style>
	<?php
}
add_action( 'wp_head', 'convoca_critical_css', 2 );

/**
 * 7. Performance: remove jQuery migrate, defer scripts, lazy loading
 */
function convoca_performance_tweaks(): void {
	// Remove emoji scripts.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'convoca_performance_tweaks' );

/**
 * Native Lazy Loading for all images.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	function ( $attr, $attachment, $size ) {
		// Skip lazy loading for images with fetchpriority="high" (first image/LCP).
		if ( isset( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] ) {
			return $attr;
		}
		$attr['loading'] = 'lazy';
		return $attr;
	},
	10,
	3
);

/**
 * 8. Accessibility: skip link
 */
function convoca_skip_link(): void {
	echo '<a class="skip-link screen-reader-text" href="#main-content">' .
		esc_html__( 'Skip to content', 'convoca' ) . '</a>';
}
add_action( 'wp_body_open', 'convoca_skip_link' );

/**
 * 9. Custom image sizes for cards
 */
function convoca_image_sizes(): void {
	add_image_size( 'convoca-card', 600, 400, true );
	add_image_size( 'convoca-hero', 1600, 900, true );
}
add_action( 'after_setup_theme', 'convoca_image_sizes' );

/**
 * Enqueue scripts and styles.
 */
function convoca_theme_scripts() {
	// Google Fonts: Lato + Outfit.
	$version_theme = wp_get_theme()->get( 'Version' );
	$theme_version = $version_theme ? $version_theme : '1.0';
	wp_enqueue_style(
		'convoca-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Outfit:wght@400..900&display=swap',
		array(),
		$theme_version
	);

	// Versión = fecha de modificación del fichero. Con la versión del theme
	// (fija), el navegador cachea la hoja y los cambios de CSS no llegan: se
	// mide y se diagnostica sobre un estado viejo. Así cada edición invalida la
	// caché sola.
	$version_css = @filemtime( get_stylesheet_directory() . '/style.css' );
	wp_enqueue_style(
		'convoca-theme-style',
		get_stylesheet_uri(),
		array( 'convoca-google-fonts' ),
		$version_css ? (string) $version_css : $theme_version
	);
}
add_action( 'wp_enqueue_scripts', 'convoca_theme_scripts' );

/**
 * 11. Admin Help Page: Ayuda Convoca
 */
function convoca_admin_menu(): void {
	add_theme_page(
		__( 'Convoca Help', 'convoca' ),
		__( 'Convoca Help', 'convoca' ),
		'edit_theme_options',
		'convoca-help',
		'convoca_help_page_html'
	);
}
add_action( 'admin_menu', 'convoca_admin_menu' );

/**
 * Pinta la página de ayuda del theme en el escritorio de WordPress.
 */
function convoca_help_page_html(): void {
	$theme = wp_get_theme();
	?>
	<div class="wrap convoca-admin-page">
		<h1><?php echo esc_html__( 'Settings & Help — Convoca Theme', 'convoca' ); ?></h1>
		
		<div class="welcome-panel" style="padding: 0; margin-top: 20px; overflow: hidden; border-radius: 8px; border: none; background: #000;">
			<div class="welcome-panel-content" style="padding: 60px 40px; background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/admin-banner.png' ); ?>'); background-size: cover; background-position: center; color: #fff;">
				<h2 style="color: #fff; font-size: 2.4em; margin: 0; font-family: 'Outfit', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"><?php printf( /* translators: %s: version of the installed theme. */ esc_html__( 'Welcome to Convoca v%s', 'convoca' ), esc_html( (string) $theme->get( 'Version' ) ) ); ?></h2>
				<p class="about-description" style="color: rgba(255,255,255,0.9); font-size: 1.2em; max-width: 600px; margin-top: 10px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><?php echo esc_html__( 'This is an FSE (Full Site Editing) theme for associations and community groups. Here you will find a quick start guide.', 'convoca' ); ?></p>
			</div>
		</div>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container">
					
					<!-- Theme Information -->
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Theme information', 'convoca' ); ?></span></h2>
						<div class="inside">
							<ul>
								<li><strong><?php echo esc_html__( 'Version:', 'convoca' ); ?></strong> <?php echo esc_html( $theme->get( 'Version' ) ); ?></li>
								<li><strong><?php echo esc_html__( 'Author:', 'convoca' ); ?></strong> <a href="<?php echo esc_url( $theme->get( 'AuthorURI' ) ); ?>" target="_blank"><?php echo esc_html( $theme->get( 'Author' ) ); ?></a></li>
								<li><strong><?php echo esc_html__( 'Documentation:', 'convoca' ); ?></strong> <a href="https://github.com/josecarlosnieto91/convoca-theme/wiki" target="_blank"><?php echo esc_html__( 'View the GitHub wiki', 'convoca' ); ?></a></li>
							</ul>
							<hr>
							<a href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Open Site Editor (FSE)', 'convoca' ); ?></a>
						</div>

					<!-- Templates & Pages Guide -->
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Templates & Pages Guide', 'convoca' ); ?></span></h2>
						<div class="inside">
							<h4><?php echo esc_html__( 'Activity Page', 'convoca' ); ?></h4>
							<p><?php echo esc_html__( 'Use the "Activity Page" template for pages that describe a specific activity. It automatically includes the registration form.', 'convoca' ); ?></p>
							
							<h4><?php echo esc_html__( 'Project Page', 'convoca' ); ?></h4>
							<p><?php echo esc_html__( 'Use the "Project Page" template for long-running project sections. It includes project-specific metadata.', 'convoca' ); ?></p>
							
							<hr>
							<p><strong><?php echo esc_html__( 'Note:', 'convoca' ); ?></strong> <?php echo esc_html__( 'If the templates do not display correctly, you can try resetting them.', 'convoca' ); ?></p>
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'themes.php?page=convoca-help&convoca_reset_templates=1' ), 'convoca_reset_templates' ) ); ?>" class="button button-link-delete" onclick="return confirm('<?php echo esc_js( __( 'Are you sure? This will delete any customization you made in the Site Editor and revert to the theme files.', 'convoca' ) ); ?>');"><?php echo esc_html__( 'Reset templates to factory defaults', 'convoca' ); ?></a>
						</div>
					</div>

					<!-- Recommended Patterns -->
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Convoca patterns', 'convoca' ); ?></span></h2>
						<div class="inside">
							<p><?php echo esc_html__( 'You can insert these pre-designed blocks from the editor (+) > Patterns > Convoca:', 'convoca' ); ?></p>
							<ul style="list-style: disc; padding-left: 20px;">
								<li><code>convoca/hero</code>: <?php echo esc_html__( 'Main header with text.', 'convoca' ); ?></li>
								<li><code>convoca/cards-grid</code>: <?php echo esc_html__( 'Grid of activities or news.', 'convoca' ); ?></li>
								<li><code>convoca/stats-bar</code>: <?php echo esc_html__( 'Animated stats bar.', 'convoca' ); ?></li>
								<li><code>convoca/inscripcion-actividad</code>: <?php echo esc_html__( 'Built-in registration form.', 'convoca' ); ?></li>
							</ul>
						</div>
					</div>

				</div>
			</div>
			</div>
			</div>
	</div>

			<style>
		.convoca-admin-page h1 { font-family: 'Outfit', sans-serif; font-weight: 700; margin-bottom: 20px; }
		.convoca-admin-page .postbox .hndle { cursor: default; }
		.convoca-admin-page .inside ul { margin-top: 10px; }
		.convoca-admin-page .button-link-delete { color: #d63638; text-decoration: none; }
		.convoca-admin-page .button-link-delete:hover { color: #b32d2e; }
	</style>
	<?php
}

/**
 * Enqueue Google Fonts in the block editor too.
 */
function convoca_editor_fonts(): void {
	wp_enqueue_style(
		'convoca-editor-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Outfit:wght@400..700&display=swap',
		array(),
		null
	);
}
add_action( 'enqueue_block_editor_assets', 'convoca_editor_fonts' );

// Shortcodes moved to plugins: convoca_mi_perfil → convoca-members, etc.

/**
 * 10. Enqueue theme JS (scroll-to-top, header shadow, animations)
 */
function convoca_enqueue_scripts(): void {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_script(
		'convoca',
		get_theme_file_uri( 'assets/js/convoca-theme.js' ),
		[],
		$version,
		[
			'strategy'  => 'defer',
			'in_footer' => true,
		]
	);

	wp_enqueue_script(
		'convoca-dark-mode',
		get_theme_file_uri( 'assets/js/dark-mode.js' ),
		[],
		$version,
		[
			'strategy'  => 'defer',
			'in_footer' => true,
		]
	);

	// Las etiquetas del boton las oye un lector de pantalla, asi que son texto de verdad y
	// viven en PHP, que es donde se traduce. El JavaScript solo las pinta.
	wp_localize_script(
		'convoca-dark-mode',
		'convocaModoOscuro',
		[
			'etiquetaOscuro' => __( 'Switch to dark mode', 'convoca' ),
			'etiquetaClaro'  => __( 'Switch to light mode', 'convoca' ),
		]
	);
}
add_action( 'wp_enqueue_scripts', 'convoca_enqueue_scripts' );

/**
 * 9. Dark Mode Inline Init (Prevents FOUC)
 */
function convoca_dark_mode_inline_init(): void {
	?>
	<script id="convoca-dark-mode-init">
		(function() {
			try {
				const mode = localStorage.getItem('convoca-theme-mode') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
				if (mode === 'dark') document.documentElement.classList.add('dark-mode'), document.body.classList.add('dark-mode');
			} catch (e) {}
		})();
	</script>
	<?php
}
add_action( 'wp_head', 'convoca_dark_mode_inline_init', 1 );

/**
 * 10. SEO: JSON-LD Structured Data for Activities (Event Schema)
 */

// ─── Process shortcodes in rendered blocks (FSE compatibility) ───

/**
 * Core/read-more appends the post title after content ("Más información : Título").
 * Keep only the explicit label on the activity cards.
 */
add_filter(
	'render_block_core/read-more',
	function ( $block_content, $block ) {
		if ( strpos( $block_content, 'wp-block-read-more' ) === false ) {
			return $block_content;
		}
		// Replace the whole inner text with the configured content label.
		$label = ! empty( $block['attrs']['content'] ) ? $block['attrs']['content'] : 'More information';
		return preg_replace(
			'#(<a[^>]*wp-block-read-more[^>]*>).*?(</a>)#s',
			'$1' . esc_html( $label ) . '$2',
			$block_content,
			1
		);
	},
	10,
	2
);

// ─── Remove useless WordPress Events & News widget (fails without internet) ───
add_action(
	'wp_dashboard_setup',
	function () {
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	},
	999
);

/**
 * Render Block Filter — Generic Placeholder Replacement
 *
 * Replaces placeholder tokens in rendered block content with dynamic values
 * from WordPress settings. This allows the theme to be generic and configurable
 * without hardcoding site-specific data.
 *
 * Supported tokens:
 *   {admin_email}      → WordPress admin email
 *   {volunteer_email}  → Filterable volunteer email (defaults to admin email)
 *   {contact_email}    → WordPress admin email
 *   {social_instagram} → Filterable Instagram handle
 *   {social_facebook}  → Filterable Facebook handle
 *   {social_youtube}   → Filterable YouTube URL
 *   {centro_url}      → Filterable center URL (defaults to home URL)
 *   {community_url}    → Filterable community/social URL (defaults to home URL)
 *   {year}             → Current year (legacy support from mu-plugin)
 *
 * @since 2.7.0
 * @param string $block_content Contenido HTML del bloque ya renderizado.
 * @param array  $block         Bloque completo (nombre, atributos...).
 * @return string Contenido con los tokens sustituidos.
 */
function convoca_theme_render_block( $block_content, $block ) {
	// Guard de recursión del bloque de más abajo (ver comentario allí).
	static $convoca_resolviendo_shortcodes = false;

	// Deprecated alias: {lugg_url} / convoca_theme_lugg_url (pre-3.0).
	$centro_url = apply_filters( 'convoca_theme_centro_url', home_url( '/' ) );
	if ( has_filter( 'convoca_theme_lugg_url' ) ) {
		$centro_url = apply_filters_deprecated( 'convoca_theme_lugg_url', [ $centro_url ], '2.7.0', 'convoca_theme_centro_url' );
	}
	// Redes sociales: fuente única en `convoca_social_links` (Convoca Core), que
	// es la que usan el shortcode [convoca_socials] y estos tokens. Antes cada uno
	// leía su propio filtro (`convoca_theme_social_*`), de modo que había que
	// declarar las mismas URLs dos veces.
	$social_links = (array) apply_filters( 'convoca_social_links', array() );

	$replacements = apply_filters(
		'convoca_theme_footer_replacements',
		[
			'{admin_email}'      => get_bloginfo( 'admin_email' ),
			'{volunteer_email}'  => apply_filters( 'convoca_theme_volunteer_email', get_bloginfo( 'admin_email' ) ),
			'{social_instagram}' => (string) ( $social_links['instagram'] ?? '' ),
			'{social_facebook}'  => (string) ( $social_links['facebook'] ?? '' ),
			'{social_youtube}'   => (string) ( $social_links['youtube'] ?? '' ),
			'{social_handle}'    => (string) ( $social_links['handle'] ?? '' ),
			'{centro_url}'       => $centro_url,
			'{lugg_url}'         => $centro_url, // deprecated token, kept for templates existentes.
			'{community_url}'    => apply_filters( 'convoca_theme_community_url', home_url( '/' ) ),
			'{contact_email}'    => get_bloginfo( 'admin_email' ),
			'{year}'             => (string) gmdate( 'Y' ),
			'{site_name}'        => get_bloginfo( 'name' ),
			'{site_tagline}'     => get_bloginfo( 'description' ),
			// Descripción larga del sitio y datos de contacto: el tema trae un valor por
			// defecto razonable (la descripción del sitio) y vacío donde no puede saberlo,
			// y la instalación los rellena con su filtro. Un token sin valor no deja enlace.
			'{site_description}' => get_bloginfo( 'description' ),
			'{phone_url}'        => '',
			'{phone_label}'      => '',
			'{cta_url}'          => convoca_theme_get_cta_url(),
			'{cta_label}'        => convoca_theme_get_cta_label(),
			'{copyright_extra}'  => apply_filters( 'convoca_theme_copyright_extra', '' ),
			// Texto de muestra del lateral: la instalación lo sustituye por el suyo con el
			// filtro de reemplazos, y quien no lo haga ve una presentación neutra.
			'{sidebar_description}' => __( 'A non-profit association dedicated to environmental education and citizen participation.', 'convoca' ),
			'{cta_heading}'      => (string) apply_filters( 'convoca_theme_cta_heading', __( 'Want to be part of the change?', 'convoca' ) ),
			'{cta_text}'         => (string) apply_filters( 'convoca_theme_cta_text', __( 'Join as a member, volunteer, or just come and meet us. Every action counts.', 'convoca' ) ),
		]
	);

	foreach ( convoca_theme_get_site_links() as $link_key => $link_url ) {
		$replacements[ '{' . $link_key . '_url}' ] = $link_url;
	}
	foreach ( convoca_theme_get_link_labels() as $link_key => $link_label ) {
		$replacements[ '{' . $link_key . '_label}' ] = $link_label;
	}

	// ¿Traía este bloque tokens de enlace que pueden quedarse sin resolver?
	$had_link_tokens = (bool) preg_match( '/\{[a-z0-9_]+_(?:url|label)\}/', $block_content );

	// Los valores se escapan ANTES de entrar en el HTML, y segun para que sean: los que van
	// a un atributo de enlace, como URL; el resto, como texto. Sustituir a ciegas dejaba que
	// un valor con comillas rompiera el atributo donde cayera. Un token sin valor no se
	// sustituye: se queda para que la limpieza de abajo retire el enlace entero.
	$seguros = array();
	foreach ( $replacements as $convoca_token => $convoca_valor ) {
		$convoca_valor = (string) $convoca_valor;
		if ( '' === $convoca_valor ) {
			continue;
		}
		if ( '{copyright_extra}' === $convoca_token ) {
			$seguros[ $convoca_token ] = wp_kses_post( $convoca_valor );
		} elseif ( preg_match( '/_(?:url|instagram|facebook|youtube)$/', $convoca_token ) ) {
			$seguros[ $convoca_token ] = esc_url( $convoca_valor );
		} else {
			$seguros[ $convoca_token ] = esc_html( $convoca_valor );
		}
	}

	$block_content = str_replace( array_keys( $seguros ), array_values( $seguros ), $block_content );

	if ( $had_link_tokens ) {
		// Un enlace sin URL configurada no se muestra (ni elementos vacíos).
		// Nota: delimitador `~` (los patrones contienen `#` para href="#...").
		$patterns = array(
			'~<li[^>]*>\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</li>~is',
			'~<li[^>]*wp-social-link[^>]*>\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</li>~is',
			'~<div class="wp-block-button(?:\s[^"]*)?">\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</div>~is',
			'~<div class="wp-block-buttons[^"]*">\s*</div>~is',
			// Un enlace cuyo token no se ha podido resolver tampoco se muestra: nunca debe
			// quedar a la vista un href con llaves.
			'~<li[^>]*>\s*<a[^>]*href="[^"]*\{[a-z0-9_]+\}[^"]*"[^>]*>.*?</a>\s*</li>~is',
			'~<div class="wp-block-button(?:\s[^"]*)?">\s*<a[^>]*href="[^"]*\{[a-z0-9_]+\}[^"]*"[^>]*>.*?</a>\s*</div>~is',
		);
		foreach ( $patterns as $pattern ) {
			$cleaned = preg_replace( $pattern, '', $block_content );
			if ( null !== $cleaned ) {
				$block_content = $cleaned;
			}
		}
	}

	// Resolve shortcodes inside FSE patterns (do_blocks does not run them). Solo en los
	// bloques que pueden traerlos (patron sincronizado, patron y HTML): no se le da a todo
	// el HTML renderizado la capacidad de ejecutar shortcodes de cualquier plugin.
	$convoca_bloques_con_shortcodes = array( 'core/block', 'core/pattern', 'core/html' );
	if ( in_array( $block['blockName'] ?? '', $convoca_bloques_con_shortcodes, true )
		&& strpos( $block_content, '[' ) !== false
		&& ! $convoca_resolviendo_shortcodes ) {
		// Guard de recursión: un shortcode puede volver a renderizar bloques (un
		// patrón sincronizado que se contiene a sí mismo, HTML con el mismo
		// shortcode). Sin esto, esta reentrada giraba en CPU hasta agotar
		// `max_execution_time`: la petición acababa en 500 y dejaba un worker
		// ocupado dos minutos al 100 %, que con el pool lleno tumbaba el sitio.
		$convoca_resolviendo_shortcodes = true;
		try {
			$block_content = do_shortcode( $block_content );
		} finally {
			$convoca_resolviendo_shortcodes = false;
		}
	}
	return $block_content;
}
add_filter( 'render_block', 'convoca_theme_render_block', 10, 2 );

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
 * @return string HTML del selector.
 */
function convoca_theme_lang_switcher_html(): string {
	$languages = convoca_theme_lang_languages();
	if ( count( $languages ) < 2 ) {
		return '';
	}

	$current_code = 'EN';
	foreach ( $languages as $lang ) {
		if ( ! empty( $lang['is_current'] ) ) {
			$current_code = strtoupper( $lang['code'] );
			break;
		}
	}

	$id = 'convoca-lang-' . wp_unique_id();

	$html  = '<li class="menu-item menu-item-type-custom menu-item-object-custom convoca-lang-switcher convoca-lang-switcher--dropdown">';
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
	$html .= '</ul></li>';

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
add_filter( 'wp_nav_menu_items', 'convoca_theme_language_switcher', 20, 2 );

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
	// Insertar antes del cierre del <nav> o del contenedor del bloque.
	if ( preg_match( '#(</nav>)#', $block_content, $m, PREG_OFFSET_CAPTURE ) ) {
		$pos = $m[1][1];
		return substr( $block_content, 0, $pos ) . $switcher . substr( $block_content, $pos );
	}
	return $block_content . $switcher;
}
add_filter( 'render_block', 'convoca_theme_lang_switcher_block', 20, 2 );

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
add_action( 'wp_head', 'convoca_theme_lang_switcher_styles', 99 );

/**
 * Excluir páginas de traducción EN (slugs terminados en -2) del bloque
 * wp:page-list del header. WPML las registra como páginas publicadas y el
 * page-list las listaría duplicadas junto a la versión ES.
 */
add_filter(
	'wp_list_pages_excludes',
	function ( $excludes ) {
		$pages = get_posts(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		);
		foreach ( $pages as $p ) {
			if ( preg_match( '/-2$/', $p->post_name ) ) {
				$excludes[] = $p->ID;
			}
		}
		return $excludes;
	}
);

/*
==========================================================================
 * Diseño y comportamiento del sitio (integrado desde el antiguo theme hijo)
 * ==========================================================================
 *
 * Capa de presentación que vivía en el theme hijo, ya renombrada a `convoca-*`
 * e integrada aquí para dejar un único theme. Aquí NO hay valores concretos de
 * una instalación: esos viven en el mu-plugin de sitio.
 */

/**
 * Clase de identidad en <body>.
 *
 * La hoja de estilos del theme (foco visible, tipografía, superficies) se
 * apoya en `.convoca-site` para no tocar el editor ni el escritorio.
 */
add_filter(
	'body_class',
	function ( array $clases ): array {
		$clases[] = 'convoca-site';
		return $clases;
	}
);

/**
 * Menús clásicos: se mantienen las mismas ubicaciones (Principal, Superior,
 * Sociales y Pie) para no perder la gestión de menús del escritorio y para
 * poder seguir usando los menús existentes desde FSE.
 */
add_action(
	'after_setup_theme',
	function (): void {
		register_nav_menus(
			array(
				'primary' => 'Primary navigation',
				'top'     => 'Top navigation (thin bar)',
				'socials' => 'Social networks',
				'footer'  => 'Footer navigation',
			)
		);
	},
	5
);

// El menú de las plantillas FSE lo pinta Convoca Core (shortcode [convoca_menu]).
/**
 * Meta de evento de una entrada.
 *
 * Los datos y su formulario viven en Convoca Core (`includes/event-meta.php`),
 * que es el plugin base del producto. Aquí sólo se delega para no duplicar la
 * lectura: el theme se limita a pintar lo que Core le da. Sin Core activo no hay
 * dato de evento (y por tanto tampoco schema ni fecha de evento).
 *
 * @param int    $post_id    ID de la entrada.
 * @param string $meta_key   Clave canónica (p. ej. '_convoca_event_start_date').
 * @return string Valor de la meta, o cadena vacía si no hay dato.
 */
function convoca_get_event_meta( int $post_id, string $meta_key ): string {
	if ( ! function_exists( '\Convoca\Core\event_meta' ) ) {
		return '';
	}

	return \Convoca\Core\event_meta( $post_id, $meta_key );
}

/**
 * Convierte una fecha escrita por quien edita a ISO 8601 en UTC, o null si no es válida.
 *
 * Acepta el formato del campo `datetime-local` (sin zona horaria) y otros habituales. Un
 * valor vacío o imposible de interpretar devuelve null, para que quien llama decida no
 * publicar nada en vez de publicar una fecha falsa.
 *
 * @param string $valor Fecha tal como se guardó en la meta.
 * @return string|null Fecha en ISO 8601 con zona UTC, o null.
 */
function convoca_theme_iso_datetime( string $valor ): ?string {
	$valor = trim( $valor );
	if ( '' === $valor ) {
		return null;
	}

	$zona  = wp_timezone();
	$fecha = false;

	foreach ( array( 'Y-m-d\TH:i', 'Y-m-d H:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s' ) as $formato ) {
		$fecha = \DateTimeImmutable::createFromFormat( $formato, $valor, $zona );
		if ( false !== $fecha ) {
			break;
		}
	}

	if ( false === $fecha ) {
		$sello = strtotime( $valor );
		if ( false === $sello ) {
			return null;
		}
		$fecha = ( new \DateTimeImmutable( '@' . $sello ) )->setTimezone( $zona );
	}

	return $fecha->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'c' );
}

/**
 * JSON-LD de evento en la entrada (marcado manual o pertenencia a las
 * categorías de actividad).
 */
function convoca_event_schema(): void {
	if ( ! is_single() ) {
		return;
	}

	$post_id    = get_the_ID();
	$has_event  = convoca_get_event_meta( $post_id, '_convoca_has_event' );
	$start_date = convoca_get_event_meta( $post_id, '_convoca_event_start_date' );

	$in_event_cat = false;
	foreach ( array( 'actividades', 'local' ) as $cat_slug ) {
		if ( has_category( $cat_slug ) ) {
			$in_event_cat = true;
			break;
		}
	}

	if ( '1' !== $has_event && ! $in_event_cat ) {
		return;
	}

	$location_address = convoca_get_event_meta( $post_id, '_convoca_event_address' );

	// Las fechas se validan antes de publicar el schema: un valor mal escrito por quien edita
	// no puede acabar en datos estructurados. Y un valor de tipo `datetime-local` viene sin
	// zona horaria, asi que se interpreta en la del sitio y se convierte a UTC al serializar;
	// antes se interpretaba en la del servidor y se etiquetaba como UTC, con lo que la hora
	// del evento salia desplazada.
	$start_date = convoca_get_event_meta( $post_id, '_convoca_event_start_date' );
	$iso_start  = convoca_theme_iso_datetime( (string) $start_date );
	if ( null === $iso_start ) {
		return;
	}

	$end_date = convoca_get_event_meta( $post_id, '_convoca_event_end_date' );
	$iso_end  = convoca_theme_iso_datetime( (string) $end_date );

	// La region y el pais no se dan por sabidos: los declara el sitio con sus filtros. Si no
	// los declara, el schema no los lleva, en vez de inventarse unos.
	$region  = (string) apply_filters( 'convoca_theme_event_region', '' );
	$country = (string) apply_filters( 'convoca_theme_event_country', '' );

	$lugar = array(
		'@type' => 'Place',
		'name'  => ( '' !== $location_address ) ? $location_address : get_bloginfo( 'name' ),
	);
	$direccion = array( '@type' => 'PostalAddress' );
	if ( '' !== $region ) {
		$direccion['addressRegion'] = $region;
	}
	if ( '' !== $country ) {
		$direccion['addressCountry'] = $country;
	}
	if ( count( $direccion ) > 1 ) {
		$lugar['address'] = $direccion;
	}

	$data = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'Event',
		'name'                => get_the_title(),
		'description'         => wp_trim_words( wp_strip_all_tags( get_the_content() ), 35 ),
		'url'                 => get_permalink(),
		'startDate'           => $iso_start,
		'image'               => (string) get_the_post_thumbnail_url( get_post(), 'large' ),
		'location'            => $lugar,
		'organizer'           => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'performer'           => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'offers'              => array(
			'@type'         => 'Offer',
			'price'         => '0',
			'priceCurrency' => 'EUR',
			'availability'  => 'https://schema.org/InStock',
			'validFrom'     => get_the_date( 'c' ),
		),
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
	);

	if ( $iso_end ) {
		$data['endDate'] = $iso_end;
	}

	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
	echo "\n" . '</script>' . "\n";
}
add_action( 'wp_head', 'convoca_event_schema', 20 );

/**
 * Seguimiento de clics en formularios externos (GA4). Si no hay GA cargado, el
 * script no hace nada.
 */
add_action(
	'wp_head',
	function (): void {
		?>
	<script>
	(function() {
		if (typeof gtag === "undefined") return;
		document.addEventListener("DOMContentLoaded", function() {
			var fp = /forms\.gle|docs\.google\.com\/forms|google\.com\/forms/i;
			document.querySelectorAll("a").forEach(function(el) {
				if (fp.test(el.href)) {
					el.addEventListener("click", function() {
						gtag("event", "form_click", {
							form_url: el.href,
							page_location: window.location.href
						});
					});
				}
			});
		});
	})();
	</script>
		<?php
	},
	99
);

/**
 * Ocultar la versión de WordPress (endurecimiento heredado del theme clásico).
 */
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'style_loader_src', 'convoca_fse_remove_version', 9999 );
add_filter( 'script_loader_src', 'convoca_fse_remove_version', 9999 );

/**
 * Sella los assets con su fecha de modificación y oculta la versión del core.
 *
 * @param mixed $src URL del recurso; la traen los filtros de WordPress y no siempre
 *                    es una cadena (puede llegar vacío o como arreglo).
 * @return mixed URL con la versión sellada.
 */
function convoca_fse_remove_version( $src ) {
	if ( ! is_string( $src ) || '' === $src ) {
		return $src;
	}

	// Las hojas del PROPIO theme se sellan con la FECHA DEL FICHERO, sin importar
	// quién las encole. Sin esto la URL del CSS no cambia nunca, el navegador
	// sirve una copia vieja y se acaba midiendo y diagnosticando sobre un estado
	// que no es el del fichero.
	if ( false !== strpos( $src, '/themes/' ) ) {
		$sin_version = strtok( $src, '?' );
		$en_disco    = str_replace(
			array( get_template_directory_uri(), get_stylesheet_directory_uri() ),
			array( get_template_directory(), get_stylesheet_directory() ),
			$sin_version
		);

		if ( is_readable( $en_disco ) ) {
			return add_query_arg( 'ver', (string) filemtime( $en_disco ), $sin_version );
		}

		return $sin_version;
	}

	// Core y plugins: se sigue ocultando la versión de WordPress.
	if ( false !== strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}

/**
 * Una sola fecha por entrada: la que informa.
 *
 * En una actividad, la fecha de publicación es ruido al lado de la fecha de la
 * actividad. Si el contenido tiene fecha de evento, se retira el bloque de
 * fecha de publicación y queda el dato del evento, que sale por [convoca_cuando].
 */
add_filter(
	'render_block_core/post-date',
	function ( $block_content ) {
		if ( ! is_singular() ) {
			return $block_content;
		}
		$cuando = convoca_get_event_meta( get_the_ID(), '_convoca_event_start_date' );
		return $cuando ? '' : $block_content;
	}
);

/**
 * Etiquetas del menú sin emojis.
 *
 * Los emojis de los elementos de menú son contenido (los puso quien editó el
 * menú), así que NO se tocan: se limpian al pintarlos. Por eso funciona igual
 * el día que se migre, y si algún día se quieren recuperar, siguen estando en
 * el menú.
 *
 * @param string $texto Texto de entrada.
 * @return string Texto sin emojis.
 */
function convoca_fse_sin_emojis( string $texto ): string {
	// Emojis + modificadores + caracteres invisibles que se cuelan con ellos
	// (espacio de ancho cero, espacios finos, marca de dirección...).
	$emoji = '/[\x{1F000}-\x{1FAFF}\x{1F1E6}-\x{1F1FF}\x{2190}-\x{21FF}\x{2300}-\x{27BF}'
		. '\x{2B00}-\x{2BFF}\x{FE0E}\x{FE0F}\x{200B}-\x{200F}\x{2000}-\x{200A}'
		. '\x{2060}\x{200D}\x{E0000}-\x{E007F}]+/u';

	$limpio = preg_replace( $emoji, '', $texto );
	if ( null === $limpio ) {
		return $texto;
	}

	return trim( preg_replace( '/\s{2,}/', ' ', $limpio ) );
}

/**
 * El bloque de navegación NO pasa por los filtros del menú clásico: pinta cada
 * elemento con su propio bloque, así que la etiqueta hay que limpiarla aquí.
 * Se limpia el texto de cada enlace (y de los submenús, que van anidados).
 *
 * @param string $html Texto de entrada.
 * @return string Texto sin emojis.
 */
function convoca_fse_limpiar_emojis_enlaces( string $html ): string {
	$limpio = preg_replace_callback(
		'/(<a\b[^>]*>)(.*?)(<\/a>)/s',
		function ( $m ) {
			return $m[1] . convoca_fse_sin_emojis( $m[2] ) . $m[3];
		},
		$html
	);

	return null === $limpio ? $html : $limpio;
}

add_filter(
	'render_block_core/navigation-link',
	function ( $block_content ) {
		return convoca_fse_limpiar_emojis_enlaces( (string) $block_content );
	}
);

add_filter(
	'render_block_core/navigation-submenu',
	function ( $block_content ) {
		return convoca_fse_limpiar_emojis_enlaces( (string) $block_content );
	}
);

// Cubre las dos vías: el menú clásico por wp_nav_menu y el bloque de navegación
// cuando lee el menú clásico (los dos pasan por wp_setup_nav_menu_item).
add_filter(
	'wp_setup_nav_menu_item',
	function ( $item ) {
		if ( isset( $item->title ) ) {
			$item->title = convoca_fse_sin_emojis( (string) $item->title );
		}
		return $item;
	}
);

add_filter(
	'nav_menu_item_title',
	function ( $titulo ) {
		return convoca_fse_sin_emojis( (string) $titulo );
	}
);

/**
 * En móvil, el menú «Superior» viaja dentro del menú principal.
 *
 * En una pantalla de móvil la franja oscura es un apretón de enlaces: se
 * esconden y se añaden dentro del desplegable del menú principal, en un grupo
 * propio y separado. En escritorio no cambia nada, porque el bloque inyectado
 * sólo se muestra dentro del desplegable.
 */
add_filter(
	'render_block_core/navigation',
	function ( $block_content, $block ) {
		if ( ! is_string( $block_content ) || false === strpos( $block_content, 'wp-block-navigation__responsive-container-content' ) ) {
			return $block_content;
		}

		$enlaces = do_shortcode( '[convoca_menu location="top" class="convoca-topnav"]' );
		if ( '' === $enlaces ) {
			return $block_content;
		}

		$grupo = '<div class="convoca-topnav-en-menu">' . $enlaces . '</div>';

		// Se inserta al FINAL del contenido del desplegable, no al principio: el
		// bloque de navegación lleva el foco al primer enlace, y si el grupo va
		// primero el menú abre desplazado hasta él y parece cortado.
		$apertura = 'wp-block-navigation__responsive-container-content';
		$pos      = strpos( $block_content, $apertura );
		if ( false === $pos ) {
			return $block_content;
		}

		$desde = strpos( $block_content, '>', $pos ) + 1;
		$nivel = 1;
		$i     = $desde;
		$largo = strlen( $block_content );

		while ( $i < $largo && $nivel > 0 ) {
			$abre   = strpos( $block_content, '<div', $i );
			$cierra = strpos( $block_content, '</div>', $i );

			if ( false === $cierra ) {
				break;
			}

			if ( false !== $abre && $abre < $cierra ) {
				++$nivel;
				$i = $abre + 4;
			} else {
				--$nivel;
				$i = $cierra + 6;
			}
		}

		if ( 0 !== $nivel ) {
			return $block_content;
		}

		$cierre_contenedor = $i - 6;

		return substr( $block_content, 0, $cierre_contenedor ) . $grupo . substr( $block_content, $cierre_contenedor );
	},
	10,
	2
);

/**
 * En la entrada, fuera la imagen destacada.
 *
 * La destacada se usa recortada en las rejillas (4/5), y en una entrada a ancho
 * completo se ve pequeña y borrosa. El contenido ya trae su imagen a tamaño
 * completo, así que en la vista individual sobra: se retira SOLO si el contenido
 * tiene alguna imagen, para no dejar sin ilustración a una entrada que no la
 * lleve dentro.
 */
add_filter(
	'render_block_core/post-featured-image',
	function ( $block_content, $block ) {
		if ( ! is_singular( 'post' ) ) {
			return $block_content;
		}
		$id           = get_the_ID();
		$contenido    = $id ? (string) get_post_field( 'post_content', $id ) : '';
		$tiene_imagen = ( false !== strpos( $contenido, 'wp:image' ) ) || ( false !== strpos( $contenido, '<img' ) );

		return $tiene_imagen ? '' : $block_content;
	},
	10,
	2
);

/**
 * Ninguna fecha de publicación: sólo fechas de actividad.
 *
 * Una fecha de publicación junto a la fecha del evento se lee como duplicada, y
 * la de publicación no le sirve a nadie en una actividad. Regla:
 *   - en la entrada y las páginas, no se pinta;
 *   - en las rejillas, se pinta la fecha DEL EVENTO si el contenido la tiene, y
 *     si no la tiene, no se pinta nada (nada de caer a la de publicación).
 */
add_filter(
	'render_block_core/post-date',
	function ( $block_content, $block ) {
		// Dentro de una rejilla, get_the_ID() NO devuelve la entrada de la tarjeta:
		// el dato fiable es el contexto del bloque.
		$id = isset( $block['context']['postId'] ) ? (int) $block['context']['postId'] : (int) get_the_ID();

		if ( is_singular() ) {
			return '';
		}

		$inicio = $id ? (string) convoca_get_event_meta( $id, '_convoca_event_start_date' ) : '';
		if ( '' === $inicio ) {
			return '';
		}

		$ts = strtotime( $inicio );
		if ( ! $ts ) {
			return '';
		}

		$clase = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		return sprintf(
			'<div class="wp-block-post-date convoca-fecha-evento %1$s"><span class="convoca-dato-evento convoca-dato-evento--cuando"><time datetime="%2$s">%3$s</time></span></div>',
			esc_attr( $clase ),
			esc_attr( $inicio ),
			esc_html( date_i18n( 'j \\d\\e F', $ts ) )
		);
	},
	10,
	2
);

// La fecha del evento la formatea Convoca Core (event_when()); el shortcode de la
// plantilla es el de Core.

// Las relacionadas las sirve Convoca Core (shortcode [convoca_relacionadas]).
/**
 * Estilos de bloque propios del diseño del sitio.
 *
 * Lo que en un theme clásico pedía un plugin o CSS a mano, en FSE se ofrece como
 * estilo de bloque: quien edita elige «Banda naranja» o «Marco editorial» en el
 * panel de estilos y no tiene que escribir una clase.
 */
function convoca_register_site_block_styles(): void {
	$estilos = array(
		array( 'core/group', 'convoca-banda-naranja', __( 'Orange band', 'convoca' ) ),
		array( 'core/group', 'convoca-bloque-carbon', __( 'Charcoal block', 'convoca' ) ),
		array( 'core/image', 'convoca-marco', __( 'Editorial frame', 'convoca' ) ),
		array( 'core/button', 'convoca-fantasma', __( 'Ghost button', 'convoca' ) ),
		array( 'core/quote', 'convoca-cita', __( 'Featured quote', 'convoca' ) ),
		array( 'core/heading', 'convoca-regla', __( 'Heading with rule', 'convoca' ) ),
	);

	foreach ( $estilos as $estilo ) {
		register_block_style(
			$estilo[0],
			array(
				'name'  => $estilo[1],
				'label' => $estilo[2],
			)
		);
	}
}
add_action( 'init', 'convoca_register_site_block_styles' );

/**
 * Acordeón de los submenús del desplegable móvil.
 *
 * WordPress no marca estado en los submenús dentro del desplegable (comprobado
 * en el DOM: sin atributos data-wp-class y con display:flex por una regla suya),
 * así que se pliegan por CSS y se abren aquí, al tocar la flecha. En escritorio
 * no se toca nada: el desplegable de ratón sigue siendo el del bloque.
 */
add_action(
	'wp_footer',
	function () {
		if ( is_admin() ) {
			return;
		}
		?>
	<script>
	document.addEventListener( 'click', function ( evento ) {
		var flecha = evento.target.closest( '.wp-block-navigation__submenu-icon, .wp-block-navigation-submenu__toggle' );
		if ( ! flecha ) {
			return;
		}

		var item = flecha.closest( '.has-child' );
		var desplegable = item ? item.closest( '.wp-block-navigation__responsive-container.is-menu-open' ) : null;
		if ( ! item || ! desplegable ) {
			return;
		}

		evento.preventDefault();
		item.classList.toggle( 'convoca-sub-abierto' );

		var control = item.querySelector( 'button[aria-expanded]' );
		if ( control ) {
			control.setAttribute( 'aria-expanded', item.classList.contains( 'convoca-sub-abierto' ) ? 'true' : 'false' );
		}
	} );
	</script>
		<?php
	},
	20
);

/**
 * Una sola categoría en las tarjetas.
 *
 * Con varias categorías asignadas, la línea de metadatos se estiraba hasta
 * ocupar varios renglones (en la portada hay una tarjeta con seis). En las
 * tarjetas se queda la primera y el resto se ve al entrar en la entrada, que
 * sigue pintando el bloque completo. Se filtra el HTML ya renderizado del
 * bloque, nunca el contenido: no se toca ninguna entrada ni sus categorías.
 */
add_filter(
	'render_block_core/post-terms',
	function ( $html, $block ) {
		if ( 'category' !== ( $block['attrs']['term'] ?? '' ) || is_singular() ) {
			return $html;
		}

		// La etiqueta es la CATEGORÍA PRINCIPAL elegida en la entrada (Yoast), no
		// la primera que devuelva WordPress: es la que el equipo marca a mano para
		// cada contenido. Si no hay principal, se cae a la primera asignada.
		$term    = null;
		$post_id = function_exists( 'get_the_ID' ) ? get_the_ID() : 0;
		if ( $post_id ) {
			$principal = (int) get_post_meta( $post_id, '_yoast_wpseo_primary_category', true );
			if ( $principal ) {
				$term = get_term( $principal, 'category' );
			}
		}
		if ( ! $term || is_wp_error( $term ) ) {
			if ( ! preg_match( '~<a\b[^>]*href="([^"]+)"~', $html, $m ) ) {
				return $html;
			}
			$term = get_term_by( 'slug', basename( (string) wp_parse_url( $m[1], PHP_URL_PATH ) ), 'category' );
		}
		if ( ! $term ) {
			return $html;
		}

		// Se conserva el contenedor tal cual y dentro va un único enlace.
		if ( ! preg_match( '~^(<div\b[^>]*>).*(</div>)$~s', $html, $caja ) ) {
			return $html;
		}

		return $caja[1] . sprintf(
			'<a href="%s" rel="tag">%s</a>',
			esc_url( (string) get_term_link( $term ) ),
			esc_html( $term->name )
		) . $caja[2];
	},
	10,
	2
);

/**
 * 27. Paginación de la búsqueda: la búsqueda tiene que sobrevivir al cambio de página
 *
 * Al paginar los resultados, WordPress construía direcciones del tipo `/page/2/?s=Setas`.
 * Esa URL NO es la página 2 de la búsqueda: el core la resuelve como la portada paginada
 * (medido: servía «BIODEVAS - Página 2 de 71»), así que el visitante salía de los resultados
 * y acababa en el blog. El origen está en `paginate_links()` y en `get_pagenum_link()`, que
 * arman la dirección desde la URL actual y pierden la búsqueda por el camino.
 *
 * Aquí se reescribe `get_pagenum_link()` —que es por donde pasan los tres bloques de la
 * paginación— y se reconstruye a partir de la URL de búsqueda real:
 *
 *   - Sitio con base de búsqueda («/search/Setas/»): se pagina pegando la base de paginación
 *     → `/search/Setas/page/2/` (comprobado que sirve la página 2).
 *   - Sitio sin ella («/?s=Setas»): con parámetro → `/?s=Setas&paged=2` (también comprobado).
 *
 * Solo toca las búsquedas; el resto de paginaciones (portada, archivos, categorías) siguen
 * con `/page/N/`, que en ellas es lo correcto.
 */
add_filter(
	'get_pagenum_link',
	function ( string $link, int $pagenum ): string {
		if ( ! is_search() ) {
			return $link;
		}

		$base = get_search_link( get_search_query( false ) );

		if ( $pagenum <= 1 ) {
			return $base;
		}

		// Con base de búsqueda la URL es "bonita" y la paginación va pegada; sin ella
		// hay que conservar la búsqueda como parámetro, porque la ruta sola la pierde.
		if ( ! str_contains( $base, '?' ) ) {
			return user_trailingslashit(
				trailingslashit( $base ) . $GLOBALS['wp_rewrite']->pagination_base . '/' . $pagenum,
				'paged'
			);
		}

		return add_query_arg( 'paged', $pagenum, $base );
	},
	10,
	2
);
