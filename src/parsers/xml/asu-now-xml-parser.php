<?php

namespace Apiarium\Parsers\Xml_Parsers;

use Apiarium\Models\Feed_Item;

class Asu_Now_Xml_Parser {
  const ASU_PREFIX = 'https://asunow.asu.edu/';


  public function __construct() {
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

      $formatted_date = $item['post_date'];
      $formatted_date = strtotime( $formatted_date );
      $formatted_date = date( 'F j, Y', $formatted_date );

      $feed_item->id          = $item['id'];
      $feed_item->title       = $item['title'];
      $feed_item->image       = $item['image'];
      $feed_item->description = '<p class="apiarium__small-text">' . $formatted_date . '</p>' . $item['description'];
      $feed_item->post_date   = $item['post_date'];

      $feed_items[] = $feed_item;
    }

    return $feed_items;
  }

  private function get_items( $url ) {
    $feed = $this->get_feed( $url );

    if ( ! empty( $feed ) && ! empty( $feed->node ) ) {
      $items = [];

      foreach( $feed->node as $item ) {
        $feed_item = [];

        $feed_item['id'] = $item->nid;
        $feed_item['title'] = $item->title;
        $feed_item['image'] = $item->hero_image;
        // $item->body has CDATA, so it won't show up if you var_dump it, you
        // need to cast it as a string!
        $feed_item['description'] = (string)$item->body;
        $feed_item['post_date'] = $item->post_date;

        $items[] = $feed_item;
      }
      
      // TODO get data from feeds
      return $items;
    } else {
      return [];
    }
  }

  private function get_feed( $url ) {
    $feed = simplexml_load_file( $url );

    try {
      // TODO
    } catch ( \Exception $e ) {
      error_log( 'Could not load ASU Now XML Feed' );
      return;
    }

    return $feed;
  }
}