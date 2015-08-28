<?php

namespace Apiarium\Services\Rss_Parsers;

class Wordpress_Rss_Parser {
  const PURL_RSS = 'http://purl.org/rss/1.0/modules/content/';

  public function can_parse( $item ) {
    if ( $item instanceof \SimplePie_Item ) {
      if ( ! empty( $item->get_item_tags( self::PURL_RSS, 'encoded' ) ) ) {
        return true;  
      }
    }

    return false;
  }

  // TODO remove the "The post XYZ appeared first on ABC" text from
  // the description
  public function parse( $item ) {
    $title       = $item->get_title();
    $description = $item->get_description();
    $image       = $this->get_image( $item );

    return [
      'title'       => $title,
      'description' => $description,
      'image'       => $image,
    ];
  }

  private function get_image( $item ) {
    $raw = $item->get_item_tags(
        self::PURL_RSS,
        'encoded'
    );

    if ( ! empty( $raw ) ) {
      $description = $raw[0]['data'];

      $image = $this->get_image_from_text( $description );

      return $image;
    }

    return false;
  }

  private function get_image_from_text( $content ) {
    $image_regex = '/<img[^>]+>/i';
    $image_source_regex = '/src="([^"]+)"/i';

    preg_match_all( $image_regex, $content, $pics );

    if ( 1 <= count( $pics[0] ) ) {
      // Pull out the image source and the image alt
      preg_match( $image_source_regex, $pics[0][0], $source_matches );
      if ( count( $source_matches ) > 0 ) {
        $image_source = $source_matches[1];
      }
      
      // TODO extract this special Flickr case
      return str_replace( '_m.jpg', '_b.jpg', $image_source );
    }

    return '';
  }
}
