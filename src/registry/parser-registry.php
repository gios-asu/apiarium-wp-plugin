<?php

/**
 * Register any feed parsers here. They should have
 * `parse( $url ) : array<Feed_Item>` and
 * `can_parse( $url ) : boolean` methods.
 */

return [
  Apiarium\Services\Rss_Parsers\Flickr_Rss_Parser::class,
  Apiarium\Services\Rss_Parsers\Wordpress_Rss_Parser::class,
  Apiarium\Services\Json_Parsers\Twitter_Json_Parser::class,
  Apiarium\Services\Json_Parsers\Yahoo_Weather_Json_Parser::class,
];
