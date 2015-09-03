<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Calendar_Shortcodes extends Hook {
  public function __construct() {
    $this->define_hooks();
  }

  public function define_hooks() {
    $this->add_shortcode( 'display-calendar', $this, 'display_calendar' );
  }

  public function display_calendar( $atts, $content = '' ) {
    $calender_template = <<<HTML
        <div class="apiarium__calendar__date">
          <div class="apiarium__calendar__date__icon">
            <div class="apiarium__calendar__date__icon__month">%s</div>
            <div class="apiarium__calendar__date__icon__date">%s</div>
          </div>
        </div>
        <div class="apiarium__calendar__time" id="apiarium-calendar-time">
          %s
        </div>
HTML;
    date_default_timezone_set( 'America/Phoenix' );
    $month                = date( 'M' );
    $day_of_the_month     = date( 'd' );
    $current_time         = date( 'h:i A' );
    $calender_time        = sprintf(
        $calender_template,
        $month,
        $day_of_the_month,
        $current_time
    );
    $kiosk_time_div = '<div class="apiarium__calendar" id="apiarium-calendar">'
        . $calender_time . '</div>';
    return $kiosk_time_div;
  }
}