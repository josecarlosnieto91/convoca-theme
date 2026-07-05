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
 * Title: Hero principal
 * Slug: convoca/hero
 * Categories: convoca, convoca-layout
 * Description: Sección hero de portada con imagen destacada, degradado, título y botones CTA.
 * Keywords: hero, portada, cover
 */
?>
<!-- wp:cover {"useFeaturedImage":true,"dimRatio":70,"minHeight":85,"minHeightUnit":"vh","isDark":true,"gradient":"sunset-hero","className":"is-style-topographic","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-cover is-dark is-style-topographic"
    style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:85vh">
    <span aria-hidden="true"
        class="wp-block-cover__background has-background-dim-0 has-background-dim has-sunset-hero-gradient-background has-background-gradient"></span>
    <div class="wp-block-cover__inner-container">
        <!-- wp:group {"layout":{"type":"constrained","contentSize":"700px"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
        <div class="wp-block-group">
            <!-- wp:heading {"level":1,"textColor":"blanco","fontSize":"xx-large","style":{"typography":{"letterSpacing":"-0.02em"}}} -->
            <h1 class="wp-block-heading has-blanco-color has-text-color has-xx-large-font-size"
                style="letter-spacing:-0.02em">Sembrando conciencia, transformando el mañana</h1>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.2rem"},"color":{"text":"rgba(255,255,255,0.9)"}}} -->
            <p style="color:rgba(255,255,255,0.9);font-size:1.2rem">Asociación socioambiental asturiana. Educación,
                conservación y acción comunitaria desde 2017.</p>
            <!-- /wp:paragraph -->
            <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)">
                <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco"} -->
                <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background wp-element-button" href="/actividades/">Ver actividades</a></div>
                <!-- /wp:button -->
                <!-- wp:button {"className":"is-style-outline","textColor":"blanco","style":{"border":{"color":"rgba(255,255,255,0.5)","width":"2px"}}} -->
                <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-blanco-color has-text-color has-border-color wp-element-button" href="/quienes-somos/" style="border-color:rgba(255,255,255,0.5);border-width:2px">Conócenos</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
    </div>
</div>
<!-- /wp:cover -->