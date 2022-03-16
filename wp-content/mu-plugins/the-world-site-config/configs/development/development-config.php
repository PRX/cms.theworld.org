<?php
/**
 * Configuration for development environments.
 */


/**
 * STOP development configuration here.
 */

/**
 * Load local specific config now so it can override
 * development settings.
 */
if (file_exists(dirname(__FILE__) . '/local-config.php')) {
  require_once(dirname(__FILE__) . '/local-config.php');
}
