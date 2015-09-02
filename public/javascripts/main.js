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
  } );
}( jQuery );
