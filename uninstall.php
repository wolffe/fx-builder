<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Options only; builder post meta (_fxb_*) is kept so the layouts survive a reinstall.
foreach ( [ 'fx-builder_post_types', 'fxb_enabled_modules', 'fxb_breakpoint_small', 'fxb_breakpoint_medium', 'fxb_hide_empty_paragraphs', 'fxb_google_fonts', 'fxb_bunny_fonts', 'fxb_google_fonts_api' ] as $fxb_option ) {
    delete_option( $fxb_option );
}
