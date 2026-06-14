<?php
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
			echo '<div class="notice notice-success"><p><strong>Convoca:</strong> Se han reiniciado ' . $deleted . ' plantillas y partes de plantilla del theme. Se leerán directamente de los archivos del tema.</p></div>';
		});
	}
});

/**
 * 1. Theme Setup
 */
function convoca_setup(): void
{
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
	
	// Protección contra ausencia de plugins: Verificar existencia de CPTs antes de contar
	$has_enroll = post_type_exists('inscripcion');
	$inscripciones_count = $has_enroll ? wp_count_posts('inscripcion') : (object)[];
	
	$has_members = post_type_exists('miembro');
	$miembros_count = $has_members ? wp_count_posts('miembro') : (object)[];
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

					<!-- Estado de Inscripciones -->
					<?php if ($has_enroll) : ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Estado de Inscripciones', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<p><?php echo esc_html__('Resumen de participación actual (Convoca Enroll):', 'convoca-theme'); ?></p>
							<ul>
								<li><strong><?php echo esc_html__('Confirmadas:', 'convoca-theme'); ?></strong> <?php echo esc_html($inscripciones_count->publish ?? 0); ?></li>
								<li><strong><?php echo esc_html__('Pendientes:', 'convoca-theme'); ?></strong> <?php echo esc_html($inscripciones_count->pending ?? 0); ?></li>
								<li><strong><?php echo esc_html__('Canceladas:', 'convoca-theme'); ?></strong> <?php echo esc_html($inscripciones_count->trash ?? 0); ?></li>
							</ul>
							<a href="<?php echo esc_url(admin_url('edit.php?post_type=inscripcion')); ?>" class="button"><?php echo esc_html__('Gestionar Inscripciones', 'convoca-theme'); ?></a>
						</div>
					</div>
					<?php else : ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Estado de Inscripciones', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<div class="notice notice-warning inline" style="margin: 0;"><p><?php echo esc_html__('El plugin Convoca Enroll no está activo.', 'convoca-theme'); ?></p></div>
						</div>
					</div>
					<?php endif; ?>

					<!-- Estado de Miembros -->
					<?php if ($has_members) : ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Estado de Socios/Miembros', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<p><?php echo esc_html__('Resumen de la comunidad (Convoca Members):', 'convoca-theme'); ?></p>
							<ul>
								<li><strong><?php echo esc_html__('Activos:', 'convoca-theme'); ?></strong> <?php echo esc_html($miembros_count->publish ?? 0); ?></li>
								<li><strong><?php echo esc_html__('Pendientes:', 'convoca-theme'); ?></strong> <?php echo esc_html($miembros_count->pending ?? 0); ?></li>
							</ul>
							<a href="<?php echo esc_url(admin_url('edit.php?post_type=miembro')); ?>" class="button"><?php echo esc_html__('Ver Miembros', 'convoca-theme'); ?></a>
						</div>
					</div>
					<?php else : ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Estado de Socios/Miembros', 'convoca-theme'); ?></span></h2>
						<div class="inside">
							<div class="notice notice-warning inline" style="margin: 0;"><p><?php echo esc_html__('El plugin Convoca Members no está activo.', 'convoca-theme'); ?></p></div>
						</div>
					</div>
					<?php endif; ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">
					
					<!-- Guía de Plantillas -->
					<div class="postbox">
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

// Include shortcodes.
require_once get_theme_file_path('includes/shortcodes.php');

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
 * 11. Shortcode dinámico para inscripción en página de actividad.
 * Uso: [convoca_inscripcion_actual] -会自动获取当前活动的ID。
 */
add_shortcode('convoca_inscripcion_actual', function () {
	if (!is_singular('actividad')) {
		if (current_user_can('manage_options')) {
			return '<div class="convoca-alert convoca-alert--info" style="display:block;padding:15px;margin:10px 0;">' . esc_html__('💡 Este shortcode solo funciona en la página de una actividad individual.', 'convoca-theme') . '</div>';
		}
		return '';
	}
	$id = get_queried_object_id();

	// Validation: Ensure we have a positive integer and the post type is correct.
	if (!$id || !is_numeric($id) || $id <= 0 || get_post_type($id) !== 'actividad') {
		return '';
	}

	// Check if the Enroll plugin shortcode exists
	if (!shortcode_exists('convoca_form_inscripcion')) {
		return '';
	}

	return do_shortcode('[convoca_form_inscripcion id="' . (int) $id . '"]');
});

/**
 * 10. Filtro para mostrar solo actividades futuras en el archive.
 */
add_action('pre_get_posts', function ($query) {
	if (!is_admin() && $query->is_post_type_archive('actividad') && $query->is_main_query()) {
		$query->set('meta_key', '_conv_fecha_inicio');
		$query->set('meta_compare', '>=');
		$query->set('meta_value', wp_date('Y-m-d'));
		$query->set('orderby', 'meta_value');
		$query->set('order', 'ASC');
	}
	return $query;
});

/**
 * 11. Shortcode para mostrar metadatos de actividad.
 * Uso: [convoca_actividad_meta field="ubicacion"]
 */
add_shortcode('convoca_actividad_meta', function ($atts) {
	$atts = shortcode_atts([
		'field' => 'ubicacion',
	], $atts, 'convoca_actividad_meta');

	// Works in both singular (single actividad) and archive/list context
	$id = get_the_ID() ?: get_queried_object_id();
	if (!$id || get_post_type($id) !== 'actividad') {
		return '';
	}

	$meta_key = '_conv_' . sanitize_key($atts['field']);
	$value = get_post_meta($id, $meta_key, true);

	if ($value === "" || $value === null || $value === false) {
		return '';
	}

	// Format special fields
	if ($atts['field'] === 'precio') {
		return (float)$value > 0 ? number_format((float)$value, 2, ',', '.') . ' €' : 'Gratis';
	}
	if ($atts['field'] === 'plazas_disponibles') {
		$total = get_post_meta($id, '_conv_plazas_totales', true);
		return (int)$value . ' / ' . (int)$total;
	}
	if ($atts['field'] === 'fecha_inicio' || $atts['field'] === 'fecha_fin') {
		$ts = strtotime($value);
		return $ts ? date_i18n('j M Y', $ts) : '';
	}

	return esc_html($value);
});

/**
 * 12. SEO: JSON-LD Structured Data for Activities (Event Schema)
 */
function convoca_actividad_schema(): void
{
	if (!is_singular('actividad')) {
		return;
	}

	$post_id = get_queried_object_id();
	$post = get_post($post_id);

	// Get Meta
	$start_date = get_post_meta($post_id, '_conv_fecha_inicio', true);
	$end_date   = get_post_meta($post_id, '_conv_fecha_fin', true);
	$location   = get_post_meta($post_id, '_conv_ubicacion', true);
	$price      = get_post_meta($post_id, '_conv_precio_general', true);
	$plazas_dis = (int) get_post_meta($post_id, '_conv_plazas_disponibles', true);
	
	// Validate start_date: required for schema
	if (empty($start_date) || !strtotime($start_date)) {
		return;
	}
	$start_ts = strtotime($start_date);
	
	// Default to start + 2 hours if end date is missing or invalid
	if (empty($end_date) || !strtotime($end_date)) {
		$end_ts = $start_ts + 7200; // +2 hours
	} else {
		$end_ts = strtotime($end_date);
	}
	
	// Format as ISO8601
	$start_iso = wp_date('c', $start_ts);
	$end_iso   = wp_date('c', $end_ts);

	// Availability
	$availability = ($plazas_dis > 0) ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut';

	// Get lowest price from all options
	$precio_socio = get_post_meta($post_id, '_conv_precio_socio', true);
	$precio_socio_dia = get_post_meta($post_id, '_conv_precio_socio_dia', true);
	$precio_general = get_post_meta($post_id, '_conv_precio_general', true);
	
	$prices = array_filter([$precio_socio, $precio_socio_dia, $precio_general], function($v) {
		return $v !== '' && strtolower($v) !== 'gratis';
	});
	$lowest_price = !empty($prices) ? min(array_map(function($v) {
		return (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $v));
	}, $prices)) : 0;

	$price_numeric = (string) $lowest_price;

	// Schema Construction
	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'EducationEvent',
		'name'        => get_the_title($post_id),
		'description' => wp_strip_all_tags(get_the_excerpt($post_id)),
		'startDate'   => $start_iso,
		'endDate'     => $end_iso,
		'eventStatus' => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'location'    => [
			'@type' => 'Place',
			'name'  => $location,
			'address' => [
				'@type' => 'PostalAddress',
				'streetAddress' => $location,
				'addressLocality' => 'Asturias',
				'addressRegion' => 'Asturias',
				'addressCountry' => 'ES'
			]
		],
		'image'       => [
			get_the_post_thumbnail_url($post_id, 'full')
		],
		'offers'      => [
			'@type'         => 'Offer',
			'url'           => get_permalink($post_id),
			'price'         => $price_numeric,
			'priceCurrency' => 'EUR',
			'availability'  => $availability,
			'validFrom'     => $post->post_date_gmt
		],
		'organizer'   => [
			'@type' => 'Organization',
			'name'  => 'Asociación Convoca',
			'url'   => 'https://biodevas.org'
		]
	];

	echo "\n" . '<script type="application/ld+json" id="convoca-event-schema">' . "\n";
	echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	echo "\n" . '</script>' . "\n";
}
add_action('wp_head', 'convoca_actividad_schema');

