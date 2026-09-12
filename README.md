# Convoca

A block theme (Full Site Editing) for community, environmental and non-profit
organisations. It ships a complete, editable site: templates, template parts, block
patterns, a documented colour and type scale, a dark mode, and a small set of filters so
the installation supplies its own texts, links and figures.

The theme is **presentation only**. It holds no content of any particular site: texts,
links, phone numbers and figures come from the installation through the filters documented
below. Any visible string is translatable (text domain `convoca`).

## Requirements

- WordPress 6.4 or later (tested up to 7.0)
- PHP 8.1 or later
- No external dependencies, no build step: clone or upload and activate

## What it includes

**Templates** (11): front page, blog home, index, archive, single, page, two page layouts
(`page-landing`, `page-institucional`), `page-proyecto`, search and 404.

**Template parts** (8): header, footer, sidebar, hero, call to action, activity card,
project card and activity meta.

**Patterns** (15), in the block inserter under the `convoca` category:

| Pattern | What it is |
|---|---|
| `convoca/hero`, `convoca/hero-imagen`, `convoca/hero-gradient` | Three opening sections |
| `convoca/cards-3`, `convoca/cards-grid` | Card grids |
| `convoca/stats-bar`, `convoca/stats` | Figures bar; `stats` reads real figures from the installation |
| `convoca/cta`, `convoca/cta-centered` | Calls to action |
| `convoca/proximas-actividades`, `convoca/inscripcion-actividad` | Activity sections |
| `convoca/pricing-grid` | Membership tiers |
| `convoca/transparencia-table` | Transparency table |
| `convoca/contact-banner` | Contact band |
| `convoca/section-violeta` | Dark section |

**Design system** in `theme.json`: 25 colours, 9 gradients, 7 font sizes, 2 font families
and 5 shadows, all as presets you can change. **23 blocks** have their own styles. The
theme also registers editor styles for blocks and a set of global styles.

**Dark mode**, with the visitor's choice remembered and the system preference respected.
The toggle exposes its state to screen readers.

**Event information** on posts: an "Event" box in the editor (start date, end date,
place), and JSON-LD `Event` schema on the front end when the post is marked as an event.
Dates are validated and timezone-correct; nothing is published if the date cannot be read.

**Language switcher** in the header, for installations running WPML or Polylang.

**Admin help page** under Appearance, with what the theme offers and the filters it uses.

## Installation

1. Upload the `convoca` folder to `wp-content/themes/`, or upload the ZIP from
   Appearance → Themes → Add New.
2. Activate it.
3. Edit everything else in **Appearance → Editor** (Site Editor): templates, parts and
   patterns. Colours and typography live in **Styles**.

## Configuring the installation's data

The theme prints placeholders between braces in its templates and patterns. The
installation fills them, either with the filters below or with any token of your own
through `convoca_theme_footer_replacements`. A placeholder that has no value is removed
together with its link, so nothing is ever published as `{something}`.

**Tokens provided by the theme:**

| Token | Value |
|---|---|
| `{site_name}`, `{site_tagline}`, `{site_description}` | Site name, tagline and description from Settings → General |
| `{year}` | Current year |
| `{contact_email}`, `{volunteer_email}` | Administration email; volunteer email |
| `{cta_url}`, `{cta_label}`, `{cta_heading}`, `{cta_text}` | Header call to action |
| `{centro_url}`, `{centro_label}`, `{community_url}` | Additional site links |
| `{social_instagram}`, `{social_facebook}`, `{social_youtube}`, `{social_handle}` | Social profiles |
| `{phone_url}`, `{phone_label}` | Phone, as a link and as text |
| `{sidebar_description}` | Short text for the sidebar |
| `{copyright_extra}` | Extra line for the copyright notice (allows safe HTML) |
| `{<key>_url}`, `{<key>_label}` | One pair for every link key the installation declares |

**Filters:**

| Filter | What it fills |
|---|---|
| `convoca_theme_site_links` | Array of `key => URL`. Each key produces `{key_url}` |
| `convoca_theme_link_labels` | Array of `key => label` for the same keys |
| `convoca_theme_footer_replacements` | Array of `token => value`: your own placeholders |
| `convoca_theme_cta_url`, `_label`, `_heading`, `_text` | The header call to action |
| `convoca_theme_stats` | Array of `key => ['value' => '', 'label' => '']`; the figures bar |
| `convoca_theme_volunteer_email` | Volunteer contact address |
| `convoca_theme_social_instagram`, `_facebook`, `_youtube`, `_handle` | Social profiles |
| `convoca_theme_centro_url`, `convoca_theme_community_url` | Additional links |
| `convoca_theme_copyright_extra` | Extra line in the footer |

Example, in a small plugin or in your child theme's `functions.php`:

```php
add_filter( 'convoca_theme_site_links', function ( array $links ): array {
	$links['activities'] = get_category_link( get_category_by_slug( 'activities' ) );
	$links['contact']    = home_url( '/contact/' );
	return $links;
} );

add_filter( 'convoca_theme_link_labels', function ( array $labels ): array {
	$labels['activities'] = __( 'What we do', 'my-site' );
	return $labels;
} );

add_filter( 'convoca_theme_footer_replacements', function ( array $replacements ): array {
	$replacements['{phone_url}']   = 'tel:+34600000000';
	$replacements['{phone_label}'] = '600 000 000';
	return $replacements;
} );
```

## For developers

- No build step, no package manager, no bundled library. What you download is what runs.
- `functions.php` keeps the installation-facing logic in one place, with the filters above.
- Block styles and patterns are registered in `convoca_register_block_styles()` and
  `convoca_register_pattern_categories()`.
- Strings use the `convoca` text domain; a `.pot` is included in `languages/`.
- PHP coding standard: WordPress Coding Standards. Static analysis: PHPStan, both clean.

## Accessibility

Semantic landmarks, skip link, visible focus, 44 px touch targets, and colour pairs that
meet WCAG 2.1 AA contrast for text. The dark mode toggle exposes `aria-pressed` and
updates its label. Colours in `theme.json` are presets: if you change them, check
contrast.

## Performance

No jQuery dependency, no render-blocking third-party assets, block styles loaded by
WordPress. Stylesheets are versioned by file modification time so caches refresh. Long
lists avoid `transition: all` so the browser only watches the properties that change.

## Credits

- Fonts: Outfit and Lato (Google Fonts), loaded through a preload for the editor.
- Icons: inline SVG, no icon font.

## License

GPL-2.0-or-later. See `LICENSE`.

Copyright (C) 2026 Jose Carlos Nieto Ramos.
