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
 * Title: Llamada a acción centrada
 * Slug: convoca/cta-centered
 * Categories: convoca
 * Description: CTA centrada con fondo degradado oscuro, titular y dos botones.
 * Keywords: cta, llamada, acción, botones
 */
?>
<!-- wp:group {"gradient":"cta-dark","textColor":"blanco","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"600px"}} -->
<div class="wp-block-group has-cta-dark-gradient-background has-background has-blanco-color has-text-color"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

    <!-- wp:heading {"textAlign":"center","textColor":"amarillo","fontSize":"x-large"} -->
    <h2 class="wp-block-heading has-text-align-center has-amarillo-color has-text-color has-x-large-font-size"></h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"color":{"text":"rgba(255,255,255,0.85)"}}} -->
    <p class="has-text-align-center" style="color:rgba(255,255,255,0.85)"></p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
    <div class="wp-block-buttons">
        <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background wp-element-button"></a></div>
        <!-- /wp:button -->
        <!-- wp:button {"className":"is-style-outline","textColor":"blanco"} -->
        <div class="wp-block-button is-style-outline"><a
                class="wp-block-button__link has-blanco-color has-text-color wp-element-button"></a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->

</div>
<!-- /wp:group -->