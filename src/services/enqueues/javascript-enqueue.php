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
class Javascript_Enqueue extends Hook {
  // TODO inject the version
  public function __construct() {
    $this->plugin_slug = 'apiarium-javascript-styles';
    $this->version     = '0.1';
    $this->javascript  = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'public/javascripts/main.js';

    $this->define_hooks();
  }

  /**
   * Setup WordPress hooks for filters and actions.
   * @override
   */
  public function define_hooks() {
    $this->add_action( 'wp_enqueue_scripts',  $this, 'enqueue_scripts', 99 );
  }
  /**
  * Enqueue styles.
  */
  function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_slug, $this->javascript, '', $this->version );
  }
}
