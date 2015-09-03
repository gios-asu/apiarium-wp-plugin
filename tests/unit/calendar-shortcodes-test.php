<?php

namespace Apiarium\Tests;

/**
 * Test the calendar shortcode 
 *
 * @group service
 * @group shortcode
 */
class Calendar_Shortcode_Test extends \PHPUnit_Framework_TestCase {
  function test_shortcode_exists() {
    $this->assertTrue( shortcode_exists( 'display-calendar' ) );
  }

  function test_shortcode_provides_date() {
    $html = do_shortcode( '[display-calendar]' );

    $current_day   = date( 'd' );
    $current_month = date( 'M' );

    $this->assertContains( $current_day, $html );
    $this->assertContains( $current_month, $html );
  }
}
