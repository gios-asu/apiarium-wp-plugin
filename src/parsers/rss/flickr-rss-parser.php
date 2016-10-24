<?php

namespace Apiarium\Parsers\Rss_Parsers;

use Apiarium\Models\Feed_Item;
use Honeycomb\Services\Wordpress_Feed_Service;
use Nectary\Models\Rss_Feed;

/**
 * Parse Flicker feeds
 */
class Flickr_Rss_Parser {
  private $flickr_url_identifiers; // class constants can't be an array
  private $feed_service;

  /**
   * @constructor
   * @param Wordpress_Rss_Facade $feed_service
   */
  public function __construct( Wordpress_Feed_Service $feed_service ) {
    $this->feed_service = $feed_service;
    $this->flickr_url_identifiers = ['api.flickr.com', '/flickr-rss/']; 
  }

  /**
   * @param String $url
   * @return Boolean
   */
  public function can_parse( $url ) {
    if ( is_string( $url ) ) {
      foreach( $this->flickr_url_identifiers as $possible_url_match ) {
        if ( strpos( $url, $possible_url_match) !== false ) {
          if ( $feed = $this->get_feed( $url ) ) {
            if ( $feed instanceof Rss_Feed ) {
              return true;
            }
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

  /** get_image( SimplePie $item ): URL as a String
   * Look through the description field in an item for an img tag,
   * returns the first value from the src attribute it finds. 
   * Also will return the large image size when small is given.
   */
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

      // see also: https://www.flickr.com/services/api/misc.urls.html
      // if small photos are given, replace with large
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
      error_log( 'Could not load Flickr RSS Feed: '.$url.' : '.$e->getMessage() );
      return;
    }

    return $feed;
  }
}
