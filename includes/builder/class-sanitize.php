<?php
namespace fx_builder\builder;
use fx_builder\Sanitize as Fs;
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Sanitize Functions.
 * @since 1.0.0
 */
class Sanitize {
    /* ROWS DATAS
    ------------------------------------------ */
    public static function rows_data( $input ) {
        if ( ! is_array( $input ) || empty( $input ) ) {
            return [];
        }

        $rows = [];

        foreach ( $input as $row_id => $row_data ) {
            $default = [
                'id'                  => $row_id,
                'index'               => '',
                'state'               => 'open',
                'col_num'             => '1',
                'layout'              => '1',
                'col_1'               => '',
                'col_2'               => '',
                'col_3'               => '',
                'col_4'               => '',
                'col_5'               => '',
                'row_title'              => '',
                'row_html_width'         => 'default',
                'row_content_page_width' => '',
                'row_html_height'        => '',
                'row_html_height_unit'   => 'px',
                'row_html_id'         => '',
                'row_html_class'      => '',
                'row_column_align'    => 'start',
                'row_column_gap'      => '',
                'row_column_gap_unit' => '',
                'row_bg_color'        => '',
                'row_bg_image'        => '',
                'row_col_padding'     => '',
                'row_col_padding_unit'=> 'px',
                'col_1_bg_color'      => '',
                'col_2_bg_color'      => '',
                'col_3_bg_color'      => '',
                'col_4_bg_color'      => '',
                'col_5_bg_color'      => '',
            ];

            $rows[ $row_id ]                    = wp_parse_args( $row_data, $default );
            $rows[ $row_id ]['id']              = wp_strip_all_tags( $rows[ $row_id ]['id'] );
            $rows[ $row_id ]['index']           = wp_strip_all_tags( $rows[ $row_id ]['index'] );
            $rows[ $row_id ]['state']           = self::state( $rows[ $row_id ]['state'] );
            $rows[ $row_id ]['col_num']         = Functions::get_col_num( $rows[ $row_id ]['layout'] );
            $rows[ $row_id ]['layout']          = self::layout( $rows[ $row_id ]['layout'] );
            $rows[ $row_id ]['col_1']           = self::ids( $rows[ $row_id ]['col_1'] );
            $rows[ $row_id ]['col_2']           = self::ids( $rows[ $row_id ]['col_2'] );
            $rows[ $row_id ]['col_3']           = self::ids( $rows[ $row_id ]['col_3'] );
            $rows[ $row_id ]['col_4']           = self::ids( $rows[ $row_id ]['col_4'] );
            $rows[ $row_id ]['col_5']           = self::ids( $rows[ $row_id ]['col_5'] );
            $rows[ $row_id ]['row_title']              = sanitize_text_field( $rows[ $row_id ]['row_title'] );
            $rows[ $row_id ]['row_html_width']         = self::row_html_width( $rows[ $row_id ]['row_html_width'] );
            $rows[ $row_id ]['row_content_page_width'] = self::checkbox( $rows[ $row_id ]['row_content_page_width'] );
            $rows[ $row_id ]['row_html_height']        = sanitize_text_field( $rows[ $row_id ]['row_html_height'] );
            $rows[ $row_id ]['row_html_height_unit']   = self::unit( $rows[ $row_id ]['row_html_height_unit'], [ 'px', '%', 'em', 'rem', 'vh' ], 'px' );
            $rows[ $row_id ]['row_html_id']     = sanitize_html_class( $rows[ $row_id ]['row_html_id'] );
            $rows[ $row_id ]['row_html_class']  = self::html_classes( $rows[ $row_id ]['row_html_class'] );

            $rows[ $row_id ]['row_column_align']    = self::row_column_align( $rows[ $row_id ]['row_column_align'] );
            $rows[ $row_id ]['row_column_gap']      = sanitize_text_field( $rows[ $row_id ]['row_column_gap'] );
            $rows[ $row_id ]['row_column_gap_unit'] = sanitize_text_field( $rows[ $row_id ]['row_column_gap_unit'] );

            $rows[ $row_id ]['row_bg_color'] = sanitize_hex_color( $rows[ $row_id ]['row_bg_color'] ) ?: '';
            $rows[ $row_id ]['row_bg_image'] = esc_url_raw( (string) $rows[ $row_id ]['row_bg_image'] );

            $rows[ $row_id ]['row_col_padding'] = sanitize_text_field( $rows[ $row_id ]['row_col_padding'] );
            $rows[ $row_id ]['row_col_padding'] = preg_replace( '/[^0-9.]/', '', (string) $rows[ $row_id ]['row_col_padding'] );
            $rows[ $row_id ]['row_col_padding_unit'] = sanitize_text_field( $rows[ $row_id ]['row_col_padding_unit'] );
            $rows[ $row_id ]['row_col_padding_unit'] = in_array( $rows[ $row_id ]['row_col_padding_unit'], [ 'px', '%', 'em', 'rem' ], true ) ? $rows[ $row_id ]['row_col_padding_unit'] : 'px';

            for ( $col_i = 1; $col_i <= 5; $col_i++ ) {
                $bg_key                          = "col_{$col_i}_bg_color";
                $rows[ $row_id ][ $bg_key ]      = sanitize_hex_color( $rows[ $row_id ][ $bg_key ] ) ?: '';
            }
        }

        return $rows;
    }


