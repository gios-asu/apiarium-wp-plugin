<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Structure_Shortcodes extends Hook {
  public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_shortcode( 'display', $this, 'display' );
    $this->add_shortcode( 'display-row', $this, 'display_row' );
    $this->add_shortcode( 'display-column', $this, 'display_column' );
    $this->add_shortcode( 'display-flex', $this, 'display_flex' );
  }

  public function display( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'theme' => 'na'
        ),
        $atts,
        'apiarium'
    );

    $theme  = $atts['theme'];
    $inner_html = do_shortcode( $content );

    return "<div class='apiarium__{$theme}'>{$inner_html}</div>";
  }

  public function display_row( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'size' => 1
        ),
        $atts,
        'apiarium'
    );

    $flex_size  = $atts['size'];
    $inner_html = do_shortcode( $content );

    return "<div class='apiarium__row' style='flex: {$flex_size}'>{$inner_html}</div>";
  }

  public function display_column( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'size' => 1
        ),
        $atts,
        'apiarium'
    );

    $flex_size  = $atts['size'];
    $inner_html = do_shortcode( $content );

    return "<div class='apiarium__column' style='flex: {$flex_size}'>{$inner_html}</div>";
  }

  public function display_flex( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
          'size' => 1,
          'classes' => '',
        ),
        $atts,
        'apiarium'
    );

    $flex_size  = $atts['size'];
    $classes    = $atts['classes'];
    $inner_html = do_shortcode( $content );

    return "<div class='apiarium__flex {$classes}' style='flex: {$flex_size}'>{$inner_html}</div>";
  }
}
