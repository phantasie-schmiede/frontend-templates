# PSBits Templates

Basic templates for TYPO3 providing page layouts, content elements, Fluid ViewHelpers, and TypoScript setup — built on [Bootstrap](https://getbootstrap.com/) with [Content Blocks](https://github.com/FriendsOfTYPO3/content-blocks).

## Requirements

| Dependency                                                                        | Version |
|-----------------------------------------------------------------------------------|---------|
| TYPO3 CMS                                                                         | 13.4    |
| PHP                                                                               | 8.3     |
| [psbits/foundation](https://github.com/phantasie-schmiede/foundation)             | 3.2     |
| [psbits/view-helpers](https://github.com/phantasie-schmiede/view-helpers)         | 3.1     |
| [friendsoftypo3/content-blocks](https://github.com/FriendsOfTYPO3/content-blocks) | 1.5     |
| [wapplersystems/ws-scss](https://github.com/wapplersystems/ws-scss)               | 13      |

## Installation

```bash
composer require psbits/templates
```

Activate the site set in your site configuration, or add it as a dependency of your own site set:

```yaml
# config/sites/your-site/config.yaml
dependencies:
  - psbits/templates
```

## Content Elements

Nine content elements are provided via Content Blocks:

| Element          | Description                                                                                                                                                         |
|------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Hero**         | Full-width hero section with background image, text overlay, optional CTA link, and configurable text alignment.                                                    |
| **Slider**       | Image carousel (Swiper) with configurable pagination, navigation arrows, loop, and eager loading.                                                                   |
| **Logo Slider**  | Auto-playing logo carousel with grayscale option; ideal for partner/client showcases.                                                                               |
| **CTA**          | Call-to-action button with selectable Bootstrap color type (primary/secondary/etc.), size, and full-width option.                                                   |
| **Contact Lead** | Introductory section above a contact form with configurable success/error messages and emphasis style.                                                              |
| **FAQ**          | Collapsible FAQ section with an optional intro text and a collection of question/answer pairs.                                                                      |
| **Feature**      | Content section with image and bodytext; layout variants (image left/right/stacked) and background variants (default/light/dark). Optionally links to CTA elements. |
| **Teaser Grid**  | Grid of teaser cards with optional category-based filtering, rendered from child content elements.                                                                  |
| **Accordion**    | Expandable sections in flush or card style, built from child content elements.                                                                                      |

All content elements include German and English labels (`language/de.labels.xlf`, `language/labels.xlf`).

## Page Templates (Backend Layouts)

Sixteen backend layouts are registered in `Configuration/TSconfig/Page/Includes/BackendLayouts/`, each mapping to a Fluid template in `Resources/Private/Templates/Page/` and a layout in `Resources/Private/Layouts/Page/`:

| Layout                                 | Description                                                    |
|----------------------------------------|----------------------------------------------------------------|
| Blank                                  | Minimal page without header/footer — useful for landing pages. |
| OneColumn                              | Single full-width column.                                      |
| TwoColumns-1-1 / 1-2 / 2-1 / 1-3 / 3-1 | Two-column layouts with various width ratios.                  |
| OneColumnTwoColumns-1-1 / 1-2 / 2-1    | Full-width top row followed by two columns.                    |
| ThreeColumns-1-1-1 / 1-2-1             | Three equal or varied-width columns.                           |
| OneColumnThreeColumns-1-1-1 / 1-1-1    | Full-width top row followed by three columns.                  |
| OneColumnThreeColumns-1-2-1            | Full-width top row, centered middle column, two side columns.  |
| Grid-2x2                               | Four equal cells in a 2x2 grid.                                |

Content is delivered via `colPos` positions: `0` (content1), `1` (content2), and `100` (stage content, used by hero/slider).

## TypoScript Settings

Settings are configured in the TypoScript constants module under `plugin.tx_templates.settings`:

| Setting                          | Type   | Description                                    |
|----------------------------------|--------|------------------------------------------------|
| `alternativeWebsiteTitle`        | string | Alternate website title used in Fluid layouts. |
| `logos.default.file`             | string | Default logo image reference.                  |
| `logos.default.height`           | int    | Default logo height.                           |
| `logos.default.width`            | int    | Default logo width.                            |
| `logos.default.showWebsiteTitle` | bool   | Show the website title alongside the logo.     |
| `logos.favicon`                  | string | Favicon image reference.                       |
| `navigation.footer`              | int    | Page ID for the footer navigation.             |

The site set also provides crop variant configuration for `desktop`, `tablet`, and `mobile` (via `Configuration/Sets/Template/page.tsconfig`).

### SCSS Color Variables

Eight Bootstrap color variables are configurable via [ws-scss](https://github.com/wapplersystems/ws-scss) constants (`plugin.tx_wsscss.variables`): `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `dark`, `light`.

## Global Template Variables

Global variables are available in Fluid via the `templates` namespace:

- `templates:globalVariables.currentPage()` — the full current page record.
- `templates:globalVariables.settings()` — extension settings, including `headerButtons` (a collection of header button records resolved from the page rootline).

## ViewHelpers

### `templates:svgIcon`

Renders inline SVG from the TYPO3 icon registry, with optional custom dimensions and additional CSS classes.

```html
<templates:svgIcon identifier="content-info" width="24" height="24" />
```

### `templates:component.button`

Renders a `<button>` or `<a>` element with optional icon (via `svgIcon`), spinner text, and external-link handling. Supports Bootstrap button styling via CSS classes.

```html
<templates:component.button
    href="/contact"
    iconIdentifier="content-contact"
    label="Get in touch" />
```

### `templates:component.progressBar`

Renders a Bootstrap progress bar with automatic color coding (green/yellow/red) based on percentage. Accepts `minValue`, `maxValue`, and `value`.

### `templates:format.reduceWhitespace`

Trims and collapses whitespace in its child content — useful for minifying inline header HTML.

### `templates:data.countryNames`

Returns a country name by ISO 3166-1 alpha-2 code, or all country names when called without arguments.

### `templates:event.additionalContent`

Dispatches a PSR-14 event implementing `AdditionalContentEventInterface` and returns the resulting content string. Used to inject dynamic content into templates.

### `templates:page.header`

Adds arbitrary HTML to the TYPO3 page header, deduplicated by `identifier`. Supports inline content (`html` attribute) or child content.

### `templates:page.rowHighlightClass`

Returns a CSS class name based on the row highlight setting of the current page (for use in backend layout row styling).

### `templates:image.aiStatus`

Returns the `AiStatus` enum value (Created, Modified, or None) from a file reference's `tx_templates_ai_status` property.

### `templates:image.aspectRatio`

Computes and returns the aspect ratio (width / height) of a file reference, respecting crop variant settings.

## Extensibility

The extension provides a PSR-14 event system for additional content. Create a class implementing
`PSBits\Templates\Event\AdditionalContentEventInterface` (extend `AbstractAdditionalContentEvent` for convenience), register it in `Configuration/Services.yaml`, and dispatch it via the `templates:event.additionalContent` ViewHelper.

## Third-Party Extension Overrides

Fluid templates and partials for **News** (`georgringer/news`) and **Calendarize** (`lochmueller/calendarize`) are overridden in `Resources/Private/Extensions/` to apply consistent styling. Layout and partial paths are configured in `Configuration/TypoScript/Includes/Setup/extensions.typoscript`.

## Quality

All quality checks are defined in `composer.json` and run in CI via `.github/workflows/quality.yml`. Configuration is centralized in `Build/Quality/`.

```bash
# Read-only verification (all linters)
composer lint:all

# Auto-fix formatting issues
composer fix:all

# Individual checks
composer analyze:php
composer audit:composer
composer lint:classes
composer lint:htmlxml
composer lint:js
composer lint:php
composer lint:scss
composer lint:typoscript
composer lint:yaml
composer validate:composer
```

A pre-commit hook (from the psbits/qa package) is installed automatically via `.Build/bin/qa link` in `composer post-install-cmd` and runs `composer fix:all` followed by `composer lint:all`.

## License

GPL-3.0-or-later. See [LICENSE.txt](LICENSE.txt).