    /* ITEMS DATAS
    ------------------------------------------ */
    public static function items_data( $input ) {
        if ( ! is_array( $input ) || empty( $input ) ) {
            return [];
        }
        $items = [];
        foreach ( $input as $item_id => $item_data ) {
            $default = [
                'item_id'    => $item_id,
                'item_index' => '',
                'item_state' => 'open',
                'item_type'  => 'text',
                'row_id'     => '',
                'col_index'  => 'col_1',
                'content'    => '',
            ];

            $items[ $item_id ]               = wp_parse_args( $item_data, $default );
            $items[ $item_id ]['item_id']    = wp_strip_all_tags( $items[ $item_id ]['item_id'] );
            $items[ $item_id ]['item_index'] = wp_strip_all_tags( $items[ $item_id ]['item_index'] );
            $items[ $item_id ]['item_state'] = self::state( $items[ $item_id ]['item_state'] );
            $items[ $item_id ]['item_type']  = self::item_type( $items[ $item_id ]['item_type'] );
            $items[ $item_id ]['row_id']     = wp_strip_all_tags( $items[ $item_id ]['row_id'] );
            $items[ $item_id ]['col_index']  = self::item_col_index( $items[ $item_id ]['col_index'] );
            $items[ $item_id ]['content']    = wp_kses_post( $items[ $item_id ]['content'] );
        }
        return $items;
    }


    /* Other Sanitize Functions
    ------------------------------------------ */

    /**
     * Return $input if it appears in $valid, otherwise $default.
     */
    private static function enum_or_default( $input, array $valid, $default ) {
        return in_array( $input, $valid ) ? $input : $default;
    }

    public static function state( $input ) {
        return self::enum_or_default( $input, [ 'open', 'close' ], 'open' );
    }

    public static function layout( $layout ) {
        return self::enum_or_default(
            $layout,
            [ '1', '12_12', '13_23', '23_13', '13_13_13', '14_14_14_14', '15_15_15_15_15' ],
            '1'
        );
    }

    public static function row_html_width( $input ) {
        return self::enum_or_default( $input, [ 'default', 'fullwidth' ], 'default' );
    }

    public static function row_column_align( $input ) {
        return self::enum_or_default( $input, [ 'stretch', 'start', 'center', 'end' ], 'start' );
    }

    public static function checkbox( $input ) {
        return '1' === (string) $input ? '1' : '';
    }

    public static function unit( $input, array $valid, $default ) {
        return self::enum_or_default( $input, $valid, $default );
    }

    public static function item_type( $input ) {
        return self::enum_or_default( $input, [ 'text' ], 'text' );
    }

    public static function item_col_index( $input ) {
        return self::enum_or_default( $input, [ 'col_1', 'col_2', 'col_3', 'col_4', 'col_5' ], 'col_1' );
    }

    /**
     * Sanitize IDs
     */
    public static function ids( $input ) {
        $output = explode( ',', $input );
        $output = array_map( 'wp_strip_all_tags', $output );
        $output = implode( ',', $output );
        return $output;
    }

    /**
     * Sanitize HTML Classes
     */
    public static function html_classes( $classes ) {
        $classes = explode( ' ', $classes );
        $classes = array_map( 'sanitize_html_class', $classes );
        $classes = implode( ' ', $classes );
        return $classes;
    }


    /**
     * Sanitize Version
     */
    public static function version( $input ) {
        $output = sanitize_text_field( $input );
        $output = str_replace( ' ', '', $output );
        return trim( esc_attr( $output ) );
    }

    /**
     * Sanitize Custom CSS
     * Strips any HTML tags (including <script>/<style> blocks); print_css() escapes at output.
     */
    public static function css( $css ) {
        return wp_strip_all_tags( (string) $css );
    }
} // end class
