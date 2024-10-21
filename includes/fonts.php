<?php
/**
 * Build Google fonts string
 */
function fxb_google_fonts() {
    $font_array       = [];
    $fxb_google_fonts = array_map( 'sanitize_text_field', (array) get_option( 'fxb_google_fonts' ) );

    if ( ! empty( $fxb_google_fonts ) ) {
        foreach ( $fxb_google_fonts as $font ) {
            $font_array[] = 'family=' . $font . ':wght@300;400;500;700';
        }
    }

    $font_array = array_filter( $font_array );
    $font_array = array_unique( $font_array );
    $font_array = str_replace( ' ', '+', $font_array );

    $fxb_fonts = 'https://fonts.googleapis.com/css2?' . urlencode( implode( '&', $font_array ) ) . '&display=swap';

    return $fxb_fonts;
}

/**
 * Build Bunny fonts string
 */
function fxb_bunny_fonts() {
    $font_array      = [];
    $fxb_bunny_fonts = array_map( 'sanitize_text_field', (array) get_option( 'fxb_bunny_fonts' ) );

    if ( ! empty( $fxb_bunny_fonts ) ) {
        foreach ( $fxb_bunny_fonts as $font ) {
            $font_array[] = $font . ':300,400,500,700';
        }
    }

    $font_array = array_filter( $font_array );
    $font_array = array_unique( $font_array );
    $font_array = str_replace( ' ', '+', $font_array );

    $fxb_fonts = 'https://fonts.bunny.net/css?family=' . urlencode( implode( '|', $font_array ) ) . '&display=swap';

    return $fxb_fonts;
}

function fxb_enqueue() {
    if ( count( (array) get_option( 'fxb_google_fonts' ) ) > 0 ) {
        wp_enqueue_style( 'fxb-google-fonts', fxb_google_fonts(), [], FX_BUILDER_VERSION );
    }

    if ( count( (array) get_option( 'fxb_bunny_fonts' ) ) > 0 ) {
        wp_enqueue_style( 'fxb-bunny-fonts', fxb_bunny_fonts(), [], FX_BUILDER_VERSION );
    }
}

add_action( 'wp_enqueue_scripts', 'fxb_enqueue' );
add_action( 'admin_enqueue_scripts', 'fxb_enqueue' );
