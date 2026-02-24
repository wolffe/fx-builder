<?php
namespace fx_builder\builder;
use fx_builder\Functions as Fs;
if ( ! defined( 'WPINC' ) ) {
    die;
}
Tools::get_instance();

/**
 * Tools: Export Import
 * @since 1.0.0
 */
class Tools {

    /**
     * Returns the instance.
     */
    public static function get_instance() {
        static $instance = null;
        if ( is_null( $instance ) ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {

        /* Add CSS Button */
        add_action( 'fxb_switcher_nav', [ $this, 'add_tools_control' ], 11 );

        /* Scripts */
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

        /* Ajax: To JSON */
        add_action( 'wp_ajax_fxb_export_to_json', [ $this, 'ajax_export_to_json' ] );
        add_action( 'wp_ajax_fxb_import_data', [ $this, 'ajax_import_data' ] );
    }

    /**
     * CSS Control
     */
    public function add_tools_control( $post ) {
        $post_id = $post->ID;
        ?>
        <a href="#" id="fxb-nav-tools" class="fxb-nav-tools"><i class="ai-arrow-right-left"></i> <?php esc_attr_e( 'Tools', 'fx-builder' ); ?></a>
        <?php
        Functions::render_settings(
            [
                'id'       => 'fxb-tools', // data-target
                'title'    => __( 'Tools', 'fx-builder' ),
                'width'    => '400px',
                'height'   => '380px',
                'callback' => function () use ( $post_id ) {
                    ?>
                    <ul class="wp-tab-bar">
                        <li id="fxb-export-tab" class="tabs wp-tab-active">
                            <a class="fxb-tools-nav-bar" href="#fxb-export-panel"><?php esc_attr_e( 'Export', 'fx-builder' ); ?></a>
                        </li><!-- .tabs -->
                        <li id="fxb-import-tab" class="tabs">
                            <a class="fxb-tools-nav-bar" href="#fxb-import-panel"><?php esc_attr_e( 'Import', 'fx-builder' ); ?></a>
                        </li><!-- .tabs -->
                    </ul><!-- .wp-tab-bar -->

                    <div id="fxb-export-panel" class="fxb-tools-panel wp-tab-panel" style="display:block;">
                        <textarea autocomplete="off" id="fxb-tools-export-textarea" readonly="readonly" style="display:none;" placeholder="<?php esc_attr_e( 'No Data', 'fx-builder' ); ?>"></textarea>
                        <p><a id="fxb-tools-export-action" href="#" class="button button-primary"><?php esc_attr_e( 'Generate Export Code', 'fx-builder' ); ?></a></p>
                    </div><!-- .wp-tab-panel -->

                    <div id="fxb-import-panel" class="fxb-tools-panel wp-tab-panel" style="display:none;">
                        <textarea autocomplete="off" id="fxb-tools-import-textarea" placeholder="<?php esc_attr_e( 'Paste your FX Builder data here...', 'fx-builder' ); ?>"></textarea>
                        <p><a id="fxb-tools-import-action" href="#" data-confirm="<?php esc_attr_e( 'Are you sure you want to import this new data?', 'fx-builder' ); ?>" data-alert="<?php esc_attr_e( 'Your data is not valid.', 'fx-builder' ); ?>" class="button button-primary disabled"><?php esc_attr_e( 'Import FX Builder Data', 'fx-builder' ); ?></a></p>
                    </div><!-- .wp-tab-panel -->
                    <?php
                },
            ]
        );
        ?>
        <?php
    }

    /**
     * Admin Scripts
     * @since 1.0.0
     */
    public function admin_scripts( $hook_suffix ) {
        global $post_type;
        if ( ! in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] ) ) {
            return false;
        }
        if ( post_type_supports( $post_type, 'editor' ) && post_type_supports( $post_type, 'fx_builder' ) ) {

            /* CSS */
            wp_enqueue_style( 'fx-builder-tools', URI . 'assets/tools.css', [ 'fx-builder' ], VERSION );

            /* JS */
            wp_enqueue_script( 'fx-builder-tools', URI . 'assets/tools.js', [ 'fx-builder-core', 'fx-builder-item', 'fx-builder-row', 'wp-util' ], VERSION, true );
            $ajax_data = [
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'ajax_nonce' => wp_create_nonce( 'fxb_tools_nonce' ),
            ];
            wp_localize_script( 'fx-builder-tools', 'fxb_tools', $ajax_data );
        }
    }

    /**
     * Ajax Export To JSon
     */
    public function ajax_export_to_json() {

        /* Strip Slash */
        $request = stripslashes_deep( $_POST );

        /* Check Ajax */
        check_ajax_referer( 'fxb_tools_nonce', 'nonce' );

        $data = [
            'row_ids' => isset( $request['row_ids'] ) ? $request['row_ids'] : '',
            'rows'    => isset( $request['rows'] ) ? $request['rows'] : [],
            'items'   => isset( $request['items'] ) ? $request['items'] : [],
        ];

        echo wp_json_encode( $data );
        wp_die();
    }

    /**
     * Ajax Import Data
     */
    public function ajax_import_data() {
        $request = stripslashes_deep( $_POST );

        check_ajax_referer( 'fxb_tools_nonce', 'nonce' );

        $data    = isset( $request['data'] ) ? $request['data'] : '';
        $data    = json_decode( $data, true );
        $default = [
            'row_ids' => '',
            'rows'    => [],
            'items'   => [],
        ];
        $data    = wp_parse_args( $data, $default );

        // Sanitize/normalize payload.
        $row_ids    = Sanitize::ids( $data['row_ids'] );
        $rows_data  = Sanitize::rows_data( $data['rows'] );
        $items_data = Sanitize::items_data( $data['items'] );

        // Validate: if row_ids is non-empty, we should have row data for those ids.
        if ( $row_ids ) {
            $rows = array_filter( array_map( 'trim', explode( ',', $row_ids ) ) );
            foreach ( $rows as $row_id ) {
                if ( ! isset( $rows_data[ $row_id ] ) ) {
                    wp_send_json_error(
                        [
                            'message' => __( 'Your data is not valid.', 'fx-builder' ),
                        ]
                    );
                }
            }
        }

        wp_send_json_success(
            [
                'row_ids' => $row_ids,
                'rows'    => $rows_data,
                'items'   => $items_data,
            ]
        );
    }
}
