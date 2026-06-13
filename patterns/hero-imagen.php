<?php
/**
 * Title: Hero con imagen
 * Slug: convoca/hero-imagen
 * Categories: biodevas, biodevas-layout
 * Description: Hero con imagen de fondo personalizable, overlay degradado, título y botones CTA.
 * Keywords: hero, portada, imagen, banner
 */
?>
<!-- wp:cover {"url":"","dimRatio":70,"minHeight":85,"minHeightUnit":"vh","isDark":true,"gradient":"sunset-hero","className":"is-style-topographic hero-topographic","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-cover is-dark is-style-topographic hero-topographic"
    style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:85vh">
    <span aria-hidden="true"
        class="wp-block-cover__background has-background-dim-70 has-background-dim has-sunset-hero-gradient-background has-background-gradient"></span>
    <div class="wp-block-cover__inner-container">
        <!-- wp:group {"layout":{"type":"constrained","contentSize":"700px"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
        <div class="wp-block-group">
            <!-- wp:heading {"level":1,"textColor":"blanco","fontSize":"xx-large","style":{"typography":{"letterSpacing":"-0.02em"}}} -->
            <h1 class="wp-block-heading has-blanco-color has-text-color has-xx-large-font-size"
                style="letter-spacing:-0.02em">Tu título aquí</h1>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.2rem"},"color":{"text":"rgba(255,255,255,0.9)"}}} -->
            <p style="color:rgba(255,255,255,0.9);font-size:1.2rem">Tu descripción aquí.</p>
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