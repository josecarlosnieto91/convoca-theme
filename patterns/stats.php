<?php
/**
 * Title: Contador de estadísticas
 * Slug: convoca/stats
 * Categories: biodevas, biodevas-layout
 * Description: Franja con 4 columnas de estadísticas sobre fondo oscuro.
 * Keywords: stats, estadísticas, cifras, números
 */
?>
<!-- wp:group {"gradient":"stats-dark","textColor":"blanco","className":"biodevas-stats","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group biodevas-stats has-stats-dark-gradient-background has-background has-blanco-color has-text-color"
    style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
    <!-- wp:columns {"isStackedOnMobile":true} -->
    <div class="wp-block-columns is-stacked-on-mobile">
        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value">+200</p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label">Socios/as activos</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value">+50</p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label">Actividades/año</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value">8</p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label">Años de historia</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value">3</p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label">Proyectos activos</p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->