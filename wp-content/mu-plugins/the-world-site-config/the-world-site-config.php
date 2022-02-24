<?php
/**
 * @package the_world_site_config
 */
/*
Plugin Name: The World Site Config
Plugin URI: https://pantheon.io/docs/environment-specific-config
Description: Activates and deactivates plugins based on environment.
Version: 1.1
Author: Joe Tower, Rick Peterman
Text Domain: the_world_site_config
*/

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require plugins helpers so configs can manage plugins.
 */
require_once(dirname(__FILE__) . '/includes/plugins.php');

/**
 * Gather plugins to activate.
 */
$plugins_to_activate = array();

// Get global plugins.
require_once(dirname(__FILE__) . '/configs/global/global-plugins.php');
$plugins_to_activate = array_merge($plugins_to_activate, GLOBAL_PLUGINS);

// Get production plugins.
if ( in_array( getenv('WP_ENVIRONMENT_TYPE'), array( 'staging', 'production' ) ) ){
  require_once(dirname(__FILE__) . '/configs/production/production-plugins.php');
  $plugins_to_activate = array_merge($plugins_to_activate, PRODUCTION_PLUGINS);
}
// Get development plugins.
else {
  require_once(dirname(__FILE__) . '/configs/development/development-plugins.php');
  $plugins_to_activate = array_merge($plugins_to_activate, DEVELOPMENT_PLUGINS);
}

// Activate plugins.
tw_activate_plugins($plugins_to_activate);

// Deactivate all other plugins.
$all_plugins = array_keys(get_plugins());
$plugins_to_deactivate = array_diff($all_plugins, $plugins_to_activate);
tw_deactivate_plugins($plugins_to_deactivate);

/**
 * Enable global configs.
 */
require_once(dirname(__FILE__) . '/configs/global/global-config.php');

/**
 * Enable production environment configs.
 * - Staging
 * - Production
*/
if ( in_array( getenv('WP_ENVIRONMENT_TYPE'), array( 'staging', 'production' ) ) ){
  require_once(dirname(__FILE__) . '/configs/production/production-config.php');
}
/**
 * Enable development environment configs.
 * - Local
 * - Development
*/
else {
  require_once(dirname(__FILE__) . '/configs/development/development-config.php');
}
