<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Honeycomb\Utilities\Option_Utilities;
use Nectary\Facades\Twitter_Json_Facade;
use Apiarium\Services\Admin_Panel;

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
          'screenname' => false,
          'search' => false,
        ),
        $atts,
        'apiarium-twitter'
    );

    $query_type = 'screenname';
    $query_text = 'asugreen';
    if ( $atts['screenname'] !== false ) {
      $query_text = $atts['screenname'];
    } else if ( $atts['search'] !== false ) {
      $query_type = 'search';
      $query_text = $atts['search'];
    }

    $oauth_access_token = Option_Utilities::get_or_default(
        Admin_Panel::OPTIONS_NAME,
        Admin_Panel::TWITTER_OAUTH_ACCESS_TOKEN,
        false
    );

    $oauth_access_token_secret = Option_Utilities::get_or_default(
        Admin_Panel::OPTIONS_NAME,
        Admin_Panel::TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
        false
    );

    $consumer_key = Option_Utilities::get_or_default(
        Admin_Panel::OPTIONS_NAME,
        Admin_Panel::TWITTER_CONSUMER_KEY,
        false
    );

    $consumer_secret = Option_Utilities::get_or_default(
        Admin_Panel::OPTIONS_NAME,
        Admin_Panel::TWITTER_CONSUMER_SECRET,
        false
    );

    if ( $oauth_access_token === false || $oauth_access_token_secret === false ||
         $consumer_key === false || $consumer_secret == false ) {
      return $this->create_error();
    }

    $feed_items = Parser_Register::parse(
        array(
            array(
              'type' => 'twitter',
              'query_type' => $query_type,
              'query' => $query_text,
              'limit' => $atts['limit'],
              'oauth_access_token'        => $oauth_access_token,
              'oauth_access_token_secret' => $oauth_access_token_secret,
              'consumer_key'              => $consumer_key,
              'consumer_secret'           => $consumer_secret,
            )
        )
    );

    return $this->create_html( $feed_items, $atts, $query_text, $query_type );
  }

  public function create_html( $feed_items, $attributes, $query_text, $query_type ) {
    $query = $query_text;
    $limit = $attributes['limit'];
    $html       = "
      <div class='apiarium__tweets' data-query='$query' data-query-type='$query_type' data-limit='$limit'>
        <ul>
          %s
        </ul>
      </div>
    ";
    $inner_html = '';

    foreach( $feed_items as $feed_item ) {
      $inner_html .= "
        <li class='apiarium__tweet'>
          <div class='apiarium__tweet__image'>
            <img src='{$feed_item->image}' />
          </div>
          <div class='apiarium__tweet__date'>
            <time>
              {$feed_item->post_date}
            </time>
          </div>
          <div class='apiarium__tweet__name'>
            <h4>{$feed_item->title}<h4>
          </div>
          <div class='apiarium__tweet__description'>
            <p>{$feed_item->description}</p>
          </div>
        </li>
      ";
    }

    foreach( $feed_items as $feed_item ) {
      $inner_html .= "
        <li class='apiarium__tweet'>
          <div class='apiarium__tweet__image'>
            <img src='{$feed_item->image}' />
          </div>
          <div class='apiarium__tweet__date'>
            <time>
              {$feed_item->post_date}
            </time>
          </div>
          <div class='apiarium__tweet__name'>
            <h4>{$feed_item->title}<h4>
          </div>
          <div class='apiarium__tweet__description'>
            <p>{$feed_item->description}</p>
          </div>
        </li>
      ";
    }

    return sprintf( $html, $inner_html );
  }

  public function create_error() {
    return "
      <p class='error'>
        Please set your Twitter keys in the WordPress
        Dashboard panel under Kiosk Settings.
      </p>
    ";
  }
}
