<?php
namespace fx_builder\builder;
use fx_builder\Functions as Fs;
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Functions.
 * @since 1.0.0
 */
class Functions {

    /**
     * Add Row
     */
    public static function add_row_field( $method = 'prepend' ) {
        global $fxb_admin_color;
        $img = URI . 'assets/layout-images/';
        ?>
        <div class="fxb-add-row" data-add_row_method="<?php echo esc_attr( $method ); ?>" style="color:<?php echo esc_attr( $fxb_admin_color['2'] ); ?>">

            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="1" data-row-col_num="1"><img src="<?php echo esc_url( $img . 'layout-1.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="12_12" data-row-col_num="2"><img src="<?php echo esc_url( $img . 'layout-12_12.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="13_23" data-row-col_num="2"><img src="<?php echo esc_url( $img . 'layout-13_23.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="23_13" data-row-col_num="2"><img src="<?php echo esc_url( $img . 'layout-23_13.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="13_13_13" data-row-col_num="3"><img src="<?php echo esc_url( $img . 'layout-13_13_13.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="14_14_14_14" data-row-col_num="4"><img src="<?php echo esc_url( $img . 'layout-14_14_14_14.png' ); ?>"></div>
            </div>
            <div class="layout-thumb-wrap">
                <div class="layout-thumb" data-row-layout="15_15_15_15_15" data-row-col_num="5"><img src="<?php echo esc_url( $img . 'layout-15_15_15_15_15.png' ); ?>"></div>
            </div>

        </div><!-- .fxb-add-row -->
        <?php
    }

    /**
     * Render Modal Box Settings HTML
     * @since 1.0.0
     */
    public static function render_settings( $args = [] ) {
        global $fxb_admin_color;
        $args_default = [
            'id'       => '',
            'title'    => '',
            'callback' => '__return_false',
            'width'    => '500px',
            'height'   => 'auto',
        ];

        $args = wp_parse_args( $args, $args_default );
        ?>
        <div class="<?php echo esc_attr( sanitize_title( $args['id'] ) ); ?> fxb-modal" style="display:none;width:<?php echo esc_attr( $args['width'] ); ?>;height:<?php echo esc_attr( $args['height'] ); ?>;">
            <div class="fxb-modal-container">
                <div class="fxb-modal-title"><?php echo esc_attr( $args['title'] ); ?><span class="fxb-modal-close" style="background-color:<?php echo esc_attr( $fxb_admin_color['2'] ); ?>"><?php esc_attr_e( 'Apply', 'fx-builder' ); ?></span></div><!-- .fxb-modal-title -->

                <div class="fxb-modal-content">
                    <?php
                    if ( is_callable( $args['callback'] ) ) {
                        call_user_func( $args['callback'] );
                    }
                    ?>
                </div><!-- .fxb-modal-content -->

            </div><!-- .fxb-modal-container -->
        </div><!-- .fxb-modal -->
        <?php
    }


    /**
     * Row Settings (Modal Box)
     * This is loaded in underscore template in each row
     */
    public static function row_settings() {
        ?>
        <?php /* Row Title */ ?>
        <div class="fxb-modal-field fxb-modal-field-text">
            <label for="fxb_rows[{{data.id}}][row_title]">
                <?php esc_html_e( 'Label', 'fx-builder' ); ?>
            </label>

            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_title]" data-row_field="row_title" name="_fxb_rows[{{data.id}}][row_title]" type="text" value="{{data.row_title}}">
        </div><!-- .fxb-modal-field -->

        <?php /* Row Layout */ ?>
        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][layout]">
                <?php esc_html_e( 'Layout', 'fx-builder' ); ?>
            </label>

            <select id="fxb_rows[{{data.id}}][layout]" data-row_field="layout" name="_fxb_rows[{{data.id}}][layout]" autocomplete="off">
                <option data-col_num="1" value="1" <# if( data.layout == '1' ){ print('selected="selected"') } #>><?php esc_attr_e( '1 Column', 'fx-builder' ); ?></option>
                <option data-col_num="2" value="12_12" <# if( data.layout == '12_12' ){ print('selected="selected"') } #>><?php esc_attr_e( '1/2 - 1/2', 'fx-builder' ); ?></option>
                <option data-col_num="2" value="13_23" <# if( data.layout == '13_23' ){ print('selected="selected"') } #>><?php esc_attr_e( '1/3 - 2/3', 'fx-builder' ); ?></option>
                <option data-col_num="2" value="23_13" <# if( data.layout == '23_13' ){ print('selected="selected"') } #>><?php esc_attr_e( '2/3 - 1/3', 'fx-builder' ); ?></option>
                <option data-col_num="3" value="13_13_13" <# if( data.layout == '13_13_13' ){ print('selected="selected"') } #>><?php esc_attr_e( '1/3 - 1/3 - 1/3', 'fx-builder' ); ?></option>
                <option data-col_num="4" value="14_14_14_14" <# if( data.layout == '14_14_14_14' ){ print('selected="selected"') } #>><?php esc_attr_e( '1/4 - 1/4 - 1/4 - 1/4', 'fx-builder' ); ?></option>
                <option data-col_num="5" value="15_15_15_15_15" <# if( data.layout == '15_15_15_15_15' ){ print('selected="selected"') } #>><?php esc_attr_e( '1/5 - 1/5 - 1/5 - 1/5 - 1/5', 'fx-builder' ); ?></option>
            </select>
        </div><!-- .fxb-modal-field -->

        <?php /* Row Width */ ?>
        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][row_html_width]">
                <?php esc_html_e( 'Width', 'fx-builder' ); ?>
            </label>

