<?php
/**
 * Enable FX Builder for Post Types
 */
function fxb_add_builder_support() {
    // Get post types from option or default to 'page'
    $post_types = get_option( 'fx-builder_post_types' );

    if ( ! is_array( $post_types ) || empty( $post_types ) ) {
        $post_types = [ 'page' ];
    } else {
        $post_types = fxb_check_post_types_exists( $post_types );
    }

    // Add support for each post type
    foreach ( $post_types as $pt ) {
        add_post_type_support( $pt, 'fx_builder' );
    }
}

/**
 * Sanitize and Check Post Types
 * @param array $input Post types to check.
 * @return array Valid post types.
 * @since 1.0.0
 */
function fxb_check_post_types_exists( $input ) {
    $input = is_array( $input ) ? $input : [];

    return array_filter( $input, 'post_type_exists' );
}

add_action( 'init', 'fxb_add_builder_support' );


/**
 * Enqueue Tom Select on the FX Builder Settings Page
 * Used to transform the massive font multiselects into searchable tags.
 */
function fxb_settings_page_scripts( $hook ) {
    if ( strpos( $hook, 'fx-builder' ) === false && strpos( $hook, 'fx_builder' ) === false ) {
        return;
    }
    wp_enqueue_style( 'tom-select-css', plugins_url( 'includes/builder/assets/css/tom-select.default.min.css', FX_BUILDER_FILE ), [], '2.3.1' );
    wp_enqueue_script( 'tom-select-js', plugins_url( 'includes/builder/assets/js/tom-select.complete.min.js', FX_BUILDER_FILE ), [], '2.3.1', true );
}
add_action( 'admin_enqueue_scripts', 'fxb_settings_page_scripts' );