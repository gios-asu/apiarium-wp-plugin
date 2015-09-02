<?php

/**
 * Register any shortcodes, pages, enqueuers, etc here
 */

return [
  Apiarium\Services\Structure_Shortcodes::class,
  Apiarium\Services\Slider_Shortcodes::class,
  Apiarium\Services\Weather_Shortcodes::class,
  Apiarium\Services\Twitter_Shortcodes::class,
  Apiarium\Services\Css_Enqueue::class,
  Apiarium\Services\Javascript_Enqueue::class,
  Apiarium\Services\Page_Templates::class,
  Apiarium\Services\Admin_Panel::class,
];
