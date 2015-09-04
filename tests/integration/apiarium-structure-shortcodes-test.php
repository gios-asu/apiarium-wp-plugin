<?php

namespace Apiarium\Tests\Integration;

/**
 * @group shortcode
 * @group integration
 */
class Aparium_Structure_Shortcodes_Test extends \PHPUnit_Framework_TestCase {
  function setup() {
    $this->display_content = '[display theme=green]Test[/display]';
    $this->display_row_content = '[display-row]Test[/display-row]';
    $this->display_row_content_with_size = '[display-row size=2]Test[/display-row]';
    $this->display_column_content = '[display-column]Test[/display-column]';
    $this->display_column_content_with_size = '[display-column size=2]Test[/display-column]';
  }

  function test_display_shortcode_wraps_with_given_theme() {
    $content = do_shortcode( $this->display_content );
    $this->assertContains( "<div class='apiarium__green'>Test</div>", $content );
  }

  function test_display_row_shortcode_wraps_with_flex_row() {
    $content = do_shortcode( $this->display_row_content );

    $this->assertContains( "<div class='apiarium__row' style='flex: 1'>Test</div>", $content );
  }

  function test_display_row_shortcode_wraps_with_flex_row_and_size() {
    $content = do_shortcode( $this->display_row_content_with_size );

    $this->assertContains( "<div class='apiarium__row' style='flex: 2'>Test</div>", $content );
  }

  function test_display_column_shortcode_wraps_with_flex_column() {
    $content = do_shortcode( $this->display_column_content );

    $this->assertContains( "<div class='apiarium__column' style='flex: 1'>Test</div>", $content );
  }

  function test_display_column_shortcode_wraps_with_flex_column_and_size() {
    $content = do_shortcode( $this->display_column_content_with_size );

    $this->assertContains( "<div class='apiarium__column' style='flex: 2'>Test</div>", $content );
  }
}