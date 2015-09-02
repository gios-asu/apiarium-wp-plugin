<?php

namespace Apiarium\Services;

use Honeycomb\Wordpress\Hook;

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

class Page_Templates extends Hook {
  use \Honeycomb\Traits\Page_Template_Trait;

  protected $templates;
  protected $path_to_templates;

  public function __construct() {
    $this->templates = array(
      'default-template.php' => 'Kiosk Template'
    );

    $this->path_to_templates = plugin_dir_path( __FILE__ );
    $this->path_to_templates .= 'views/';

    $this->load_dependencies();
    $this->define_hooks();
  }
}
