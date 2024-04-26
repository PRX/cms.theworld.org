<?php
/**
 * Plugin Name: TW Previews
 * Description: Creates admin UI to interact with front-end preview URL's via iframe and post events.
 * Version:     1.0.0
 * Text Domain: tw-text
 *
 * @package tw_previews
 */

// No direct access allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
* Define Plugins Contants
*/
define( 'TW_PREVIEWS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'TW_PREVIEWS_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );

// Setup admin UI.
require_once plugin_dir_path( __FILE__ ) . 'preview/ui.php';
