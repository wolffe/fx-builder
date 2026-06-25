<?php
/**
 * FX Builder module: Image Controls.
 *
 * Adds a TinyMCE modal for editing selected image spacing, radius, loading, and fetch priority.
 */

defined( 'ABSPATH' ) || exit;

function fxb_image_controls_user_can_edit(): bool {
    return current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );
}

function fxb_image_controls_editor_plugin_url(): string {
    $path = FX_BUILDER_PATH . 'modules/image-controls/editor-plugin.js';
    $url  = FX_BUILDER_URI . 'modules/image-controls/editor-plugin.js';

    return file_exists( $path )
        ? add_query_arg( 'ver', (string) filemtime( $path ), $url )
        : $url;
}

function fxb_image_controls_add_tinymce_plugin( array $plugins ): array {
    if ( fxb_image_controls_user_can_edit() ) {
        $plugins['fxb_image_controls'] = fxb_image_controls_editor_plugin_url();
    }

    return $plugins;
}

function fxb_image_controls_add_tinymce_button( array $buttons ): array {
    if ( fxb_image_controls_user_can_edit() ) {
        $buttons[] = 'fxb_image_controls';
    }

    return $buttons;
}

function fxb_image_controls_allow_tinymce_image_attributes( array $init ): array {
    $img = 'img[src|srcset|sizes|alt|title|width|height|class|style|loading|fetchpriority]';

    if ( empty( $init['extended_valid_elements'] ) ) {
        $init['extended_valid_elements'] = $img;
        return $init;
    }

    if ( ! str_contains( $init['extended_valid_elements'], 'fetchpriority' ) ) {
        $init['extended_valid_elements'] .= ',' . $img;
    }

    return $init;
}

function fxb_image_controls_allow_image_attributes_in_saved_html( array $tags, $context ): array {
    if ( 'post' !== $context ) {
        return $tags;
    }

    if ( empty( $tags['img'] ) || ! is_array( $tags['img'] ) ) {
        $tags['img'] = [];
    }

    $tags['img']['loading']       = true;
    $tags['img']['fetchpriority'] = true;

    return $tags;
}

function fxb_image_controls_allow_image_css_properties( array $properties ): array {
    return array_values(
        array_unique(
            array_merge(
                $properties,
                [
                    'border-radius',
                    'margin',
                    'margin-top',
                    'margin-right',
                    'margin-bottom',
                    'margin-left',
                    'padding',
                    'padding-top',
                    'padding-right',
                    'padding-bottom',
                    'padding-left',
                ]
            )
        )
    );
}

add_filter( 'mce_external_plugins', 'fxb_image_controls_add_tinymce_plugin' );
add_filter( 'mce_buttons', 'fxb_image_controls_add_tinymce_button' );
add_filter( 'tiny_mce_before_init', 'fxb_image_controls_allow_tinymce_image_attributes' );
add_filter( 'wp_kses_allowed_html', 'fxb_image_controls_allow_image_attributes_in_saved_html', 10, 2 );
add_filter( 'safe_style_css', 'fxb_image_controls_allow_image_css_properties' );
