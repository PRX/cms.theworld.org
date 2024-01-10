<?php
/**
 * Plugin Name: TW Episode Importer
 * Description: Creates admin UI and API to import episodes and segments from Dovetail using public API.
 * Version:     1.0.0
 * Text Domain: tw-text
 *
 * @package tw_episode_importer
 */

// No direct access allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
* Define Plugins Contants
*/
define( 'TW_EPISODE_IMPORTER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'TW_EPISODE_IMPORTER_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'TW_EPISODE_IMPORTER_CACHE_GROUP', 'tw-cache' );
define( 'TW_EPISODE_IMPORTER_CACHE_POST_IDS_KEY_PREFIX', 'post_ids_for_guid' );
define( 'TW_EPISODE_IMPORTER_CACHE_AUDIO_ID_KEY_PREFIX', 'audio_id_for_guid' );
define( 'TW_API_ROUTE_BASE', 'tw/v2' );
define( 'TW_EPISODE_IMPORTER_SETTINGS_PAGE', 'episode-importer-settings' );
define( 'TW_EPISODE_IMPORTER_SETTINGS_SECTION', 'episode-importer-settings-section' );
define( 'TW_EPISODE_IMPORTER_SETTINGS_API', 'episode-importer-settings-api' );
define( 'TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY', 'episodes-api-url' );
define( 'TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY', 'segments-api-url' );
define( 'TW_EPISODE_IMPORTER_AUTHOR_USER_ID_KEY', 'author-user-id' );
define( 'TW_EPISODE_IMPORTER_PROGRAM_ID_KEY', 'program-id' );

// Setup settings page.
// SEE: https://alazierplace.com/2018/06/how-to-save-custom-settings-for-your-wordpress-plugin/ .
require_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';

// Setup API endpoints.
require_once plugin_dir_path( __FILE__ ) . 'api/api.php';

// Setup admin UI.
require_once plugin_dir_path( __FILE__ ) . 'admin/ui.php';
