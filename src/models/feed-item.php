<?php

namespace Apiarium\Models;

/**
 * Simple data model for passing
 * around item data from a feed,
 * whether that feed is RSS or JSON.
 */
class Feed_Item {
  public $id;
  public $title;
  public $description;
  public $image;
  public $post_date;
  public $metadata;
}
