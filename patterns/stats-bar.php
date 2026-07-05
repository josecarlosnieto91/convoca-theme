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
 * Title: Barra de estadísticas
 * Slug: convoca/stats-bar
 * Categories: convoca
 * Description: Barra de 4 estadísticas con fondo degradado oscuro y valores en amarillo.
 * Keywords: estadísticas, números, stats, datos
 */
?>
<!-- wp:group {"gradient":"stats-dark","textColor":"blanco","className":"convoca-stats","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group convoca-stats has-stats-dark-gradient-background has-background has-blanco-color has-text-color"
    style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">

    <!-- wp:columns {"isStackedOnMobile":true} -->
    <div class="wp-block-columns is-stacked-on-mobile">

        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value"></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label"></p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value"></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label"></p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value"></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label"></p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"25%"} -->
        <div class="wp-block-column" style="flex-basis:25%">
            <!-- wp:paragraph {"align":"center","className":"stat-value"} -->
            <p class="has-text-align-center stat-value"></p><!-- /wp:paragraph -->
            <!-- wp:paragraph {"align":"center","className":"stat-label"} -->
            <p class="has-text-align-center stat-label"></p><!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->