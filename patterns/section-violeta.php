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
 * Title: Sección violeta oscuro
 * Slug: convoca/section-violeta
 * Categories: convoca
 * Description: Sección de ancho completo con fondo violeta oscuro y texto blanco.
 * Keywords: sección, violeta, oscuro
 */
?>
<!-- wp:group {"backgroundColor":"violeta","textColor":"blanco","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-blanco-color has-violeta-background-color has-text-color has-background"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

    <!-- wp:heading {"textAlign":"center","textColor":"naranja","fontSize":"x-large"} -->
    <h2 class="wp-block-heading has-text-align-center has-naranja-color has-text-color has-x-large-font-size"></h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"color":{"text":"rgba(255,255,255,0.85)"}}} -->
    <p class="has-text-align-center" style="color:rgba(255,255,255,0.85)"></p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->