// ─── Register bdv_calendario shortcode for activities list ───
add_shortcode('bdv_calendario', function () {
    $activities = \Convoca\Enroll\CPT_Actividad::get_upcoming(20);
    if (empty($activities)) {
        return '<div class="convoca-alert convoca-alert--info" style="display:block;padding:20px;margin:20px 0;border-radius:12px;"><p style="margin:0;font-size:1.1rem;">🔭 No hay actividades programadas próximamente. Vuelve pronto.</p></div>';
    }
    $html = '<div class="bdv-calendario-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;margin:30px 0;">';
    foreach ($activities as $a) {
        $id = $a->ID;
        $title = esc_html(get_the_title($id));
        $excerpt = esc_html(get_the_excerpt($id) ?: wp_trim_words(strip_tags(get_post_field('post_content', $id)), 30));
        $fecha = get_post_meta($id, '_conv_fecha_inicio', true);
        $fecha_fin = get_post_meta($id, '_conv_fecha_fin', true);
        $lugar = get_post_meta($id, '_conv_lugar', true) ?: get_post_meta($id, '_conv_ubicacion', true);
        $precio = get_post_meta($id, '_conv_precio', true);
        $plazas_disp = (int) get_post_meta($id, '_conv_plazas_disponibles', true);
        $plazas_total = (int) get_post_meta($id, '_conv_plazas_totales', true);
        $requires_payment = get_post_meta($id, '_conv_requires_payment', true);
        $permalink = esc_url(get_permalink($id));
        
        $fecha_str = '';
        if ($fecha) {
            $ts = strtotime($fecha);
            $fecha_str = date_i18n('j \d\e F \d\e\l Y', $ts);
            if ($fecha_fin) {
                $fecha_str .= ' — ' . date_i18n('j \d\e F \d\e\l Y', strtotime($fecha_fin));
            }
        }
        
        $precio_str = $requires_payment && $precio > 0 ? number_format($precio, 2, ',', '.') . ' €' : 'Gratis';
        $plazas_str = $plazas_disp . ' / ' . $plazas_total;
        
        $html .= '<div class="activitat-card" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:box-shadow 0.3s;">';
        $html .= '<div style="padding:20px;">';
        $html .= '<h3 style="margin:0 0 10px;font-size:1.1rem;line-height:1.3;"><a href="' . $permalink . '" style="color:#1a1a1a;text-decoration:none;">' . $title . '</a></h3>';
        if ($fecha_str) $html .= '<p style="margin:0 0 6px;color:#64748b;font-size:0.9rem;">📅 ' . $fecha_str . '</p>';
        if ($lugar) $html .= '<p style="margin:0 0 6px;color:#64748b;font-size:0.9rem;">📍 ' . esc_html($lugar) . '</p>';
        $html .= '<p style="margin:0 0 6px;color:#64748b;font-size:0.9rem;">👥 Plazas: ' . $plazas_str . '</p>';
        $html .= '<p style="margin:0 0 12px;color:var(--wp--preset--color--naranja, #ff8700);font-weight:700;font-size:1rem;">💰 ' . $precio_str . '</p>';
        $html .= '<p style="margin:0 0 15px;color:#555;font-size:0.9rem;line-height:1.5;">' . $excerpt . '</p>';
        $html .= '<a href="' . $permalink . '" class="wp-block-button__link has-naranja-background-color has-background" style="padding:8px 20px;font-size:0.85rem;border-radius:50px;text-decoration:none;display:inline-block;">Ver detalles →</a>';
        $html .= '</div></div>';
    }
    $html .= '</div>';
    return $html;
});

