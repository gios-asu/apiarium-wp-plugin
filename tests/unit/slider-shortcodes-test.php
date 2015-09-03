<?php

namespace Apiarium\Tests;

/**
 * Test the slider shortcode
 *
 * @group service
 * @group shortcode
 */
class Slider_Shortcode_Test extends \PHPUnit_Framework_TestCase {
  function test_shortcode_exists() {
    $this->assertTrue( shortcode_exists( 'display-slider' ) );
  }
}
