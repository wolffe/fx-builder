<?php
/**
 * Setup Plugin
 * @since 1.0.0
 **/
namespace fx_builder;

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( __NAMESPACE__ . '\URI', $uri );
define( __NAMESPACE__ . '\PATH', $path );
define( __NAMESPACE__ . '\FILE', $file );
define( __NAMESPACE__ . '\PLUGIN', $plugin );
define( __NAMESPACE__ . '\VERSION', $version );

require_once PATH . 'includes/settings/setup.php';
require_once PATH . 'includes/builder/setup.php';
