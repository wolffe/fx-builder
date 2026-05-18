<?php
/**
 * Build a fonts CSS URL from a saved option list.
 */
function fxb_build_fonts_url( $option_key, $base_url, $separator, $font_format ) {
    $fonts = array_map( 'sanitize_text_field', (array) get_option( $option_key ) );
    $fonts = array_filter(
        $fonts,
        static function ( $v ) {
            $v = (string) $v;
            return $v !== '' && $v !== '0';
        }
    );
    if ( empty( $fonts ) ) {
        return '';
    }

    $parts = array_map(
        static function ( $font ) use ( $font_format ) {
            return sprintf( $font_format, $font );
        },
        $fonts
    );
    $parts = array_unique( str_replace( ' ', '+', $parts ) );

    return $base_url . implode( $separator, $parts ) . '&display=swap';
}

/**
 * Build Google fonts URL.
 */
function fxb_google_fonts() {
    return fxb_build_fonts_url(
        'fxb_google_fonts',
        'https://fonts.googleapis.com/css2?',
        '&',
        'family=%s:wght@300;400;500;700'
    );
}

/**
 * Build Bunny fonts URL.
 */
function fxb_bunny_fonts() {
    return fxb_build_fonts_url(
        'fxb_bunny_fonts',
        'https://fonts.bunny.net/css?family=',
        '|',
        '%s:300,400,500,700'
    );
}

function fxb_enqueue() {
    if ( is_admin() ) {
        wp_enqueue_style( 'fxb-admin-ui', plugins_url( 'includes/builder/assets/fxb-admin-ui.css', FX_BUILDER_FILE ), [], FX_BUILDER_VERSION );
    }

    $google_url = fxb_google_fonts();
    if ( $google_url !== '' ) {
        wp_enqueue_style( 'fxb-google-fonts', $google_url, [], FX_BUILDER_VERSION );
    }

    $bunny_url = fxb_bunny_fonts();
    if ( $bunny_url !== '' ) {
        wp_enqueue_style( 'fxb-bunny-fonts', $bunny_url, [], FX_BUILDER_VERSION );
    }
}

add_action( 'wp_enqueue_scripts', 'fxb_enqueue' );
add_action( 'admin_enqueue_scripts', 'fxb_enqueue' );
