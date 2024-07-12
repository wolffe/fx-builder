<?php
/**
 * Enable font size, font family and formats dropdowns in the editor
 */
add_filter(
    'mce_buttons',
    function( $buttons ) {
        array_push( $buttons, 'styleselect' );

        return $buttons;
    }
);

add_filter(
    'mce_buttons_2',
    function( $buttons ) {
        array_unshift( $buttons, 'fontselect' ); // Font family dropdown
        array_unshift( $buttons, 'fontsizeselect' );

        // Used for different versions of TinyMCE
        // Keeping them here for reference and further updates
        //array_unshift( $buttons, 'fontsize_formats' );
        //array_unshift( $buttons, 'fontsize' );

        return $buttons;
    }
);



// Customize the classic editor font size dropdown.
add_filter(
    'tiny_mce_before_init',
    function( $init_array ) {
        $init_array['fontsize_formats'] = '8px 10px 12px 14px 16px 18px 24px 32px 48px 64px 72px 96px';

        return $init_array;
    }
);

/*
add_filter(
    'tiny_mce_before_init',
    function( $init_array ) {
        $init_array['fontsizeselect'] = "9px 10px 12px 13px 14px 16px 18px 21px 24px 28px 32px 37px";

        return $init_array;
    }
);
add_filter(
    'tiny_mce_before_init',
    function( $init_array ) {
        $init_array['fontsize'] = "9px 10px 12px 13px 14px 16px 18px 21px 24px 28px 32px 38px";

        return $init_array;

    }
);
/**/

// Add custom Fonts to the Fonts list. Disabled for now.
/*
add_filter(
    'tiny_mce_before_init',
    function( $initArray ) {
        $initArray['font_formats'] = 'LOL=LOL;Space+Grotesk=Space+Grotesk;Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';

        return $initArray;
    }
);

// Add Google Scripts for use with the editor.
add_action(
    'admin_init',
    function() {
        $font_url = 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap';

        add_editor_style( str_replace( ',', '%2C', $font_url ) );
    }
);
/**/


// Add new styles to the TinyMCE "formats" menu dropdown.
add_filter(
    'tiny_mce_before_init',
    function( $settings ) {
        $new_styles = [
            [
                'title' => __( 'Font Size', 'fx-builder' ),
                'items' => [
                    [
                        'title'    => __( 'Small', 'fx-builder' ),
                        'selector' => 'p',
                        //'inline'  => 'span',
                        //'classes' => 'highlight',
                        'styles'   => [
                            'font-size' => '12px',
                        ],
                    ],
                    [
                        'title'    => __( 'Medium', 'fx-builder' ),
                        'selector' => 'p',
                        'styles'   => [
                            'font-size' => '18px',
                        ],
                    ],
                    [
                        'title'    => __( 'Large', 'fx-builder' ),
                        'selector' => 'p',
                        'styles'   => [
                            'font-size' => '24px',
                        ],
                    ],
                    [
                        'title'    => __( 'Extra Large', 'fx-builder' ),
                        'selector' => 'p',
                        'styles'   => [
                            'font-size' => '36px',
                        ],
                    ],
                ],
            ],
            [
                'title' => __( 'Font Weight', 'fx-builder' ),
                'items' => [
                    [
                        'title'  => __( 'Light', 'fx-builder' ),
                        'inline' => 'span',
                        'styles' => [
                            'font-weight' => '300',
                        ],
                    ],
                    [
                        'title'  => __( 'Regular', 'fx-builder' ),
                        'inline' => 'span',
                        'styles' => [
                            'font-weight' => '400',
                        ],
                    ],
                    [
                        'title'  => __( 'Semibold', 'fx-builder' ),
                        'inline' => 'span',
                        'styles' => [
                            'font-weight' => '500',
                        ],
                    ],
                    [
                        'title'  => __( 'Bold', 'fx-builder' ),
                        'inline' => 'span',
                        'styles' => [
                            'font-weight' => '700',
                        ],
                    ],
                ],
            ],
        ];

        $settings['style_formats_merge'] = false;
        $settings['style_formats']       = wp_json_encode( $new_styles );

        return $settings;
    }
);

// Add custom Fonts to the Fonts list.
add_filter(
    'tiny_mce_before_init',
    function( $init_array ) {
        $init_array['font_formats'] = 'System UI=-apple-system, BlinkMacSystemFont, Segoe UI Variable Text, Segoe UI, Roboto, Helvetica, Helvetica Neue, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Arial, sans-serif, Apple Color Emoji, Twemoji Mozilla, Segoe UI Emoji, Android Emoji;Segoe UI=Segoe UI;Segoe UI Variable Text=Segoe UI Variable Text;Segoe UI Variable Heading=Segoe UI Variable Heading;Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino;Helvetica=helvetica;Times New Roman=times new roman,times;Monospace=ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace';

        return $init_array;
    }
);
