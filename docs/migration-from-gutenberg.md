---
purpose: "Convert a WordPress (Gutenberg) page into FX Builder data for a ClassicPress site."
output_format: "FX Builder Tools Import JSON (paste into FX Builder → Tools → Import)."
fx_builder_version: ">= 1.6.0"
allowed_layouts: ["1", "12_12", "13_23", "23_13", "13_13_13", "14_14_14_14", "15_15_15_15_15"]
allowed_height_units: ["px", "%", "em", "rem", "vh"]
allowed_gap_units: ["px", "%", "em", "rem", "vw"]
allowed_padding_units: ["px", "%", "em", "rem"]
allowed_row_widths: ["default", "fullwidth"]
allowed_column_align: ["start", "center", "end"]
fx_shortcodes_required: true
---

# FX Builder — Migration Reference (Gutenberg → ClassicPress)

This document is **context for an AI assistant**. Read it cold and follow it to convert a
WordPress page (Gutenberg block markup) into an FX Builder data payload that can be
imported into a ClassicPress site running the FX Builder plugin.

## How to use this document

You'll be shown the source of an existing WordPress page — usually one of:

- Raw Gutenberg HTML (the `post_content` of the source page, containing `<!-- wp:... -->`
  comments interleaved with HTML), or
- Rendered HTML scraped from a live URL.

