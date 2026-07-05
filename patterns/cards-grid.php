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
 * Title: Grid de tarjetas (3 columnas)
 * Slug: convoca/cards-grid
 * Categories: convoca
 * Description: Rejilla de 3 tarjetas con imagen, etiqueta, título, descripción y botón.
 * Keywords: tarjetas, grid, cards, proyectos
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

    <!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
    <h2 class="wp-block-heading has-text-align-center has-x-large-font-size"></h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","className":"is-style-lead"} -->
    <p class="has-text-align-center is-style-lead"></p>
    <!-- /wp:paragraph -->

    <!-- wp:spacer {"height":"var:preset|spacing|30"} -->
    <div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:columns -->
    <div class="wp-block-columns">

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-card">
                <!-- wp:image {"sizeSlug":"medium","style":{"border":{"radius":"0px"}},"height":"200px"} -->
                <figure class="wp-block-image size-medium" style="border-radius:0px"><img src="" alt=""
                        style="height:200px;object-fit:cover" /></figure>
                <!-- /wp:image -->
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group"
                    style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                    <!-- wp:heading {"level":3} -->
                    <h3 class="wp-block-heading"></h3><!-- /wp:heading -->
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"},"color":{"text":"#555555"}}} -->
                    <p style="color:#555555;font-size:0.95rem"></p><!-- /wp:paragraph -->
                    <!-- wp:buttons -->
                    <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","fontSize":"small"} -->
                        <div class="wp-block-button is-style-outline"><a
                                class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button"></a></div><!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-card">
                <!-- wp:image {"sizeSlug":"medium","style":{"border":{"radius":"0px"}},"height":"200px"} -->
                <figure class="wp-block-image size-medium" style="border-radius:0px"><img src="" alt=""
                        style="height:200px;object-fit:cover" /></figure>
                <!-- /wp:image -->
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group"
                    style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                    <!-- wp:heading {"level":3} -->
                    <h3 class="wp-block-heading"></h3><!-- /wp:heading -->
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"},"color":{"text":"#555555"}}} -->
                    <p style="color:#555555;font-size:0.95rem"></p><!-- /wp:paragraph -->
                    <!-- wp:buttons -->
                    <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","fontSize":"small"} -->
                        <div class="wp-block-button is-style-outline"><a
                                class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button"></a></div><!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"is-style-card","layout":{"type":"constrained"}} -->
            <div class="wp-block-group is-style-card">
                <!-- wp:image {"sizeSlug":"medium","style":{"border":{"radius":"0px"}},"height":"200px"} -->
                <figure class="wp-block-image size-medium" style="border-radius:0px"><img src="" alt=""
                        style="height:200px;object-fit:cover" /></figure>
                <!-- /wp:image -->
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group"
                    style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                    <!-- wp:heading {"level":3} -->
                    <h3 class="wp-block-heading"></h3><!-- /wp:heading -->
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"},"color":{"text":"#555555"}}} -->
                    <p style="color:#555555;font-size:0.95rem"></p><!-- /wp:paragraph -->
                    <!-- wp:buttons -->
                    <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","fontSize":"small"} -->
                        <div class="wp-block-button is-style-outline"><a
                                class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button"></a></div><!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->