<?php
/**
 * Shortcodes
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Register shortcodes.
 */
add_shortcode(
    'cover',
    function ( $atts, $content = null ) {
        static $did_enqueue_css = false;

        /**
         * [cover] shortcode parameters:
         * - image: Background image URL
         * - overlay: Overlay opacity (0-1, default: 0.5)
         * - overlay-color: Overlay color (hex, default: #000000)
         * - text-color: Text color (hex, default: #ffffff)
         * - padding: Inner padding (default: 2em)
         * - height: Height (auto or CSS length, default: auto)
         * - min-height: Minimum height (default: 300px)
         * - align: Text alignment (left/center/right, default: center)
         * - valign: Vertical align (top/center/bottom, default: center)
         * - fullscreen: Use FX Builder fullwidth behavior (0/1, default: 0)
         */
        $atts = is_array( $atts ) ? $atts : [];
        // Back-compat: support underscore variants too.
        $alias_map = [
            'overlay_color' => 'overlay-color',
            'min_height'    => 'min-height',
            'text_color'    => 'text-color',
        ];
        foreach ( $alias_map as $from => $to ) {
            if ( isset( $atts[ $from ] ) && ! isset( $atts[ $to ] ) ) {
                $atts[ $to ] = $atts[ $from ];
            }
        }

        $atts = shortcode_atts(
            [
                'image'         => '',
                'overlay'       => '0.5',
                'overlay-color' => '#000000',
                'text-color'    => '#ffffff',
                'padding'       => '2em',
                'height'        => 'auto',
                'min-height'    => '300px',
                'align'         => 'center',
                'valign'        => 'center',
                'fullscreen'    => '0',
            ],
            $atts,
            'cover'
        );

        $image_url = is_string( $atts['image'] ) ? trim( $atts['image'] ) : '';
        $image_url = $image_url !== '' ? esc_url_raw( $image_url ) : '';

        $overlay_opacity = (float) $atts['overlay'];
        $overlay_opacity = max( 0, min( 1, $overlay_opacity ) );

        $overlay_color = is_string( $atts['overlay-color'] ) ? trim( $atts['overlay-color'] ) : '';
        $overlay_color = sanitize_hex_color( $overlay_color );
        if ( ! is_string( $overlay_color ) || $overlay_color === '' ) {
            $overlay_color = '#000000';
        }

        $text_color = is_string( $atts['text-color'] ) ? trim( $atts['text-color'] ) : '';
        $text_color = sanitize_hex_color( $text_color );
        if ( ! is_string( $text_color ) || $text_color === '' ) {
            $text_color = '#ffffff';
        }

        $padding = is_string( $atts['padding'] ) ? trim( $atts['padding'] ) : '';
        // Allow 1-4 simple length tokens (or 0). Keep it conservative.
        if ( $padding === '' || ! preg_match( '/^(0|\d+(\.\d+)?(px|%|em|rem|vh|vw))( (0|\d+(\.\d+)?(px|%|em|rem|vh|vw))){0,3}$/', $padding ) ) {
            $padding = '2em';
        }

        $height = is_string( $atts['height'] ) ? strtolower( trim( $atts['height'] ) ) : 'auto';
        if ( $height !== 'auto' && ! preg_match( '/^\d+(\.\d+)?(px|%|em|rem|vh|vw)$/', $height ) ) {
            $height = 'auto';
        }

        $min_height = is_string( $atts['min-height'] ) ? trim( $atts['min-height'] ) : '';
        if ( $min_height === '' || ! preg_match( '/^\d+(\.\d+)?(px|%|em|rem|vh|vw)$/', $min_height ) ) {
            $min_height = '300px';
        }

        $align = is_string( $atts['align'] ) ? strtolower( trim( $atts['align'] ) ) : 'center';
        if ( ! in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
            $align = 'center';
        }

        $valign = is_string( $atts['valign'] ) ? strtolower( trim( $atts['valign'] ) ) : 'center';
        $valign_map = [
            'top'    => 'flex-start',
            'center' => 'center',
            'bottom' => 'flex-end',
        ];
        $valign_css = $valign_map[ $valign ] ?? 'center';

        $fullscreen_raw = is_string( $atts['fullscreen'] ) ? strtolower( trim( $atts['fullscreen'] ) ) : '0';
        $is_fullscreen  = in_array( $fullscreen_raw, [ '1', 'true', 'yes', 'on' ], true );

        if ( ! $did_enqueue_css ) {
            // Only add CSS if the shortcode is actually used.
            wp_register_style( 'fxb-cover-shortcode', false, [], FX_BUILDER_VERSION );
            wp_enqueue_style( 'fxb-cover-shortcode' );
            wp_add_inline_style(
                'fxb-cover-shortcode',
                '.fxb-cover{position:relative;display:flex;justify-content:center;overflow:hidden;background-size:cover;background-position:center}' .
                '.fxb-cover::before{content:"";position:absolute;inset:0;background:var(--fxb-cover-overlay-color,#000);opacity:var(--fxb-cover-overlay-opacity,.5)}' .
                '.fxb-cover__inner{position:relative;z-index:1;width:100%;padding:var(--fxb-cover-padding,2em);color:var(--fxb-cover-text-color,#fff)}'
            );
            $did_enqueue_css = true;
        }

        $style_parts = [
            'min-height:' . $min_height,
            'align-items:' . $valign_css,
            'text-align:' . $align,
            '--fxb-cover-overlay-color:' . $overlay_color,
            '--fxb-cover-overlay-opacity:' . (string) $overlay_opacity,
            '--fxb-cover-text-color:' . $text_color,
            '--fxb-cover-padding:' . $padding,
        ];
        if ( $height !== 'auto' ) {
            $style_parts[] = 'height:' . $height;
        }
        if ( $image_url !== '' ) {
            $style_parts[] = "background-image:url('{$image_url}')";
        }
        $style = esc_attr( implode( ';', $style_parts ) . ';' );

        return sprintf(
            '<div class="fxb-cover%s" style="%s">' .
                '<div class="fxb-cover__inner">%s</div>' .
            '</div>',
            $is_fullscreen ? ' fxb-fullwidth' : '',
            $style,
            do_shortcode( shortcode_unautop( $content ?? '' ) )
        );
    }
);

