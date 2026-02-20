# FX Builder 1.6.0 — CSS Grid Layout System

## Overview

Version 1.6.0 introduces a **CSS Grid–based layout system** for all FX Builder rows and columns. The front-end no longer uses Flexbox for the main grid; instead, each row is a Grid container whose columns are defined by simple, predictable template rules. This improves reliability (no more 50% wrapping issues), keeps the markup the same, and makes responsive behavior easier to control from **Design → Responsive Breakpoints**.

---

## How the Grid System Works

### The Grid Container

Every FX Builder row uses a **single grid container**: the inner wrapper (`.fxb-row > .fxb-wrap`) that holds the columns. That wrapper has:

- **`display: grid`** — so all direct children (the columns) participate in the grid.
- **`gap`** — spacing between columns (and rows when they wrap). The value comes from your row settings (e.g. “Column gap”) and falls back to `2em` via `var(--fxb-template-gap, 2em)`.

Columns don’t have fixed widths anymore. Their size is determined entirely by **`grid-template-columns`** on the container.

### Layout Templates

Each row layout (full width, ½–½, ⅓–⅔, etc.) is implemented as a **grid template**: a list of column track sizes. FX Builder uses the `fr` unit so columns share space in proportion without percentage or flex hacks.

| Layout            | Grid template              | Effect                    |
|-------------------|----------------------------|---------------------------|
| Full width        | `1fr`                      | One column, 100% width    |
| ½ – ½             | `1fr 1fr`                  | Two equal columns         |
| ⅓ – ⅔             | `1fr 2fr`                  | One narrow, one wide      |
| ⅔ – ⅓             | `2fr 1fr`                  | One wide, one narrow      |
| ⅓ – ⅓ – ⅓         | `1fr 1fr 1fr`              | Three equal columns       |
| ¼ – ¼ – ¼ – ¼     | `1fr 1fr 1fr 1fr`          | Four equal columns        |
| ⅕ – ⅕ – ⅕ – ⅕ – ⅕ | `1fr 1fr 1fr 1fr 1fr`      | Five equal columns        |

The template is applied to the row’s wrap using the existing layout class (e.g. `.fxb-row-layout-12_12 > .fxb-wrap`). No per-column width rules are needed.

### Responsive Breakpoints (Design Options)

Responsive behavior is driven by **Design → Responsive Breakpoints** in the FX Builder settings. The breakpoint values (e.g. **Medium** 768px, **Small** 480px) are stored as options and injected into the page as two media queries that **override only the grid template**:

1. **At or below the Medium breakpoint**  
   All rows use **`grid-template-columns: 1fr 1fr`**.  
   So every row shows at most two equal columns (50% / 50%); rows with more than two columns wrap into multiple rows.

2. **At or below the Small breakpoint**  
   All rows use **`grid-template-columns: 1fr`**.  
   So every row stacks into a single column (100% width).

No pixel values are hardcoded in the main stylesheet; the actual breakpoint numbers come from your Design settings. That keeps behavior consistent with what you configure and makes it easy to tune “medium” and “small” per site.

### Why Grid Instead of Flex?

- **No 50% wrap issues** — With Flexbox, “50% + 50% + gap” could exceed 100% and force wrapping. Grid’s `gap` is accounted for in the track sizing, so two `1fr` columns stay on one row.
- **Simpler responsive rules** — Changing one property (`grid-template-columns`) on the container is enough; no long selector lists or width hacks.
- **Cleaner code** — Layout intent is expressed as a single template per layout and two media overrides, which is easier to maintain and to document.

---

## For Theme and Plugin Developers

- **Row container:** `.fxb-container .fxb-row > .fxb-wrap`  
  This is the grid container. Custom CSS that targeted the old flex row can target this instead; `align-items` and `gap` (or equivalent) in inline styles still apply.
- **Columns:** `.fxb-row .fxb-col` (and `.fxb-col-1`, `.fxb-col-2`, …)  
  Columns remain grid items. They use `box-sizing: border-box`, `min-width: 0` (to avoid overflow), and your row’s padding variable. No width is set in the base styles.
- **Breakpoints** — Responsive rules are added inline by FX Builder using `get_option('fxb_breakpoint_medium', 768)` and `get_option('fxb_breakpoint_small', 480)`. To change when the layout switches, use **FX Builder → Design → Responsive Breakpoints**.

---

## Summary

In 1.6.0, FX Builder’s front-end layout is **CSS Grid**: one grid container per row, `grid-template-columns` for each layout, and two responsive overrides (medium → two columns, small → one column) driven by your Design breakpoint settings. The same row/column structure and options you already use are unchanged; only the underlying layout mechanism has moved from Flexbox to Grid for more predictable, maintainable behavior.
