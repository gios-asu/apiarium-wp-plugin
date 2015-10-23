<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;
use Honeycomb\Utilities\Option_Utilities;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

/**
 * Add a panel to the WordPress dashboard for users who have
 * the privledge of changing the settings.
 */
class Admin_Panel extends Hook {
  const ADMIN_PAGE_TITLE                  = 'Kiosk Plugin Settings';
  const ADMIN_MENU_TITLE                  = 'Kiosk Settings';
  const ADMIN_CAPABILITY                  = 'manage_options';
  const OPTIONS_NAME                      = 'apiarium_admin_options';
  const OPTIONS_GROUP                     = 'apiarium_admin_options_group';
  const OPTIONS_SECTION_NAME              = 'apiarium_admin_section_name';
  const OPTIONS_SECTION_ID                = 'apiarium_admin_section_id';
  const OPTIONS_TWITTER_SECTION_ID        = 'apiarium_admin_twitter_section_id';
  const TWITTER_OAUTH_ACCESS_TOKEN        = 'apiarium_twitter_oauth_access_token';
  const TWITTER_OAUTH_ACCESS_TOKEN_SECRET = 'apiarium_twitter_oauth_access_token_secret';
  const TWITTER_CONSUMER_KEY              = 'apiarium_twitter_consumer_key';
  const TWITTER_CONSUMER_SECRET           = 'apiarium_twitter_consumer_secret';

  // TODO inject the version
  public function __construct() {
    $this->plugin_slug = 'apiarium-admin-panel';
    $this->version     = '0.1';

    $this->set_default_options();
    $this->load_dependencies();
    $this->define_hooks();
  }

  /**
   * Set the default options for the admin panel
   */
  private function set_default_options() {
    add_option(
        self::OPTIONS_NAME,
        array(
          self::TWITTER_OAUTH_ACCESS_TOKEN        => '',
          self::TWITTER_OAUTH_ACCESS_TOKEN_SECRET => '',
          self::TWITTER_CONSUMER_KEY              => '',
          self::TWITTER_CONSUMER_SECRET           => '',
        )
    );
  }

  private function define_hooks() {
    $this->add_action( 'admin_enqueue_scripts', $this, 'admin_enqueue_scripts' );
    $this->add_action( 'admin_menu', $this, 'admin_menu' );
    $this->add_action( 'admin_init', $this, 'admin_init' );
  }

  public function admin_enqueue_scripts() {
    // TODO
  }

  public function admin_menu() {
    $path = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) );

    add_menu_page(
        self::ADMIN_PAGE_TITLE,
        self::ADMIN_MENU_TITLE,
        self::ADMIN_CAPABILITY,
        $this->plugin_slug,
        array( $this, 'render_title' ),
        $path . './public/images/admin/icon.png'
    );
  }

  public function admin_init() {
    register_setting(
        self::OPTIONS_GROUP,
        self::OPTIONS_NAME,
        array( $this, 'form_submit' )
    );

    add_settings_section(
        self::OPTIONS_TWITTER_SECTION_ID,
        'Twitter Settings',
        array(
          $this,
          'print_twitter_section_info',
        ),
        self::OPTIONS_SECTION_ID
    );

    add_settings_field(
        self::TWITTER_OAUTH_ACCESS_TOKEN,
        'Twitter OAUTH Access Token',
        function () {
          $default = Option_Utilities::get_or_default(
              self::OPTIONS_NAME,
              self::TWITTER_OAUTH_ACCESS_TOKEN,
              ''
          );

          printf(
              '<input id="%s" name="%s[%s]" value="%s" />',
              self::TWITTER_OAUTH_ACCESS_TOKEN,
              self::OPTIONS_NAME,
              self::TWITTER_OAUTH_ACCESS_TOKEN,
              $default
          );
        },
        self::OPTIONS_SECTION_ID,
        self::OPTIONS_TWITTER_SECTION_ID
    );

    add_settings_field(
        self::TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
        'Twitter OAUTH Access Token Secret',
        function () {
          $default = Option_Utilities::get_or_default(
              self::OPTIONS_NAME,
              self::TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
              ''
          );

          printf(
              '<input id="%s" name="%s[%s]" value="%s" />',
              self::TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
              self::OPTIONS_NAME,
              self::TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
              $default
          );
        },
        self::OPTIONS_SECTION_ID,
        self::OPTIONS_TWITTER_SECTION_ID
    );

    add_settings_field(
        self::TWITTER_CONSUMER_KEY,
        'Twitter Consumer Key',
        function () {
          $default = Option_Utilities::get_or_default(
              self::OPTIONS_NAME,
              self::TWITTER_CONSUMER_KEY,
              ''
          );

          printf(
              '<input id="%s" name="%s[%s]" value="%s" />',
              self::TWITTER_CONSUMER_KEY,
              self::OPTIONS_NAME,
              self::TWITTER_CONSUMER_KEY,
              $default
          );
        },
        self::OPTIONS_SECTION_ID,
        self::OPTIONS_TWITTER_SECTION_ID
    );

    add_settings_field(
        self::TWITTER_CONSUMER_SECRET,
        'Twitter Consumer Secret',
        function () {
          $default = Option_Utilities::get_or_default(
              self::OPTIONS_NAME,
              self::TWITTER_CONSUMER_SECRET,
              ''
          );

          printf(
              '<input id="%s" name="%s[%s]" value="%s" />',
              self::TWITTER_CONSUMER_SECRET,
              self::OPTIONS_NAME,
              self::TWITTER_CONSUMER_SECRET,
              $default
          );
        },
        self::OPTIONS_SECTION_ID,
        self::OPTIONS_TWITTER_SECTION_ID
    );
  }

  public function render_title() {
    $title       = self::ADMIN_PAGE_TITLE;
    $inner_html  = '';

    ob_start();

    settings_fields( self::OPTIONS_GROUP );
    do_settings_sections( self::OPTIONS_SECTION_ID );
    submit_button();

    $inner_html = ob_get_contents();

    ob_end_clean();


    $html = "
      <h2>{$title}</h2>
      <div class='wrap'>
        <form method='post' action='options.php'>
          {$inner_html}
        </form>
      </div>
    ";
 
    print $html;
  }

  /**
   * Handle and filter the form submission
   */
  public function form_submit( $input ) {
    return $input;
  }

  public function print_twitter_section_info() {
    // TODO this should also print documentation on how
    // to obtain a set of Twitter tokens
    print 'Enter your Twitter tokens below:';
  }
}