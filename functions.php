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

defined('ABSPATH') || exit;

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
add_action('admin_init', function () {
	if (
		!empty($_GET['convoca_reset_templates'])
		&& current_user_can('manage_options')
		&& wp_verify_nonce($_GET['_wpnonce'] ?? '', 'convoca_reset_templates')
		&& is_admin()
		&& wp_doing_ajax() === false
	) {
		// Rate limit: only once per hour
		$reset_key = 'convoca_templates_reset_time';
		$last_reset = get_option($reset_key, 0);
		if (time() - $last_reset < HOUR_IN_SECONDS) {
			add_action('admin_notices', function () {
				echo '<div class="notice notice-warning"><p><strong>Convoca:</strong> El reinicio de plantillas solo puede hacerse una vez por hora. Espera unos minutos.</p></div>';
			});
			return;
		}

		$types = ['wp_template', 'wp_template_part'];
		$deleted = 0;
		foreach ($types as $type) {
			$posts = get_posts([
				'post_type'      => $type,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
				'tax_query'      => [[
					'taxonomy' => 'wp_theme',
					'field'    => 'slug',
					'terms'    => get_stylesheet(),
				]],
			]);
			foreach ($posts as $id) {
				if (wp_delete_post($id, true)) {
					$deleted++;
				}
			}
		}

		update_option($reset_key, time());
		add_action('admin_notices', function () use ($deleted) {
			echo '<div class="notice notice-success"><p><strong>Convoca:</strong> ' . sprintf( esc_html__( 'Se han reiniciado %d plantillas y partes de plantilla del theme. Se leerán directamente de los archivos del tema.', 'convoca-theme' ), $deleted ) . '</p></div>';
		});
	}
});

/**
 * 1. Theme Setup
 */
