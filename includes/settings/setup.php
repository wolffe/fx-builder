<?php
/**
 * Enable FX Builder for Post Types
 */
function fxb_add_builder_support() {
    // Saved post types that still exist, or 'page' when nothing is saved.
    $post_types = get_option( 'fx-builder_post_types' );
    $post_types = is_array( $post_types ) && $post_types ? array_filter( $post_types, 'post_type_exists' ) : [ 'page' ];

    foreach ( $post_types as $pt ) {
        add_post_type_support( $pt, 'fx_builder' );
    }
}

add_action( 'init', 'fxb_add_builder_support' );
