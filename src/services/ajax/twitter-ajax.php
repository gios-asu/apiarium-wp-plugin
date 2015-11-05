<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

class Twitter_Ajax extends Hook {
  public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_action( 'wp_ajax_apiarium-twitter', $this, 'handle_ajax' );
    $this->add_action( 'wp_ajax_nopriv_apiarium-twitter', $this, 'handle_ajax' );
  }

  public function handle_ajax() {
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'apiarium-twitter' ) ) {
      wp_send_json_error();
    }

    $data = json_decode( stripslashes( $_REQUEST['serialized'] ), true );

    $query     = $data['query'];
    $queryType = $data['queryType'];
    $limit     = $data['limit'];

    if ($queryType === 'screenname') {
      $search = " screenname='{$query}' ";
    } else {
      $search = " search='{$query}' ";
    }

    $twitter_html = do_shortcode( "[display-twitter {$search} limit='{$limit}']" );

    wp_send_json_success( array(
        'script_response' => $twitter_html,
        'nonce'           => wp_create_nonce( 'apiarium-twitter' ),
    ) );
  }
}
