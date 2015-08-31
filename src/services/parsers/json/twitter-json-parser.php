<?php

namespace Apiarium\Services\Json_Parsers;

use Nectary\Facades\Twitter_Json_Facade;

class Twitter_Json_Parser {
  private $json_facade;

  public function __construct( Twitter_Json_Facade $json_facade ) {
    $this->json_facade = $json_facade;
  }

  public function can_parse( $url ) {
    // TODO
    return false;
  }

  public function parse( $url ) {
    // TODO
    return null;
  }
}
