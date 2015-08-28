<?php

namespace Apiarium\Factories;

use Nectary\Factories\Html_Slide_Factory;

class Overlay_Slide_Factory extends Html_Slide_Factory {
  private $image;
  private $caption;

  public function add_heading( $html, $options = [] ) {
    $this->caption .= $this->with_heading( $html, $options );
  }

  public function add_text( $html, $options = [] ) {
    $this->caption .= $this->with_text( $html, $options );
  }

  public function add_image( $html, $options = [] ) {
    $this->image = $this->with_image( $html, $options );
  }

  public function build() {
    $this->html = "{$this->image}<div class='carousel-caption'>{$this->caption}</div>";
    return parent::build();
  }
}
