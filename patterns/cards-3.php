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
 * Title: 3 tarjetas de contenido
 * Slug: convoca/cards-3
 * Categories: convoca, convoca-layout
 * Description: Grid de 3 tarjetas con icono, título y descripción.
 * Keywords: tarjetas, cards, grid, columnas
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
    <!-- wp:heading {"textAlign":"center","level":2,"fontSize":"x-large","fontFamily":"display"} -->
    <h2 class="wp-block-heading has-text-align-center has-display-font-family has-x-large-font-size">Nuestras áreas de
        acción</h2>
    <!-- /wp:heading -->
    <!-- wp:columns {"isStackedOnMobile":true,"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"},"margin":{"top":"var:preset|spacing|40"}}}} -->
    <div class="wp-block-columns is-stacked-on-mobile" style="margin-top:var(--wp--preset--spacing--40)">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-group is-style-card" style="padding:var(--wp--preset--spacing--30)">
                <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
                <p class="has-text-align-center" style="font-size:2.5rem">🌿</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"textAlign":"center","level":3} -->
                <h3 class="wp-block-heading has-text-align-center">Educación Ambiental</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Talleres, charlas y actividades formativas para fomentar la conciencia
                    ecológica en la comunidad.</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-group is-style-card" style="padding:var(--wp--preset--spacing--30)">
                <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
                <p class="has-text-align-center" style="font-size:2.5rem">🏔️</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"textAlign":"center","level":3} -->
                <h3 class="wp-block-heading has-text-align-center">Conservación</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Proyectos de restauración, limpieza de espacios naturales y protección
                    de la biodiversidad asturiana.</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-group is-style-card" style="padding:var(--wp--preset--spacing--30)">
                <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
                <p class="has-text-align-center" style="font-size:2.5rem">🤝</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"textAlign":"center","level":3} -->
                <h3 class="wp-block-heading has-text-align-center">Acción Comunitaria</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">Voluntariado vecinal, actividades comunitarias y redes de colaboración
                    con otras asociaciones.</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->