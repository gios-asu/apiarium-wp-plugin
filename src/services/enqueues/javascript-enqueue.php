<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Apiarium\Services\Page_Templates;

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
    $this->version     = '0.1.2';
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
    if ( is_page_template ( Page_Templates::TEMPLATE_NAME ) ) {
      wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js', array(), '1.12.4', true );
      wp_enqueue_script( 'bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), '3.3.7', true );
      wp_enqueue_script( $this->plugin_slug, $this->javascript, array( 'jquery', 'bootstrap' ), $this->version, true );
      wp_localize_script(
          $this->plugin_slug,
          'apiarium',
          array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'apiarium-twitter' ),
          )
      );
    }
  }
}
