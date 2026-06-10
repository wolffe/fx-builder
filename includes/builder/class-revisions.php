<?php
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}
new Revisions();

/**
 * Revisions
 * @since 1.0.0
 */
class Revisions {

    public function __construct() {

        /* Save FX Builder Revision */
        add_action( 'save_post', [ $this, 'save_revision' ], 11, 2 );

        /* Restore Post Revisions */
        add_action( 'wp_restore_post_revision', [ $this, 'restore_revision' ], 10, 2 );
    }

    const REVISION_META_KEYS = [
        '_fxb_db_version',
        '_fxb_row_ids',
        '_fxb_rows',
        '_fxb_items',
        '_fxb_custom_css',
        '_fxb_custom_css_disable',
    ];

    /**
     * Save Revision
     * Simply Clone To Revision If FX Builder Data Exists In Post
     * @link https://johnblackbourn.com/post-meta-revisions-wordpress
     */
    public function save_revision( $post_id, $post ) {
        $parent_id = wp_is_post_revision( $post_id );
        if ( ! $parent_id ) {
            return;
        }

        $active = get_post_meta( $parent_id, '_fxb_active', true );
        if ( $active ) {
            add_metadata( 'post', $post_id, '_fxb_active', $active );
        }

        foreach ( self::REVISION_META_KEYS as $key ) {
            $value = get_post_meta( $parent_id, $key, true );
            if ( false !== $value ) {
                add_metadata( 'post', $post_id, $key, $value );
            }
        }
    }


    /**
     * Restore Revisions
     * @link https://johnblackbourn.com/post-meta-revisions-wordpress
     */
    public function restore_revision( $post_id, $revision_id ) {
        $active = get_metadata( 'post', $revision_id, '_fxb_active', true );
        if ( $active ) {
            update_post_meta( $post_id, '_fxb_active', $active );
        } else {
            delete_post_meta( $post_id, '_fxb_active' );
        }

        foreach ( self::REVISION_META_KEYS as $key ) {
            $value = get_metadata( 'post', $revision_id, $key, true );
            if ( false !== $value ) {
                update_post_meta( $post_id, $key, $value );
            } else {
                delete_post_meta( $post_id, $key );
            }
        }
    }
}
