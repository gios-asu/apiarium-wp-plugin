<?php

namespace Apiarium\Tests;

use Apiarium\Factories\Overlay_Slide_Factory;
use Apiarium\Models\Feed_item;

/**
 * Test the Newspaper Slide Factory
 *
 * @group factory
 */
class Overlay_Slide_Factory_Test extends \PHPUnit_Framework_TestCase {
  protected $heading;
  protected $text;
  protected $image;
  protected $factory;

  function setUp() {
    $this->heading = 'wow';
    $this->text    = 'oh caption, my caption';
    $this->image   = 'my image';

    $this->factory = new Overlay_Slide_Factory();
  }

  function test_empty_newspaper_slide_contains_markup() {
    $content = $this->factory->build();

    $this->assertContains( 'carousel-caption', $content );
  }

  function test_empty_newspaper_slide_contains_added_values() {
    $this->factory->add_heading( $this->heading );
    $this->factory->add_text( $this->text );
    $this->factory->add_image( $this->image );
    $content = $this->factory->build();

    $this->assertContains( 'carousel-caption', $content );
    $this->assertContains( 'wow', $content );
    $this->assertContains( 'oh caption, my caption', $content );
    $this->assertContains( 'my image', $content );
  }
}