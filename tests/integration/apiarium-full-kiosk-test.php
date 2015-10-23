<?php

namespace Apiarium\Tests\Integration;

use Apiarium\Services\Admin_Panel;

/**
 * @group integration
 */
class Apiarium_Full_Kiosk_Test extends \PHPUnit_Framework_TestCase {
  /**
   * Random Twitter API keys pulled from the Internet
   */
  const CONSUMER_KEY              = 'VXD22AD9kcNyNgsfW6cwkWRkw';
  const CONSUMER_SECRET           = 'y0k3z9Y46V0DMAKGe4Az2aDtqNt9aXjg3ssCMCldUheGBT0YL9';
  const OAUTH_ACCESS_TOKEN        = '3232926711-kvMvNK5mFJlUFzCdtw3ryuwZfhIbLJtPX9e8E3Y';
  const OAUTH_ACCESS_TOKEN_SECRET = 'EYrFp0lfNajBslYV3WgAGmpHqYZvvNxP5uxxSq8Dbs1wa';

  function setUp() {
    $this->content = '
[display theme=green]
  [display-row]
    [display-column size=1]
      [display-flex size=1 classes=apiarium__no-border]
        <img src="https://commguide.asu.edu/files/endorsed/color/JAW-GIOS_RGB.png" />
      [/display-flex]
      [display-flex size=2]
        [display-weather]
      [/display-flex]
      [display-flex size=3]
        <h2>Twitter</h2>
        [display-twitter search="@asugreen"]
      [/display-flex]
    [/display-column]
    [display-column size=2]
      [display-flex size=1 classes=apiarium__no-border]
      [/display-flex]
    [/display-column]
    [display-column size=1]
      [display-flex size=1 classes=apiarium__no-border]
        [display-calendar]
      [/display-flex]
      [display-flex size=2]
        [display-slider content=heading,image]
          https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200
        [/display-slider]
      [/display-flex]
      [display-flex size=3]
        <h2>ASU News</h2>
      [/display-flex]
    [/display-column]
  [/display-row]
[/display]
    ';

    update_option(
        Admin_Panel::OPTIONS_NAME,
        array(
          Admin_Panel::TWITTER_OAUTH_ACCESS_TOKEN        => self::OAUTH_ACCESS_TOKEN,
          Admin_Panel::TWITTER_OAUTH_ACCESS_TOKEN_SECRET => self::OAUTH_ACCESS_TOKEN_SECRET,
          Admin_Panel::TWITTER_CONSUMER_KEY              => self::CONSUMER_KEY,
          Admin_Panel::TWITTER_CONSUMER_SECRET           => self::CONSUMER_SECRET,
        )
    );

    // Disable https checking for testing
    add_filter( 'https_ssl_verify', '__return_false' );
  }

  function test_kiosk_has_components() {
    $content = do_shortcode( $this->content );

    $this->assertContains( "<div class='apiarium__green'>", $content );
    $this->assertContains( "<div class='apiarium__weather'>", $content );
    $this->assertContains( '<h2>Tempe, AZ</h2>', $content );
    $this->assertContains( '<h2>Twitter</h2>', $content );
    $this->assertContains( "<div class='apiarium__weather__forecast'>", $content );
    $this->assertContains( "<div class='apiarium__tweets'", $content );
    $this->assertContains( "data-query='@asugreen'", $content );
    $this->assertContains( "data-limit='15'", $content );
    $this->assertContains( "<div class='apiarium__flex apiarium__no-border' style='flex: 1'>", $content );
    $this->assertContains( "<div class='apiarium__slider'>", $content );
  }
}