add_shortcode(
    'buttons',
    function ( $atts, $content = null ) {
        /**
         * Enqueue CSS once (shared by [buttons]/[button]).
         */
        if ( ! function_exists( 'fxb_enqueue_buttons_shortcode_css' ) ) {
            function fxb_enqueue_buttons_shortcode_css(): void {
                static $did_enqueue_css = false;
                if ( $did_enqueue_css ) {
                    return;
                }

                wp_register_style( 'fxb-buttons-shortcode', false, [], FX_BUILDER_VERSION );
                wp_enqueue_style( 'fxb-buttons-shortcode' );
                wp_add_inline_style(
                    'fxb-buttons-shortcode',
                    '.fxb-buttons{display:flex;flex-wrap:wrap;gap:.75em;align-items:center}' .
                    '.fxb-button{display:inline-flex;align-items:center;justify-content:center;padding:.6em 1em;border-radius:var(--fxb-btn-radius,0);text-decoration:none;line-height:1.2;' .
                        'background-color:var(--fxb-btn-bg,#111);color:var(--fxb-btn-color,#fff);border:var(--fxb-btn-border,0)}' .
                    '.fxb-button:hover{background-color:color-mix(in srgb,var(--fxb-btn-bg,#111) 80%,#fff 20%);text-decoration:none}' .
                    '.fxb-button:focus-visible{outline:2px solid currentColor;outline-offset:2px}'
                );

                $did_enqueue_css = true;
            }
        }

        /**
         * [buttons] shortcode parameters:
         * - align: left/center/right (default: left)
         * - id: optional wrapper id
         * - class: optional extra wrapper classes
         */
        $atts = shortcode_atts(
            [
                'align' => 'left',
                'id'    => '',
                'class' => '',
            ],
            is_array( $atts ) ? $atts : [],
            'buttons'
        );

        $align = is_string( $atts['align'] ) ? strtolower( trim( $atts['align'] ) ) : 'left';
        $align_map = [
            'left'   => 'flex-start',
            'center' => 'center',
            'right'  => 'flex-end',
        ];
        $justify = $align_map[ $align ] ?? 'flex-start';

        fxb_enqueue_buttons_shortcode_css();

        $id_attr = '';
        $id = is_string( $atts['id'] ) ? trim( $atts['id'] ) : '';
        if ( $id !== '' ) {
            $id = preg_replace( '/[^A-Za-z0-9\-_:.]/', '', $id );
            if ( $id !== '' ) {
                $id_attr = ' id="' . esc_attr( $id ) . '"';
            }
        }

        $extra_classes = '';
        $class_raw = is_string( $atts['class'] ) ? trim( $atts['class'] ) : '';
        if ( $class_raw !== '' ) {
            $tokens = preg_split( '/\s+/', $class_raw ) ?: [];
            $tokens = array_filter( array_map( 'sanitize_html_class', $tokens ) );
            if ( ! empty( $tokens ) ) {
                $extra_classes = ' ' . implode( ' ', $tokens );
            }
        }

        return sprintf(
            '<div class="fxb-buttons%s"%s style="%s">%s</div>',
            esc_attr( $extra_classes ),
            $id_attr,
            esc_attr( 'justify-content:' . $justify . ';' ),
            do_shortcode( shortcode_unautop( $content ?? '' ) )
        );
    }
);

