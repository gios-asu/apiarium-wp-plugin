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

    foreach ( self::$parsers as $parser ) {
      foreach ( $urls as $url ) {
        if ( $parser->can_parse( $url ) ) {
          $feed_items += $parser->parse( $url );
        }
      }
    }

    // TODO get the unique items

    // TODO limit

    return $feed_items;
  }
}