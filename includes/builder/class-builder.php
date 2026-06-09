<?php
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}

new Builder();

/**
 * Builder
 * @since 1.0.0
 */
class Builder {

    public function __construct() {
        /* Add it after editor in edit screen */
        add_action( 'edit_form_after_editor', [ $this, 'form' ] );

        /* Save Builder Data */
        add_action( 'save_post', [ $this, 'save' ], 10, 2 );

        /* Scripts */
        add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ], 99 );
    }


    /**
     * Builder Form
     */
    public function form( $post ) {
        if ( ! post_type_supports( $post->post_type, 'fx_builder' ) ) {
            return;
        }
        $post_id = $post->ID;
        ?>

        <div id="fxb-wrapper">

            <div class="fxb-modal-overlay" style="display:none;"></div>

            <?php Functions::add_row_field( 'prepend' ); ?>

            <div id="fxb">
            </div><!-- #fxb -->

            <?php Functions::add_row_field( 'append' ); ?>

            <input type="hidden" name="_fxb_row_ids" value="<?php echo esc_attr( get_post_meta( $post_id, '_fxb_row_ids', true ) ); ?>" autocomplete="off">
            <input type="hidden" name="_fxb_db_version" value="<?php echo esc_attr( VERSION ); ?>" autocomplete="off">
            <?php wp_nonce_field( __FILE__, 'fxb_nonce' ); // create nonce ?>

            <?php /* Load Custom Editor */ ?>

            <?php
            Functions::render_settings(
                [
                    'id'       => 'fxb-editor', // data-target
                    'title'    => __( 'Edit Content', 'fx-builder' ),
                    'width'    => '800px',
                    'callback' => function () {

                        wp_editor(
                            '',
                            'fxb_editor',
                            [
                                'tinymce'       => [
                                    'wp_autoresize_on' => false,
                                    'resize'           => false,
                                ],
                                'editor_height' => 300,
                            ]
                        );
                    },
                ]
            );
            ?>

            <div id="fxb-templates">
                <?php require_once PATH . 'templates/tmpl-row.php'; ?>
                <?php require_once PATH . 'templates/tmpl-item.php'; ?>
            </div>
            <div id="fxb-template-loader">
                <?php $this->load_templates( $post_id ); ?>
            </div>

        </div><!-- #fxb-wrapper -->
        <?php
    }


    /**
     * Load Template
     */
    public function load_templates( $post_id ) {

        $data = Functions::get_post_builder_data( $post_id );

        // Provide a bootstrap payload. Rendering is handled by FXB core on DOMContentLoaded.
        $payload = [
            'row_ids' => $data['row_ids'],
            'rows'    => $data['rows'],
            'items'   => $data['items'],
        ];

        echo '<script type="text/javascript">window.FXB_BOOTSTRAP = ' . wp_json_encode( $payload ) . ';</script>';
    }


    /**
     * Save FX Builder Data
     * @since 1.0.0
     */
    public function save( $post_id, $post ) {

        /* Prepare
        ------------------------------------------ */
        $request = stripslashes_deep( $_POST );
        if ( ! isset( $request['fxb_nonce'] ) || ! wp_verify_nonce( $request['fxb_nonce'], __FILE__ ) ) {
            return $post_id;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        $post_type = get_post_type_object( $post->post_type );
        if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }
        $wp_preview = isset( $request['wp-preview'] ) ? esc_attr( $request['wp-preview'] ) : false;
        if ( $wp_preview ) {
            return $post_id;
        }

        /* Check Switcher
        ------------------------------------------ */
        $active = isset( $request['_fxb_active'] ) ? $request['_fxb_active'] : false;

        /* FX Builder Active */
        if ( $active ) {
            update_post_meta( $post_id, '_fxb_active', 1 );
        } else {
            /* FX Builder Not Selected: deactivate but preserve row/item data
            so switching back to FX Builder restores the previous layout. */
            delete_post_meta( $post_id, '_fxb_active' );

            return;
        }

        /* FX Builder Data
        ------------------------------------------ */
        $meta_map = [
            '_fxb_db_version' => [ Sanitize::class, 'version' ],
            '_fxb_row_ids'    => [ Sanitize::class, 'ids' ],
            '_fxb_rows'       => [ Sanitize::class, 'rows_data' ],
            '_fxb_items'      => [ Sanitize::class, 'items_data' ],
        ];
        foreach ( $meta_map as $key => $sanitizer ) {
            $value = isset( $request[ $key ] ) ? call_user_func( $sanitizer, $request[ $key ] ) : null;
            if ( $value ) {
                update_post_meta( $post_id, $key, $value );
            } else {
                delete_post_meta( $post_id, $key );
            }
        }

        /* Content Data
        ------------------------------------------ */
        $pb_content = Functions::content_raw( $post_id );
        $this_post  = [
            'ID'           => $post_id,
            'post_content' => sanitize_post_field( 'post_content', $pb_content, $post_id, 'db' ),
        ];
        /**
         * Prevent infinite loop.
         * @link https://developer.wordpress.org/reference/functions/wp_update_post/
         */
        remove_action( 'save_post', [ $this, __FUNCTION__ ] );
        wp_update_post( $this_post );
        add_action( 'save_post', [ $this, __FUNCTION__ ], 10, 2 );
    }


    /**
     * Admin Scripts
     * @since 1.0.0
     */
    public function scripts( $hook_suffix ) {
        global $post_type;
        if ( ! isset( $post_type ) || ! post_type_supports( $post_type, 'fx_builder' ) ) {
            return;
        }

        /* In Page Edit Screen */
        if ( in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] ) ) {

            /* Enqueue CSS */
            wp_enqueue_style( 'fx-builder', URI . 'assets/page-builder.css', [], VERSION );
            wp_enqueue_style( 'fx-builder-akar-icons', URI . 'assets/fonts/akar-icons/akar-icons.min.css', [], VERSION );

            // Core utilities / namespace. Depends on wp-util (wp.template()).
            wp_enqueue_script( 'fx-builder-core', URI . 'assets/fxb-core.js', [ 'wp-util' ], VERSION, true );

            /* Enqueue JS: ROW */
            wp_enqueue_script( 'fx-builder-row', URI . 'assets/page-builder-row.js', [ 'fx-builder-core', 'sortable-js', 'wp-util' ], VERSION, true );
            /* Enqueue JS: COLUMN */
            wp_enqueue_script( 'fx-builder-col', URI . 'assets/page-builder-col.js', [ 'fx-builder-core' ], VERSION, true );
            /* Enqueue JS: ITEM */
            wp_enqueue_script( 'fx-builder-item', URI . 'assets/page-builder-item.js', [ 'fx-builder-core', 'sortable-js', 'wp-util' ], VERSION, true );
            $ajax_data = [
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'ajax_nonce' => wp_create_nonce( 'fxb_ajax_nonce' ),
            ];
            wp_localize_script( 'fx-builder-item', 'fxb_ajax', $ajax_data );
        }
    }
}
