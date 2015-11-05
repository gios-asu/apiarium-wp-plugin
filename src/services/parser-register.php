<?php

namespace Apiarium\Services;

use Honeycomb\Services\Register;
use Nectary\Factories\Dependency_Injection_Factory;

// TODO handle priorities

class Parser_Register extends Register {
  private static $parsers = [];

  public function __construct() {}

  public function register( $parsers = [] ) {
    foreach ( $parsers as $parser ) {

      $factory = new Dependency_Injection_Factory(
          $parser,
          '__construct',
          []
      );


      self::$parsers[] = $factory->build()[0];
    }
  }

  public static function parse( $urls ) {
    $feed_items = [];

    foreach ( $urls as $url ) {
      foreach ( self::$parsers as $parser ) {
        if ( $parser->can_parse( $url ) ) {
          $feed_items += $parser->parse( $url );
          break;
        }
      }
    }

    // TODO get the unique items

    // TODO limit

    $short_name_for_urls = self::generate_short_name( $urls );

    $feed_items = self::check_and_update_cache( $feed_items, $short_name_for_urls );

    return $feed_items;
  }

  private static function generate_short_name( $urls ) {
    $joined = join( '', $urls );

    return 'apiarium__' . md5( $joined );
  }

  private static function check_and_update_cache( $feed_items, $transient_name ) {
    if ( count( $feed_items ) === 0 ) {
      // Get the old transient
      $transient = get_transient( $transient_name );

      if ( $transient === false ) {
        return $feed_items;
      } else {
        // If there is an old transient, use that data
        return $transient;
      }      
    } else {
      // Update the transient
      set_transient( $transient_name, $feed_items, 24 * 60 * 60 * 1000 /* 1 day */ );
      return $feed_items;
    }
  }
}
