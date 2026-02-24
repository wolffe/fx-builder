# FX Builder 1.6.0 Release Notes

## Overview

Version 1.6.0 adds a **CSS Grid layout system**, a **Design** tab with configurable **Responsive Breakpoints**, an **FX Builder column** on post list screens, and several compatibility and UX improvements. The front-end no longer uses Flexbox for the main grid; each row is a Grid container whose columns are defined by template rules. Breakpoints are configurable from **FX Builder → Design → Responsive Breakpoints**, with no hardcoded pixel values in the stylesheet.

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

## Design Tab and Responsive Breakpoints

- A new **Design** tab in FX Builder settings hosts **Responsive Breakpoints**.
- **Small Screen** (default 480 px) and **Medium Screen** (default 768 px) set when the layout switches to two columns and then one column. **Large Screen** is read-only and shows Medium + 1.
- Each breakpoint has a **range slider** and a number input; they stay in sync. If you drag Small past Medium, Medium (and thus Large) increases so the order stays Small < Medium < Large. If you lower Medium below Small, Small is reduced to Medium − 1.
- A short **“Frequently used widths”** reference list (480, 768, 1024, 1280, 1920 px) with typical device contexts is shown below the fields.

---

## Other Changes in 1.6.0

**List column**
- On the Posts/Pages (and other supported post types) list screen, an **FX Builder** column indicates which items were built with FX Builder (icon + label, linking to edit). Similar to Elementor’s list column.

**Update URI and ClassicPress**
- The plugin header now supports **Update URI** (e.g. `https://getbutterfly.com`) so ClassicPress and custom updaters can use the correct update source.
- The updater normalizes API responses: if the update server returns `requires` but not `requires_cp`, the plugin sets `requires_cp` from that value so ClassicPress can show compatibility correctly.

**Modals**
- **Tools** and **Custom CSS** modals: the **Cancel** button now closes the modal (previously only “Save & Close” did).

---

## Summary

In 1.6.0, the front-end layout uses **CSS Grid**: one grid container per row, `grid-template-columns` per layout, and two responsive overrides (medium → two columns, small → one column) from Design breakpoint options. The **Design** tab lets you set and link Small/Medium breakpoints with sliders and a device reference. A new **FX Builder** column appears on post list screens, and Update URI plus `requires_cp` normalization improve ClassicPress and custom update behavior. Cancel on Tools and Custom CSS modals now closes the modal.
