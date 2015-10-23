<?php

/**
 * Register any feed parsers here. They should have
 * `parse( $url ) : array<Feed_Item>` and
 * `can_parse( $url ) : boolean` methods.
 */

return [
  Apiarium\Parsers\Rss_Parsers\Flickr_Rss_Parser::class,
  Apiarium\Parsers\Xml_Parsers\Asu_Now_Xml_Parser::class,
  Apiarium\Parsers\Rss_Parsers\Wordpress_Rss_Parser::class,
  Apiarium\Parsers\Json_Parsers\Twitter_Json_Parser::class,
  Apiarium\Parsers\Json_Parsers\Yahoo_Weather_Json_Parser::class,
];
