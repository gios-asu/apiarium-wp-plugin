<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Honeycomb\Facades\Wordpress_Rss_Facade;
use Apiarium\Factories\Apiarium_Carousel_Factory;
use Apiarium\Factories\Html_Slide_Factory;
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
    $this->add_filter( 'wp_feed_cache_transient_lifetime', $this, 'lifetime' );
    $this->add_action( 'wp_feed_options', $this, 'do_not_cache_feeds' );
  }

  /**
   * No feed caching!
   *
   * If a feed fails (such as the Yahoo Weather API, which may fail periodically due to heavy traffic),
   * SimplePie (which is what WordPress uses to fetch feeds) will cache the failed feed using
   * WordPress transients. Basically, if a feed fails, then that failure is cached.
   *
   * Obviously, we don't want to cache failed feeds, but until we find a way to only stop failed
   * feeds from caching, all feed caching will be disabled by returning a lifetime of 0 for the
   * transients.
   */
  public function lifetime( $a ) {
    return 0;
  }

  public function do_not_cache_feeds( &$feed ) {
    $feed->enable_cache( false );
  }

  public function display_slider( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
            'limit'   => 15,
            'content' => 'image,caption,heading',
            'layout'  => 'overlay',
            'interval' => null
        ),
        $atts,
        'apiarium-slider'
    );

    $feed_urls = explode( "\n", $content );
    $feed_urls = $this->clean_urls( $feed_urls );

    $feed_items = Parser_Register::parse( $feed_urls );

    $carousel_factory = new Apiarium_Carousel_Factory(
        $feed_items
    );

    $this->set_content_to_include( $carousel_factory, $atts['content'] );

    $carousel_factory->set_slide_factory(
        $this->get_slide_factory( $atts['layout'] )
    );

    if( ! empty( $atts['interval'] ) ) {
      $carousel_factory->set_slide_interval( $atts['interval'] );
    }

    // TODO pass the limit to the Parser_Register
    // $carousel_factory->set_limit( $atts['limit'] );

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
      case 'html':
        $factory->set_include_html( true );
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
      $url = trim( str_replace( array( "\n\r", "\n", "\r", '<br />', '<br/>' ), '', $url ) );

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
