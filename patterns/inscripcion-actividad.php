<?php
/**
 * Title: Sección de inscripción a actividad
 * Slug: convoca/inscripcion-actividad
 * Categories: biodevas
 * Description: Sección con formulario de inscripción (usa el shortcode dinámico para detectar la actividad actual).
 * Keywords: inscripción, actividad, formulario, plugin
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"backgroundColor":"gris-piedra","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-gris-piedra-background-color has-background"
    style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

    <!-- wp:columns -->
    <div class="wp-block-columns">

        <!-- wp:column {"width":"55%"} -->
        <div class="wp-block-column" style="flex-basis:55%">
            <!-- wp:heading {"fontSize":"x-large","textColor":"violeta"} -->
            <h2 class="wp-block-heading has-x-large-font-size has-violeta-color has-text-color">Información de la actividad</h2>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"color":{"text":"#555555"}}} -->
            <p style="color:#555555">Completa el formulario para inscribirte en esta actividad. Si eres socio/a de Biodevas, el descuento se aplicará automáticamente.</p>
            <!-- /wp:paragraph -->
            <!-- wp:list {"style":{"color":{"text":"#666666"}}} -->
            <ul style="color:#666666">
                <li><strong>📅 Fecha:</strong> [convoca_actividad_meta field="fecha_inicio"]</li>
                <li><strong>📍 Ubicación:</strong> [convoca_actividad_meta field="ubicacion"]</li>
                <li><strong>👥 Plazas:</strong> [convoca_actividad_meta field="plazas_disponibles"] disponibles de [convoca_actividad_meta field="plazas_totales"]</li>
                <li><strong>💰 Precio:</strong> [convoca_actividad_meta field="precio_general"]€ (socio/a: [convoca_actividad_meta field="precio_socio"]€)</li>
            </ul>
            <!-- /wp:list -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"45%"} -->
        <div class="wp-block-column" style="flex-basis:45%">
            <!-- wp:group {"className":"biodevas-form","style":{"border":{"radius":"12px","width":"2px","color":"var:preset|color|naranja"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"blanco","layout":{"type":"constrained"}} -->
            <div class="wp-block-group biodevas-form has-blanco-background-color has-background has-border-color has-naranja-border-color"
                style="border-color:var(--wp--preset--color--naranja);border-width:2px;border-radius:12px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                <!-- wp:heading {"level":3,"textColor":"naranja","fontFamily":"display"} -->
                <h3 class="wp-block-heading has-naranja-color has-text-color has-display-font-family">Inscripción</h3>
                <!-- /wp:heading -->
                <!-- wp:shortcode -->
                [biodevas_inscripcion_actual]
                <!-- /wp:shortcode -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->