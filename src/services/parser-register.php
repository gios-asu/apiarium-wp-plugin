<?php

namespace Apiarium\Services;

use Honeycomb\Services\Register;

// TODO handle priorities

class Parser_Register extends Register {
  private static $parsers = [];

  public function __construct() {}

  public function register( $parsers = [] ) {
    foreach ( $parsers as $parser ) {
      self::$parsers[] = new $parser();
    }
  }

  public static function parse( $item ) {
    foreach ( self::$parsers as $parser ) {
      if ( $parser->can_parse( $item ) ) {
        return $parser->parse( $item );
      }
    }

    return [];
  }
}