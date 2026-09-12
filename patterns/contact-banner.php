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
 * Title: Contact banner
 * Slug: convoca/contact-banner
 * Categories: convoca, convoca-layout
 * Description: Contact banner with organisation details and buttons.
 * Keywords: contact, banner, email, social
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}},"color":{"background":"#f8f6f2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-background" style="background-color:#f8f6f2;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"textAlign":"center","level":2} -->
	<h2 class="wp-block-heading has-text-align-center">Contact</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Have questions or want to get involved? Write to us or follow us on social media.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"isStackedOnMobile":true,"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-columns is-stacked-on-mobile">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">📧 <a href="mailto:{contact_email}">{contact_email}</a>
			</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">📧 <a href="mailto:{volunteer_email}">{volunteer_email}</a>
			</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:social-links {"iconColor":"naranja","iconColorValue":"#ff8700","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|10"}}}} -->
			<ul class="wp-block-social-links has-icon-color">
				<!-- wp:social-link {"url":"https://www.instagram.com/{social_handle}","service":"instagram"} /-->
				<!-- wp:social-link {"url":"https://www.facebook.com/{social_handle}","service":"facebook"} /-->
				<!-- wp:social-link {"url":"https://www.youtube.com/@{social_handle}","service":"youtube"} /-->
			</ul>
			<!-- /wp:social-links -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
