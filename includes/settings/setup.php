<?php
/**
 * Setup Settings NameSpace
 * @since 1.0.0
 **/
namespace fx_builder\settings;

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( __NAMESPACE__ . '\URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( __NAMESPACE__ . '\PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( __NAMESPACE__ . '\VERSION', $version );

add_action( 'init', 'fxb_add_builder_support' );

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
