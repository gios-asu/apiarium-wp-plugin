<?php

namespace Apiarium\Factories;

use Nectary\Factories\Html_Carousel_Factory;
use Nectary\Factories\Html_Slide_Factory;
use Apiarium\Services\Parser_Register;
use Apiarium\Models\Feed_Item;

/**
 * Special Carousel builder for Apiarium
 *
 * This builder knows how to build generic slides for the
 * carousel.
 */
class Apiarium_Carousel_Factory extends Html_Carousel_Factory {
  private $feed_items;
  private $slide_factory_class;
  private $include_heading;
  private $include_caption;
  private $include_image;
  private $slide_interval;


  /**
   * Setup defaults
   *
   * @constructor
   * @param $feed_items Array<Apiarium\Models\Feed_Item>
   */
  public function __construct( $feed_items ) {
    $this->feed_items = $feed_items;

    $this->slide_factory_class = Html_Slide_Factory::class;
    $this->include_heading     = false;
    $this->include_caption     = false;
    $this->include_image       = false;
    $this->include_html        = false;
    $this->slide_interval      = null;
  }

  /**
   * Set the correct slide factory class to use.
   *
   * Defaults to the Html_Slide_Factory, which is not
   * always desired
   *
   * @param $slide_factory_class String
   */
  public function set_slide_factory( $slide_factory_class ) {
    $this->slide_factory_class = $slide_factory_class;
  }

  /**
   * Set whether to include the heading provided by the
   * Feed Items.
   */
  public function set_include_heading( $include = true ) {
    $this->include_heading = $include;
  }

  /**
   * Set whether to include the caption provided by the
   * Feed Items
   */
  public function set_include_caption( $include = true ) {
    $this->include_caption = $include;
  }

  /**
   * Set whether to include the image provided by the
   * Feed Items
   */
  public function set_include_image( $include = true ) {
    $this->include_image = $include;
  }

  /**
   * Set whether to include the description as html
   */
  public function set_include_html( $include = true ) {
    $this->include_html = $include;
  }

  /**
   * Set the number of seconds to give each slide
   */
  public function set_slide_interval( $seconds = 5 ) {
    $this->slide_interval = $seconds * 1000; // Bootstrap expects milliseconds
  }


  /**
   * Create the slider.
   *
   * @return String html
   */
  public function build() {
    $slides   = $this->build_slides();
    $carousel = $this->build_carousel( $slides );

    return $carousel;
  }

  private function build_slides() {
    $slides = [];

    $is_active = true;

    foreach( $this->feed_items as $feed_item ) {
      $slides[] = $this->build_slide( $feed_item, $is_active );

      if ( $is_active ) {
        $is_active = false;
      }
    }

    return $slides;
  }

  private function build_slide( Feed_Item $feed_item, $is_active = false ) {
    $slide = new $this->slide_factory_class();

    if ( $is_active ) {
      $slide->is_active();
    }

    if ( $this->include_heading ) {
      $slide->add_heading(
          $feed_item->title,
          array(
            'level' => 3
          )
      );
    }

    if ( $this->include_caption ) {
      $slide->add_text(
          $feed_item->description
      );
    }

    if ( $this->include_image ) {
      $slide->add_image(
          $feed_item->image
      );
    }

    if ( $this->include_html ) {
      $slide->add_div(
         $feed_item->description
      );
    }

    return $slide->build();
  }

  private function build_carousel( $slides ) {

    $carousel = new Html_Carousel_Factory();

    foreach ( $slides as $slide ) {
      $carousel->add_slide( $slide );
    }

    if( ! empty($this->slide_interval) ) {
      $carousel->add_data_attributes( "data-interval='{$this->slide_interval}' ");
    }

    return $carousel->build();
  }
}
