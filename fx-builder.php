<?php
/**
 * Plugin Name: FX Builder
 * Plugin URI: https://getbutterfly.com/classicpress-plugins/fx-builder/
 * Description: A simple page builder plugin. The one you can actually use.
 * Version: 1.1.1
 * Requires PHP: 7.2
 * Requires CP: 2.0
 * Author: Ciprian Popescu
 * Author URI: https://getbutterfly.com/classicpress-plugins/fx-builder/
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
define( 'FX_BUILDER_VERSION', '1.1.1' );

include FX_BUILDER_PATH . '/includes/updater.php';

add_action( 'plugins_loaded', 'fx_builder_init' );

function fx_builder_init() {
    $uri     = FX_BUILDER_URI;
    $path    = FX_BUILDER_PATH;
    $file    = FX_BUILDER_FILE;
    $plugin  = FX_BUILDER_PLUGIN;
    $version = FX_BUILDER_VERSION;

    require_once $path . 'includes/prepare.php';
    if ( ! $sys_req->check() ) {
        return;
    }

    require_once $path . 'includes/setup.php';
}