// ─── Register actividad meta keys for FSE block visibility ───
add_action('init', function () {
    $keys = ['fecha_inicio', 'fecha_fin', 'lugar', 'precio', 'plazas_disponibles', 'plazas_totales', 'difultad', 'requires_payment'];
    foreach ($keys as $key) {
        register_meta('post', '_conv_' . $key, [
            'type' => 'string',
            'description' => 'Convoca Actividad: ' . $key,
            'single' => true,
            'show_in_rest' => true,
            'default' => '',
        ], 'actividad');
    }
});
// ─── Process shortcodes in rendered blocks (FSE compatibility) ───

// ─── Inject actividad meta data into archive template markers ───
add_filter('render_block_core/post-template', function ($block_content, $block) {
    // Check if this is an actividad query
    $query = $block->context['query'] ?? [];
    $post_type = $query['postType'] ?? '';
    if ($post_type !== 'actividad' && strpos($block_content, '%%') === false) {
        return $block_content;
    }

    // Process each post item
    return preg_replace_callback('/%%(FECHA_INICIO|LUGAR|PRECIO|PLAZAS)%%/', function ($m) {
        $id = get_the_ID();
        if (!$id) return '';
        
        switch ($m[1]) {
            case 'FECHA_INICIO':
                $v = get_post_meta($id, '_conv_fecha_inicio', true);
                return $v ? date_i18n('j M Y', strtotime($v)) : '';
            case 'LUGAR':
                $v = get_post_meta($id, '_conv_lugar', true) ?: get_post_meta($id, '_conv_ubicacion', true);
                return esc_html($v);
            case 'PRECIO':
                $v = (float) get_post_meta($id, '_conv_precio', true);
                return $v > 0 ? number_format($v, 2, ',', '.') . ' €' : 'Gratis';
            case 'PLAZAS':
                $disp = (int) get_post_meta($id, '_conv_plazas_disponibles', true);
                $total = (int) get_post_meta($id, '_conv_plazas_totales', true);
                return $disp . ' / ' . $total;
        }
        return '';
    }, $block_content);
}, 10, 2);

