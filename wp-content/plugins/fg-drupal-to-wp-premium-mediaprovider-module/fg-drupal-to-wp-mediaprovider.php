<?php
/**
 * Plugin Name: FG Drupal to WordPress Premium Media Provider module
 * Depends:		FG Drupal to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * Description: A plugin to import the media field links (YouTube video, Vimeo video, SoundCloud podcasts)
 * 				Needs the plugin «FG Drupal to WordPress Premium» to work
 * Version:     1.2.1
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'fgd2wp_mediaprovider_test_requirements' );

if ( !function_exists( 'fgd2wp_mediaprovider_test_requirements' ) ) {
	function fgd2wp_mediaprovider_test_requirements() {
		new fgd2wp_mediaprovider_requirements();
	}
}

if ( !class_exists('fgd2wp_mediaprovider_requirements', false) ) {
	class fgd2wp_mediaprovider_requirements {
		private $parent_plugin = 'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php';
		private $required_premium_version = '2.7.0';

		public function __construct() {
			load_plugin_textdomain( 'fgd2wp_mediaprovider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			if ( !is_plugin_active($this->parent_plugin) ) {
				add_action( 'admin_notices', array($this, 'error') );
			} else {
				$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->parent_plugin);
				if ( !$plugin_data or version_compare($plugin_data['Version'], $this->required_premium_version, '<') ) {
					add_action( 'admin_notices', array($this, 'version_error') );
				}
			}
		}
		
		/**
		 * Print an error message if the Premium plugin is not activated
		 */
		function error() {
			echo '<div class="error"><p>[fgd2wp_mediaprovider] '.__('The Media Provider module needs the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong>.', 'fgd2wp_mediaprovider').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>';
		}
		
		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function version_error() {
			printf('<div class="error"><p>[fgd2wp_mediaprovider] '.__('The Media Provider module needs at least the <strong>version %s</strong> of the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong> at least the <strong>version %s</strong>.', 'fgd2wp_mediaprovider').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version);
		}
	}
}

if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') && !defined('DOING_CRON') && !defined('WP_CLI') ) {
	return;
}

add_action( 'plugins_loaded', 'fgd2wp_mediaprovider_load', 25 );

if ( !function_exists( 'fgd2wp_mediaprovider_load' ) ) {
	function fgd2wp_mediaprovider_load() {
		if ( !defined('FGD2WPP_LOADED') ) return;

		load_plugin_textdomain( 'fgd2wp_mediaprovider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		global $fgd2wpp;
		new fgd2wp_mediaprovider($fgd2wpp);
	}
}

if ( !class_exists('fgd2wp_mediaprovider', false) ) {
	class fgd2wp_mediaprovider {
		
		private $plugin = '';
		private $s3_path = '';
		private $youtube_path = '';
		private $vimeo_path = '';
		private $soundcloud_path = '';
		
		/**
		 * Sets up the plugin
		 *
		 */
		public function __construct($plugin) {
			
			$this->plugin = $plugin;
			
			add_filter('fgd2wp_pre_display_admin_page', array($this, 'process_admin_page'), 11, 1);
			add_action('fgd2wp_set_default_file_paths', array($this, 'set_default_file_paths'));
			add_filter('fgd2wp_get_path_from_uri', array($this, 'convert_uri'));
			add_filter('fgd2wp_get_custom_field', array($this, 'modify_custom_field'), 10, 3);
		}
		
		/**
		 * Add information to the admin page
		 * 
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page($data) {
			$data['title'] .= ' ' . __('+ Media Provider module', __CLASS__);
			$data['description'] .= "<br />" . __('The Media Provider module will also convert the external media URLs (YouTube, SoundCloud, S3).', __CLASS__);
			
			return $data;
		}
		
		/**
		 * Set the default paths
		 * 
		 */
		public function set_default_file_paths() {
			$bucket = $this->plugin->get_drupal_variable('amazons3_bucket');
			$region = '-' . $this->plugin->get_drupal_variable('s3fs_region');
			if ( empty($bucket) ) {
				$bucket = $this->plugin->get_drupal_variable('s3fs_bucket');
				$region = '';
			}
			$this->s3_path = trailingslashit('https://' . $bucket . '.s3' . $region . '.amazonaws.com/'); // Amazon S3 path
			$this->youtube_path = 'https://www.youtube.com/watch?v='; // YouTube path
			$this->vimeo_path = 'https://vimeo.com/'; // Vimeo path
			$this->soundcloud_path = 'https://soundcloud.com/'; // SoundCloud path
		}

		/**
		 * Converts the URIs
		 * 
		 * @param string $uri URI
		 * @return string URI
		 */
		public function convert_uri($uri) {
			$uri = str_replace('s3://', $this->s3_path, $uri); // Amazon S3
			$uri = str_replace('youtube://v/', $this->youtube_path, $uri); // YouTube
			$uri = str_replace('vimeo://v/', $this->vimeo_path, $uri); // Vimeo
			$uri = preg_replace('#^soundcloud://u/(.*?)/a/(.*)#', $this->soundcloud_path . "$1/$2", $uri); // SoundCloud
			return $uri;
		}
		
		/**
		 * Modify a custom field before using it during the import
		 * 
		 * @param array $field Field data
		 * @param array $data Data
		 * @param array $data_instance Data instance
		 * @return array Field data
		 */
		public function modify_custom_field($field, $data, $data_instance) {
			if ( isset($data_instance['widget']['settings']['browser_plugins']['media_internet']) && ($data_instance['widget']['settings']['browser_plugins']['media_internet'] === 'media_internet') ) {
				// Embedded media (SoundCloud, YouTube, Vimeo)
				$field['type'] = 'embed';
			}
			return $field;
		}
		
	}
}
