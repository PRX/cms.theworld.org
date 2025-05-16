<?php
/**
 * Plugin Name: TW RSS Helper
 * Plugin URI: https://dinkuminteractive.com
 * Description: Adds RSS audio support to posts using ACF.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version: 1.0.0
 * Author: Dinkum Interactive
 * Author URI: https://dinkuminteractive.com
 * License: GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 */
define( 'TW_RSS_HELPER_VERSION', '1.0.0' );

/**
 * Plugin path.
 */
define( 'TW_RSS_HELPER_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Include the RSS functionality.
 */
require_once TW_RSS_HELPER_PATH . 'includes/rss.php';