Produce **one JSON document** in the *Tools Import* shape described under
[Output format](#output-format). The user will paste that JSON into FX Builder's
**Tools → Import** modal.

Do not produce PHP, REST calls, SQL, or any other artifact. Just the JSON.

## What FX Builder is, in one paragraph

FX Builder is a simple ClassicPress page builder. A page is a sequence of **rows**.
Each row picks a **layout** (number and proportion of columns: 1, 1/2-1/2, 1/3-2/3,
2/3-1/3, 1/3-1/3-1/3, 1/4-1/4-1/4-1/4, or 1/5×5). Each column holds an ordered list
of **items**. Items currently only have one type: `text`, whose `content` is a string
of HTML (sanitized via `wp_kses_post`). There is no first-class image/quote/heading
block — those live inside an item's HTML content. Structure comes from rows + layouts;
formatting comes from inline HTML.

Companion plugin **FX Shortcodes** provides a single nestable `[element type="..."]`
shortcode for richer blocks (`cover`, `button`, `card`, `columns`, `details`, `sticky`,
etc.). Use it inside text item content when a Gutenberg block has no direct FX Builder
equivalent. See [Block mapping](#block-mapping).

## Output format

Emit a JSON object with exactly three keys:

```json
{
  "row_ids": "<comma-separated ordered list of row ids>",
  "rows":    { "<row_id>": { ...row fields... }, ... },
  "items":   { "<item_id>": { ...item fields... }, ... }
}
```

- `row_ids` is the **order** of rows on the page. The order of keys inside `rows`
  is not authoritative — only `row_ids` is.
- Inside each row, `col_1`, `col_2`, ... carry comma-separated **item id** lists
  defining the order of items within that column.
- Every id referenced from `row_ids` must exist as a key in `rows`. Every id
  referenced from any `col_N` must exist as a key in `items` (otherwise the
  import is silently dropped).

IDs can be any unique strings. FX Builder itself uses millisecond Unix timestamps
(e.g. `"1748351234567"`). For migration, sequential ids like `"r1"`, `"r2"`, `"i1"`,
`"i2"` are equally valid and easier to read — pick a scheme and stay consistent.

### Row object schema

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `id` | string | (the key) | Same as the parent map key. |
| `index` | string | `""` | Render order hint; safe to leave empty. |
| `state` | string | `"open"` | `"open"` (expanded in editor) or `"close"`. Use `"open"`. |
| `layout` | string | `"1"` | One of the seven layout codes. See [Layouts](#layouts). |
| `col_num` | string | derived | The builder derives this from `layout`; emit it anyway. |
| `col_1` | string | `""` | Comma-separated item ids in the first column. |
| `col_2` | string | `""` | Second column (only if layout has ≥2 columns). |
| `col_3` | string | `""` | Third column (only if layout has ≥3 columns). |
| `col_4` | string | `""` | Fourth column (only if layout has ≥4 columns). |
| `col_5` | string | `""` | Fifth column (only if layout is `15_15_15_15_15`). |
| `row_title` | string | `""` | Editor-only label, not rendered on front end. |
| `row_html_width` | string | `"default"` | `"default"` keeps row inside content width; `"fullwidth"` stretches to viewport. |
| `row_html_height` | string | `""` | Numeric value of fixed row height (no unit), or empty for auto. |
| `row_html_height_unit` | string | `"px"` | One of `px`, `%`, `em`, `rem`, `vh`. |
| `row_html_id` | string | `""` | Optional HTML id on the row (`#fxb-row-<id>` is generated if empty). |
| `row_html_class` | string | `""` | Space-separated extra CSS classes. |
| `row_column_align` | string | `"start"` | Vertical alignment of columns: `start`, `center`, `end`. |
| `row_column_gap` | string | `""` | Numeric gap between columns, paired with the unit below. Empty = use global default. |
| `row_column_gap_unit` | string | `""` | `px`, `%`, `em`, `rem`, `vw`. |
| `row_bg_color` | string | `""` | Hex color (e.g. `"#f5f5f5"`) for the row background. |
| `row_col_padding` | string | `""` | Numeric inside-column padding (applies to every column). |
| `row_col_padding_unit` | string | `"px"` | `px`, `%`, `em`, `rem`. |

### Item object schema

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `item_id` | string | (the key) | Same as the parent map key. |
| `item_index` | string | `""` | Safe to leave empty. |
| `item_state` | string | `"open"` | Always `"open"` for migration output. |
| `item_type` | string | `"text"` | Only `"text"` is supported. |
| `row_id` | string | — | The id of the row this item belongs to. |
| `col_index` | string | `"col_1"` | Which column inside the row: `col_1` .. `col_5`. |
| `content` | string | `""` | HTML content. Anything `wp_kses_post` accepts: `<p>`, `<h2>`, `<a>`, `<img>`, `<figure>`, `<blockquote>`, `<ul>`/`<ol>`, etc. Shortcodes are allowed. |

### Layouts

| `layout` code | Columns | Proportions |
|--------------|---------|-------------|
| `1` | 1 | 1 |
| `12_12` | 2 | 1/2 — 1/2 |
| `13_23` | 2 | 1/3 — 2/3 |
| `23_13` | 2 | 2/3 — 1/3 |
| `13_13_13` | 3 | 1/3 — 1/3 — 1/3 |
| `14_14_14_14` | 4 | 1/4 × 4 |
| `15_15_15_15_15` | 5 | 1/5 × 5 |

Any other value will be silently coerced to `"1"` (single column) by the sanitizer.

## Block mapping

| Gutenberg block | FX Builder approach | Notes |
|-----------------|---------------------|-------|
| `core/paragraph` | one text item per paragraph; `content = "<p>...</p>"` | Or combine consecutive paragraphs into a single item if they were one logical block. |
| `core/heading` | text item; `content = "<h2>...</h2>"` (or `h1`–`h6` as in source) | Preserve `id` if the source had a `name`/`id` attribute. |
| `core/list` | text item; `content = "<ul>...</ul>"` or `<ol>` | Inline list-item content as-is. |
| `core/quote` | text item; `content = "<blockquote>...</blockquote>"` | Preserve `<cite>` if present. |
| `core/image` | text item; `content = "<figure><img src=... alt=... /><figcaption>...</figcaption></figure>"` | Preserve `alt` and caption. |
| `core/gallery` | text item; emit a `<figure class="wp-block-gallery">` with inner `<figure>` per image | FX Builder doesn't render WP block CSS — accept a degraded look or use FX Shortcodes' `[element type="columns"]`. |
| `core/separator` | text item; `content = "<hr />"` | — |
| `core/spacer` | text item; `content = "[element type=\"spacer\" height=\"<source_height>\" /]"` | Requires FX Shortcodes. Otherwise omit. |
| `core/html` | text item; `content = <inner HTML>` | Pass through verbatim. |
| `core/columns` | **new row** with layout chosen from the inner column count (see below) | One row, one column-per-source-column. The inner-block content of each WP column becomes the items of the matching FX Builder column. |
| `core/column` | column inside the parent `core/columns` row | Not a row of its own. |
| `core/group` | usually a **new row** with layout `1` | Items derived from the group's inner blocks. If the group has a background color or padding, set `row_bg_color` / `row_col_padding` (+ unit) on the row. |
| `core/cover` | new row, layout `1`, single text item; `content = "[element type=\"cover\" background=\"<image>\" overlay=\"<color>\" overlay-opacity=\"<0..1>\"]...inner HTML...[/element]"` | Requires FX Shortcodes. The cover's inner content becomes the inner HTML. |
| `core/buttons` | text item; `content = "[element type=\"button\" url=\"...\"]Label[/element]"` (one per button) | Requires FX Shortcodes. |
| `core/button` | as above, alone | — |
| `core/embed` | text item; `content = the embed URL on its own line` | WordPress oEmbed will resolve it on the front end. For YouTube/Vimeo, the raw URL alone is enough. |
| `core/video` / `core/audio` | text item; `content = "<video controls src=...></video>"` / `<audio>` | — |
| `core/table` | text item; `content = "<table>...</table>"` | — |
| `core/code` / `core/preformatted` | text item; `content = "<pre><code>...</code></pre>"` | — |
| **Unknown / custom block** | text item; `content = inner HTML, stripped of `<!-- wp:... -->` comments` | Best-effort fallback. |

### Mapping `core/columns` to a layout

Inspect the **count and widths** of the child `core/column` blocks:

- 1 column → `layout: "1"`
- 2 columns of equal width (or no widths declared) → `"12_12"`
- 2 columns where the first is ~33% → `"13_23"`
- 2 columns where the first is ~66% → `"23_13"`
- 3 columns → `"13_13_13"` (FX Builder doesn't support arbitrary 3-column widths; coerce to equal thirds)
- 4 columns → `"14_14_14_14"`
- 5 columns → `"15_15_15_15_15"`
- More than 5 columns → split into multiple rows, or pick the closest layout and append overflow as additional rows.

For non-supported proportions (e.g. 70/30, 1/4-3/4), pick the closest supported
layout and note the approximation in your migration summary if you produce one.

### Choosing where to break rows

Each top-level Gutenberg block creates one FX Builder row — unless consecutive
paragraphs / lists / images are clearly part of the same prose block, in which case
they can be grouped into a single row with multiple items. Rule of thumb: every
`<!-- wp:columns -->`, `<!-- wp:group -->`, `<!-- wp:cover -->` boundary is a new
row.

## Worked example

### Input (Gutenberg post_content)

```html
<!-- wp:heading -->
<h2 id="intro">Welcome to our site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We build great pages. Here's why we're different.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns">
  <!-- wp:column -->
  <div class="wp-block-column">
    <!-- wp:heading {"level":3} -->
    <h3>Fast</h3>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Loads instantly.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:column -->
  <!-- wp:column -->
  <div class="wp-block-column">
    <!-- wp:heading {"level":3} -->
    <h3>Reliable</h3>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Battle-tested.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:image {"id":42} -->
<figure class="wp-block-image"><img src="https://example.com/photo.jpg" alt="A photo" /></figure>
<!-- /wp:image -->
```

### Output (Tools Import JSON)

```json
{
  "row_ids": "r1,r2,r3",
  "rows": {
    "r1": {
      "id": "r1",
      "index": "",
      "state": "open",
      "layout": "1",
      "col_num": "1",
      "col_1": "i1,i2",
      "row_title": "Intro",
      "row_html_width": "default",
      "row_column_align": "start"
    },
    "r2": {
      "id": "r2",
      "index": "",
      "state": "open",
      "layout": "12_12",
      "col_num": "2",
      "col_1": "i3,i4",
      "col_2": "i5,i6",
      "row_title": "Features",
      "row_html_width": "default",
      "row_column_align": "start"
    },
    "r3": {
      "id": "r3",
      "index": "",
      "state": "open",
      "layout": "1",
      "col_num": "1",
      "col_1": "i7",
      "row_title": "Photo",
      "row_html_width": "default",
      "row_column_align": "start"
    }
  },
  "items": {
    "i1": {
      "item_id": "i1",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r1",
      "col_index": "col_1",
      "content": "<h2 id=\"intro\">Welcome to our site</h2>"
    },
    "i2": {
      "item_id": "i2",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r1",
      "col_index": "col_1",
      "content": "<p>We build great pages. Here's why we're different.</p>"
    },
    "i3": {
      "item_id": "i3",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r2",
      "col_index": "col_1",
      "content": "<h3>Fast</h3>"
    },
    "i4": {
      "item_id": "i4",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r2",
      "col_index": "col_1",
      "content": "<p>Loads instantly.</p>"
    },
    "i5": {
      "item_id": "i5",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r2",
      "col_index": "col_2",
      "content": "<h3>Reliable</h3>"
    },
    "i6": {
      "item_id": "i6",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r2",
      "col_index": "col_2",
      "content": "<p>Battle-tested.</p>"
    },
    "i7": {
      "item_id": "i7",
      "item_state": "open",
      "item_type": "text",
      "row_id": "r3",
      "col_index": "col_1",
      "content": "<figure><img src=\"https://example.com/photo.jpg\" alt=\"A photo\" /></figure>"
    }
  }
}
```

Notes on this example:

- The two intro blocks (heading + paragraph) became **two items in a single row**
  rather than two rows. Use judgment: a sequence of prose blocks belongs together.
- The `core/columns` block became one row with `layout: "12_12"`. Each WP column's
  inner blocks turned into multiple items inside that FX Builder column.
- The `core/image` became its own row, layout `1`. The `<img>` lives inside a
  `<figure>` in the item's HTML content.
- `row_title` is editor-only; it's nice for debugging but not required.

## Limitations and fallbacks

- **No first-class image, gallery, embed, or button blocks.** Everything is text
  HTML inside an item. When the source uses `core/cover`, `core/buttons`,
  `core/columns` inside a row, etc., prefer **FX Shortcodes** (`[element type=...]`)
  for the inner content, falling back to inline HTML.
- **Column widths are quantized** to the seven supported layouts. Custom widths
  (e.g. 70/30) collapse to the nearest supported layout.
- **Reusable blocks / patterns**: expand the source post first; emit the resolved
  HTML.
- **Theme-specific block styles** (e.g. theme.json colors, block variations) won't
  follow. Translate to inline `style="..."` or to CSS classes the destination theme
  understands.
- **Cover/sticky from FX Builder ≤ 1.6.0**: as of FX Builder 1.7.0, the old
  `[cover]` and `[sticky]` shortcodes have moved to FX Shortcodes. Existing posts
  using `[cover ...]` will not render; migrate to `[element type="cover" ...]`.
  `[sticky]` keeps working via a back-compat shim in FX Shortcodes.

## Tips for the assistant

- **Strip `<!-- wp:... -->` comments** from any HTML you place into item `content`.
  They're harmless but visually noisy in the editor and not needed at render time.
- **Don't escape HTML in `content`**. The field is sanitized server-side with
  `wp_kses_post`, which expects raw HTML, not entity-encoded text.
- **Quote JSON values carefully**. Inside JSON strings, `"` must be `\"` and `\`
  must be `\\`. Newlines inside HTML content can stay as actual `\n` characters
  (JSON allows them) or be escaped — either works.
- **Use stable, sequential ids** (`r1`, `r2`, ..., `i1`, `i2`, ...) when generating
  a fresh migration. Don't reuse ids across rows and items.
- **Verify the output** before handing it over: every id in `row_ids` should be a
  key in `rows`; every id in any `col_N` should be a key in `items`; every item's
  `row_id` should match the row it belongs to and its `col_index` should match
  which `col_N` actually lists it.
- **Don't invent fields**. The sanitizer keeps unknown keys but they have no effect.
  Stick to the schemas above.

## Where this lives

This document is part of the FX Builder repository at
`docs/migration-from-gutenberg.md`. Paste it into an AI assistant's system context
(or attach as a project file) before asking for a Gutenberg → FX Builder migration.
