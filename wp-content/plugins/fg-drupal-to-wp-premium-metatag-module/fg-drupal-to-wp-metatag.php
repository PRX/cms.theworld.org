<?php
/**
 * Plugin Name: FG Drupal to WordPress Premium Metatag module
 * Depends:		FG Drupal to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * Description: A plugin to migrate the meta tags from Drupal to WordPress
 * 				Needs the plugin «FG Drupal to WordPress Premium» to work
 *				Needs the Yoast SEO plugin to manage the meta tags in WordPress
 * Version:     1.8.0
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'fgd2wp_metatag_test_requirements' );

if ( !function_exists( 'fgd2wp_metatag_test_requirements' ) ) {
	function fgd2wp_metatag_test_requirements() {
		new fgd2wp_metatag_requirements();
	}
}

if ( !class_exists('fgd2wp_metatag_requirements', false) ) {
	class fgd2wp_metatag_requirements {
		private $parent_plugin = 'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php';
		private $required_premium_version = '1.56.0';

		public function __construct() {
			load_plugin_textdomain( 'fgd2wp_metatag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
			echo '<div class="error"><p>[fgd2wp_metatag] '.__('The Metatag module needs the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong>.', 'fgd2wp_metatag').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>';
		}
		
		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function version_error() {
			printf('<div class="error"><p>[fgd2wp_metatag] '.__('The Metatag module needs at least the <strong>version %s</strong> of the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong> at least the <strong>version %s</strong>.', 'fgd2wp_metatag').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version);
		}
	}
}

if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') && !defined('DOING_CRON') && !defined('WP_CLI') ) {
	return;
}

add_action( 'plugins_loaded', 'fgd2wp_metatag_load', 25 );

if ( !function_exists( 'fgd2wp_metatag_load' ) ) {
	function fgd2wp_metatag_load() {
		if ( !defined('FGD2WPP_LOADED') ) return;

		load_plugin_textdomain( 'fgd2wp_metatag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		global $fgd2wpp;
		new fgd2wp_metatag($fgd2wpp);
	}
}

if ( !class_exists('fgd2wp_metatag', false) ) {
	class fgd2wp_metatag {
		
		private $metatag_table_exists = false;
		private $node__field_meta_table_exists = false;
		private $node__field_meta_tags_table_exists = false;
		private $node__field_metatags_table_exists = false;
		private $nodewords_table_exists = false;
		private $page_title_table_exists = false;
		private $simplemeta_table_exists = false;
		private $revision_id_column_exists = false;
		
		/**
		 * Sets up the plugin
		 *
		 */
		public function __construct($plugin) {
			
			$this->plugin = $plugin;
			
			add_action( 'fgd2wp_pre_import', array ($this, 'check_tables'));
			add_action( 'fgd2wp_post_insert_post', array ($this, 'import_metas'), 15, 2);
			add_filter( 'fgd2wp_pre_display_admin_page', array ($this, 'process_admin_page'), 11, 1 );
		}
		
		/**
		 * Check if some Drupal tables exist
		 * 
		 * @since 1.1.0
		 */
		public function check_tables() {
			$this->metatag_table_exists = $this->plugin->table_exists('metatag');
			$this->node__field_meta_table_exists = $this->plugin->table_exists('node__field_meta');
			$this->node__field_meta_tags_table_exists = $this->plugin->table_exists('node__field_meta_tags');
			$this->node__field_metatags_table_exists = $this->plugin->table_exists('node__field_metatags');
			$this->nodewords_table_exists = $this->plugin->table_exists('nodewords');
			$this->page_title_table_exists = $this->plugin->table_exists('page_title');
			$this->simplemeta_table_exists = $this->plugin->table_exists('simplemeta');
			$this->revision_id_column_exists = $this->plugin->column_exists('metatag', 'revision_id');
		}
		
		/**
		 * Import the metas tags
		 * 
		 * @param int $new_post_id New post ID
		 * @param array $node Drupal Node
		 */
		public function import_metas($new_post_id, $node) {
			$metas = $this->get_metas($node['nid']);
			if ( is_array($metas) && count($metas) > 0 ) {
				if ( array_key_exists('title', $metas) && !empty($metas['title']) ) {
					update_post_meta($new_post_id, '_yoast_wpseo_title', $this->convert_shortcodes($metas['title'], $node));
				}
				if ( array_key_exists('description', $metas) && !empty($metas['description']) ) {
					update_post_meta($new_post_id, '_yoast_wpseo_metadesc', $this->convert_shortcodes($metas['description'], $node));
				}
				if ( array_key_exists('keywords', $metas) && !empty($metas['keywords']) ) {
					update_post_meta($new_post_id, '_yoast_wpseo_metakeywords', $metas['keywords']);
				}
			}
		}

		/**
		 * Get the metas
		 * 
		 * @param int $node_id Drupal node ID
		 * @return array Array of metas
		 */
		private function get_metas($node_id) {
			$metas = array();
			if ( $this->node__field_meta_table_exists ) {
				// Drupal 8 node__field_meta
				$metas = $this->get_drupal8_metatags($node_id, 'field_meta');
			} elseif ( $this->node__field_meta_tags_table_exists ) {
				// Drupal 8 node__field_meta_tags
				$metas = $this->get_drupal8_metatags($node_id, 'field_meta_tags');
			} elseif ( $this->node__field_metatags_table_exists ) {
				// Drupal 8 node__field_metatags
				$metas = $this->get_drupal8_metatags($node_id, 'field_metatags');
			} elseif ( $this->simplemeta_table_exists ) {
				// Simplemeta module
				$metas = $this->get_simplemeta($node_id);
			} elseif ( $this->metatag_table_exists ) {
				// Metatag module
				$metas = $this->get_metatag($node_id);
			} else {
				$metas = array();
				if ( $this->nodewords_table_exists ) {
					// Nodewords module
					$metas = $this->get_nodewords($node_id);
				}
				if ( $this->page_title_table_exists ) {
					// Page Title module
					$page_title = $this->get_page_title($node_id);
					if ( !empty($page_title) ) {
						$metas['title'] = $page_title;
					}
				}
			}
			return $metas;
		}
		
		/**
		 * Get the Drupal 8 metas
		 * 
		 * @since 1.3.0
		 * 
		 * @param int $node_id Drupal node ID
		 * @param string $field_name Drupal meta field name
		 * @return array Array of metas
		 */
		private function get_drupal8_metatags($node_id, $field_name) {
			$metas = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT m.{$field_name}_value AS data
				FROM ${prefix}node__{$field_name} m
				WHERE m.entity_id = '$node_id'
				ORDER BY m.revision_id DESC
				LIMIT 1
			";
			$result = $this->plugin->drupal_query($sql);
			if ( isset($result[0]['data']) ) {
				$serialized_metatags = $result[0]['data'];
				$metas = unserialize($serialized_metatags);
			}
			return $metas;
		}
		
		/**
		 * Get the Metatag metas
		 * 
		 * @since 1.1.0
		 * 
		 * @param int $node_id Drupal node ID
		 * @return array Array of metas
		 */
		private function get_metatag($node_id) {
			$metas = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			if ( $this->revision_id_column_exists ) {
				$order_by = 'ORDER BY m.revision_id DESC';
			} else {
				$order_by = '';
			}
			$sql = "
				SELECT m.data
				FROM ${prefix}metatag m
				WHERE m.entity_type = 'node'
				AND m.entity_id = '$node_id'
				$order_by
				LIMIT 1
			";
			$result = $this->plugin->drupal_query($sql);
			if ( isset($result[0]['data']) ) {
				$serialized_metatags = $result[0]['data'];
				$metas = unserialize($serialized_metatags);

				// Remove the "value" level
				foreach ( $metas as &$value ) {
					if ( isset($value['value']) ) {
						$value = $value['value'];
					}
				}
			}
			return $metas;
		}
		
		/**
		 * Get the Nodewords metas
		 * 
		 * @since 1.1.0
		 * 
		 * @param int $node_id Drupal node ID
		 * @return array Array of metas
		 */
		private function get_nodewords($node_id) {
			$metas = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT n.name, n.content
				FROM ${prefix}nodewords n
				WHERE n.id = '$node_id'
				AND name IN ('description', 'keywords', 'canonical')
			";
			$result = $this->plugin->drupal_query($sql);
			foreach ( $result as $row ) {
				if ( strpos($row['content'], 'a:') === 0 ) {
					// Serialized content
					$content_unserialize = unserialize($row['content']);
					if ( isset($content_unserialize['value']) ) {
						$metas[$row['name']] = $content_unserialize['value'];
					} else {
						$metas[$row['name']] = $content_unserialize;
					}
				} else {
					// Raw content
					$metas[$row['name']] = $row['content'];
				}
			}
			return $metas;
		}
		
		/**
		 * Get the Simple metas
		 * 
		 * @since 1.4.0
		 * 
		 * @param int $node_id Drupal node ID
		 * @return array Array of metas
		 */
		private function get_simplemeta($node_id) {
			$metas = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT s.data
				FROM ${prefix}simplemeta s
				WHERE s.path = 'node/$node_id'
				LIMIT 1
			";
			$result = $this->plugin->drupal_query($sql);
			if ( isset($result[0]['data']) ) {
				$serialized_metatags = $result[0]['data'];
				$metas = unserialize($serialized_metatags);
			}
			return $metas;
		}
		
		/**
		 * Get the Page Title
		 * 
		 * @since 1.1.0
		 * 
		 * @param int $node_id Drupal node ID
		 * @return string Page title
		 */
		private function get_page_title($node_id) {
			$page_title = '';
			$prefix = $this->plugin->plugin_options['prefix'];

			$sql = "
				SELECT p.page_title
				FROM ${prefix}page_title p
				WHERE p.type = 'node'
				AND p.id = '$node_id'
				LIMIT 1
			";
			$result = $this->plugin->drupal_query($sql);
			if ( count($result) > 0 ) {
				$page_title = $result[0]['page_title'];
			}
			return $page_title;
		}
		
		/**
		 * Convert the shortcodes contained in the meta fields
		 * 
		 * @since 1.5.0
		 * 
		 * @param string $content Content
		 * @param array $node Node
		 * @return string Content
		 */
		private function convert_shortcodes($content, $node) {
			if ( isset($node['body_summary']) ) {
				$content = preg_replace('#' . preg_quote('[node:summary]') . '#', $node['body_summary'], $content);
			}
			$content = preg_replace('#' . preg_quote('[site:name]') . '#', '%%sitename%%', $content);
			return $content;
		}
		
		/**
		 * Add information to the admin page
		 * 
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page($data) {
			$data['title'] .= ' ' . __('+ Metatag module', __CLASS__);
			$data['description'] .= "<br />" . sprintf(__('The Metatag module will also import the Drupal meta data (meta title, meta description, meta keywords) to WordPress. It requires the <a href="%s" target="_blank">Yoast SEO plugin</a> to manage the meta data.', __CLASS__), 'https://wordpress.org/plugins/wordpress-seo/');
			
			return $data;
		}

	}
}