function convoca_setup(): void
{
	load_theme_textdomain( 'convoca-theme', get_template_directory() . '/languages' );

	add_theme_support('wp-block-styles');
	add_theme_support('editor-styles');
	add_theme_support('responsive-embeds');
	add_theme_support('custom-logo', [
		'height' => 80,
		'width' => 200,
		'flex-height' => true,
		'flex-width' => true,
	]);
	add_theme_support('html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	]);
	add_theme_support('post-thumbnails');

	// Load editor stylesheet.
	add_editor_style('style.css');
}
add_action('after_setup_theme', 'convoca_setup');

/**
 * 2. Register Block Pattern Categories
 */
function convoca_register_pattern_categories(): void
{
	register_block_pattern_category('convoca', [
		'label' => __('Convoca', 'convoca-theme'),
		'description' => __('Patrones del theme Convoca.', 'convoca-theme'),
	]);
	register_block_pattern_category('convoca-layout', [
		'label' => __('Convoca — Layout', 'convoca-theme'),
		'description' => __('Secciones de página completas.', 'convoca-theme'),
	]);
}
add_action('init', 'convoca_register_pattern_categories');

/**
 * 3. Register Custom Block Styles
 */
function convoca_register_block_styles(): void
{
	// Paragraph: Lead style.
	register_block_style('core/paragraph', [
		'name' => 'lead',
		'label' => __('Destacado (Lead)', 'convoca-theme'),
	]);

	// Group: Card style.
	register_block_style('core/group', [
		'name' => 'card',
		'label' => __('Tarjeta', 'convoca-theme'),
	]);

	// Group: Coordinator box style.
	register_block_style('core/group', [
		'name' => 'coordinator',
		'label' => __('Caja Coordinador', 'convoca-theme'),
	]);

	// Group: Glass (frosted glass effect).
	register_block_style('core/group', [
		'name' => 'glass',
		'label' => __('Cristal Esmerilado', 'convoca-theme'),
	]);

	// Cover: Topographic overlay.
	register_block_style('core/cover', [
		'name' => 'topographic',
		'label' => __('Overlay Topográfico', 'convoca-theme'),
	]);

	// Table: Convoca styled table.
	register_block_style('core/table', [
		'name' => 'convoca',
		'label' => __('Tabla Convoca', 'convoca-theme'),
	]);

	// Buttons: Secondary (outline on dark).
	register_block_style('core/button', [
		'name' => 'secondary',
		'label' => __('Secundario', 'convoca-theme'),
	]);

	// Image: Rounded + shadow.
	register_block_style('core/image', [
		'name' => 'elevated',
		'label' => __('Elevada', 'convoca-theme'),
	]);
}
add_action('init', 'convoca_register_block_styles');

/**
 * 4. Resource Hints & Performance
 */
function convoca_resource_hints(array $urls, string $relation): array
{
	if ('preconnect' === $relation || 'dns-prefetch' === $relation) {
		$urls[] = [
			'href' => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		];
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter('wp_resource_hints', 'convoca_resource_hints', 10, 2);

/**
 * 5. Preload Google Fonts Stylesheet
 */
function convoca_style_loader_tag(string $tag, string $handle): string
{
	if ('convoca-google-fonts' === $handle) {
		return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag) .
			'<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Outfit:wght@400..700&display=swap"></noscript>';
	}
	return $tag;
}
add_filter('style_loader_tag', 'convoca_style_loader_tag', 10, 2);

/**
 * 6. Inline Critical CSS (Header & Above the fold)
 */
function convoca_critical_css(): void
{
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
add_action('wp_head', 'convoca_critical_css', 2);

/**
 * 7. Performance: remove jQuery migrate, defer scripts, lazy loading
 */
function convoca_performance_tweaks(): void
{
	// Remove emoji scripts.
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'convoca_performance_tweaks');

/**
 * Native Lazy Loading for all images.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    // Skip lazy loading for images with fetchpriority="high" (first image/LCP)
    if (isset($attr['fetchpriority']) && $attr['fetchpriority'] === 'high') {
        return $attr;
    }
    $attr['loading'] = 'lazy';
    return $attr;
}, 10, 3);

/**
 * 8. Accessibility: skip link
 */
function convoca_skip_link(): void
{
	echo '<a class="skip-link screen-reader-text" href="#main-content">' .
		esc_html__('Ir al contenido', 'convoca-theme') . '</a>';
}
add_action('wp_body_open', 'convoca_skip_link');

/**
 * 9. Custom image sizes for cards
 */
function convoca_image_sizes(): void
{
	add_image_size('convoca-card', 600, 400, true);
	add_image_size('convoca-hero', 1600, 900, true);
}
add_action('after_setup_theme', 'convoca_image_sizes');

/**
 * Enqueue scripts and styles.
 */
function convoca_theme_scripts()
{
	// Google Fonts: Lato + Outfit.
	$theme_version = wp_get_theme()->get('Version') ?: '1.0';
	wp_enqueue_style(
		'convoca-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Outfit:wght@400..700&display=swap',
		array(),
		$theme_version
	);
	wp_enqueue_style('convoca-theme-style', get_stylesheet_uri(), array('convoca-google-fonts'), $theme_version);
}
add_action('wp_enqueue_scripts', 'convoca_theme_scripts');

/**
 * 11. Admin Help Page: Ayuda Convoca
 */
function convoca_admin_menu(): void
{
	add_theme_page(
		__('Ayuda Convoca', 'convoca-theme'),
		__('Ayuda Convoca', 'convoca-theme'),
		'edit_theme_options',
		'convoca-help',
		'convoca_help_page_html'
	);
}
add_action('admin_menu', 'convoca_admin_menu');

function convoca_help_page_html(): void
{
	$theme = wp_get_theme();
	?>
	<div class="wrap convoca-admin-page">
		<h1><?php echo esc_html__('Configuración y Ayuda — Theme Convoca', 'convoca-theme'); ?></h1>
		
		<div class="welcome-panel" style="padding: 0; margin-top: 20px; overflow: hidden; border-radius: 8px; border: none; background: #000;">
			<div class="welcome-panel-content" style="padding: 60px 40px; background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/admin-banner.png' ); ?>'); background-size: cover; background-position: center; color: #fff;">
				<h2 style="color: #fff; font-size: 2.4em; margin: 0; font-family: 'Outfit', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"><?php printf(__('Bienvenido a Convoca v%s', 'convoca-theme'), $theme->get('Version')); ?></h2>
				<p class="about-description" style="color: rgba(255,255,255,0.9); font-size: 1.2em; max-width: 600px; margin-top: 10px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><?php echo esc_html__('Este es un theme FSE (Full Site Editing) optimizado para la Asociación Convoca. Aquí encontrarás una guía rápida de uso.', 'convoca-theme'); ?></p>
			</div>
		</div>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container">
					
					<!-- Información del Theme -->
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Información del Theme', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<ul>
								<li><strong><?php echo esc_html__('Versión:', 'convoca-theme'); ?></strong> <?php echo esc_html($theme->get('Version')); ?></li>
								<li><strong><?php echo esc_html__('Autor:', 'convoca-theme'); ?></strong> <a href="<?php echo esc_url($theme->get('AuthorURI')); ?>" target="_blank"><?php echo esc_html($theme->get('Author')); ?></a></li>
								<li><strong><?php echo esc_html__('Documentación:', 'convoca-theme'); ?></strong> <a href="https://github.com/josecarlosnieto91/convoca-theme/wiki" target="_blank"><?php echo esc_html__('Ver Wiki en GitHub', 'convoca-theme'); ?></a></li>
							</ul>
							<hr>
							<a href="<?php echo admin_url('site-editor.php'); ?>" class="button button-primary"><?php echo esc_html__('Abrir Editor de Sitios (FSE)', 'convoca-theme'); ?></a>
						</div>
					</div>
						<h2 class="hndle"><span><?php echo esc_html__('Guía de Plantillas y Páginas', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<h4><?php echo esc_html__('Página de Actividad', 'convoca-theme'); ?></h4>
							<p><?php echo esc_html__('Usa la plantilla "Página de Actividad" para las páginas que describen una actividad específica. Incluye automáticamente el formulario de inscripción.', 'convoca-theme'); ?></p>
							
							<h4><?php echo esc_html__('Página de Proyecto', 'convoca-theme'); ?></h4>
							<p><?php echo esc_html__('Usa la plantilla "Página de Proyecto" para secciones de proyectos de largo recorrido. Incluye metadatos específicos del proyecto.', 'convoca-theme'); ?></p>
							
							<hr>
							<p><strong><?php echo esc_html__('Nota:', 'convoca-theme'); ?></strong> <?php echo esc_html__('Si las plantillas no se visualizan correctamente, puedes intentar reiniciarlas.', 'convoca-theme'); ?></p>
							<a href="<?php echo esc_url(wp_nonce_url(admin_url('themes.php?page=convoca-help&convoca_reset_templates=1'), 'convoca_reset_templates')); ?>" class="button button-link-delete" onclick="return confirm('<?php echo esc_js(__('¿Estás seguro? Esto borrará cualquier personalización que hayas hecho en el Editor de Sitios y volverá a los archivos del theme.', 'convoca-theme')); ?>');"><?php echo esc_html__('Reiniciar Plantillas a valores de fábrica', 'convoca-theme'); ?></a>
						</div>
					</div>

					<!-- Patrones Recomendados -->
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Patrones Convoca', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<p><?php echo esc_html__('Puedes insertar estos bloques pre-diseñados desde el editor (+) > Patrones > Convoca:', 'convoca-theme'); ?></p>
							<ul style="list-style: disc; padding-left: 20px;">
								<li><code>convoca/hero</code>: <?php echo esc_html__('Cabecera principal con texto.', 'convoca-theme'); ?></li>
								<li><code>convoca/cards-grid</code>: <?php echo esc_html__('Cuadrícula de actividades o noticias.', 'convoca-theme'); ?></li>
								<li><code>convoca/stats-bar</code>: <?php echo esc_html__('Barra de estadísticas animada.', 'convoca-theme'); ?></li>
								<li><code>convoca/inscripcion-actividad</code>: <?php echo esc_html__('Formulario de inscripción integrado.', 'convoca-theme'); ?></li>
							</ul>
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
function convoca_editor_fonts(): void
{
	wp_enqueue_style(
		'convoca-editor-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Outfit:wght@400..700&display=swap',
		array(),
		null
	);
}
add_action('enqueue_block_editor_assets', 'convoca_editor_fonts');

// Shortcodes moved to plugins: convoca_mi_perfil → convoca-members, etc.

/**
 * 10. Enqueue theme JS (scroll-to-top, header shadow, animations)
 */
function convoca_enqueue_scripts(): void
{
	$version = wp_get_theme()->get('Version');

	wp_enqueue_script(
		'convoca-theme',
		get_theme_file_uri('assets/js/convoca-theme.js'),
		[],
		$version,
		['strategy' => 'defer', 'in_footer' => true]
	);

	wp_enqueue_script(
		'convoca-dark-mode',
		get_theme_file_uri('assets/js/dark-mode.js'),
		[],
		$version,
		['strategy' => 'defer', 'in_footer' => true]
	);
}
add_action('wp_enqueue_scripts', 'convoca_enqueue_scripts');

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
add_action('wp_head', 'convoca_dark_mode_inline_init', 1);

/**
 * 10. SEO: JSON-LD Structured Data for Activities (Event Schema)
 */

// ─── Process shortcodes in rendered blocks (FSE compatibility) ───

/**
 * core/read-more appends the post title after content ("Más información : Título").
 * Keep only the explicit label on the activity cards.
 */
add_filter('render_block_core/read-more', function ($block_content, $block) {
    if (strpos($block_content, 'wp-block-read-more') === false) {
        return $block_content;
    }
    // Replace the whole inner text with the configured content label.
    $label = !empty($block['attrs']['content']) ? $block['attrs']['content'] : 'Más información';
    return preg_replace(
        '#(<a[^>]*wp-block-read-more[^>]*>).*?(</a>)#s',
        '$1' . esc_html($label) . '$2',
        $block_content,
        1
    );
}, 10, 2);

// ─── Remove useless WordPress Events & News widget (fails without internet) ───
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
}, 999);

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
 */
function convoca_theme_render_block($block_content, $block) {
    // Deprecated alias: {lugg_url} / convoca_theme_lugg_url (pre-3.0).
    $centro_url = apply_filters('convoca_theme_centro_url', home_url('/'));
    if (has_filter('convoca_theme_lugg_url')) {
        $centro_url = apply_filters_deprecated('convoca_theme_lugg_url', [$centro_url], '2.7.0', 'convoca_theme_centro_url');
    }
    $replacements = apply_filters('convoca_theme_footer_replacements', [
        '{admin_email}'       => get_bloginfo('admin_email'),
        '{volunteer_email}'   => apply_filters('convoca_theme_volunteer_email', get_bloginfo('admin_email')),
        '{social_instagram}'  => apply_filters('convoca_theme_social_instagram', ''),
        '{social_facebook}'   => apply_filters('convoca_theme_social_facebook', ''),
        '{social_youtube}'    => apply_filters('convoca_theme_social_youtube', ''),
        '{social_handle}'     => apply_filters('convoca_theme_social_handle', ''),
        '{centro_url}'        => $centro_url,
        '{lugg_url}'          => $centro_url, // deprecated token, kept for templates existentes.
        '{community_url}'     => apply_filters('convoca_theme_community_url', home_url('/')),
        '{contact_email}'     => get_bloginfo('admin_email'),
        '{year}'              => (string) gmdate('Y'),
        '{site_tagline}'      => get_bloginfo('description'),
        '{cta_url}'           => convoca_theme_get_cta_url(),
        '{cta_label}'         => convoca_theme_get_cta_label(),
        '{copyright_extra}'   => apply_filters('convoca_theme_copyright_extra', ''),
        '{cta_heading}'       => (string) apply_filters('convoca_theme_cta_heading', __('¿Quieres ser parte del cambio?', 'convoca-theme')),
        '{cta_text}'          => (string) apply_filters('convoca_theme_cta_text', __('Únete como socio/a, participa como voluntario/a, o simplemente ven a conocernos. Cada acción cuenta.', 'convoca-theme')),
    ]);

    foreach (convoca_theme_get_site_links() as $link_key => $link_url) {
        $replacements['{' . $link_key . '_url}'] = $link_url;
    }
    foreach (convoca_theme_get_link_labels() as $link_key => $link_label) {
        $replacements['{' . $link_key . '_label}'] = $link_label;
    }

    // ¿Traía este bloque tokens de enlace que pueden quedarse sin resolver?
    $had_link_tokens = (bool) preg_match('/\{[a-z0-9_]+_(?:url|label)\}/', $block_content);

    $block_content = str_replace(array_keys($replacements), array_values($replacements), $block_content);

    if ($had_link_tokens) {
        // Un enlace sin URL configurada no se muestra (ni elementos vacíos).
        // Nota: delimitador `~` (los patrones contienen `#` para href="#...").
        $patterns = array(
            '~<li[^>]*>\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</li>~is',
            '~<li[^>]*wp-social-link[^>]*>\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</li>~is',
            '~<div class="wp-block-button(?:\s[^"]*)?">\s*<a[^>]*href="(?:\s*|#)"[^>]*>.*?</a>\s*</div>~is',
            '~<div class="wp-block-buttons[^"]*">\s*</div>~is',
        );
        foreach ($patterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $block_content);
            if (null !== $cleaned) {
                $block_content = $cleaned;
            }
        }
    }

    // Resolve shortcodes inside FSE patterns (do_blocks does not run them).
    if (strpos($block_content, '[') !== false) {
        $block_content = do_shortcode($block_content);
    }
    return $block_content;
}
add_filter('render_block', 'convoca_theme_render_block', 10, 2);

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
function convoca_theme_get_site_links(): array
{
    return apply_filters('convoca_theme_site_links', array(
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
    ));
}

/**
 * Etiquetas de los enlaces del sitio.
 *
 * @since 2.8.0
 * @return array<string,string> clave => texto visible.
 */
function convoca_theme_get_link_labels(): array
{
    return apply_filters('convoca_theme_link_labels', array(
        'about'        => __('¿Quiénes somos?', 'convoca-theme'),
        'transparency' => __('Transparencia', 'convoca-theme'),
        'projects'     => __('Proyectos', 'convoca-theme'),
        'alliances'    => __('Alianzas', 'convoca-theme'),
        'activities'   => __('Actividades', 'convoca-theme'),
        'membership'   => __('Hazte socio/a', 'convoca-theme'),
        'volunteer'    => __('Voluntariado', 'convoca-theme'),
        'donations'    => __('Donaciones', 'convoca-theme'),
        'centro'       => __('Centro', 'convoca-theme'),
        'privacy'      => __('Privacidad', 'convoca-theme'),
        'cookies'      => __('Cookies', 'convoca-theme'),
        'legal'        => __('Aviso legal', 'convoca-theme'),
    ));
}

/**
 * URL del CTA de cabecera. Sin URL, el botón no se muestra.
 *
 * @since 2.8.0
 */
function convoca_theme_get_cta_url(): string
{
    $url = (string) apply_filters('convoca_theme_cta_url', '');
    if ('' === $url) {
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
function convoca_theme_get_cta_label(): string
{
    return (string) apply_filters('convoca_theme_cta_label', __('Asóciate', 'convoca-theme'));
}

/**
 * Redes sociales del theme: shortcode [convoca_socials].
 *
 * Los enlaces llegan por filtros (convoca_theme_social_instagram, etc.). No se
 * pueden poner como placeholder dentro del atributo `url` de un bloque: los
 * atributos se resuelven al analizar el bloque, antes de `render_block`. Por eso
 * el theme construye el bloque en PHP y lo renderiza él mismo.
 *
 * @since 2.8.0
 */
function convoca_theme_socials_html(): string
{
	static $busy = false;
	if ( $busy ) {
		return '';
	}

	$socials = array_filter(
		array(
			'instagram' => (string) apply_filters('convoca_theme_social_instagram', ''),
			'facebook'  => (string) apply_filters('convoca_theme_social_facebook', ''),
			'youtube'   => (string) apply_filters('convoca_theme_social_youtube', ''),
		),
		fn( $url ) => '' !== trim( $url )
	);

	if ( empty( $socials ) ) {
		return '';
	}

	$inner = '';
	foreach ( $socials as $service => $url ) {
		$inner .= sprintf(
			'<!-- wp:social-link {"url":"%s","service":"%s"} /-->',
			esc_url_raw( $url ),
			esc_attr( $service )
		);
	}

	$markup = sprintf(
		'<!-- wp:social-links {"iconColor":"blanco","iconColorValue":"#ffffff"} --><ul class="wp-block-social-links has-icon-color">%s</ul><!-- /wp:social-links -->',
		$inner
	);

	$busy = true;
	$html = do_blocks( $markup );
	$busy = false;

	return $html;
}
add_shortcode('convoca_socials', 'convoca_theme_socials_html');

/**
 * Cifras reales de la instalación para la franja de estadísticas.
 *
 * Sin números fijos: cada cifra sale de datos reales y se puede sobrescribir
 * con el filtro `convoca_theme_stats`. Si no hay ningún dato, devuelve [] y el
 * patrón convoca/stats no se pinta.
 *
 * @since 2.8.0
 * @return array<string,array{value:string,label:string}>
 */
function convoca_theme_get_stats(): array
{
    $stats = array();
    $year  = (int) gmdate('Y');

    $published = (int) wp_count_posts('post')->publish;
    if ($published > 0) {
        $stats['publicaciones'] = array(
            'value' => '+' . number_format_i18n($published),
            'label' => __('Publicaciones', 'convoca-theme'),
        );
    }

    $this_year = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'date_query'     => array(array('year' => $year)),
    ));
    if ($this_year->found_posts > 0) {
        $stats['este_ano'] = array(
            'value' => (string) number_format_i18n($this_year->found_posts),
            'label' => sprintf(__('Publicaciones en %s', 'convoca-theme'), $year),
        );
    }

    $oldest = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'order'          => 'ASC',
        'orderby'        => 'date',
    ));
    if (!empty($oldest)) {
        $years = max(1, $year - (int) get_the_date('Y', $oldest[0]));
        $stats['anos'] = array(
            'value' => (string) number_format_i18n($years),
            'label' => __('Años de trayectoria', 'convoca-theme'),
        );
    }

    return apply_filters('convoca_theme_stats', $stats);
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
function convoca_theme_lang_languages(): array
{
    $languages = array();

    if (function_exists('icl_get_languages')) {
        $raw = icl_get_languages('skip_missing=0&orderby=code');
        if (!empty($raw)) {
            foreach ($raw as $lang) {
                $languages[] = array(
                    'code'  => $lang['language_code'],
                    'name'  => $lang['native_name'] ?: $lang['translated_name'],
                    'url'   => $lang['url'],
                    'is_current' => !empty($lang['active']),
                );
            }
        }
    } elseif (function_exists('pll_the_languages')) {
        // Polylang fallback.
        $pll = pll_the_languages(array('raw' => 1, 'hide_current' => 0));
        if (is_array($pll)) {
            foreach ($pll as $lang) {
                $languages[] = array(
                    'code'  => $lang['slug'],
                    'name'  => $lang['name'],
                    'url'   => $lang['url'],
                    'is_current' => !empty($lang['current_lang']),
                );
            }
        }
    }

    return $languages;
}

function convoca_theme_lang_switcher_html(): string
{
    $languages = convoca_theme_lang_languages();
    if (count($languages) < 2) {
        return '';
    }

    $current_code = 'EN';
    foreach ($languages as $lang) {
        if (!empty($lang['is_current'])) {
            $current_code = strtoupper($lang['code']);
            break;
        }
    }

    $id = 'convoca-lang-' . wp_unique_id();

    $html = '<li class="menu-item menu-item-type-custom menu-item-object-custom convoca-lang-switcher convoca-lang-switcher--dropdown">';
    $html .= '<button type="button" class="convoca-lang-switcher__toggle" aria-label="' . esc_attr__('Cambiar idioma', 'convoca-theme') . '" aria-expanded="false" aria-controls="' . esc_attr($id) . '">';
    $html .= '<span class="convoca-lang-switcher__globe" aria-hidden="true">🌐</span>';
    $html .= '<span class="convoca-lang-switcher__code">' . esc_html($current_code) . '</span>';
    $html .= '<span class="convoca-lang-switcher__caret" aria-hidden="true">▾</span>';
    $html .= '</button>';
    $html .= '<ul class="convoca-lang-switcher__dropdown" id="' . esc_attr($id) . '">';
    foreach ($languages as $lang) {
        $cls = $lang['is_current'] ? 'convoca-lang-switcher__link is-active' : 'convoca-lang-switcher__link';
        $html .= '<li class="convoca-lang-switcher__item">';
        $html .= '<a class="' . esc_attr($cls) . '" href="' . esc_url($lang['url']) . '" hreflang="' . esc_attr($lang['code']) . '" lang="' . esc_attr($lang['code']) . '">' . esc_html($lang['name']) . '</a>';
        $html .= '</li>';
    }
    $html .= '</ul></li>';

    return $html;
}

function convoca_theme_language_switcher($items, $args)
{
    if (!function_exists('icl_get_languages') && !function_exists('pll_the_languages')) {
        return $items;
    }

    return $items . convoca_theme_lang_switcher_html();
}
add_filter('wp_nav_menu_items', 'convoca_theme_language_switcher', 20, 2);

/**
 * FSE: inyecta el selector en los bloques de navegación (core/navigation).
 */
function convoca_theme_lang_switcher_block($block_content, $block)
{
    if (empty($block['blockName']) || $block['blockName'] !== 'core/navigation') {
        return $block_content;
    }
    $switcher = convoca_theme_lang_switcher_html();
    if ($switcher === '') {
        return $block_content;
    }
    // Insertar antes del cierre del <nav> o del contenedor del bloque.
    if (preg_match('#(</nav>)#', $block_content, $m, PREG_OFFSET_CAPTURE)) {
        $pos = $m[1][1];
        return substr($block_content, 0, $pos) . $switcher . substr($block_content, $pos);
    }
    return $block_content . $switcher;
}
add_filter('render_block', 'convoca_theme_lang_switcher_block', 20, 2);

/**
 * Estilos del selector de idioma (inline para no depender de assets compilados).
 */
function convoca_theme_lang_switcher_styles(): void
{
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
add_action('wp_head', 'convoca_theme_lang_switcher_styles', 99);

/**
 * Excluir páginas de traducción EN (slugs terminados en -2) del bloque
 * wp:page-list del header. WPML las registra como páginas publicadas y el
 * page-list las listaría duplicadas junto a la versión ES.
 */
add_filter('wp_list_pages_excludes', function ($excludes) {
    $pages = get_posts(array(
        'post_type'   => 'page',
        'post_status' => 'publish',
        'numberposts' => -1,
    ));
    foreach ($pages as $p) {
        if (preg_match('/-2$/', $p->post_name)) {
            $excludes[] = $p->ID;
        }
    }
    return $excludes;
});
