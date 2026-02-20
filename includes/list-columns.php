<?php
/**
 * FX Builder list table column for post types that support the builder.
 * Shows whether a post was built with FX Builder (like Elementor).
 *
 * @since 1.5.1
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Register FX Builder column for supported post types.
 */
function fxb_list_columns_register() {
    $post_types = get_option( 'fx-builder_post_types' );
    if ( ! is_array( $post_types ) || empty( $post_types ) ) {
        $post_types = [ 'page' ];
    } else {
        $post_types = fxb_check_post_types_exists( $post_types );
    }

    foreach ( $post_types as $pt ) {
        add_filter( "manage_edit-{$pt}_columns", 'fxb_list_column_header' );
        add_action( "manage_{$pt}_posts_custom_column", 'fxb_list_column_content', 10, 2 );
    }
}

add_action( 'init', 'fxb_list_columns_register', 20 );

/**
 * Add FX Builder column header to the list table.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function fxb_list_column_header( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['fxb_builder'] = __( 'FX Builder', 'fx-builder' );
        }
    }
    if ( ! isset( $new['fxb_builder'] ) ) {
        $new['fxb_builder'] = __( 'FX Builder', 'fx-builder' );
    }
    return $new;
}

/**
 * Output FX Builder column content.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function fxb_list_column_content( $column, $post_id ) {
    if ( $column !== 'fxb_builder' ) {
        return;
    }

    $active = get_post_meta( $post_id, '_fxb_active', true );
    if ( $active ) {
        $edit_url = get_edit_post_link( $post_id, 'raw' );
        printf(
            '<a href="%s" class="fxb-column-badge" title="%s" aria-label="%s"><span class="dashicons dashicons-block-default" style="font-size:18px;width:18px;height:18px;vertical-align:middle;"></span> %s</a>',
            esc_url( $edit_url ),
            esc_attr__( 'Edit with FX Builder', 'fx-builder' ),
            esc_attr__( 'Edit with FX Builder', 'fx-builder' ),
            esc_html__( 'FX Builder', 'fx-builder' )
        );
    } else {
        echo '<span class="fxb-column-empty" aria-hidden="true">—</span>';
    }
}