// ─── Meta injection for archive-actividad template markers ───
add_filter('render_block', function ($html, $block) {
    if (strpos($html, '%%') === false) return $html;
    global $post;
    if (!$post || get_post_type($post) !== 'actividad') return $html;
    $id = $post->ID;
    $map = [
        '%%FECHA_INICIO%%' => function() use($id) { $v = get_post_meta($id, '_conv_fecha_inicio', true); return $v ? date_i18n('j M Y', strtotime($v)) : ''; },
        '%%LUGAR%%' => function() use($id) { return esc_html(get_post_meta($id, '_conv_lugar', true) ?: get_post_meta($id, '_conv_ubicacion', true) ?: ''); },
        '%%PRECIO%%' => function() use($id) { $p = (float) get_post_meta($id, '_conv_precio', true); return $p > 0 ? number_format($p, 2, ',', '.') . ' €' : 'Gratis'; },
        '%%PLAZAS%%' => function() use($id) { return ((int) get_post_meta($id, '_conv_plazas_disponibles', true)) . ' / ' . ((int) get_post_meta($id, '_conv_plazas_totales', true)); },
    ];
    foreach ($map as $k => $fn) $html = str_replace($k, $fn(), $html);
    return $html;
}, 10, 2);

// ─── Remove useless WordPress Events & News widget (fails without internet) ───
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
}, 999);
