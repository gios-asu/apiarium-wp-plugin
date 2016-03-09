<?php
/*
Template Name: Posts Default Template
Description: Simple scaffolding to display the posts content and the sidebar.
*/

// Avoid direct calls to this file
if ( ! defined( 'APIARIUM_WP_VERSION' ) ) {
  header( 'Status: 403 Forbidden' );
  header( 'HTTP/1.1 403 Forbidden' );
  exit();
}

function enqueue_head() {
  echo '<link
  rel="stylesheet"
  href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
  crossorigin="anonymous">';
}

add_action('wp_head', 'enqueue_head');

wp_head(); ?>



<div class="apiarium__viewport">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
    the_content();
endwhile; endif; ?>
</div>

<?php wp_footer();