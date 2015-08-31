<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Weather_Shortcodes extends Hook {
  const LIMIT = 4;

  public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_shortcode( 'display-weather', $this, 'display_weather' );
  }

  public function display_weather( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'location' => 'tempe, az'
        ),
        $atts,
        'apiarium-weather'
    );

    $feed_url  = 'http://query.yahooapis.com/v1/public/yql';
    $feed_url .= '?q=';
    $feed_url .= urlencode(
        'select * from weather.forecast ' .
        'where woeid in (' .
          'select woeid from geo.places(1) ' .
          'where text="' . $atts['location'] . '"' .
        ')'
    );
    $feed_url .= '&format=json';

    $feed_items = Parser_Register::parse( [ $feed_url ] );

    $feed_items = array_splice( $feed_items, 0, self::LIMIT );

    return $this->create_html( $feed_items );
  }

  public function create_html( $feed_items ) {
    if ( ! empty( $feed_items ) ) {
      $weather = "
      <div class='apiarium__weather'>
        <h2>{$feed_items[0]->metadata['city']}, {$feed_items[0]->metadata['region']}</h2>
        <div class='apiarium__weather__channel'>
        Humidity {$feed_items[0]->metadata['current_humidity']}%% &dot;
        Wind {$feed_items[0]->metadata['current_wind_direction']} 
        {$feed_items[0]->metadata['current_wind_speed']}
        {$feed_items[0]->metadata['current_wind_speed_units']}
        </div>
        <div class='apiarium__weather__forecasts'>
          %s
        </div>
      </div>
      ";
      $forecasts = '';

      foreach( $feed_items as $forecast ) {
        $forecasts .= "
        <div class='apiarium__weather__forecast'>
          <h3>{$forecast->title}</h3>
          <img src='{$forecast->image}' />
          <p>{$forecast->description}</p>
        </div>
        ";
      }

      return sprintf( $weather, $forecasts );
    } else {
      $weather = "
      <div class='apiarium__weather'>
        <h2>Could not load weather.</h2>
      </div>
      ";
    }
  }
}
