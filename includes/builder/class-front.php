<?php
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}
Front::get_instance();

/**
 * Front-End Implementation
 * @since 1.0.0
 */
class Front {

    /**
     * Returns the instance.
     */
    public static function get_instance() {
        static $instance = null;
        if ( is_null( $instance ) ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {

        /* Filter content with FX Builder content. */
        add_filter( 'the_content', [ $this, 'content_filter' ], 1 );

        /* Enqueue Scripts */
        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 1 );

        /* Post Class */
        add_filter( 'post_class', [ $this, 'post_class' ], 10, 3 );
    }

    /**
     * Content Filter
     * This will format content with FX Builder data.
     */
    public function content_filter( $content ) {

        /* Check Post Support */
        $post_id   = get_the_ID();
        $post_type = get_post_type( $post_id );
        if ( ! post_type_supports( $post_type, 'fx_builder' ) ) {
            return $content;
        }

        $active = get_post_meta( $post_id, '_fxb_active', true );

        remove_filter( 'the_content', 'wpautop' );
        if ( $active ) {
            $content = Functions::content( $post_id ); // autop added in this function.
        } else {
            add_filter( 'the_content', 'wpautop' );
        }
        return $content;
    }

    /**
     * Front-End Scripts
     */
    public function scripts() {
        if ( apply_filters( 'fx_builder_css', true ) ) {
            wp_enqueue_style( 'fx-builder', URI . 'assets/front.css', [], VERSION );
            $medium = max( 320, min( 1920, absint( get_option( 'fxb_breakpoint_medium', 768 ) ) ) );
            $small  = max( 320, min( 1920, absint( get_option( 'fxb_breakpoint_small', 480 ) ) ) );
            $inline = sprintf(
                '@media screen and (max-width:%dpx){.fxb-container .fxb-row>.fxb-wrap{grid-template-columns:1fr 1fr}}',
                $medium
            ) . sprintf(
                '@media screen and (max-width:%dpx){.fxb-container .fxb-row>.fxb-wrap{grid-template-columns:1fr}}',
                $small
            );
            wp_add_inline_style( 'fx-builder', $inline );
        }
    }

    /**
     * Post Class
     */
    public function post_class( $classes, $class, $post_id ) {
        $post_type = get_post_type( $post_id );
        if ( ! post_type_supports( $post_type, 'fx_builder' ) ) {
            return $classes;
        }
        $active = get_post_meta( $post_id, '_fxb_active', true );
        if ( $active ) {
            $classes[] = 'fx-builder-entry';
        }
        return $classes;
    }
}
