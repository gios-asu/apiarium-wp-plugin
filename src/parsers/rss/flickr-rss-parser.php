<?php

namespace Apiarium\Parsers\Rss_Parsers;

use Apiarium\Models\Feed_Item;
use Honeycomb\Services\Wordpress_Feed_Service;
use Nectary\Models\Rss_Feed;

/**
 * Parse Flicker feeds
 */
class Flickr_Rss_Parser {
  const FLICKR_URL = 'api.flickr.com';

  private $feed_service;

  /**
   * @constructor
   * @param Wordpress_Rss_Facade $feed_service
   */
  public function __construct( Wordpress_Feed_Service $feed_service ) {
    $this->feed_service = $feed_service;
  }

  /**
   * @param String $url
   * @return Boolean
   */
  public function can_parse( $url ) {
    if ( is_string( $url ) ) {
      if ( strpos( $url, self::FLICKR_URL ) !== false ) {
        if ( $feed = $this->get_feed( $url ) ) {
          if ( $feed instanceof Rss_Feed ) {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * @param String $url
   * @return Array<Feed_Items>
   */
  public function parse( $url ) {
    // Don't worry about refetching RSS feeds, they are cached by
    // WordPress
    $items      = $this->get_items( $url );
    $feed_items = [];

    foreach ( $items as $item ) {
      $feed_item = new Feed_Item();

      $image                  = $this->get_image( $item );

      // The image should be a good enough ID
      $feed_item->id          = $image;
      $feed_item->title       = $item->get_title();
      $feed_item->description = $item->get_description();
      $feed_item->post_date   = $item->get_date();
      $feed_item->image       = $image;

      $feed_items[] = $feed_item;
    }

    return $feed_items;
  }

  private function get_image( $item ) {
    $image_regex = '/<img[^>]+>/i';
    $image_source_regex = '/src="([^"]+)"/i';

    preg_match_all( $image_regex, $item->get_description(), $pics );

    if ( 1 <= count( $pics[0] ) ) {
      // Pull out the image source and the image alt
      preg_match( $image_source_regex, $pics[0][0], $source_matches );
      if ( count( $source_matches ) > 0 ) {
        $image_source = $source_matches[1];
      }

      return str_replace( '_m.jpg', '_b.jpg', $image_source );
    }

    return '';
  }

  private function get_items( $url ) {
    $feed = $this->get_feed( $url );
    $feed->sort_by_date( 'desc' );
    $items = $feed->get_items();

    return $items;
  }

  private function get_feed( $url ) {
    $feed = $this->feed_service->get_feed( $url );

    try {
      $feed->retrieve_items();
    } catch ( Exception $e ) {
      error_log( 'Could not load Flickr RSS Feed' );
      return;
    }

    return $feed;
  }
}
