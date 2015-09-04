<?php

namespace Apiarium\Tests;

use Apiarium\Parsers\Json_Parsers\Yahoo_Weather_Json_Parser;

/**
 * @group parser
 * @group yahoo
 */
class Yahoo_Weather_Json_Parser_Test extends \PHPUnit_Framework_TestCase {
  function setUp() {
    $this->feed_service_mock = $this->getMockBuilder( 'Nectary\Services\Json_Feed_Service' )
    ->setMethods(['get_feed'])
    ->getMock();

    $this->parser = new Yahoo_Weather_Json_Parser(
      $this->feed_service_mock
    );

    $this->good_request = 'http://query.yahooapis.com/v1/public/yql?q=select+%2A+from+weather.forecast+where+woeid+in+%28select+woeid+from+geo.places%281%29+where+text%3D%22tempe%2C+az%22%29&format=json';

    $this->bad_request = 'whatever';

    $this->json_feed = $this->getMockBuilder( 'Object' )
    ->setMethods( ['get_items', 'retrieve_items'] )
    ->getMock();

    $this->json_feed->method( 'get_items' )
    ->will(
        $this->returnValue(
          json_decode( '{"query":{"count":1,"created":"2015-09-04T23:40:48Z","lang":"en-US","results":{"channel":{"title":"Yahoo! Weather - Tempe, AZ","link":"http://us.rd.yahoo.com/dailynews/rss/weather/Tempe__AZ/*http://weather.yahoo.com/forecast/USAZ0233_f.html","description":"Yahoo! Weather for Tempe, AZ","language":"en-us","lastBuildDate":"Fri, 04 Sep 2015 3:50 pm MST","ttl":"60","location":{"city":"Tempe","country":"United States","region":"AZ"},"units":{"distance":"mi","pressure":"in","speed":"mph","temperature":"F"},"wind":{"chill":"94","direction":"150","speed":"12"},"atmosphere":{"humidity":"30","pressure":"29.76","rising":"0","visibility":"10"},"astronomy":{"sunrise":"6:03 am","sunset":"6:49 pm"},"image":{"title":"Yahoo! Weather","width":"142","height":"18","link":"http://weather.yahoo.com","url":"http://l.yimg.com/a/i/brand/purplelogo//uh/us/news-wea.gif"},"item":{"title":"Conditions for Tempe, AZ at 3:50 pm MST","lat":"33.43","long":"-111.94","link":"http://us.rd.yahoo.com/dailynews/rss/weather/Tempe__AZ/*http://weather.yahoo.com/forecast/USAZ0233_f.html","pubDate":"Fri, 04 Sep 2015 3:50 pm MST","condition":{"code":"28","date":"Fri, 04 Sep 2015 3:50 pm MST","temp":"94","text":"Mostly Cloudy"},"description":"\n<img src=\"http://l.yimg.com/a/i/us/we/52/28.gif\"/><br />\n<b>Current Conditions:</b><br />\nMostly Cloudy, 94 F<BR />\n<BR /><b>Forecast:</b><BR />\nFri - Scattered Thunderstorms. High: 96 Low: 77<br />\nSat - Mostly Sunny. High: 97 Low: 78<br />\nSun - Mostly Sunny. High: 100 Low: 79<br />\nMon - Sunny. High: 102 Low: 80<br />\nTue - Mostly Sunny. High: 102 Low: 82<br />\n<br />\n<a href=\"http://us.rd.yahoo.com/dailynews/rss/weather/Tempe__AZ/*http://weather.yahoo.com/forecast/USAZ0233_f.html\">Full Forecast at Yahoo! Weather</a><BR/><BR/>\n(provided by <a href=\"http://www.weather.com\" >The Weather Channel</a>)<br/>\n","forecast":[{"code":"47","date":"4 Sep 2015","day":"Fri","high":"96","low":"77","text":"Scattered Thunderstorms"},{"code":"34","date":"5 Sep 2015","day":"Sat","high":"97","low":"78","text":"Mostly Sunny"},{"code":"34","date":"6 Sep 2015","day":"Sun","high":"100","low":"79","text":"Mostly Sunny"},{"code":"32","date":"7 Sep 2015","day":"Mon","high":"102","low":"80","text":"Sunny"},{"code":"34","date":"8 Sep 2015","day":"Tue","high":"102","low":"82","text":"Mostly Sunny"}],"guid":{"isPermaLink":"false","content":"USAZ0233_2015_09_08_7_00_MST"}}}}}}', true )
        )
    );

    $this->bad_json_feed = $this->getMockBuilder( 'Object' )
    ->setMethods( ['get_items', 'retrieve_items'] )
    ->getMock();

    $this->bad_json_feed->method( 'get_items' )
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

    $this->very_bad_json_feed = $this->getMockBuilder( 'Object' )
    ->setMethods( ['get_items', 'retrieve_items'] )
    ->getMock();

    $this->very_bad_json_feed->method( 'retrieve_items' )
    ->will( $this->throwException( new \Exception() ) );
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
    ->will( $this->returnValue( $this->json_feed ) );

    $feed = $this->parser->parse( $this->good_request );

    $this->assertInternalType( 'array', $feed );
    $this->assertCount( 5, $feed );
  }

  function test_returns_empty_data_when_feed_has_errors() {
    $this->feed_service_mock->expects( $this->once() )
    ->method( 'get_feed' )
    ->will( $this->returnValue( $this->bad_json_feed ) );

    $feed = $this->parser->parse( $this->good_request );

    $this->assertInternalType( 'array', $feed );
    $this->assertCount( 0, $feed );
  }

  function test_returns_empty_data_when_feed_throws_exception() {
    $this->feed_service_mock->expects( $this->once() )
    ->method( 'get_feed' )
    ->will( $this->returnValue( $this->very_bad_json_feed ) );

    $feed = $this->parser->parse( $this->good_request );

    $this->assertInternalType( 'array', $feed );
    $this->assertCount( 0, $feed );
  }
}
