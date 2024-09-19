# FX Builder

## How to Disable Front End CSS

To disable front-end CSS, you can use this code:

```
add_filter( 'fx_builder_css', '__return_false' );
```

## How to Disable Settings

This option will be removed in a future version.

### Disable Settings

To disable settings you can use this code:

```
add_filter( 'fx_builder_settings', '__return_false' );
```

## Add support for "fx_builder" to post type manually:

If you disable settings, you need to enable FX Builder for each post type by yourself, by adding support for it. Here's how to add FX Builder to a "property" post type:

```
add_action(
    'init',
    function() {
       add_post_type_support( 'property', 'fx_builder' );
    }
);
```

## Meta Keys

### List of Post Meta Keys in FX Builder

`_fxb_db_version`

Database version. This is simply saving plugin version so we can do stuff in the future when needed.

`_fxb_active`

This is switcher status. "1" if active null if not active.

`_fxb_custom_css`

Custom CSS for the page. This CSS only loaded in singular pages

`_fxb_custom_css_disable`

If true, css is not loaded in front end. Useful to disable CSS without removing the CSS.

`_fxb_row_ids`

Comma separated of row IDs. Row IDs are creation timestamps.

`_fxb_rows`

This is a multidimentional array with all rows data the data will look like this:

```
_fxb_rows = [
	{row id} => [
		'id'             => the time stamp when row created.
		'index'          => row index order
		'state'          => open/close
		'col_num'        => number of column active (1-4)
		'layout'         => settings: layout (e.g 1/2 -1/2)
		'col_order'      => collapse order "l2r" (left to right) or "r2l" (right to left)
		'col_1'          => comma separated item ids in col 1
		'col_2'          => comma separated item ids in col 2
		'col_3'          => comma separated item ids in col 3
		'col_4'          => comma separated item ids in col 4
		'row_title'      => row title/admin label
		'row_html_id'    => front end element html id
		'row_html_class' => front end element html classes
    ]
	{other row id} => ....
]
```

`_fxb_items`

This is a multidimentional array with all data of an item the data will look like this:

```
_fxb_items = [
	{item_id} => [
		'item_id'      => {the time stamp when item created}
		'item_index'   => the order of an item.
		'item_state'   => "open" or "close"
		'item_type'    => "text" (currently only have one type)
		'row_id'       => the ID of the row
		'col_index'    => 1-4. the column index. 1st - 4th.
		'content'      => content of the item.
    ]
	{other item id} => ....
]
```

# Dev

Uses https://github.com/ClassicPress/dev-workflows
