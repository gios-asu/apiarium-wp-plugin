<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Slider_Shortcodes extends Hook {
   public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_shortcode( 'display-slider', $this, 'display_slider' );
  }

  public function display_slider( $atts, $content = '' ) {
    $feed_urls = explode( "\n", $content );

    var_dump( $feed_urls );

    $slider_factory = new Slider_Factory(
        $feed_urls
    );

    $html = $factory->build();

    return "<div class='aparium__slider'>{$html}</div>";
  }
}