<?php
/**
 * Title: Banner de contacto
 * Slug: convoca/contact-banner
 * Categories: biodevas, biodevas-layout
 * Description: Franja de contacto con email, teléfono y redes sociales.
 * Keywords: contacto, email, teléfono, redes
 */
?>
<!-- wp:group {"backgroundColor":"gris-piedra","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group has-gris-piedra-background-color has-background"
    style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
    <!-- wp:heading {"textAlign":"center","level":2,"fontSize":"x-large","fontFamily":"display"} -->
    <h2 class="wp-block-heading has-text-align-center has-display-font-family has-x-large-font-size">¿Hablamos?</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
    <p class="has-text-align-center" style="margin-bottom:var(--wp--preset--spacing--30)">Escríbenos o síguenos en
        redes. Estamos encantados de conocerte.</p>
    <!-- /wp:paragraph -->
    <!-- wp:columns {"isStackedOnMobile":true,"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
    <div class="wp-block-columns is-stacked-on-mobile">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:paragraph {"align":"center"} -->
            <p class="has-text-align-center">📧 <a href="mailto:coordinacion@biodevas.org">coordinacion@biodevas.org</a>
            </p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:paragraph {"align":"center"} -->
            <p class="has-text-align-center">📧 <a href="mailto:voluntarios@biodevas.org">voluntarios@biodevas.org</a>
            </p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:social-links {"iconColor":"naranja","iconColorValue":"#ff8700","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|10"}}}} -->
            <ul class="wp-block-social-links has-icon-color">
                <!-- wp:social-link {"url":"https://www.instagram.com/biodevas","service":"instagram"} /-->
                <!-- wp:social-link {"url":"https://www.facebook.com/biodevas","service":"facebook"} /-->
                <!-- wp:social-link {"url":"https://www.youtube.com/channel/UCfF80C0d0lFs7fXM9kNqyfQ","service":"youtube"} /-->
            </ul>
            <!-- /wp:social-links -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->