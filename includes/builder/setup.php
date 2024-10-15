<?php
/**
 * Setup Builder NameSpace
 * @since 1.0.0
 **/
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}

/* Constants
------------------------------------------ */
define( __NAMESPACE__ . '\URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( __NAMESPACE__ . '\PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( __NAMESPACE__ . '\VERSION', $version );

/* Load Files
------------------------------------------ */
require_once PATH . 'class-sanitize.php';
require_once PATH . 'class-functions.php';
require_once PATH . 'class-switcher.php';
require_once PATH . 'class-custom-css.php';
require_once PATH . 'class-tools.php';
require_once PATH . 'class-builder.php';
require_once PATH . 'class-revisions.php';
require_once PATH . 'class-front.php';
