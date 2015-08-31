![Apiarium](documentation/images/apiarium-with-text.png)

Television Screen and Kiosk WordPress Plugin

## Icons

Icons are provided thanks to [MerlinTheRed](http://merlinthered.deviantart.com/art/plain-weather-icons-157162192) under the Creative Commons Attribution-ShareAlike license.

## Example Settings

```
[display theme=green]
  [display-row]
    [display-column size=1]
      [display-flex size=1]
        <img src="https://commguide.asu.edu/files/endorsed/color/JAW-GIOS_RGB.png" />
      [/display-flex]
      [display-flex size=2]
        [display-weather]
      [/display-flex]
      [display-flex size=3]
        [display-twitter search="@asugreen"]
      [/display-flex]
    [/display-column]
    [display-column size=2]
      [display-flex size=1]
        [display-slider content=image]
          /tag/test/feed/
        [/display-slider]
      [/display-flex]
    [/display-column]
    [display-column size=1]
      [display-flex size=1]
        [display-calendar]
      [/display-flex]
      [display-flex size=2]
        [display-slider content=heading,image]
          https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200
        [/display-slider]
      [/display-flex]
      [display-flex size=3]
        <h2>ASU News</h2>
        [display-slider layout=newspaper]
          /tag/test/feed/
        [/display-slider]
      [/display-flex]
    [/display-column]
  [/display-row]
[/display]
```