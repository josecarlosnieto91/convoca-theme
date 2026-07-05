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
 * Title: Grid de precios (membresías)
 * Slug: convoca/pricing-grid
 * Categories: convoca
 * Description: Tres planes de membresía en grid con el plan central destacado.
 * Keywords: precios, membresía, socios, pricing
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1000px"}} -->
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
            <!-- wp:group {"className":"convoca-price-card","layout":{"type":"constrained"}} -->
            <div class="wp-block-group convoca-price-card">
                <!-- wp:heading {"level":3,"textAlign":"center"} -->
                <h3 class="wp-block-heading has-text-align-center"></h3><!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center","className":"price-amount"} -->
                <p class="has-text-align-center price-amount"><span></span></p><!-- /wp:paragraph -->
                <!-- wp:list {"className":"price-features"} -->
                <ul class="price-features">
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <!-- /wp:list -->
                <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","fontSize":"small"} -->
                    <div class="wp-block-button is-style-outline"><a
                            class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button"></a></div><!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"convoca-price-card featured","layout":{"type":"constrained"}} -->
            <div class="wp-block-group convoca-price-card featured">
                <!-- wp:heading {"level":3,"textAlign":"center"} -->
                <h3 class="wp-block-heading has-text-align-center"></h3><!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center","className":"price-amount"} -->
                <p class="has-text-align-center price-amount"><span></span></p><!-- /wp:paragraph -->
                <!-- wp:list {"className":"price-features"} -->
                <ul class="price-features">
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <!-- /wp:list -->
                <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco","fontSize":"small"} -->
                    <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background has-small-font-size wp-element-button"></a></div><!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"convoca-price-card","layout":{"type":"constrained"}} -->
            <div class="wp-block-group convoca-price-card">
                <!-- wp:heading {"level":3,"textAlign":"center"} -->
                <h3 class="wp-block-heading has-text-align-center"></h3><!-- /wp:heading -->
                <!-- wp:paragraph {"align":"center","className":"price-amount"} -->
                <p class="has-text-align-center price-amount"><span></span></p><!-- /wp:paragraph -->
                <!-- wp:list {"className":"price-features"} -->
                <ul class="price-features">
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <!-- /wp:list -->
                <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","fontSize":"small"} -->
                    <div class="wp-block-button is-style-outline"><a
                            class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button"></a></div><!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->