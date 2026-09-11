<?php

/**
 * Convoca Theme
 *
 * @package    Convoca\Theme
 * @subpackage Patterns
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
 * Title: Cifras de la comunidad (dinámicas)
 * Slug: convoca/stats
 * Categories: convoca, convoca-layout
 * Description: Franja con cifras REALES de la instalación (no valores fijos). Cada cifra se puede sobrescribir con el filtro convoca_theme_stats; las que no tengan dato se omiten.
 * Keywords: stats, estadísticas, cifras, números
 *
 * Este patrón NO lleva números hardcodeados: los obtiene en PHP de los datos
 * reales del sitio (entradas publicadas, antigüedad, páginas hijas...) y admite
 * sobrescritura por filtro:
 *
 *     add_filter( 'convoca_theme_stats', function ( $stats ) {
 *         $stats['socios'] = 210; // sólo si existe un dato real
 *         return $stats;
 *     } );
 *
 * @since 2.8.0
 */

$convoca_stats = function_exists( 'convoca_theme_get_stats' ) ? convoca_theme_get_stats() : array();

if ( empty( $convoca_stats ) ) {
	return; // Sin datos reales no se pinta una franja vacía.
}

$convoca_cols = count( $convoca_stats );
?>
<!-- wp:group {"gradient":"stats-dark","textColor":"blanco","className":"convoca-stats","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group convoca-stats has-stats-dark-gradient-background has-background has-blanco-color has-text-color"
	style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:columns {"isStackedOnMobile":true} -->
	<div class="wp-block-columns is-stacked-on-mobile">
		<?php foreach ( $convoca_stats as $convoca_key => $convoca_stat ) : ?>
		<!-- wp:column {"width":"<?php echo esc_attr( round( 100 / $convoca_cols, 4 ) ); ?>%"} -->
		<div class="wp-block-column" style="flex-basis:<?php echo esc_attr( round( 100 / $convoca_cols, 4 ) ); ?>%">
			<!-- wp:paragraph {"align":"center","className":"stat-value"} -->
			<p class="has-text-align-center stat-value"><?php echo esc_html( $convoca_stat['value'] ); ?></p><!-- /wp:paragraph -->
			<!-- wp:paragraph {"align":"center","className":"stat-label"} -->
			<p class="has-text-align-center stat-label"><?php echo esc_html( $convoca_stat['label'] ); ?></p><!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
