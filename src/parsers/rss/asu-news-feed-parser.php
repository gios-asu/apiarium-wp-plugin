<?php

namespace Apiarium\Parsers\Rss_Parsers;

use Apiarium\Models\Feed_Item;
use Honeycomb\Services\Wordpress_Feed_Service;
use Nectary\Model\Rss_Feed;

class Asu_News_Rss_Parser {
  const ASU_PREFIX = 'https://asunews.asu.edu/';

  private $rss_service;

  public function __construct( Wordpress_Feed_Service $rss_service ) {
    $this->rss_service = $rss_service;
  }

  public function can_parse( $url ) {
    try {
      if ( is_string( $url ) ) {
        if ( starts_with( $url, self::ASU_PREFIX ) )  {
          return true;
        }
      }

    } catch ( \Exception $e ) {
      return false;
    }

    return false;
  }

  // TODO remove the "read more" link
  public function parse( $url ) {
    $items = $this->get_items( $url );
    $feed_items = [];

    foreach ( $items as $item ) {
      $feed_item = new Feed_Item();

      $feed_item->id          = $item->get_title() . '-' . $item->get_date();
      $feed_item->title       = $item->get_title();
      $feed_item->description = $item->get_description();
      $feed_item->post_date   = $item->get_date();

      $feed_items[] = $feed_item;
    }

    return $feed_items;
  }

  private function get_items( $url ) {
    $feed = $this->get_feed( $url );
    $feed->sort_by_date( 'desc' );
    $items = $feed->get_items();

    return $items;
  }

  private function get_feed( $url ) {
    $feed = $this->rss_service->get_feed( $url );

    try {
      $feed->retrieve_items();
    } catch ( \Exception $e ) {
      error_log( 'Could not load ASU News Feed' );
      return;
    }

    return $feed;
  }
}