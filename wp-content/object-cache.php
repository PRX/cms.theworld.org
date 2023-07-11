<?php
# This is a Windows-friendly symlink
$objectCacheFile = WP_CONTENT_DIR . '/plugins/wp-redis/object-cache.php';
if (!empty($_ENV['PANTHEON_ENVIRONMENT']) && !empty($_ENV['CACHE_HOST']) && file_exists($objectCacheFile)) {
    require_once $objectCacheFile;
}
