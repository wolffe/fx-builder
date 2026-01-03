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

            $gap      = (string) get_option( 'fxb_templates_gap', '2' );
            $gap_unit = (string) get_option( 'fxb_templates_gap_unit', 'em' );
            $debug    = (string) get_option( 'fxb_templates_debug', '0' );

            $gap = preg_replace( '/[^0-9.]/', '', $gap );
            $gap_unit = in_array( $gap_unit, [ 'px', 'em', 'rem' ], true ) ? $gap_unit : 'em';
            $gap_value = $gap !== '' ? $gap . $gap_unit : '';

            $css = '';
            if ( $gap_value !== '' ) {
                $css .= '.fxb-container{--fxb-template-gap:' . esc_attr( $gap_value ) . ';}';
            }
            if ( $debug === '1' ) {
                $css .= '.fxb-container .fxb-row{outline:1px dotted #1d4ed8;outline-offset:-1px;}';
                $css .= '.fxb-container .fxb-col{outline:1px dotted #dc2626;outline-offset:-1px;}';
            }
            if ( $css !== '' ) {
                wp_add_inline_style( 'fx-builder', $css );
            }
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
