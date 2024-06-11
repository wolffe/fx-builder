<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

delete_option( 'fx-builder_post_types' );
delete_option( 'fx-builder_welcome' );
