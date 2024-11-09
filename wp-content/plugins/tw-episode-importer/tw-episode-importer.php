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

// Actions and filters.

if ( ! function_exists( 'tw_episode_importer_before_delete_post' ) ) {
	/**
	 * Action hook to remove deleted post id from post ID's cache for its attached audio.
	 *
	 * @param string $post_id ID of post.
	 * @return void
	 */
	function tw_episode_importer_before_delete_post( $post_id ) {
		$post_audio_id = get_field( 'audio', $post_id );

		if ( is_array( $post_audio_id ) ) {
			$post_audio_id = $post_audio_id['ID'];
		}

		if ( $post_audio_id ) {
			$post_audio         = get_post( $post_audio_id );
			$guid               = preg_replace( '~$https?://~', '', $post_audio->guid );
			$post_ids_cache_key = TW_EPISODE_IMPORTER_CACHE_POST_IDS_KEY_PREFIX . ':' . $guid;
			$ids                = wp_cache_get( $post_ids_cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );

			if ( $ids ) {
				$cache_ids = array_filter(
					$ids,
					function ( $id ) use ( $post_id ) {
						return ( $id !== $post_id );
					}
				);
				wp_cache_set( $post_ids_cache_key, $cached_ids, TW_EPISODE_IMPORTER_CACHE_GROUP );
			}
		}
	}
}
add_action( 'before_delete_post', 'tw_episode_importer_before_delete_post', 99, 1 );

if ( ! function_exists( 'tw_episode_importer_delete_attachment' ) ) {
	/**
	 * Action hook to remove audio id cache for deleted attachment.
	 *
	 * @param string $post_id ID of attachment post.
	 * @return void
	 */
	function tw_episode_importer_delete_attachment( $post_id ) {
		$post               = get_post( $post_id );
		$guid               = preg_replace( '~$https?://~', '', $post->guid );
		$audio_id_cache_key = TW_EPISODE_IMPORTER_CACHE_AUDIO_ID_KEY_PREFIX . ':' . $guid;
		wp_cache_delete( $audio_id_cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );
	}
}
add_action( 'delete_attachment', 'tw_episode_importer_delete_attachment', 99, 1 );
