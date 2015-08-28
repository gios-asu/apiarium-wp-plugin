<?php

namespace Apiarium\Services\Rss_Parsers;

use Apiarium\Models\Feed_Item;
use Honeycomb\Facades\Wordpress_Rss_Facade;
use Nectary\Models\Rss_Feed;

class Flickr_Rss_Parser {
  const FLICKR_URL = 'api.flickr.com';
  private $rss_facade;

  public function __construct( Wordpress_Rss_Facade $rss_facade ) {
    $this->rss_facade = $rss_facade;
  }

  public function can_parse( $url ) {
    if ( $feed = $this->get_feed( $url ) ) {
      if ( $feed instanceof Rss_Feed ) {
        if ( strpos( $url, self::FLICKR_URL ) > -1 ) {
          return true;
        }
      }
    }

    return false;
  }

  public function parse( $url ) {
    // Don't worry about refetching RSS feeds, they are cached by
    // WordPress
    $items      = $this->get_items( $url );
    $feed_items = [];

    foreach ( $items as $item ) {
      $feed_item = new Feed_Item();

      $feed_item->title       = $item->get_title();
      $feed_item->description = $item->get_description();
      $feed_item->image       = $this->get_image( $item );

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
    $feed = $this->rss_facade->get_feed( $url );

    try {
      $feed->retrieve_items();
    } catch ( Exception $e ) {
      error_log( 'Could not load Flickr RSS Feed' );
      return;
    }

    return $feed;
  }
}