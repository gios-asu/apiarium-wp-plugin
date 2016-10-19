<?php
/*
Plugin Name: Apiarium WordPress Plugin
Plugin URI: http://github.com/gios-asu/apiarium-wp-plugin
Description: Television Screen and Kiosk WordPress Plugin
Version: 2.2.1
Author: The Global Institute of Sustainability
License: Copyright 2015

GitHub Plugin URI: https://github.com/gios-asu/apiarium-wp-plugin
GitHub Branch: prod
*/

if ( ! function_exists( 'add_filter' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  define( 'APIARIUM_WP_VERSION', '1.1' );
}

if ( ! defined( 'APIARIUM_REGISTRY' ) ) {
  define( 'APIARIUM_REGISTRY', __DIR__ . '/src/registry' );
}

require __DIR__ . '/vendor/autoload.php';

$registry = new \Honeycomb\Services\Register();
$registry->register(
    require APIARIUM_REGISTRY . '/wordpress-registry.php'
);

$parser_registry = new \Apiarium\Services\Parser_Register();
$parser_registry->register(
    require APIARIUM_REGISTRY . '/parser-registry.php'
);
