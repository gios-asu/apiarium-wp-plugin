<?php

namespace Apiarium\Services\Json_Parsers;

use Nectary\Facades\Generic_Json_Facade;
use Apiarium\Models\Feed_Item;

class Yahoo_Weather_Json_Parser {
  const YAHOO_API_URL = 'yahooapis.com';

  private $json_facade;

  public function __construct( Generic_Json_Facade $json_facade ) {
    $this->json_facade = $json_facade;
  }

  public function can_parse( $url ) {
    if ( strpos( $url, self::YAHOO_API_URL ) > -1 ) {
      return true;
    }

    return false;
  }

  public function parse( $url ) {
    $feed = $this->get_feed( $url );
    $items = $feed->get_items();

    $forecast = $items['query']['results']['channel']['item']['forecast'];

    $feed_items = $this->create_feed_items( $forecast, $items );

    return iterator_to_array( $feed_items );
  }

  private function get_feed( $url ) {
    $feed = $this->json_facade->get_feed( $url );

    try {
      $feed->retrieve_items();
    } catch ( Exception $e ) {
      error_log( 'Could not load Yahoo Weather JSON Feed' );
      return;
    }

    return $feed;
  }

  private function create_feed_items( $items, $full_feed ) {
    foreach ( $items as $item ) {
      $feed_item = new Feed_Item();

      $feed_item->title = $item['day'];
      $feed_item->description = $item['low'] . '&deg; / ' . $item['high'] . '&deg;';
      $feed_item->image = $this->get_image( $item['code'] );
      $feed_item->metadata = array(
        'city' => $this->get( $full_feed, 'query.results.channel.location.city' ),
        'region' => $this->get( $full_feed, 'query.results.channel.location.region' ),
        'temperature_unit' => $this->get( $full_feed, 'query.results.channel.units.temperature' ),
        'current_humidity' => $this->get( $full_feed, 'query.results.channel.atmosphere.humidity' ),
        'current_wind_speed' => $this->get( $full_feed, 'query.results.channel.wind.speed' ),
        'current_wind_speed_units' => $this->get( $full_feed, 'query.results.channel.units.speed' ),
        'current_wind_direction' => $this->degrees_to_direction(
            $this->get( $full_feed, 'query.results.channel.wind.direction' )
        ),
      );

      yield $feed_item;
    }
  }

  private function get_image( $code ) {
    if ( empty( $code ) ) {
      return '';
    } else {
      return plugins_url(
          "public/images/flat-weather/{$code}.png",
          dirname( dirname( dirname( dirname( __FILE__ ) ) ) )
      );
    }
  }

  private function get( $feed, $path ) {
    $path_parts = explode( '.', $path );

    $current = $feed;

    foreach ( $path_parts as $part ) {
      if ( is_array( $current ) &&
           array_key_exists( $part, $current ) ) {
        $current = $current[ $part ];  
      } else {
        break;
      }
    }

    return $current;
  }

  private function degrees_to_direction( $degrees ) {
    $data = array(
      0     => 'N',
      22.5  => 'NE',
      67.5  => 'E',
      112.5 => 'SE',
      157.5 => 'S',
      202.5 => 'SW',
      247.2 => 'W',
      292.5 => 'NW',
      337.5 => 'N'
    );

    foreach( array_reverse( $data, true ) as $key => $direction ) {
      if ( $key < $degrees ) {
        return $direction;
      }
    }

    return 'NA';
  }
}
