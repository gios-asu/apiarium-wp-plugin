<?php

namespace Apiarium\Factories;

use Nectary\Factories\Html_Carousel_Factory;
use Nectary\Factories\Html_Slide_Factory;
use Nectary\Facades\Rss_Facade;
use Apiarium\Services\Parser_Register;

class Rss_Carousel_Factory extends Html_Carousel_Factory {
  private $rss_feed_urls;
  private $rss_facade;
  private $limit;
  private $slide_factory_class;
  private $include_heading;
  private $include_caption;
  private $include_image;

  public function __construct( $rss_feed_urls, Rss_Facade $rss_facade ) {
    $this->urls       = $rss_feed_urls;
    $this->rss_facade = $rss_facade;
    $this->limit      = 15;

    $this->slide_factory_class = Html_Slide_Factory::class;

    $this->include_heading  = false;
    $this->include_caption = false;
    $this->include_image   = false;
  }

  public function set_slide_factory( $slide_factory_class ) {
    $this->slide_factory_class = $slide_factory_class;
  }

  public function set_include_heading( $include = true ) {
    $this->include_heading = $include;
  }

  public function set_include_caption( $include = true ) {
    $this->include_caption = $include;
  }

  public function set_include_image( $include = true ) {
    $this->include_image = $include;
  }

  public function set_limit( $limit ) {
    $this->limit = $limit;
  }

  public function build() {
    $items    = $this->get_items();
    $slides   = $this->build_slides( $items );
    $carousel = $this->build_carousel( $slides );

    return $carousel;
  }

  private function get_items() {
    $feeds = $this->get_feeds();

    $feed  = $this->rss_facade->merge_feeds(
        $feeds,
        array(
          'unique' => true,
          'look_at' => 'title',
        )
    );

    $feed->sort_by_date( 'desc' );
    $items = $feed->get_items();
    $items = array_slice( $items, 0, $this->limit );

    return $items;
  }

  private function get_feeds() {
    $feeds = [];

    foreach ( $this->urls as $url ) {
      $feed = $this->rss_facade->get_feed( $url );

      try {
        $feed->retrieve_items();
        $feeds[] = $feed;
      } catch ( Exception $e ) {
        error_log( 'Could not load RSS Feed' );
      }
    }

    return $feeds;
  }

  private function build_slides( $items ) {
    $slides = [];

    $is_active = true;

    foreach( $items as $item ) {
      $slides[] = $this->build_slide( $item, $is_active );

      if ( $is_active ) {
        $is_active = false;
      }
    }

    return $slides;
  }

  private function build_slide( $item, $is_active = false ) {
    $slide = new $this->slide_factory_class();
    $data  = Parser_Register::parse( $item );

    if ( $is_active ) {
      $slide->is_active();  
    }
    
    if ( $this->include_heading ) {
      $slide->add_heading(
          $data['title']
      );
    }
    
    if ( $this->include_caption ) {
      $slide->add_text(
          $data['description']
      );
    }

    if ( $this->include_image ) {
      $slide->add_image(
          $data['image']
      );  
    }

    return $slide->build();
  }

  private function build_carousel( $slides ) {
    $carousel = new Html_Carousel_Factory();

    foreach ( $slides as $slide ) {
      $carousel->add_slide( $slide );
    }

    return $carousel->build();
  }
}
