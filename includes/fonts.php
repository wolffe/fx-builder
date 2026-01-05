<?php
/**
 * Build Google fonts string
 */
function fxb_google_fonts() {
    $font_array       = [];
    $fxb_google_fonts = array_map( 'sanitize_text_field', (array) get_option( 'fxb_google_fonts' ) );
    $fxb_google_fonts = array_values(
        array_filter(
            $fxb_google_fonts,
            function ( $v ) {
                $v = (string) $v;
                return $v !== '' && $v !== '0';
            }
        )
    );

        foreach ( $fxb_google_fonts as $font ) {
            $font_array[] = 'family=' . $font . ':wght@300;400;500;700';
    }

    $font_array = array_filter( $font_array );
    $font_array = array_unique( $font_array );
    $font_array = str_replace( ' ', '+', $font_array );

    if ( empty( $font_array ) ) {
        return '';
    }

    // Build URL (fonts are already sanitized; spaces converted to '+').
    $fxb_fonts = 'https://fonts.googleapis.com/css2?' . implode( '&', $font_array ) . '&display=swap';

    return $fxb_fonts;
}

/**
 * Build Bunny fonts string
 */
function fxb_bunny_fonts() {
    $font_array      = [];
    $fxb_bunny_fonts = array_map( 'sanitize_text_field', (array) get_option( 'fxb_bunny_fonts' ) );
    $fxb_bunny_fonts = array_values(
        array_filter(
            $fxb_bunny_fonts,
            function ( $v ) {
                $v = (string) $v;
                return $v !== '' && $v !== '0';
            }
        )
    );

        foreach ( $fxb_bunny_fonts as $font ) {
            $font_array[] = $font . ':300,400,500,700';
    }

    $font_array = array_filter( $font_array );
    $font_array = array_unique( $font_array );
    $font_array = str_replace( ' ', '+', $font_array );

    if ( empty( $font_array ) ) {
        return '';
    }

    $fxb_fonts = 'https://fonts.bunny.net/css?family=' . implode( '|', $font_array ) . '&display=swap';

    return $fxb_fonts;
}

function fxb_enqueue() {
    wp_enqueue_style( 'fxb-admin-ui', plugins_url( 'includes/builder/assets/fxb-admin-ui.css', FX_BUILDER_FILE ), [], FX_BUILDER_VERSION );

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
