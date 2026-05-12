<?php
/**
 * Title: Próximas actividades
 * Slug: biodevas/proximas-actividades
 * Categories: biodevas, biodevas-layout
 * Description: Grid de las próximas actividades del CPT actividad con metadatos.
 * Keywords: actividades, agenda, eventos
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

    <!-- wp:heading {"textAlign":"center","textColor":"violeta","fontFamily":"display","fontSize":"x-large","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
    <h2 class="wp-block-heading has-text-align-center has-violeta-color has-text-color has-display-font-family has-x-large-font-size"
        style="margin-bottom:var(--wp--preset--spacing--20)">Próximas actividades</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","style":{"color":{"text":"#555555"},"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
    <p class="has-text-align-center" style="color:#555555;margin-bottom:var(--wp--preset--spacing--40)">Talleres,
        voluntariados y salidas para conectar con la naturaleza.</p>
    <!-- /wp:paragraph -->

    <!-- wp:query {"queryId":10,"query":{"perPage":6,"pages":1,"offset":0,"postType":"actividad","order":"asc","orderBy":"meta_value","metaKey":"_bde_fecha_inicio","inherit":false}} -->
    <div class="wp-block-query">

        <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
        <!-- wp:group {"className":"is-style-card","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|20"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group is-style-card" style="padding-bottom:var(--wp--preset--spacing--20)">
            <!-- wp:post-featured-image {"isLink":true,"style":{"border":{"radius":"8px"}},"height":"200px"} /-->
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|30","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|10"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group"
                style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--30)">
                <!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontFamily":"var:preset|font-family-display","fontSize":"1.1rem"}}} /-->
                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
                <div class="wp-block-group">
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem","fontWeight":"500"},"color":{"text":"var:preset|color|naranja"}}} -->
                    <p class="has-custom-font-size" style="color:var(--wp--preset--color--naranja);font-size:0.85rem;font-weight:500">
                        <span class="dashicons dashicons-calendar-alt" style="vertical-align:middle"></span>
                        <!-- wp:biodevas-common/post-meta-field {"metaField":"_bde_fecha_inicio","type":"date"} /-->
                    </p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem","color":{"text":"#666666"}}} -->
                    <p class="has-custom-font-size" style="color:#666666;font-size:0.85rem">
                        <span class="dashicons dashicons-location" style="vertical-align:middle"></span>
                        <!-- wp:biodevas-common/post-meta-field {"metaField":"_bde_ubicacion"} /-->
                    </p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:group -->
                <!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
                <div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--10)">
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9rem","fontWeight":"700"}}} -->
                    <p style="font-size:0.9rem;font-weight:700">
                        <!-- wp:biodevas-common/post-meta-field {"metaField":"_bde_precio_general","type":"price"} /-->
                    </p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8rem","color":{"text":"#888888"}}} -->
                    <p style="color:#888888;font-size:0.8rem">
                        <!-- wp:biodevas-common/post-meta-field {"metaField":"_bde_plazas_disponibles"} /--> plazas
                    </p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:group -->
                <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}}} -->
                <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--10)">
                    <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco","fontSize":"small"} -->
                    <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background has-small-font-size wp-element-button" href="#">Más información</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->

        <!-- wp:query-no-results -->
        <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem"},"color":{"text":"#777777"}}} -->
        <p class="has-text-align-center" style="color:#777777;font-size:1rem">No hay actividades programadas en este
            momento. ¡Vuelve pronto!</p>
        <!-- /wp:paragraph -->
        <!-- /wp:query-no-results -->

    </div>
    <!-- /wp:query -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
    <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)">
        <!-- wp:button {"backgroundColor":"naranja","textColor":"blanco"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-blanco-color has-naranja-background-color has-text-color has-background wp-element-button" href="/actividades/">Ver todas las actividades</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->

</div>
<!-- /wp:group -->