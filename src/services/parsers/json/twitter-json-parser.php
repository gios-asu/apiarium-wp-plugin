<?php

namespace Apiarium\Services\Json_Parsers;

use Nectary\Facades\Twitter_Json_Facade;
use Nectary\Utilities\Json_Utilities;
use Apiarium\Models\Feed_Item;

class Twitter_Json_Parser {
  private $json_facade;

  public function __construct( Twitter_Json_Facade $json_facade ) {
    $this->json_facade = $json_facade;
  }

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
    $feed = $this->get_feed( $request );
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
    $feed = $this->json_facade->get_feed( $request );

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

      $feed_item->id    = $tweet['id'];
      $feed_item->title = $tweet['user']['screen_name'];
      $feed_item->description = $tweet['text'];
      $feed_item->image       = $tweet['user']['profile_image_url_https'];
      $feed_item->post_date   = $tweet['created_at'];

      yield $feed_item;
    }
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
}