            <select id="fxb_rows[{{data.id}}][row_html_width]" data-row_field="row_html_width" name="_fxb_rows[{{data.id}}][row_html_width]" autocomplete="off">
                <option value="default" <# if( data.row_html_width == 'default' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Default', 'fx-builder' ); ?></option>
                <option value="fullwidth" <# if( data.row_html_width == 'fullwidth' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Fullwidth', 'fx-builder' ); ?></option>
            </select>
        </div><!-- .fxb-modal-field -->

        <?php /* Row Height */ ?>
        <div class="fxb-modal-field fxb-modal-field-select fxb-group-control--container">
            <label for="fxb_rows[{{data.id}}][row_html_height]">
                <?php esc_html_e( 'Height', 'fx-builder' ); ?>
            </label>

            <div>
                <input autocomplete="off" inputmode="numeric" max="Infinity" min="-Infinity" step="1" type="number" id="fxb_rows[{{data.id}}][row_html_height]" data-row_field="row_html_height" name="_fxb_rows[{{data.id}}][row_html_height]" value="{{data.row_html_height}}">
                <select id="fxb_rows[{{data.id}}][row_html_height_unit]" data-row_field="row_html_height_unit" name="_fxb_rows[{{data.id}}][row_html_height_unit]" autocomplete="off" aria-label="Select unit">
                    <option value="px" <# if( data.row_html_height_unit == 'px' ){ print('selected="selected"') } #>>px</option>
                    <option value="%" <# if( data.row_html_height_unit == '%' ){ print('selected="selected"') } #>>%</option>
                    <option value="em" <# if( data.row_html_height_unit == 'em' ){ print('selected="selected"') } #>>em</option>
                    <option value="rem" <# if( data.row_html_height_unit == 'rem' ){ print('selected="selected"') } #>>rem</option>
                    <option value="vw" <# if( data.row_html_height_unit == 'vh' ){ print('selected="selected"') } #>>vh</option>
                </select>
            </div>
        </div><!-- .fxb-modal-field -->

        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][row_column_align]">
                <?php esc_html_e( 'Vertical Align', 'fx-builder' ); ?>
            </label>

            <select id="fxb_rows[{{data.id}}][row_column_align]" data-row_field="row_column_align" name="_fxb_rows[{{data.id}}][row_column_align]" autocomplete="off">
                <option value="start" <# if( data.row_column_align == 'start' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Start', 'fx-builder' ); ?></option>
                <option value="center" <# if( data.row_column_align == 'center' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Center', 'fx-builder' ); ?></option>
                <option value="end" <# if( data.row_column_align == 'end' ){ print('selected="selected"') } #>><?php esc_attr_e( 'End', 'fx-builder' ); ?></option>
            </select>
        </div><!-- .fxb-modal-field -->

        <div class="fxb-modal-field fxb-modal-field-select fxb-group-control--container">
            <label for="fxb_rows[{{data.id}}][row_column_gap]">
                <?php esc_html_e( 'Column Gap', 'fx-builder' ); ?>
            </label>

            <div>
                <input autocomplete="off" inputmode="numeric" max="Infinity" min="-Infinity" step="1" type="number" id="fxb_rows[{{data.id}}][row_column_gap]" data-row_field="row_column_gap" name="_fxb_rows[{{data.id}}][row_column_gap]" value="{{data.row_column_gap}}">
                <select id="fxb_rows[{{data.id}}][row_column_gap_unit]" data-row_field="row_column_gap_unit" name="_fxb_rows[{{data.id}}][row_column_gap_unit]" autocomplete="off" aria-label="Select unit">
                    <option value="px" <# if( data.row_column_gap_unit == 'px' ){ print('selected="selected"') } #>>px</option>
                    <option value="%" <# if( data.row_column_gap_unit == '%' ){ print('selected="selected"') } #>>%</option>
                    <option value="em" <# if( data.row_column_gap_unit == 'em' ){ print('selected="selected"') } #>>em</option>
                    <option value="rem" <# if( data.row_column_gap_unit == 'rem' ){ print('selected="selected"') } #>>rem</option>
                    <option value="vw" <# if( data.row_column_gap_unit == 'vw' ){ print('selected="selected"') } #>>vw</option>
                </select>
            </div>
        </div>

        <details class="fxb-details">
            <summary>
                <span class="summary-title">
                    <?php esc_html_e( 'Advanced', 'fx-builder' ); ?>
                </span>
                <div class="summary-chevron-up">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </summary>

            <div class="summary-content">
                <?php /* ID (Anchor) */ ?>
                <div class="fxb-modal-field fxb-modal-field-text">

                    <label for="fxb_rows[{{data.id}}][row_html_id]">
                        <?php esc_html_e( 'ID (Anchor)', 'fx-builder' ); ?>
                    </label>

                    <input autocomplete="off" id="fxb_rows[{{data.id}}][row_html_id]" data-row_field="row_html_id" name="_fxb_rows[{{data.id}}][row_html_id]" type="text" value="{{data.row_html_id}}">
                </div><!-- .fxb-modal-field -->

                <?php /* Additional CSS class(es) */ ?>
                <div class="fxb-modal-field fxb-modal-field-text">

                    <label for="fxb_rows[{{data.id}}][row_html_class]">
                        <?php esc_html_e( 'Additional CSS class(es)', 'fx-builder' ); ?>
                    </label>

                    <input autocomplete="off" id="fxb_rows[{{data.id}}][row_html_class]" data-row_field="row_html_class" name="_fxb_rows[{{data.id}}][row_html_class]" type="text" value="{{data.row_html_class}}">
                    <br><small><?php esc_html_e( 'Separate multiple classes with spaces.', 'fx-builder' ); ?></small>
                </div><!-- .fxb-modal-field -->
            </div><!-- .fxb-modal-content -->
        </details>
        <?php
    }


    /**
     * Render (empty) Column
     */
    public static function render_column( $args = [] ) {
        global $fxb_admin_color;
        $args_default = [
            'title' => '',
            'index' => '',
        ];

        $args = wp_parse_args( $args, $args_default );

        $title = $args['title'];
        $index = $args['index'];

        /* Var */
        $field = "col_{$index}";
        ?>
        <div class="fxb-col fxb-clear" data-col_index="<?php echo esc_attr( $field ); ?>">

            <?php /* Hidden input */ ?>
            <input type="hidden" data-id="item_ids" data-row_field="<?php echo esc_attr( $field ); ?>" name="_fxb_rows[{{data.id}}][<?php echo esc_attr( $field ); ?>]" value="{{data.<?php echo esc_attr( $field ); ?>}}" autocomplete="off"/>

            <?php /* Column Title */ ?>
            <h3 class="fxb-col-title"><span><?php echo esc_attr( $title ); ?></span></h3>

            <div class="fxb-col-content"></div><!-- .fxb-col-content -->

            <div class="fxb-add-item fxb-link" style="color:<?php echo esc_attr( $fxb_admin_color['2'] ); ?>">
                <span><?php esc_attr_e( 'Add Item', 'fx-builder' ); ?></span>
            </div><!-- .fxb-add-item -->

        </div><!-- .fxb-col -->
        <?php
    }

    /**
     * Format Post Builder Data To Single String
     * This is the builder data without div wrapper
     */
    public static function content_raw( $post_id ) {
        $row_ids    = Sanitize::ids( get_post_meta( $post_id, '_fxb_row_ids', true ) );
        $rows_data  = Sanitize::rows_data( get_post_meta( $post_id, '_fxb_rows', true ) );
        $items_data = Sanitize::items_data( get_post_meta( $post_id, '_fxb_items', true ) );
        if ( ! $row_ids || ! $rows_data ) {
            return false;
        }
        $rows = explode( ',', $row_ids );

        $content = '';
        foreach ( $rows as $row_id ) {
            if ( isset( $rows_data[ $row_id ] ) ) {
                $cols = range( 1, $rows_data[ $row_id ]['col_num'] );
                foreach ( $cols as $k ) {
                    $items = $rows_data[ $row_id ][ 'col_' . $k ];
                    $items = explode( ',', $items );
                    foreach ( $items as $item_id ) {
                        if ( isset( $items_data[ $item_id ]['content'] ) && ! empty( $items_data[ $item_id ]['content'] ) ) {
                            $content .= $items_data[ $item_id ]['content'] . "\r\n\r\n";
                        }
                    }
                }
            }
        }
        return apply_filters( 'fxb_content_raw', $content, $post_id, $row_ids, $rows_data, $items_data );
    }

    /**
     * Format Post Builder Data To Single String
     * This will format FX Builder data to content (single string)
     */
    public static function content( $post_id ) {
        $row_ids    = Sanitize::ids( get_post_meta( $post_id, '_fxb_row_ids', true ) );
        $rows_data  = Sanitize::rows_data( get_post_meta( $post_id, '_fxb_rows', true ) );
        $items_data = Sanitize::items_data( get_post_meta( $post_id, '_fxb_items', true ) );
        $rows       = explode( ',', $row_ids );
        if ( ! $row_ids || ! $rows_data ) {
            return false;
        }
        ob_start();
        ?>
        <div id="fxb-<?php echo esc_attr( $post_id ); ?>" class="fxb-container">

            <?php foreach ( $rows as $row_id ) { ?>
                <?php
                if ( isset( $rows_data[ $row_id ] ) ) {

                    /* = HTML ID = */
                    $row_html_id = $rows_data[ $row_id ]['row_html_id'] ? $rows_data[ $row_id ]['row_html_id'] : "fxb-row-{$row_id}";

                    /* = HTML CLASS = */
                    $row_html_class = $rows_data[ $row_id ]['row_html_class'] ? "fxb-row {$rows_data[ $row_id ]['row_html_class']}" : 'fxb-row';
                    $row_html_class = explode( ' ', $row_html_class ); // array

                    /* = HTML Width = */
                    $row_html_width = '';
                    if ( isset( $rows_data[ $row_id ]['row_html_width'] ) ) {
                        $row_html_width = 'fxb-' . $rows_data[ $row_id ]['row_html_width'];
                    }

                    /* = HTML Height = */
                    $row_html_height = '';
                    if ( isset( $rows_data[ $row_id ]['row_html_height'] ) ) {
                        $row_html_height = 'height: ' . $rows_data[ $row_id ]['row_html_height'] . $rows_data[ $row_id ]['row_html_height_unit'] . ';';
                    }

                    $row_html_class[] = $row_html_width;

                    /* ID */
                    $row_html_class[] = "fxb-row-{$row_id}";

                    /* Layout */
                    $row_html_class[] = "fxb-row-layout-{$rows_data[$row_id]['layout']}";

                    $row_html_class = array_map( 'sanitize_html_class', $row_html_class );
                    $row_html_class = implode( ' ', $row_html_class );

                    $row_column_align = $rows_data[ $row_id ]['row_column_align'] ? $rows_data[ $row_id ]['row_column_align'] : 'start';
                    $row_column_gap   = $rows_data[ $row_id ]['row_column_gap'] ? $rows_data[ $row_id ]['row_column_gap'] . $rows_data[ $row_id ]['row_column_gap_unit'] : '2em';
                    ?>

                    <div id="<?php echo esc_attr( $row_html_id ); ?>" class="<?php echo esc_attr( $row_html_class ); ?>" data-index="<?php echo intval( $rows_data[ $row_id ]['index'] ); ?>" data-layout="<?php echo esc_attr( $rows_data[ $row_id ]['layout'] ); ?>">

                        <div class="fxb-wrap" style="gap: <?php echo esc_attr( $row_column_gap ); ?>; align-items: <?php echo esc_attr( $row_column_align ); ?>; <?php echo esc_attr( $row_html_height ); ?>">

                            <?php
                            $cols = range( 1, $rows_data[ $row_id ]['col_num'] );
                            foreach ( $cols as $k ) {
                                $items = $rows_data[ $row_id ][ 'col_' . $k ];
                                $items = explode( ',', $items );
                                ?>
                                <div class="fxb-col-<?php echo intval( $k ); ?> fxb-col">
                                    <div class="fxb-wrap">

                                        <?php foreach ( $items as $item_id ) { ?>
                                            <?php if ( isset( $items_data[ $item_id ] ) ) { ?>

                                                <div id="fxb-item-<?php echo esc_attr( $item_id ); ?>" class="fxb-item">
                                                    <div class="fxb-wrap">
                                                        <?php echo wp_kses_post( wpautop( wp_kses_post( $items_data[ $item_id ]['content'] ) ) ); ?>
                                                    </div><!-- .fxb-item > .fxb-wrap -->
                                                </div><!-- .fxb-item -->

                                            <?php } ?>
                                        <?php } ?>

                                    </div><!-- .fxb-col > .fxb-wrap -->
                                </div><!-- .fxb-col -->
                                <?php
                            }
                            ?>

                        </div><!-- .fxb-row > .fxb-wrap -->

                    </div><!-- .fxb-row -->

                <?php } ?>
            <?php } ?>

        </div><!-- .fxb-container -->
        <?php
        return apply_filters( 'fxb_content', ob_get_clean(), $post_id, $row_ids, $rows_data, $items_data );
    }


    /**
     * Get Col Number from Layout
     */
    public static function get_col_num( $layout ) {
        if ( '1' == $layout ) {
            return 1;
        } elseif ( in_array( $layout, [ '12_12', '13_23', '23_13' ] ) ) {
            return 2;
        } elseif ( '13_13_13' == $layout ) {
            return 3;
        } elseif ( '14_14_14_14' == $layout ) {
            return 4;
        } elseif ( '15_15_15_15_15' == $layout ) {
            return 5;
        }
        return 1; // fallback
    }
} // end class
