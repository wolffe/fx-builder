<?php
/**
 * Setup Settings NameSpace
 * @since 1.0.0
 **/
namespace fx_builder\settings;

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( __NAMESPACE__ . '\URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( __NAMESPACE__ . '\PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( __NAMESPACE__ . '\VERSION', $version );

require_once PATH . 'class-settings.php';
