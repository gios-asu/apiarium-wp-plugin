<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

/**
 * Enqueue any CSS for the plugin here
 */
class Css_Enqueue extends Hook {
  // TODO inject the version
  public function __construct() {
    $this->plugin_slug = 'apiarium-css-styles';
    $this->version     = '0.1';
    $this->css         = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'public/css/style.css';
    $this->green_theme = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'public/css/green-theme.css';
    $this->blue_theme  = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'public/css/blue-theme.css';

    $this->define_hooks();
  }

  /**
   * Setup WordPress hooks for filters and actions.
   * @override
   */
  public function define_hooks() {
    $this->add_action( 'wp_enqueue_scripts',  $this, 'enqueue_styles', 99 );
  }
  /**
  * Enqueue styles.
  */
  function enqueue_styles() {
    wp_enqueue_style( $this->plugin_slug, $this->css, '', $this->version );
    wp_enqueue_style( $this->plugin_slug . 'green', $this->green_theme, '', $this->version );
    wp_enqueue_style( $this->plugin_slug . 'blue', $this->blue_theme, '', $this->version );
  }
}
