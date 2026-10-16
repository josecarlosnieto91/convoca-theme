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
 * Title: Inscripción para el anuncio
 * Slug: convoca/inscripcion-anuncio
 * Categories: convoca
 * Description: Sección con el formulario de inscripción de UNA actividad, para la entrada de anuncio. Se le indica la actividad en el bloque y pinta su formulario; si no se le indica ninguna, avisa en vez de dejar el hueco en blanco.
 * Keywords: inscripción, anuncio, actividad, formulario
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"backgroundColor":"blanco","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-blanco-background-color has-background"
	style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

	<!-- wp:heading {"textColor":"violeta","fontFamily":"display"} -->
	<h2 class="wp-block-heading has-violeta-color has-text-color has-display-font-family"><?php esc_html_e( 'Apúntate a esta actividad', 'convoca' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"style":{"color":{"text":"#555555"}}} -->
	<p style="color:#555555"><?php esc_html_e( 'Elige la actividad en el bloque de aquí abajo: en cuanto la indiques, su formulario aparece en este mismo sitio. Si eres socio, el descuento se aplica solo.', 'convoca' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"className":"convoca-form","style":{"border":{"radius":"12px","width":"2px","color":"var:preset|color|naranja"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group convoca-form has-border-color has-naranja-border-color"
		style="border-color:var(--wp--preset--color--naranja);border-width:2px;border-radius:12px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
		<!-- wp:convoca-enroll/formulario-inscripcion {"actividadId":0} /-->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
