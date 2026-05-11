<?php
/**
 * Enable font size, font family and formats dropdowns in the editor
 */
add_filter(
    'mce_buttons',
    function ( $buttons ) {
        array_push( $buttons, 'styleselect', 'lineheight' );
        return $buttons;
    }
);

add_filter(
    'mce_buttons_2',
    function ( $buttons ) {
        array_unshift( $buttons, 'fontselect' ); // Font family dropdown
        array_unshift( $buttons, 'fontsizeselect' );

        /**
         * Compatibility: The arrays below are used for different versions of TinyMCE.
         * Keep them here for reference and further updates.
         *
         * array_unshift( $buttons, 'fontsize_formats' );
         * array_unshift( $buttons, 'fontsize' );
         */

        return $buttons;
    }
);



// Customize TinyMCE init: font size dropdown, style formats menu, and font family list.
add_filter(
    'tiny_mce_before_init',
    function ( $init_array ) {
        $init_array['fontsize_formats'] = '8px 10px 12px 14px 16px 18px 24px 32px 48px 64px 72px 96px';

        $init_array['style_formats_merge'] = false;
        $init_array['style_formats']       = wp_json_encode(
            [
                [
                    'title' => __( 'Font Size', 'fx-builder' ),
                    'items' => [
                        [ 'title' => __( 'Small', 'fx-builder' ),       'selector' => 'p', 'styles' => [ 'font-size' => '12px' ] ],
                        [ 'title' => __( 'Medium', 'fx-builder' ),      'selector' => 'p', 'styles' => [ 'font-size' => '18px' ] ],
                        [ 'title' => __( 'Large', 'fx-builder' ),       'selector' => 'p', 'styles' => [ 'font-size' => '24px' ] ],
                        [ 'title' => __( 'Extra Large', 'fx-builder' ), 'selector' => 'p', 'styles' => [ 'font-size' => '36px' ] ],
                    ],
                ],
                [
                    'title' => __( 'Font Weight', 'fx-builder' ),
                    'items' => [
                        [ 'title' => __( 'Light', 'fx-builder' ),    'inline' => 'span', 'styles' => [ 'font-weight' => '300' ] ],
                        [ 'title' => __( 'Regular', 'fx-builder' ),  'inline' => 'span', 'styles' => [ 'font-weight' => '400' ] ],
                        [ 'title' => __( 'Semibold', 'fx-builder' ), 'inline' => 'span', 'styles' => [ 'font-weight' => '500' ] ],
                        [ 'title' => __( 'Bold', 'fx-builder' ),     'inline' => 'span', 'styles' => [ 'font-weight' => '700' ] ],
                    ],
                ],
            ]
        );

        $init_array['font_formats'] = 'System UI=-apple-system, BlinkMacSystemFont, Segoe UI Variable Text, Segoe UI, Roboto, Helvetica, Helvetica Neue, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Arial, sans-serif, Apple Color Emoji, Twemoji Mozilla, Segoe UI Emoji, Android Emoji;Segoe UI=Segoe UI;Segoe UI Variable Text=Segoe UI Variable Text;Segoe UI Variable Heading=Segoe UI Variable Heading;Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino;Helvetica=helvetica;Times New Roman=times new roman,times;Monospace=ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace;';

        foreach ( (array) get_option( 'fxb_google_fonts' ) as $font ) {
            $init_array['font_formats'] .= '[Google Fonts] ' . $font . '=' . $font . ';';
        }
        foreach ( (array) get_option( 'fxb_bunny_fonts' ) as $font ) {
            $init_array['font_formats'] .= '[Bunny Fonts] ' . $font . '=' . $font . ';';
        }

        return $init_array;
    }
);



add_filter(
    'mce_external_plugins',
    function ( $plugin_array ) {
        $plugin_array['custom_line_height'] = plugins_url( '/fx-builder/includes/builder/assets/custom-line-height-plugin.js' );
        return $plugin_array;
    }
);
