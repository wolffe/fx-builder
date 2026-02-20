# FX Builder – Cursor rules

Use this file as ongoing rules for editing the FX Builder plugin.

## Codebase

- **Plugin type:** ClassicPress/WordPress plugin (page builder).
- **Main entry:** `fx-builder.php`. Loads `includes/setup.php`, `includes/editor.php`, `includes/settings/settings.php`; in admin also loads `includes/list-columns.php`.
- **Builder logic:** `includes/builder/` (namespace `fx_builder\builder`). Key files: `class-builder.php` (save, UI), `class-front.php` (front-end output and CSS), `class-functions.php` (content/rows/items), `class-sanitize.php`, `class-switcher.php`, `class-revisions.php`, `class-custom-css.php`, `class-tools.php`.
- **Settings:** `includes/settings/settings.php` (admin page and tabs), `includes/settings/setup.php` (post type support). Options: `fx-builder_post_types`, `fxb_breakpoint_small`, `fxb_breakpoint_medium`, typography options.
- **Data:** Builder data is stored in post meta: `_fxb_active`, `_fxb_row_ids`, `_fxb_rows`, `_fxb_items`. Flattened content is also written to `post_content` on save.
- **Text domain:** `fx-builder`. Use for all user-facing strings.

## Conventions

- **Functions:** Do not create functions you don’t need. If a function is only used once, put the code directly where it’s used instead of extracting it.
- **PHP:** No namespace for global plugin code (e.g. `includes/settings/settings.php`, `includes/list-columns.php`). Use `fx_builder\builder` namespace only inside `includes/builder/` where applicable.
- **Security:** Nonce and capability checks on all form saves and AJAX. Escape output (`esc_attr`, `esc_html`, `esc_url`). Sanitize input (e.g. `absint`, `sanitize_text_field`, plugin-specific sanitizers in `class-sanitize.php`).
- **i18n:** Use `esc_html_e()` / `esc_html__()` and `esc_attr_e()` / `esc_attr__()` for translatable strings.
- **Options:** Prefer `get_option` / `update_option` with a clear prefix (`fxb_`, `fx-builder_`). Document defaults where used (e.g. breakpoints 480/768 in Design tab and front CSS).
- **Front-end CSS:** Base and layout styles in `includes/builder/assets/front.css` (CSS Grid). Responsive `grid-template-columns` is injected in `Front::scripts()` from Design options (`fxb_breakpoint_medium`, `fxb_breakpoint_small`).
- **Admin UI:** Tabs: Dashboard, Settings, Design. Design holds Responsive Breakpoints (small default 480, medium default 768, large readonly = medium + 1).

## Responsive breakpoints

- Stored: `fxb_breakpoint_small` (default 480), `fxb_breakpoint_medium` (default 768). Large is not stored; it is “above medium” and shown as readonly (medium + 1). Values are clamped 320–1920. Logic is inlined where needed (Design tab); no separate helper functions.
- Front-end layout: CSS Grid (`.fxb-row > .fxb-wrap`). Layouts use `grid-template-columns` (1fr, 1fr 1fr, 1fr 2fr, etc.) in `front.css`. Responsive: `Front::scripts()` injects two `@media` blocks from Design options—medium sets `grid-template-columns: 1fr 1fr`, small sets `1fr`. No column width hacks.

## When editing

- Preserve backward compatibility for existing post meta and options.
- Run PHPCS/CPCS if the project uses them; keep list-columns and new code consistent with existing style.
- Do not remove or repurpose existing options/keys without a migration path.
