<?php

namespace Apiarium\Tests;

use Apiarium\Parsers\Json_Parsers\Twitter_Json_Parser;

/**
 * @group parser
 * @group twitter
 */
class Twitter_Json_Parser_Test extends \PHPUnit_Framework_TestCase {
  function setUp() {
    $this->feed_service_mock = $this->getMockBuilder( 'Nectary\Services\Twitter_Feed_Service' )
    ->setMethods(['get_feed'])
    ->getMock();

    $this->parser = new Twitter_Json_Parser(
      $this->feed_service_mock
    );

    $this->good_request = array(
      'type' => 'twitter'
    );

    $this->bad_request = array(
      'type' => 'bitter'
    );

    $this->twitter_feed = $this->getMockBuilder( 'Object' )
    ->setMethods( ['get_items', 'retrieve_items'] )
    ->getMock();

    $this->twitter_feed->method( 'get_items' )
    ->will(
        $this->returnValue(
          array(
            'statuses' => array(
              array(
                'id' => '1234',
                'user' => array(
                  'screen_name' => 1234,
                  'profile_image_url_https' => 'image'
                ),
                'created_at' => 'midnight',
                'text' => 'wow'
              )
            )
          )
        )
    );

    $this->bad_twitter_feed = $this->getMockBuilder( 'Object' )
    ->setMethods( ['get_items', 'retrieve_items'] )
    ->getMock();

    $this->bad_twitter_feed->method( 'get_items' )
    ->will(
        $this->returnValue(
          array(
            'errors' => array(
              array(
                'message' => 'dang'
              )
            )
          )
        )
    );
  }

  function test_can_parse_request() {
    $can = $this->parser->can_parse( $this->good_request );

    $this->assertTrue( $can );
  }

  function test_can_not_parse_request() {
    $can = $this->parser->can_parse( $this->bad_request );

    $this->assertFalse( $can );
  }

  function test_parses_feed_request() {
    $this->feed_service_mock->expects( $this->once() )
    ->method( 'get_feed' )
    ->will( $this->returnValue( $this->twitter_feed ) );

    $feed = $this->parser->parse( $this->good_request );

    $this->assertInternalType( 'array', $feed );
    $this->assertCount( 1, $feed );
  }

  function test_returns_empty_data_when_feed_has_errors() {
    $this->feed_service_mock->expects( $this->once() )
    ->method( 'get_feed' )
    ->will( $this->returnValue( $this->bad_twitter_feed ) );

    $feed = $this->parser->parse( $this->good_request );

    $this->assertInternalType( 'array', $feed );
    $this->assertCount( 0, $feed );
  }
}