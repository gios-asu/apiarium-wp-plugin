![Apiarium](documentation/images/apiarium-with-text.png)

Television Screen and Kiosk WordPress Plugin

## Example Settings

```
[display-row]
  [display-column size=2]
    [display-flex size=1]
      [display-slider]
        /tag/test/feed/
        /tag/test/feed/
      [/display-slider]
    [/display-flex]
    [display-flex size=1]
      [display-slider content=heading,image]
        https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200
      [/display-slider]
    [/display-flex]
  [/display-column]
  [display-column size=1]
    [display-flex size=1]
      [display-slider layout=newspaper]
        /tag/test/feed/
        https://api.flickr.com/services/feeds/photos_public.gne?id=55424394@N03&lang=en-us&format=rss_200
      [/display-slider]
    [/display-flex]
    [display-flex size=1]
      [display-slider]
        /tag/test/feed/
      [/display-slider]
    [/display-flex]
  [/display-column]
[/display-row]
```