<?php
/**
 * FX Builder module registry and loader.
 */

defined( 'ABSPATH' ) || exit;

function fxb_get_modules(): array {
    return [
        'image-controls' => [
            'name'        => __( 'FX Builder Image Controls', 'fx-builder' ),
            'description' => __( 'TinyMCE modal for image spacing, radius, loading, and fetch priority.', 'fx-builder' ),
            'file'        => FX_BUILDER_PATH . 'modules/image-controls/image-controls.php',
        ],
    ];
}

function fxb_load_modules(): void {
    $enabled  = (array) get_option( 'fxb_enabled_modules', [] );
    $registry = fxb_get_modules();

    foreach ( array_intersect( $enabled, array_keys( $registry ) ) as $slug ) {
        require_once $registry[ $slug ]['file'];
    }
}

add_action( 'plugins_loaded', 'fxb_load_modules', 20 );
