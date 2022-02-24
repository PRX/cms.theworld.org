<?php

require_once(dirname(__FILE__) . '/wp-config-constants.php');

/**
 * Server platform settings.
 *
 * Loads on platform servers and Lando environments using a recipe for that platform.
 *
 * This config MUST translate platform environment type variable to the appropriate
 * value for `WP_ENVIRONMENT_TYPE`, which will be used to load environment config settings.
 */
if (file_exists(dirname(__FILE__) . '/wp-config-' . SERVER_PLATFORM_NAME . '.php') && isset($_ENV[SERVER_PLATFORM_ENVIRONMENT_VARIABLE_NAME])) {
	require_once(dirname(__FILE__) . '/wp-config-' . SERVER_PLATFORM_NAME . '.php');

/**
 * Local configuration information.
 *
 * If you are working in a local/desktop development environment and want to
 * keep your config separate, we recommend using a 'wp-config-local.php' file,
 * which you should also make sure you .gitignore.
 *
 * Not loaded when server platform config would be used, eg. Lando environment using a platform recipe.
 */
} elseif (file_exists(dirname(__FILE__) . '/wp-config-local.php') && !isset($_ENV[SERVER_PLATFORM_ENVIRONMENT_VARIABLE_NAME])){
	/**
	 * Set WP_ENVIRONMENT_TYPE to development.
	 */
	if (getenv('WP_ENVIRONMENT_TYPE') === false) {
		putenv('WP_ENVIRONMENT_TYPE=development');
	}
	# IMPORTANT: ensure your local config does not include wp-settings.php
	require_once(dirname(__FILE__) . '/wp-config-local.php');

/**
 * This block will be executed if you are NOT running on platform server or Lando recipe, and have NO
 * wp-config-local.php. Insert alternate config here if necessary.
 *
 * If you are only running on a platform server or Lando recipe, you can ignore this block.
 */
} else {
  /**
   *
   */
	// define('DB_NAME',          'database_name');
	// define('DB_USER',          'database_username');
	// define('DB_PASSWORD',      'database_password');
	// define('DB_HOST',          'database_host');
	// define('DB_CHARSET',       'utf8');
	// define('DB_COLLATE',       '');
	// define('AUTH_KEY',         'put your unique phrase here');
	// define('SECURE_AUTH_KEY',  'put your unique phrase here');
	// define('LOGGED_IN_KEY',    'put your unique phrase here');
	// define('NONCE_KEY',        'put your unique phrase here');
	// define('AUTH_SALT',        'put your unique phrase here');
	// define('SECURE_AUTH_SALT', 'put your unique phrase here');
	// define('LOGGED_IN_SALT',   'put your unique phrase here');
	// define('NONCE_SALT',       'put your unique phrase here');
}


/** Standard wp-config.php stuff from here on down. **/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * Enable production environment configs.
 * - Staging
 * - Production
*/
if ( in_array( getenv('WP_ENVIRONMENT_TYPE'), array( 'staging', 'production' ) ) ){
	# IMPORTANT: ensure production config does not include wp-settings.php
  require_once(dirname(__FILE__) . '/wp-config-production.php');
}
/**
 * Enable development environment configs.
 * - Local
 * - Development
*/
else {
	# IMPORTANT: ensure development config does not include wp-settings.php
  require_once(dirname(__FILE__) . '/wp-config-development.php');
}

/* That's all, stop editing! Happy Pressing. */




/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
