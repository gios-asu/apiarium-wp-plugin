/**
 * Set the Bootstrap Carousel items to be the
 * height of their container
 */
+function requireJquery( $ ) {
  $( function onLoad() {
    $( '.item' ).each( function setItemHeight( i, e ) {
      var el, flexEl, closestSliderEl, height;

      el              = $( e );
      flexEl          = el.closest( '.apiarium__flex' );
      closestSliderEl = el.closest( '.apiarium__slider' );

      height = flexEl.height() - 10;
      // Substract the height of all other children
      flexEl.children().not( closestSliderEl ).each( function ( i, e ) {
        height -= $( e ).outerHeight();
      } );

      el.css( 'height', height + 'px' );
    } );

    $( '.carousel' ).carousel( {
      pause: 'false'
    } );

    setInterval( function animateTweets () {
      $( '.apiarium__tweet' ).first()
      .each( function animate ( i, e ) {
        $(e).animate(
          {
            'margin-top': - $(e).height() + 'px'
          },
          'slow',
          function after () {
            $( this).parent().append( $(this ) );
            $( this ).css( 'margin-top', '0px' );
          }
        );
      } );
    }, 10000 /* 10 seconds */ );
  } );
}( jQuery );
