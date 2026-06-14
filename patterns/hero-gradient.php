<?php
/**
 * Title: Hero con degradado
 * Slug: convoca/hero-gradient
 * Categories: convoca
 * Description: Sección hero con imagen destacada, overlay degradado atardecer, título y botones CTA.
 * Keywords: hero, portada, banner
 */
?>
<!-- wp:cover {"useFeaturedImage":true,"dimRatio":70,"minHeight":85,"minHeightUnit":"vh","isDark":true,"gradient":"sunset-hero","className":"is-style-topographic hero-topographic","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-cover is-dark is-style-topographic hero-topographic"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);min-height:85vh">
    <span aria-hidden="true"
        class="wp-block-cover__background has-background-dim-70 has-background-dim has-sunset-hero-gradient-background has-background-gradient"></span>
    <div class="wp-block-cover__inner-container">
        <!-- wp:group {"layout":{"type":"constrained","contentSize":"700px"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
        <div class="wp-block-group">
            <!-- wp:heading {"level":1,"textColor":"blanco","fontSize":"xx-large"} -->
            <h1 class="wp-block-heading has-blanco-color has-text-color has-xx-large-font-size"></h1>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"1.2rem"},"color":{"text":"rgba(255,255,255,0.9)"}}} -->
            <p style="color:rgba(255,255,255,0.9);font-size:1.2rem"></p>
            <!-- /wp:paragraph -->
            <!-- wp:buttons -->
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
    </div>
</div>
<!-- /wp:cover -->