<?php

namespace Nassau\Cache;

/**
 * This resource can be cached
 **/
Interface CacheInfoInterface {
  /**
   * @retrun \DateTime last modified time or NULL (cannot be cached)
   **/
  public function getLastModifiedTime();

  /**
   * returns an ETag of resource
   *
   * ETag could be md5sum or some other hash of the contents.
   *
   * @return String ETag or NULL (cannot be cached)
   **/
  public function getETag();
}

