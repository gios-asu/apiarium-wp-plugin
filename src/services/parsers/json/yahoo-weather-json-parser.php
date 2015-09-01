<?php

namespace Apiarium\Services\Json_Parsers;

use Nectary\Facades\Generic_Json_Facade;
use Nectary\Utilities\Json_Utilities;
use Apiarium\Models\Feed_Item;

class Yahoo_Weather_Json_Parser {
  const YAHOO_API_URL = 'yahooapis.com';

  private $json_facade;

  public function __construct( Generic_Json_Facade $json_facade ) {
    $this->json_facade = $json_facade;
  }

  public function can_parse( $url ) {
    if ( is_string( $url ) ) {
      if ( strpos( $url, self::YAHOO_API_URL ) > -1 ) {
        return true;
      }
    }

    return false;
  }

  public function parse( $url ) {
    $feed = $this->get_feed( $url );
    $items = $feed->get_items();

    $forecast = Json_Utilities::get( $items, 'query.results.channel.item.forecast' );

    if ( is_array( $forecast ) ) {
      $feed_items = $this->create_feed_items( $forecast, $items );  
      return iterator_to_array( $feed_items );
    } else {
      return [];
    }
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
        'city' => Json_Utilities::get( $full_feed, 'query.results.channel.location.city' ),
        'region' => Json_Utilities::get( $full_feed, 'query.results.channel.location.region' ),
        'temperature_unit' => Json_Utilities::get( $full_feed, 'query.results.channel.units.temperature' ),
        'current_humidity' => Json_Utilities::get( $full_feed, 'query.results.channel.atmosphere.humidity' ),
        'current_wind_speed' => Json_Utilities::get( $full_feed, 'query.results.channel.wind.speed' ),
        'current_wind_speed_units' => Json_Utilities::get( $full_feed, 'query.results.channel.units.speed' ),
        'current_wind_direction' => $this->degrees_to_direction(
            Json_Utilities::get( $full_feed, 'query.results.channel.wind.direction' )
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
