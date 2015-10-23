<?php

namespace Apiarium\Tests\Integration;

/**
 * @group integration
 */
class Apiarium_Slider_Shortcodes_Test extends \PHPUnit_Framework_TestCase {
  const FLICKR_URL     = 'https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200';
  const FLICKR_URL_ALT = 'https://api.flickr.com/services/feeds/photos_public.gne?format=rss_200';
  const WORDPRESS_URL  = 'https://news.wp.prod.gios.asu.edu/tag/board-letter/feed/';

  function setUp() {
    $this->content = '
      [display-slider]
      [/display-slider]
    ';

    $this->flickr_content      = '[display-slider]' . self::FLICKR_URL . '[/display-slider]';
    $this->flickr_alt_content  = '[display-slider]' . self::FLICKR_URL_ALT . '[/display-slider]';
    $this->flickr_alt_combined = '[display-slider]' . self::FLICKR_URL_ALT . "\n" . self::FLICKR_URL . '[/display-slider]';

    $this->wordpress_content = '[display-slider]' . self::WORDPRESS_URL . '[/display-slider]';
  }

  function test_slider_shortcode_returns_slider() {
    $content = do_shortcode( $this->content );
    $this->assertContains( "<div class='apiarium__slider'>", $content );
    $this->assertContains( '</div>', $content );
  }

  function test_slider_shortcode_works_with_flickr() {
    $content = do_shortcode( $this->flickr_content );
    $this->assertContains( "<div class='apiarium__slider'>", $content );
    $this->assertContains( "<div class='item", $content );
    $this->assertContains( '</div>', $content );
  }

  function test_slider_shortcode_works_with_wordpress() {
    $content = do_shortcode( $this->wordpress_content );
    $this->assertContains( "<div class='apiarium__slider'>", $content );
    $this->assertContains( "<div class='item", $content );
    $this->assertContains( '</div>', $content );

    // TODO additionally, sheck for slides
  }

  function test_slider_shortcode_returns_merged_feed() {
    // TODO
  }

  function test_slider_shortcode_returns_unique_feed() {
    // TODO
  }

  function test_slider_only_displays_title() {
    // TODO
  }

  function test_slider_only_displays_image() {
    // TODO
  }

  function test_slider_only_displays_caption() {
    // TODO
  }

  function test_slider_layout() {
    // TODO
  }
}