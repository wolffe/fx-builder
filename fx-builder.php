<?php
/**
 * Plugin Name: FX Builder
 * Plugin URI: https://getbutterfly.com/classicpress-plugins/fx-builder/
 * Description: A simple page builder plugin. The one you can actually use.
 * Version: 1.3.1
 * Requires PHP: 7.0
 * Requires CP: 2.0
 * Author: Ciprian Popescu
 * Author URI: https://getbutterfly.com/
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: fx-builder
 * Domain Path: /languages/
 *
 * @author Ciprian Popescu <ciprian@getbutterfly.com>
 * @copyright Copyright (c) 2024, getButterfly
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'FX_BUILDER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'FX_BUILDER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FX_BUILDER_FILE', __FILE__ );
define( 'FX_BUILDER_PLUGIN', plugin_basename( __FILE__ ) );
define( 'FX_BUILDER_VERSION', '1.3.1' );

require FX_BUILDER_PATH . '/includes/updater.php';

add_action( 'plugins_loaded', 'fx_builder_init' );

function fx_builder_init() {
    $uri     = FX_BUILDER_URI;
    $path    = FX_BUILDER_PATH;
    $file    = FX_BUILDER_FILE;
    $plugin  = FX_BUILDER_PLUGIN;
    $version = FX_BUILDER_VERSION;

    load_plugin_textdomain( dirname( $plugin ), false, dirname( $plugin ) . '/languages/' );

    require_once $path . 'includes/setup.php';
    require_once $path . 'includes/editor.php';
}


/*
function fxb_wp_editor_to_textarea( $settings ) {
    $settings['tinymce']       = false;
    $settings['quicktags']     = false;
    $settings['media_buttons'] = false;

    return $settings;
}
add_filter( 'wp_editor_settings', 'fxb_wp_editor_to_textarea' );
/**/
