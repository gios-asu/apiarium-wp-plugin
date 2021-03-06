<?php

namespace Apiarium\Parsers\Json_Parsers;

use Nectary\Services\Json_Feed_Service;
use Nectary\Utilities\Json_Utilities;
use Apiarium\Models\Feed_Item;

class Yahoo_Weather_Json_Parser {
  const YAHOO_API_URL = 'yahooapis.com';

  private $feed_service;

  public function __construct( Json_Feed_Service $feed_service ) {
    $this->feed_service = $feed_service;
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
    try {
      $feed = $this->get_feed( $url );
    } catch( \Exception $e ) {
      return [];
    }

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
    $feed = $this->feed_service->get_feed( $url );
    $feed->retrieve_items();

    return $feed;
  }

  private function create_feed_items( $items, $full_feed ) {
    foreach ( $items as $item ) {
      $feed_item = new Feed_Item();

      $feed_item->title = $item['day'];
      $feed_item->description = $item['high'] . '&deg; / ' . $item['low'] . '&deg;';
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
      return $code . '.png';
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
