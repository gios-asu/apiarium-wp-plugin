<?php

namespace Apiarium\Factories;

use Nectary\Factories\Html_Slide_Factory;

class Newspaper_Slide_Factory extends Html_Slide_Factory {
  private $image;
  private $heading;
  private $caption;

  public function add_heading( $html, $options = [] ) {
    $this->heading = $this->with_heading( $html, $options );
  }

  public function add_text( $html, $options = [] ) {
    $this->caption .= $this->with_text( $html, $options );
  }

  public function add_image( $html, $options = [] ) {
    $this->image = $this->with_image( $html, $options );
  }

  public function build() {
    $this->html = "
    {$this->heading}
    <div class='pull-left thumbnail apiarium__newspaper__image' style=''>
      {$this->image}
    </div>
    {$this->caption}
";
    return parent::build();
  }
}
