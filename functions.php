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
		.site-header .wp-block-group { display: flex; align-items: center; justify-content: space-between; max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
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
			<div class="welcome-panel-content" style="padding: 60px 40px; background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo get_template_directory_uri(); ?>/assets/images/admin-banner.png'); background-size: cover; background-position: center; color: #fff;">
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
 * 10. Shortcode for Dark Mode Toggle
 * Uso: [convoca_dark_mode_toggle]
 */
add_shortcode('convoca_dark_mode_toggle', function() {
	return '
	<button class="dark-mode-toggle" aria-label="Cambiar modo de color" type="button">
		<svg class="sun" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0a.996.996 0 000-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
		<svg class="moon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-3.03 0-5.5-2.47-5.5-5.5 0-1.82.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
	</button>';
});

/**
 * 12. SEO: JSON-LD Structured Data for Activities (Event Schema)
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
    ]);
    $block_content = str_replace(array_keys($replacements), array_values($replacements), $block_content);

    // Resolve shortcodes inside FSE patterns (do_blocks does not run them).
    if (strpos($block_content, '[') !== false) {
        $block_content = do_shortcode($block_content);
    }
    return $block_content;
}
add_filter('render_block', 'convoca_theme_render_block', 10, 2);

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
