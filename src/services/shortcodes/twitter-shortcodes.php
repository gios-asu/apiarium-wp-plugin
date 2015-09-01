<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Nectary\Facades\Twitter_Json_Facade;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Twitter_Shortcodes extends Hook {
  public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_shortcode( 'display-twitter', $this, 'display_twitter' );
  }

  public function display_twitter( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'limit' => 15,
          'search' => '@asugreen'
        ),
        $atts,
        'apiarium-twitter'
    );

    $feed_items = Parser_Register::parse(
        array(
            array(
              'type' => 'twitter',
              'query' => $atts['search'],
              'limit' => $atts['limit'],
              'oauth_access_token'        => 'TODO',
              'oauth_access_token_secret' => 'TODO',
              'consumer_key'              => 'TODO',
              'consumer_secret'           => 'TODO',
            )
        )
    );
    var_dump( $feed_items );

    return $this->create_html( $feed_items );
  }

  public function create_html( $feed_items ) {
    return 'TODO';
  }
}
