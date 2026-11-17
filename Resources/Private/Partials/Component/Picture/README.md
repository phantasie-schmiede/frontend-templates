# Responsive pictures

Render `Component/Picture/Responsive` with a TYPO3 file or file reference in `file`.
The component emits AVIF and WebP sources and a regular image fallback. Candidate
widths are limited to the crop's available pixels; `srcset` descriptors use the
processed files' actual dimensions.

## Arguments

- `alt`: omitted/null uses the FAL alternative text; `''` explicitly marks a
  decorative image. A non-empty value overrides FAL. Do not use article titles
  as a substitute for descriptive alternative text.
- `title`: optional image title (same null/empty override behaviour).
- `imageClass`, `figureClass`: presentation classes. Fluid's `img-fluid` is the
  default image class; custom classes must provide responsive sizing themselves.
- `cropVariants`: ordered map of crop identifiers to `maxWidth` and `mediaQuery`.
  Defaults are `mobile`, `tablet`, and `desktop`.
- `sizes`: map with a sizes expression for each crop identifier. Defaults to
  `100vw`; cards and constrained layouts should always supply their slot sizes.
- `widths`: map with candidate widths for each crop identifier. Supply widths
  appropriate to the component, including higher-density displays.
- `fallbackCropVariant`, `fallbackWidth`: independent fallback selection;
  defaults to `desktop` and a maximum width of 1600 pixels. Specify a fallback
  crop when using custom crop identifiers.
- `loading`, `decoding`, `fetchpriority`: default to `lazy`, `async`, and `auto`.
  Use `eager` and `high` only for the page's likely LCP image.
- `metaPosition`, `metaAlignment`: image credits / AI status below the image or
  overlaid, aligned to `start` or `end`.

`Component/Picture/Preload` uses the same `file`, `cropVariants`, `sizes`, and
`widths` arguments and renders AVIF preload links through the shared `Sources`
partial. Include it only for the likely LCP image and pass the same selection
arguments as to `Responsive`. Browsers without AVIF support use the regular
picture selection without that preload.

`Component/Card` forwards `imageCropVariants`, `imageSizes`, and `imageWidths`
to the picture component. Its `alternativeText` follows the same alt semantics.

News layout rules are scoped to `.news` / `.news-card`; they do not change the
Calendarize card arrangement. News heading levels can be set using
`plugin.tx_news.settings.list.headerLevel` (default: 3).