add_shortcode(
    'button',
    function ( $atts, $content = null ) {
        if ( function_exists( 'fxb_enqueue_buttons_shortcode_css' ) ) {
            fxb_enqueue_buttons_shortcode_css();
        }

        /**
         * [button] shortcode parameters:
         * - link: URL (required)
         * - target: _self/_blank/_parent/_top (default: _self)
         * - bg-color: Background color hex (optional)
         * - text-color: Text color hex (optional)
         * - border-radius: e.g. 3px, 50%, 1em (optional)
         * - border: e.g. 2px solid green, 1px dotted #fff (optional)
         */
        $atts = is_array( $atts ) ? $atts : [];
        // Back-compat: support underscore variants too.
        $alias_map = [
            'bg_color'   => 'bg-color',
            'text_color' => 'text-color',
            'border_radius' => 'border-radius',
        ];
        foreach ( $alias_map as $from => $to ) {
            if ( isset( $atts[ $from ] ) && ! isset( $atts[ $to ] ) ) {
                $atts[ $to ] = $atts[ $from ];
            }
        }

        $atts = shortcode_atts(
            [
                'link'       => '',
                'target'     => '_self',
                'bg-color'   => '',
                'text-color' => '',
                'border-radius' => '',
                'border'     => '',
            ],
            $atts,
            'button'
        );

        $href = is_string( $atts['link'] ) ? trim( $atts['link'] ) : '';
        $href = $href !== '' ? esc_url( $href ) : '';
        if ( $href === '' ) {
            return '';
        }

        $target = is_string( $atts['target'] ) ? strtolower( trim( $atts['target'] ) ) : '_self';
        $allowed_targets = [ '_self', '_blank', '_parent', '_top' ];
        if ( ! in_array( $target, $allowed_targets, true ) ) {
            $target = '_self';
        }

        $bg_color_raw = is_string( $atts['bg-color'] ) ? trim( $atts['bg-color'] ) : '';
        $bg_color_lower = strtolower( $bg_color_raw );
        if ( $bg_color_lower === 'transparent' ) {
            $bg_color = 'transparent';
        } else {
            $bg_color = sanitize_hex_color( $bg_color_raw );
            $bg_color = is_string( $bg_color ) ? $bg_color : '';
        }

        $text_color = is_string( $atts['text-color'] ) ? trim( $atts['text-color'] ) : '';
        $text_color = sanitize_hex_color( $text_color );
        $text_color = is_string( $text_color ) ? $text_color : '';

        $border_radius = is_string( $atts['border-radius'] ) ? trim( $atts['border-radius'] ) : '';
        if ( $border_radius !== '' && ! preg_match( '/^\d+(\.\d+)?(px|%|em|rem)$/', $border_radius ) ) {
            $border_radius = '';
        }

        $border = is_string( $atts['border'] ) ? trim( $atts['border'] ) : '';
        if ( $border !== '' ) {
            $border = preg_replace( '/\s+/', ' ', $border );
            // Allow: "<width><unit> <style> <color>" where color is a keyword or hex.
            if ( ! preg_match( '/^\d+(\.\d+)?(px|em|rem) (solid|dotted|dashed|double) ([a-zA-Z]+|#[0-9a-fA-F]{3,8})$/', $border ) ) {
                $border = '';
            }
        }

        $style_parts = [];
        if ( $bg_color !== '' ) {
            $style_parts[] = '--fxb-btn-bg:' . $bg_color;
        }
        if ( $text_color !== '' ) {
            $style_parts[] = '--fxb-btn-color:' . $text_color;
        }
        if ( $border_radius !== '' ) {
            $style_parts[] = '--fxb-btn-radius:' . $border_radius;
        }
        if ( $border !== '' ) {
            $style_parts[] = '--fxb-btn-border:' . $border;
        }

        $rel = '';
        if ( $target === '_blank' ) {
            $rel = 'noopener noreferrer';
        }

        return sprintf(
            '<a class="fxb-button" href="%s"%s%s%s>%s</a>',
            esc_url( $href ),
            $target !== '_self' ? ' target="' . esc_attr( $target ) . '"' : '',
            $rel !== '' ? ' rel="' . esc_attr( $rel ) . '"' : '',
            ! empty( $style_parts ) ? ' style="' . esc_attr( implode( ';', $style_parts ) ) . '"' : '',
            do_shortcode( shortcode_unautop( $content ?? '' ) )
        );
    }
);


