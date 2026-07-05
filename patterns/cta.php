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
 * Title: Llamada a la acción
 * Slug: convoca/cta
 * Categories: convoca, convoca-layout
 * Description: Sección CTA con título, subtítulo y botones sobre fondo oscuro.
 * Keywords: cta, llamada, acción, botones
 */
?>
<!-- wp:group {"gradient":"cta-dark","textColor":"blanco","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"var:preset|spacing|50"}},"border":{"radius":"0px"}},"layout":{"type":"constrained","contentSize":"600px"}} -->
<div class="wp-block-group has-cta-dark-gradient-background has-background has-blanco-color has-text-color"
    style="border-radius:0px;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);margin-top:var(--wp--preset--spacing--50)">
    <!-- wp:heading {"textAlign":"center","level":2,"textColor":"amarillo","fontSize":"x-large","fontFamily":"display"} -->
    <h2
        class="wp-block-heading has-text-align-center has-amarillo-color has-text-color has-display-font-family has-x-large-font-size">
        ¿Quieres ser parte del cambio?</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph {"align":"center","style":{"color":{"text":"rgba(255,255,255,0.85)"},"spacing":{"margin":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|30"}}}} -->
    <p class="has-text-align-center"
        style="color:rgba(255,255,255,0.85);margin-top:var(--wp--preset--spacing--10);margin-bottom:var(--wp--preset--spacing--30)">
        Únete como socio/a, participa como voluntario/a, o simplemente ven a conocernos. Cada acción cuenta.</p>
    <!-- /wp:paragraph -->
    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
    <div class="wp-block-buttons">
        <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background wp-element-button" href="/alta-socios/">Hazte Socio/a</a></div>
        <!-- /wp:button -->
        <!-- wp:button {"className":"is-style-outline","textColor":"blanco","style":{"border":{"color":"rgba(255,255,255,0.5)","width":"2px"}}} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-blanco-color has-text-color has-border-color wp-element-button" href="/voluntariado/" style="border-color:rgba(255,255,255,0.5);border-width:2px">Voluntariado</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
</div>
<!-- /wp:group -->