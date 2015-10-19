![Apiarium](documentation/images/apiarium-with-text.png)

[![Build Status](https://travis-ci.org/gios-asu/apiarium-wp-plugin.svg?branch=ivan-develop)](https://travis-ci.org/gios-asu/apiarium-wp-plugin) [![Coverage Status](https://coveralls.io/repos/gios-asu/apiarium-wp-plugin/badge.svg?branch=develop&service=github)](https://coveralls.io/github/gios-asu/apiarium-wp-plugin?branch=develop)

Television Screen and Kiosk WordPress Plugin

## Requirements

* WordPress 4.1+
* Composer

## Installation

* Clone the repo into your `wp-plugins` directory
* Run `composer install` in the root of the plugin, ex: `cd wp-plugins/apiarium-wp-plugin; composer install`

## Example Settings

Make a Page with the following text in it:

```
[display theme=green]
  [display-row]
    [display-column size=1]
      [display-flex size=1 classes=apiarium__no-border]
        <img src="https://commguide.asu.edu/files/endorsed/color/JAW-GIOS_RGB.png" />
      [/display-flex]
      [display-flex size=2]
        [display-weather]
      [/display-flex]
      [display-flex size=3]
        <h2>Twitter</h2>
        [display-twitter search="@asugreen"]
      [/display-flex]
    [/display-column]
    [display-column size=2]
      [display-flex size=1 classes=apiarium__no-border]
        [display-slider content=image]
          https://wp.sustainability.dev.gios.asu.edu/?feed=events_rss2
        [/display-slider]
      [/display-flex]
    [/display-column]
    [display-column size=1]
      [display-flex size=1 classes=apiarium__no-border]
        [display-calendar]
      [/display-flex]
      [display-flex size=2]
        [display-slider content=heading,image]
          https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200
        [/display-slider]
      [/display-flex]
      [display-flex size=3]
        <h2>ASU News</h2>
        [display-slider layout=newspaper content="heading,caption"]
          https://asunow.asu.edu/feeds/renewable-energy 
          https://asunow.asu.edu/feeds/sustainability%2Cglobal-institute-of
          https://asunow.asu.edu/feeds/sustainability%2Cschool-of 
          https://asunow.asu.edu/feeds/sustainability 
          https://asunow.asu.edu/feeds/sustainability-asu 
        [/display-slider]
      [/display-flex]
    [/display-column]
  [/display-row]
[/display]
```

* `/tag/test/feed/` will pull in posts from your blog with the `test` tag.

## Additional Credits

Icons are provided thanks to [MerlinTheRed](http://merlinthered.deviantart.com/art/plain-weather-icons-157162192) under the Creative Commons Attribution-ShareAlike license.