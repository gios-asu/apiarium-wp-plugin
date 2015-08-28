<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Honeycomb\Facades\Wordpress_Rss_Facade;
use Apiarium\Factories\Rss_Carousel_Factory;
use Apiarium\Factories\Overlay_Slide_Factory;
use Apiarium\Factories\Newspaper_Slide_Factory;

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
    $atts = shortcode_atts(
        array(
            'limit'   => 15,
            'content' => 'image,caption,heading',
            'layout'  => 'overlay'
        ),
        $atts,
        'apiarium-slider'
    );

    $feed_urls = explode( "\n", $content );
    $feed_urls = $this->clean_urls( $feed_urls );

    $carousel_factory = new Rss_Carousel_Factory(
        $feed_urls,
        new Wordpress_Rss_Facade()
    );

    $this->set_content_to_include( $carousel_factory, $atts['content'] );

    $carousel_factory->set_slide_factory(
        $this->get_slide_factory( $atts['layout'] )
    );

    $carousel_factory->set_limit( $atts['limit'] );

    $html = $carousel_factory->build();

    return "<div class='apiarium__slider'>{$html}</div>";
  }

  private function set_content_to_include( &$factory, $content_string ) {
    $content_parts = explode( ',', $content_string );

    foreach ( $content_parts as $part ) {
      switch ( $part ) {
      case 'heading':
        $factory->set_include_heading( true );
        break;
      case 'caption':
        $factory->set_include_caption( true );
        break;
      case 'image':
        $factory->set_include_image( true );
        break;
      }
    }
  }

  private function get_slide_factory( $layout_type ) {
    switch( $layout_type ) {
    case 'newspaper':
      return Newspaper_Slide_Factory::class;
    case 'overlay':
    default:
      return Overlay_Slide_Factory::class;
    }
  }

  private function clean_urls( $urls ) {
    $cleaned = [];

    foreach ( $urls as $url ) {
      $url = trim( $url );
      if ( ! empty( $url ) ) {
        // If the url begins with a slash, than it is absolute
        // to the current blog
        if ( strpos( $url, '/' ) === 0 ) {
          $url = get_site_url() . $url;
        }

        $cleaned[] = $url;
      }
    }

    return $cleaned;
  }
}
