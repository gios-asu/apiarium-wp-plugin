<?php

namespace Apiarium\Factories;

use Nectary\Factories\Html_Slide_Factory;

/**
 * Create a slide for a carousel that has a headline
 * and text on top of an image
 */
class Overlay_Slide_Factory extends Html_Slide_Factory {
  private $image;
  private $caption;
  protected $html;

  /**
   * @override
   */
  public function add_heading( $html, $options = [] ) {
    $this->caption .= $this->with_heading( $html, $options );
  }

  /**
   * @override
   */
  public function add_text( $html, $options = [] ) {
    $this->caption .= $this->with_text( $html, $options );
  }

  /**
   * @override
   */
  public function add_image( $html, $options = [] ) {
    $this->image = $this->with_image( $html, $options );
  }

  public function add_div( $html, $options = [] ) {
    $this->html = $this->with_div( $html, $options );
  }

  /**
   * @override
   */
  public function build() {
    if ( empty( $this->html ) ) {
      $this->html = "{$this->image}<div class='carousel-caption'>{$this->caption}</div>";
    }

    return parent::build();
  }
}
