/**
 * Set the Bootstrap Carousel items to be the
 * height of their container
 */
+function requireJquery( $ ) {
  $( function onLoad() {
    $( '.item' ).each( function setItemHeight( i, e ) {
      var el, height;

      el     = $( e );
      height = el.closest( '.apiarium__flex' ).height();

      el.css( 'height', height + 'px' );
    } );

    $( '.carousel' ).carousel( {
      pause: 'false'
    } );

    setInterval( function animate_tweets () {
      $( '.apiarium__tweet').first().each( function animate ( i,e ) {
        $(e).animate({
            'margin-top': - $(e).height() + 'px'
          }, 'slow', function after () {
            $(this).parent().append( $(this ) ) 
        });
      });
    }, 10000 /* 10 seconds */ );
  } );
}( jQuery );
