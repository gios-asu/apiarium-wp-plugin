<?php

namespace Apiarium\Parsers\Json_Parsers;

use Nectary\Services\Twitter_Feed_Service;
use Nectary\Utilities\Json_Utilities;
use Apiarium\Models\Feed_Item;

class Twitter_Json_Parser {
  private $feed_service;

  public function __construct( Twitter_Feed_Service $feed_service ) {
    $this->feed_service = $feed_service;
  }

  /**
   * A request is parseable by this Parser when it
   * is an array that has a key-value pair of
   * `type=>twitter`.
   */
  public function can_parse( $request ) {
    if ( is_array( $request ) ) {
      if ( array_key_exists( 'type', $request ) ) {
        if ( $request['type'] === 'twitter' ) {
          return true;
        }
      }
    }

    return false;
  }

  public function parse( $request ) {
    $feed  = $this->get_feed( $request );
    $items = $feed->get_items();

    $error = Json_Utilities::get_or_default( $items, 'errors.0.message', false );

    if ( $error === false ) {
      $tweets = Json_Utilities::get( $items, 'statuses' );

      $feed_items = $this->create_feed_items( $tweets, $items );

      return iterator_to_array( $feed_items );
    } else {
      return [];
    }
  }

  private function get_feed( $request ) {
    $feed = $this->feed_service->get_feed( $request );

    try {
      $feed->retrieve_items();
    } catch ( Exception $e ) {
      error_log( 'Could not load Twitter JSON Feed' );
      return;
    }

    return $feed;
  }

  private function create_feed_items( $tweets, $full_feed ) {
    foreach ( $tweets as $tweet ) {
      $feed_item = new Feed_Item();

      // filter out "bad" tweets (curse words, NSFW)
      if ( $this->is_tweet_sensitive( $tweet ) ) {
        continue;
      }

      $feed_item->id          = $tweet['id'];
      $feed_item->title       = $tweet['user']['screen_name'];
      $feed_item->description = $tweet['text'];
      $feed_item->image       = $this->get_image( $tweet );
      $feed_item->post_date   = $this->get_short_date( $tweet );

      yield $feed_item;
    }
  }

  private function get_image( $tweet ) {
    $image = $tweet['user']['profile_image_url_https'];
    $image = preg_replace( '/normal/', 'bigger', $image );

    return $image;
  }

  /**
   * TODO possible_sensitive only checks links, we need to also
   * check the Twitter text
   */
  private function is_tweet_sensitive( $tweet ) {
    if ( array_key_exists( 'possibly_sensitive', $tweet ) ) {
      return $tweet['possibly_sensitive'] === true;
    } else {
      return false;
    }
  }

  private function get_short_date( $tweet ) {
    $tweet_time = $tweet['created_at'];

    $elapsed_time = time() - strtotime( $tweet_time );
    if ( $elapsed_time < 1 ) {
        return 'now';
    }
    $time_conversion = array(
          1                       => 's',
          60                      => 'm',
          60 * 60                 => 'h',
          24 * 60 * 60            => 'd',
    );

    foreach ( $time_conversion as $secs => $unit ) {
      $elapsed_time_to_unit   = $elapsed_time / $secs;
      $not_fractional_unit    = $elapsed_time_to_unit >= 1;
      $less_than_one_day      = 'h' == $unit && $elapsed_time_to_unit < 24;
      $less_than_one_hour     = 'm' == $unit && $elapsed_time_to_unit < 60;
      $less_than_one_minute   = 's' == $unit && $elapsed_time_to_unit < 60;
      if ( $not_fractional_unit
          && ( $less_than_one_day
                || $less_than_one_hour
                || $less_than_one_minute
            )
      ) {
          $rounded_time       = round( $elapsed_time_to_unit );
          return $rounded_time . $unit;
      }
    }
    return date_format( date_create( $tweet_time ),'d M' );
  }
}
