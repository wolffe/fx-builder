<?php
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Functions {
    /**
     * Row layout definitions.
     */
    public static function layouts(): array {
        return [
            '1'              => [ 'cols' => 1, 'label' => __( '1 Column', 'fx-builder' ), 'image' => 'layout-1.png' ],
            '12_12'          => [ 'cols' => 2, 'label' => __( '1/2 - 1/2', 'fx-builder' ), 'image' => 'layout-12_12.png' ],
            '13_23'          => [ 'cols' => 2, 'label' => __( '1/3 - 2/3', 'fx-builder' ), 'image' => 'layout-13_23.png' ],
            '23_13'          => [ 'cols' => 2, 'label' => __( '2/3 - 1/3', 'fx-builder' ), 'image' => 'layout-23_13.png' ],
            '13_13_13'       => [ 'cols' => 3, 'label' => __( '1/3 - 1/3 - 1/3', 'fx-builder' ), 'image' => 'layout-13_13_13.png' ],
            '14_14_14_14'    => [ 'cols' => 4, 'label' => __( '1/4 - 1/4 - 1/4 - 1/4', 'fx-builder' ), 'image' => 'layout-14_14_14_14.png' ],
            '15_15_15_15_15' => [ 'cols' => 5, 'label' => __( '1/5 - 1/5 - 1/5 - 1/5 - 1/5', 'fx-builder' ), 'image' => 'layout-15_15_15_15_15.png' ],
        ];
    }

    /**
     * Add Row
     */
    public static function add_row_field( $method = 'prepend' ) {
        $img = URI . 'assets/layout-images/';
        ?>

        <div class="fxb-add-row" data-add_row_method="<?php echo esc_attr( $method ); ?>" style="display: grid;">
            <div class="fxb-strikethrough"><?php esc_attr_e( 'Outer Sections', 'fx-builder' ); ?></div>

            <?php foreach ( self::layouts() as $layout => $config ) { ?>
                <div class="layout-thumb-wrap">
                    <div class="layout-thumb" data-row-layout="<?php echo esc_attr( $layout ); ?>" data-row-col_num="<?php echo esc_attr( (string) $config['cols'] ); ?>"><img src="<?php echo esc_url( $img . $config['image'] ); ?>"></div>
                </div>
            <?php } ?>
        </div><!-- .fxb-add-row -->
        <?php
    }

    /**
     * Render Modal Box Settings HTML
     * @since 1.0.0
     */
    public static function render_settings( $args = [] ) {
        $args_default = [
            'id'       => '',
            'title'    => '',
            'callback' => '__return_false',
            'width'    => '600px',
            'height'   => 'auto',
        ];

        $args = wp_parse_args( $args, $args_default );
        ?>
        <div class="<?php echo esc_attr( sanitize_title( $args['id'] ) ); ?> fxb-modal" style="display:none;width:<?php echo esc_attr( $args['width'] ); ?>;height:<?php echo esc_attr( $args['height'] ); ?>;">
            <div class="fxb-modal-container">
                <div class="fxb-modal-title"><?php echo esc_attr( $args['title'] ); ?><span class="fxb-modal-buttons"><span class="fxb-modal-cancel"><i class="ai-x-small"></i> <?php esc_attr_e( 'Cancel', 'fx-builder' ); ?></span><span class="fxb-modal-close" style="background-color:var(--fxb-accent-color)"><i class="ai-double-check"></i> <?php esc_attr_e( 'Save & Close', 'fx-builder' ); ?></span></span></div><!-- .fxb-modal-title -->

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
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Optional label shown in the row header (for your reference only).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about label', 'fx-builder' ); ?>">?</button>
            </label>

            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_title]" data-row_field="row_title" name="_fxb_rows[{{data.id}}][row_title]" type="text" value="{{data.row_title}}">
        </div><!-- .fxb-modal-field -->

        <?php /* Row Layout */ ?>
        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][layout]">
                <?php esc_html_e( 'Layout', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Select how many columns this row has and how they are sized.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about layout', 'fx-builder' ); ?>">?</button>
            </label>

            <select id="fxb_rows[{{data.id}}][layout]" data-row_field="layout" name="_fxb_rows[{{data.id}}][layout]" autocomplete="off">
                <?php foreach ( self::layouts() as $layout => $config ) { ?>
                    <option data-col_num="<?php echo esc_attr( (string) $config['cols'] ); ?>" value="<?php echo esc_attr( $layout ); ?>" <# if( data.layout == '<?php echo esc_js( $layout ); ?>' ){ print('selected="selected"') } #>><?php echo esc_html( $config['label'] ); ?></option>
                <?php } ?>
            </select>
        </div><!-- .fxb-modal-field -->

        <?php /* Row Content Width (fullwidth rows only) */ ?>
        <div class="fxb-modal-field fxb-modal-field-checkbox fxb-row-setting-fullwidth-only" style="display:none;">
            <label for="fxb_rows[{{data.id}}][row_content_page_width]">
                <?php esc_html_e( 'Nested content matches page width', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Keep columns inside the page content width so only the row background stretches edge to edge.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about nested content width', 'fx-builder' ); ?>">?</button>
            </label>
            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_content_page_width]" data-row_field="row_content_page_width" name="_fxb_rows[{{data.id}}][row_content_page_width]" type="checkbox" value="1" <# if( data.row_content_page_width == '1' ){ print('checked="checked"') } #>>
        </div><!-- .fxb-modal-field -->

        <?php /* Row Width */ ?>
        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][row_html_width]">
                <?php esc_html_e( 'Width', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Default keeps the row inside the content width. Fullwidth stretches to the viewport.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about width', 'fx-builder' ); ?>">?</button>
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
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Optional fixed height for the row wrapper (leave empty for auto).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about height', 'fx-builder' ); ?>">?</button>
            </label>

            <div>
                <input autocomplete="off" inputmode="numeric" max="Infinity" min="-Infinity" step="1" type="number" id="fxb_rows[{{data.id}}][row_html_height]" data-row_field="row_html_height" name="_fxb_rows[{{data.id}}][row_html_height]" value="{{data.row_html_height}}">
                <select id="fxb_rows[{{data.id}}][row_html_height_unit]" data-row_field="row_html_height_unit" name="_fxb_rows[{{data.id}}][row_html_height_unit]" autocomplete="off" aria-label="Select unit">
                    <option value="px" <# if( data.row_html_height_unit == 'px' ){ print('selected="selected"') } #>>px</option>
                    <option value="%" <# if( data.row_html_height_unit == '%' ){ print('selected="selected"') } #>>%</option>
                    <option value="em" <# if( data.row_html_height_unit == 'em' ){ print('selected="selected"') } #>>em</option>
                    <option value="rem" <# if( data.row_html_height_unit == 'rem' ){ print('selected="selected"') } #>>rem</option>
                    <option value="vh" <# if( data.row_html_height_unit == 'vh' ){ print('selected="selected"') } #>>vh</option>
                </select>
            </div>
        </div><!-- .fxb-modal-field -->

        <div class="fxb-modal-field fxb-modal-field-select">
            <label for="fxb_rows[{{data.id}}][row_column_align]">
                <?php esc_html_e( 'Vertical Align', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Stretch makes all columns the same height. Start, Center, and End align shorter columns within the row.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about vertical align', 'fx-builder' ); ?>">?</button>
            </label>

            <select id="fxb_rows[{{data.id}}][row_column_align]" data-row_field="row_column_align" name="_fxb_rows[{{data.id}}][row_column_align]" autocomplete="off">
                <option value="stretch" <# if( data.row_column_align == 'stretch' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Stretch', 'fx-builder' ); ?></option>
                <option value="start" <# if( data.row_column_align == 'start' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Start', 'fx-builder' ); ?></option>
                <option value="center" <# if( data.row_column_align == 'center' ){ print('selected="selected"') } #>><?php esc_attr_e( 'Center', 'fx-builder' ); ?></option>
                <option value="end" <# if( data.row_column_align == 'end' ){ print('selected="selected"') } #>><?php esc_attr_e( 'End', 'fx-builder' ); ?></option>
            </select>
        </div><!-- .fxb-modal-field -->

        <div class="fxb-modal-field fxb-modal-field-select fxb-group-control--container">
            <label for="fxb_rows[{{data.id}}][row_column_gap]">
                <?php esc_html_e( 'Column Gap', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Spacing between columns in this row. (Global gap can override it.)', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about column gap', 'fx-builder' ); ?>">?</button>
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

        <?php /* Row Background Color */ ?>
        <div class="fxb-modal-field fxb-modal-field-text">
            <label for="fxb_rows[{{data.id}}][row_bg_color]">
                <?php esc_html_e( 'Background Color', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Row background color (hex). Applies behind the columns.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about background color', 'fx-builder' ); ?>">?</button>
            </label>
            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_bg_color]" data-row_field="row_bg_color" name="_fxb_rows[{{data.id}}][row_bg_color]" type="text" value="{{data.row_bg_color}}" placeholder="#ffffff">
        </div><!-- .fxb-modal-field -->

        <?php /* Row Background Image */ ?>
        <div class="fxb-modal-field fxb-modal-field-text">
            <label for="fxb_rows[{{data.id}}][row_bg_image]">
                <?php esc_html_e( 'Background Image', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Row background image URL. Rendered with background-size:cover, centered.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about background image', 'fx-builder' ); ?>">?</button>
            </label>
            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_bg_image]" data-row_field="row_bg_image" name="_fxb_rows[{{data.id}}][row_bg_image]" type="url" value="{{data.row_bg_image}}" placeholder="https://...">
        </div><!-- .fxb-modal-field -->

        <?php /* Column Padding (applies to all columns in the row) */ ?>
        <div class="fxb-modal-field fxb-modal-field-select fxb-group-control--container">
            <label for="fxb_rows[{{data.id}}][row_col_padding]">
                <?php esc_html_e( 'Column Padding', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Adds padding inside each column in this row (useful for “boxed” content).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about column padding', 'fx-builder' ); ?>">?</button>
            </label>
            <div>
                <input autocomplete="off" inputmode="numeric" min="0" step="1" type="number" id="fxb_rows[{{data.id}}][row_col_padding]" data-row_field="row_col_padding" name="_fxb_rows[{{data.id}}][row_col_padding]" value="{{data.row_col_padding}}">
                <select id="fxb_rows[{{data.id}}][row_col_padding_unit]" data-row_field="row_col_padding_unit" name="_fxb_rows[{{data.id}}][row_col_padding_unit]" autocomplete="off" aria-label="Select unit">
                    <option value="px" <# if( data.row_col_padding_unit == 'px' ){ print('selected="selected"') } #>>px</option>
                    <option value="%" <# if( data.row_col_padding_unit == '%' ){ print('selected="selected"') } #>>%</option>
                    <option value="em" <# if( data.row_col_padding_unit == 'em' ){ print('selected="selected"') } #>>em</option>
                    <option value="rem" <# if( data.row_col_padding_unit == 'rem' ){ print('selected="selected"') } #>>rem</option>
                </select>
            </div>
        </div><!-- .fxb-modal-field -->

        <?php /* Remove bottom spacing */ ?>
        <div class="fxb-modal-field fxb-modal-field-checkbox">
            <label for="fxb_rows[{{data.id}}][row_no_mb]">
                <?php esc_html_e( 'Remove bottom spacing', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Removes the gap below this row so the next row sits directly underneath.', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about bottom spacing', 'fx-builder' ); ?>">?</button>
            </label>
            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_no_mb]" data-row_field="row_no_mb" name="_fxb_rows[{{data.id}}][row_no_mb]" type="checkbox" value="1" <# if( data.row_no_mb == '1' ){ print('checked="checked"') } #>>
        </div><!-- .fxb-modal-field -->

        <hr style="margin: 1em 0;">

        <?php /* ID (Anchor) */ ?>
        <div class="fxb-modal-field fxb-modal-field-text">

            <label for="fxb_rows[{{data.id}}][row_html_id]">
                <?php esc_html_e( 'ID (Anchor)', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Optional HTML id for linking to this row (e.g. from a menu).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about row id', 'fx-builder' ); ?>">?</button>
            </label>

            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_html_id]" data-row_field="row_html_id" name="_fxb_rows[{{data.id}}][row_html_id]" type="text" value="{{data.row_html_id}}">
        </div><!-- .fxb-modal-field -->

        <?php /* Additional CSS class(es) */ ?>
        <div class="fxb-modal-field fxb-modal-field-text">

            <label for="fxb_rows[{{data.id}}][row_html_class]">
                <?php esc_html_e( 'Additional CSS class(es)', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Add one or more CSS classes (separate multiple classes with spaces).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about row classes', 'fx-builder' ); ?>">?</button>
            </label>

            <input autocomplete="off" id="fxb_rows[{{data.id}}][row_html_class]" data-row_field="row_html_class" name="_fxb_rows[{{data.id}}][row_html_class]" type="text" value="{{data.row_html_class}}">
        </div><!-- .fxb-modal-field -->
        <?php
    }


    /**
     * Column Settings (Modal Box)
     */
    public static function column_settings( $index ) {
        $field = 'col_' . (int) $index . '_bg_color';
        ?>
        <div class="fxb-modal-field fxb-modal-field-text">
            <label for="fxb_rows[{{data.id}}][<?php echo esc_attr( $field ); ?>]">
                <?php esc_html_e( 'Background Color', 'fx-builder' ); ?>
                <button type="button" class="fxb-help-tip" data-tooltip="<?php esc_attr_e( 'Column background color (hex).', 'fx-builder' ); ?>" aria-label="<?php esc_attr_e( 'Help about column background color', 'fx-builder' ); ?>">?</button>
            </label>
            <input autocomplete="off" id="fxb_rows[{{data.id}}][<?php echo esc_attr( $field ); ?>]" data-row_field="<?php echo esc_attr( $field ); ?>" name="_fxb_rows[{{data.id}}][<?php echo esc_attr( $field ); ?>]" type="text" value="{{data.<?php echo esc_attr( $field ); ?>}}" placeholder="#ffffff">
        </div><!-- .fxb-modal-field -->
        <?php
    }


    /**
     * Render (empty) Column
     */
    public static function render_column( $args = [] ) {
        $args_default = [
            'title' => '',
            'index' => '',
        ];

        $args = wp_parse_args( $args, $args_default );

        $title = $args['title'];
        $index = $args['index'];

        /* Var */
        $field    = "col_{$index}";
        $bg_field = "{$field}_bg_color";
        ?>
        <div class="fxb-col fxb-clear" data-col_index="<?php echo esc_attr( $field ); ?>" style="<# if ( data.<?php echo esc_attr( $bg_field ); ?> ) { #>--fxb-col-bg-color: {{data.<?php echo esc_attr( $bg_field ); ?>}};<# } #>">

            <?php /* Hidden input */ ?>
            <input type="hidden" data-id="item_ids" data-row_field="<?php echo esc_attr( $field ); ?>" name="_fxb_rows[{{data.id}}][<?php echo esc_attr( $field ); ?>]" value="{{data.<?php echo esc_attr( $field ); ?>}}" autocomplete="off"/>

            <?php /* Column menu */ ?>
            <div class="fxb-col-menu">
                <div class="fxb-col-label"><?php echo esc_html( $title ); ?></div>
                <div style="margin-left: auto;">
                    <span data-target=".fxb-col-settings" class="fxb-icon fxb-link fxb-col-settings-trigger dashicons dashicons-admin-generic" aria-label="<?php esc_attr_e( 'Column settings', 'fx-builder' ); ?>"></span>
                    <?php
                    self::render_settings(
                        [
                            'id'       => 'fxb-col-settings',
                            'title'    => __( 'Column Settings', 'fx-builder' ),
                            'callback' => function () use ( $index ) {
                                self::column_settings( $index );
                            },
                        ]
                    );
                    ?>
                </div>
            </div><!-- .fxb-col-menu -->

            <div class="fxb-col-content">{{{ (data.col_html && data.col_html['<?php echo esc_attr( $field ); ?>']) ? data.col_html['<?php echo esc_attr( $field ); ?>'] : '' }}}</div><!-- .fxb-col-content -->

            <div class="fxb-add-item fxb-link" style="color:var(--fxb-accent-color)">
                <span><?php esc_attr_e( 'Add Item', 'fx-builder' ); ?></span>
            </div><!-- .fxb-add-item -->

        </div><!-- .fxb-col -->
        <?php
    }

    /**
     * Load builder meta for a post.
     */
    public static function get_post_builder_data( $post_id, $sanitize = false ) {
        $row_ids_raw = get_post_meta( $post_id, '_fxb_row_ids', true );
        $rows_raw    = get_post_meta( $post_id, '_fxb_rows', true );
        $items_raw   = get_post_meta( $post_id, '_fxb_items', true );

        if ( $sanitize ) {
            return [
                'row_ids' => Sanitize::ids( $row_ids_raw ),
                'rows'    => Sanitize::rows_data( is_array( $rows_raw ) ? $rows_raw : [] ),
                'items'   => Sanitize::items_data( is_array( $items_raw ) ? $items_raw : [] ),
            ];
        }

        return [
            'row_ids' => is_string( $row_ids_raw ) ? $row_ids_raw : '',
            'rows'    => is_array( $rows_raw ) ? $rows_raw : [],
            'items'   => is_array( $items_raw ) ? $items_raw : [],
        ];
    }

    /**
     * Walk rows, columns, and items in builder order.
     */
    private static function walk_builder_items( string $row_ids, array $rows_data, array $items_data, callable $callback ): void {
        foreach ( explode( ',', $row_ids ) as $row_id ) {
            if ( ! isset( $rows_data[ $row_id ] ) ) {
                continue;
            }
            foreach ( range( 1, (int) $rows_data[ $row_id ]['col_num'] ) as $k ) {
                foreach ( explode( ',', (string) $rows_data[ $row_id ][ 'col_' . $k ] ) as $item_id ) {
                    $item_id = trim( $item_id );
                    if ( $item_id === '' ) {
                        continue;
                    }
                    $callback( $row_id, $k, $item_id, $rows_data, $items_data );
                }
            }
        }
    }

    /**
     * Build post_content string from builder data arrays.
     */
    public static function content_raw_from_data( string $row_ids, array $rows_data, array $items_data ) {
        if ( ! $row_ids || ! $rows_data ) {
            return false;
        }

        $content = '';
        self::walk_builder_items(
            $row_ids,
            $rows_data,
            $items_data,
            function ( $row_id, $k, $item_id, $rows_data, $items_data ) use ( &$content ) {
                unset( $row_id, $k, $rows_data );
                if ( ! empty( $items_data[ $item_id ]['content'] ) ) {
                    $content .= $items_data[ $item_id ]['content'] . "\r\n\r\n";
                }
            }
        );

        return $content;
    }

    /**
     * Format Post Builder Data To Single String
     * This is the builder data without div wrapper
     */
    public static function content_raw( $post_id ) {
        $data = self::get_post_builder_data( $post_id );
        if ( ! $data['row_ids'] || ! $data['rows'] ) {
            return false;
        }

        $content = self::content_raw_from_data( $data['row_ids'], $data['rows'], $data['items'] );
        return apply_filters( 'fxb_content_raw', $content, $post_id, $data['row_ids'], $data['rows'], $data['items'] );
    }

    /**
     * Format Post Builder Data To Single String
     * This will format FX Builder data to content (single string)
     */
    public static function content( $post_id ) {
        $data       = self::get_post_builder_data( $post_id );
        $row_ids    = $data['row_ids'];
        $rows_data  = $data['rows'];
        $items_data = $data['items'];
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
                    if (
                        isset( $rows_data[ $row_id ]['row_html_height'], $rows_data[ $row_id ]['row_html_height_unit'] )
                        && $rows_data[ $row_id ]['row_html_height'] !== ''
                        && $rows_data[ $row_id ]['row_html_height_unit'] !== ''
                    ) {
                        $row_html_height = 'height: ' . $rows_data[ $row_id ]['row_html_height'] . $rows_data[ $row_id ]['row_html_height_unit'] . '; overflow: hidden;';
                    }

                    $row_html_class[] = $row_html_width;

                    if (
                        isset( $rows_data[ $row_id ]['row_html_width'], $rows_data[ $row_id ]['row_content_page_width'] )
                        && 'fullwidth' === $rows_data[ $row_id ]['row_html_width']
                        && '1' === $rows_data[ $row_id ]['row_content_page_width']
                    ) {
                        $row_html_class[] = 'fxb-content-contained';
                    }

                    if ( isset( $rows_data[ $row_id ]['row_no_mb'] ) && '1' === $rows_data[ $row_id ]['row_no_mb'] ) {
                        $row_html_class[] = 'fxb-row-no-mb';
                    }

                    /* ID */
                    $row_html_class[] = "fxb-row-{$row_id}";

                    /* Layout */
                    $row_html_class[] = "fxb-row-layout-{$rows_data[$row_id]['layout']}";

                    $row_html_class = implode( ' ', array_filter( $row_html_class ) );

                    $row_column_align = $rows_data[ $row_id ]['row_column_align'] ? $rows_data[ $row_id ]['row_column_align'] : 'start';
                    $row_column_gap   = $rows_data[ $row_id ]['row_column_gap'] ? $rows_data[ $row_id ]['row_column_gap'] . $rows_data[ $row_id ]['row_column_gap_unit'] : '2em';

                    $row_style_vars = '';
                    if ( ! empty( $rows_data[ $row_id ]['row_bg_color'] ) ) {
                        $row_style_vars .= '--fxb-row-bg-color:' . esc_attr( $rows_data[ $row_id ]['row_bg_color'] ) . ';';
                    }
                    if ( ! empty( $rows_data[ $row_id ]['row_bg_image'] ) ) {
                        $row_style_vars .= "--fxb-row-bg-image:url('" . esc_url( $rows_data[ $row_id ]['row_bg_image'] ) . "');";
                    }
                    if ( ! empty( $rows_data[ $row_id ]['row_col_padding'] ) && ! empty( $rows_data[ $row_id ]['row_col_padding_unit'] ) ) {
                        $row_style_vars .= '--fxb-row-col-padding:' . esc_attr( $rows_data[ $row_id ]['row_col_padding'] . $rows_data[ $row_id ]['row_col_padding_unit'] ) . ';';
                    }
                    ?>

                    <div id="<?php echo esc_attr( $row_html_id ); ?>" class="<?php echo esc_attr( $row_html_class ); ?>" data-index="<?php echo intval( $rows_data[ $row_id ]['index'] ); ?>" data-layout="<?php echo esc_attr( $rows_data[ $row_id ]['layout'] ); ?>"<?php echo $row_style_vars ? ' style="' . esc_attr( $row_style_vars ) . '"' : ''; ?>>

                        <div class="fxb-wrap fxb-col-align-<?php echo esc_attr( $row_column_align ); ?>" style="gap: var(--fxb-template-gap, <?php echo esc_attr( $row_column_gap ); ?>); <?php echo esc_attr( $row_html_height ); ?>">

                            <?php
                            $cols = range( 1, $rows_data[ $row_id ]['col_num'] );
                            foreach ( $cols as $k ) {
                                $items         = $rows_data[ $row_id ][ 'col_' . $k ];
                                $items         = explode( ',', $items );
                                $col_bg_key    = 'col_' . $k . '_bg_color';
                                $col_style_vars = '';
                                if ( ! empty( $rows_data[ $row_id ][ $col_bg_key ] ) ) {
                                    $col_style_vars = '--fxb-col-bg-color:' . esc_attr( $rows_data[ $row_id ][ $col_bg_key ] ) . ';';
                                }
                                ?>
                                <div class="fxb-col-<?php echo intval( $k ); ?> fxb-col"<?php echo $col_style_vars ? ' style="' . esc_attr( $col_style_vars ) . '"' : ''; ?>>
                                    <div class="fxb-wrap">

                                        <?php foreach ( $items as $item_id ) { ?>
                                            <?php if ( isset( $items_data[ $item_id ] ) ) { ?>

                                                <div id="fxb-item-<?php echo esc_attr( $item_id ); ?>" class="fxb-item">
                                                    <div class="fxb-wrap">
                                                        <?php echo wpautop( $items_data[ $item_id ]['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- content is already sanitized ?>
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
        $layouts = self::layouts();
        return $layouts[ $layout ]['cols'] ?? 1;
    }
} // end class
