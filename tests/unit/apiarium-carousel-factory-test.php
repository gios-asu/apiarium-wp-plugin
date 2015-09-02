<?php

namespace Apiarium\Tests;

use Apiarium\Factories\Apiarium_Carousel_Factory;
use Apiarium\Models\Feed_Item;

/**
 * Test the Apiarium Carousel Factory class in the framework
 *
 * @group factory
 */
class Apiarium_Carousel_Factory_Test extends \PHPUnit_Framework_TestCase {
  protected $feed_items;

  function setUp() {
    $this->feed_items = [];

    foreach( range( 1, 3 ) as $_ ) {
      $feed_item = new Feed_Item();
      $feed_item->title = 'wow';

      $this->feed_items[] = $feed_item;
    }
  }

  function test_simple_apiarium_carousel_factory() {
    $factory = new Apiarium_Carousel_Factory( $this->feed_items );
    $factory->set_include_heading();
    $html = $factory->build();

    $this->assertContains( 'wow', $html );
  }

  function test_use_injected_slide_factory() {
    // TODO
  }
}
