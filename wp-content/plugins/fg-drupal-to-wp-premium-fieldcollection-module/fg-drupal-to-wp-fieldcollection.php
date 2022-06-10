<?php
/**
 * Plugin Name: FG Drupal to WordPress Premium Field Collection module
 * Depends:		FG Drupal to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * Description: A plugin to migrate the Field Collection data from Drupal to WordPress
 * 				Needs the plugin «FG Drupal to WordPress Premium» to work
 *				Needs either the Toolset Types plugin or the ACF plugin
 * Version:     2.3.0
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', 'fgd2wp_fieldcollection_test_requirements' );

if ( !function_exists( 'fgd2wp_fieldcollection_test_requirements' ) ) {
	function fgd2wp_fieldcollection_test_requirements() {
		new fgd2wp_fieldcollection_requirements();
	}
}

if ( !class_exists('fgd2wp_fieldcollection_requirements', false) ) {
	class fgd2wp_fieldcollection_requirements {
		private $parent_plugin = 'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php';
		private $required_premium_version = '3.32.0';

		public function __construct() {
			load_plugin_textdomain( 'fgd2wp_fieldcollection', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
			echo '<div class="error"><p>[fgd2wp_fieldcollection] '.__('The Field Collection module needs the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong>.', 'fgd2wp_fieldcollection').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>';
		}
		
		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function version_error() {
			printf('<div class="error"><p>[fgd2wp_fieldcollection] '.__('The Field Collection module needs at least the <strong>version %s</strong> of the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong> at least the <strong>version %s</strong>.', 'fgd2wp_fieldcollection').'<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version);
		}
	}
}

if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') && !defined('DOING_CRON') && !defined('WP_CLI') ) {
	return;
}

add_action( 'plugins_loaded', 'fgd2wp_fieldcollection_load', 25 );

if ( !function_exists( 'fgd2wp_fieldcollection_load' ) ) {
	function fgd2wp_fieldcollection_load() {
		if ( !defined('FGD2WPP_LOADED') ) return;

		load_plugin_textdomain( 'fgd2wp_fieldcollection', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		global $fgd2wpp;
		new fgd2wp_fieldcollection($fgd2wpp);
	}
}

if ( !class_exists('fgd2wp_fieldcollection', false) ) {
	class fgd2wp_fieldcollection {
		
		private $plugin;
		private $field_name_count = array();
		private $registered_subfields = array();
		
		/**
		 * Sets up the plugin
		 *
		 */
		public function __construct($plugin) {
			
			$this->plugin = $plugin;
			
			add_filter('fgd2wp_pre_display_admin_page', array($this, 'process_admin_page'), 11, 1);
			add_action('fgd2wp_post_test_database_connection', array($this, 'check_required_plugins'));
			add_action('fgd2wp_post_empty_database', array($this, 'empty_database'));
			add_filter('fgd2wp_get_custom_field', array($this, 'get_custom_field'), 10, 1);
			add_filter('fgd2wp_get_custom_fields', array($this, 'get_custom_fields'), 10, 1);
			add_filter('fgd2wp_register_types_post_field', array($this, 'register_toolset_collection_field'), 10, 3); // Toolset
			add_filter('fgd2wp_post_insert_collection_field', array($this, 'register_acf_collection_field'), 10, 2); // ACF
			add_action('fgd2wp_pre_import', array($this, 'get_registered_subfields'));
			add_action('fgd2wp_import_node_field', array($this, 'import_collection_field_values'), 10, 6);
			add_action('fgd2wp_post_import_nodes_relations', array($this, 'replace_target_ids'));
		}
		
		/**
		 * Add information to the admin page
		 * 
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page($data) {
			$data['title'] .= ' ' . __('+ Field Collection module', __CLASS__);
			$data['description'] .= "<br />" . __('The Field Collection module will also import the Drupal Field Collection data to WordPress.', __CLASS__);
			
			return $data;
		}

		/**
		 * Check if the required plugins are activated
		 * 
		 * @since 2.0.0
		 */
		public function check_required_plugins() {
			if ( !$this->plugin->cpt->is_repeating_fields_supported() ) {
				if ( $this->plugin->cpt_format == 'toolset' ) {
					// Toolset
					$this->plugin->display_admin_warning(sprintf(__('The paid <a href="%s" target="_blank">Toolset Types plugin</a> version ≥ 3 is required to import the field collections as repeating fields.', __CLASS__), 'https://www.fredericgilles.net/toolset-types'));
				} elseif ( $this->plugin->cpt_format == 'acf' ) {
					// ACF
					$this->plugin->display_admin_warning(sprintf(__('The paid <a href="%s" target="_blank">Advanced Custom Fields Pro plugin</a> version ≥ 5 is required to import the field collections as repeater fields.', __CLASS__), 'https://www.advancedcustomfields.com/pro/'));
				}
			}
		}
		
		/**
		 * Actions to do when emptying the WordPress content
		 * 
		 * @since 1.1.5
		 */
		public function empty_database() {
			delete_option('fgd2wp_collection_subfields');
		}
		
		/**
		 * Get the registered subfields names
		 * 
		 * @since 1.1.5
		 */
		public function get_registered_subfields() {
			$this->registered_subfields = get_option('fgd2wp_collection_subfields');
		}
		
		/**
		 * Get the Field Collection fields
		 * 
		 * @param array $field Drupal field
		 * @return array Drupal field
		 */
		public function get_custom_field($field) {
			if ( $field['module'] == 'field_collection' ) {
				$collection_fields = $this->get_collection_fields($field['field_name']);
				if ( !empty($collection_fields) ) {
					$field['type'] = 'collection';
					$field['repetitive'] = 1;
					$field['collection'] = array();
					foreach ( $collection_fields as $collection_field ) {
						$data = unserialize($collection_field['data']);
						$data_instance = unserialize($collection_field['data_instance']);
						list($table_name, $columns) = $this->plugin->get_drupal7_storage_location($collection_field['field_name'], $data, $collection_field['type']);
						$field['collection'][] = array(
							'label' => $data_instance['label'],
							'field_name' => $collection_field['field_name'],
							'type' => $collection_field['type'],
							'table_name' => $table_name,
							'columns' => $columns,
						);
					}
				}
			}
			return $field;
		}
		
		/**
		 * Get the collection fields
		 * 
		 * @param string $field_name Field name
		 * @return array Custom fields
		 */
		private function get_collection_fields($field_name) {
			$custom_fields = array();
			
			if ( version_compare($this->plugin->drupal_version, '7', '<') ) {
				// Version 6
				$custom_fields = array(); // TODO
			} elseif ( version_compare($this->plugin->drupal_version, '8', '<') ) {
				// Version 7
				$custom_fields = $this->get_drupal_7_collection_fields($field_name);
			} else {
				// Version 8
				$custom_fields = array(); // TODO
			}
			return $custom_fields;
		}
		
		/**
		 * Get the Drupal 7 collection fields
		 * 
		 * @param string $field_name Field name
		 * @return array Custom fields
		 */
		private function get_drupal_7_collection_fields($field_name) {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			
			$sql = "
				SELECT fc.field_name, fc.type, fc.data, fci.data AS data_instance, fc.module, fc.cardinality
				FROM ${prefix}field_config fc
				INNER JOIN ${prefix}field_config_instance fci ON fci.field_id = fc.id
				WHERE fci.bundle = '$field_name'
				AND fci.entity_type = 'field_collection_item'
			";
			$custom_fields = $this->plugin->drupal_query($sql);
			return $custom_fields;
		}
		
		/**
		 * Change the collection custom field names to be unique
		 * 
		 * @since 1.1.5
		 * 
		 * @param array $custom_fields Custom fields
		 * @return array Custom fields
		 */
		public function get_custom_fields($custom_fields) {
			foreach ( $custom_fields as $field_name => $field ) {
				if ( isset($field['module']) && $field['module'] == 'field_collection' ) {
					$unique_field_name = $this->unique($field_name, $this->field_name_count); // Avoid the duplicate field slugs
					if ( $unique_field_name != $field_name ) {
						$custom_fields[$unique_field_name] = $field;
						unset($custom_fields[$field_name]);
					}
				}
			}
			return $custom_fields;
		}
		
		/**
		 * Register the Toolset Collection fields
		 * 
		 * @since 1.1.0
		 * 
		 * @param string $field_slug Collection field slug
		 * @param string $parent_field_slug Parent field slug
		 * @param array $field Field data
		 * @return string Collection field slug
		 */
		public function register_toolset_collection_field($field_slug, $parent_field_slug, $field) {
			if ( $this->plugin->cpt->is_repeating_fields_supported() && ($field['type'] == 'collection') ) {
				$group_slug = $this->build_group_slug($field_slug, $parent_field_slug);
				$relationship_slug = $this->normalize_slug($field_slug . '-' . $parent_field_slug . '-' . $field['type']);
				
				$wpcf_fields = array();
				
				// Create the Types relationship
				$this->plugin->cpt->add_toolset_relationship($relationship_slug, $group_slug, $parent_field_slug, $field['label'], 1, 'repeatable_group');
				
				// Register the repeating field group in Types
				$singular = preg_replace('/s$/', '', $field['label']);
				$this->register_repeating_field_group($group_slug, $singular, $singular . 's', $field['description']);
				
				// Register every subfields
				$subfields = array();
				$registered_subfields = get_option('fgd2wp_collection_subfields');
				foreach ( $field['collection'] as $subfield ) {
					$subfield_slug = preg_replace('/^field_/', '', $subfield['field_name']);
					$original_subfield_slug = $subfield_slug;
					$subfield_slug = sanitize_key($field['label']) . '-' . $subfield_slug;
					$registered_subfields[$field_slug . '-' . $original_subfield_slug] = $subfield_slug;
					$subfield['label'] = $field['label'] . ' ' . $subfield['label'];
					$subfields[] = $subfield_slug;
					$wpcf_field = $this->plugin->cpt->register_custom_field($subfield_slug, $subfield, 'postmeta');
					$wpcf_fields = array_merge($wpcf_fields, $wpcf_field);
				}
				update_option('wpcf-fields', array_merge(get_option('wpcf-fields', array()), $wpcf_fields));
				update_option('fgd2wp_collection_subfields', $registered_subfields);
				
				// Create the repeating field group in WordPress
				$post_id = $this->create_repeating_field_group($field['label'], $group_slug, $subfields);
				if ( !is_wp_error($post_id) ) {
					$field_slug = '_repeatable_group_' . $post_id;
				} else {
					$field_slug = '';
				}
			}
			return $field_slug;
		}
		
		/**
		 * Build a unique group slug (Toolset)
		 * 
		 * @since 2.0.2
		 * 
		 * @param string $field_slug Field slug
		 * @param string $parent_field_slug Parent field slug
		 * @return string Group slugS
		 */
		private function build_group_slug($field_slug, $parent_field_slug) {
			$crc = hash("crc32b", $parent_field_slug . $field_slug);
			$short_crc = substr($crc, 0, 2); // Keep only the 2 first characters (should be enough)
			$group_slug = $short_crc . substr($field_slug, 0, 18);
			return $group_slug;
		}
		
		/**
		 * Register the ACF Collection fields
		 * 
		 * @since 2.0.0
		 * 
		 * @param int $parent_id ID of the repeater field
		 * @param array $collection Collection
		 */
		public function register_acf_collection_field($parent_id, $collection) {
			foreach ( $collection as $subfield ) {
				$subfield_slug = preg_replace('/^field_/', '', $subfield['field_name']);
				$this->plugin->cpt->create_acf5_field($subfield_slug, $subfield, 'collection', $parent_id);
			}
		}
		
		/**
		 * Normalize the slug
		 * 
		 * @since 1.1.7
		 * 
		 * @param string $slug Slug
		 * @return string Slug
		 */
		private function normalize_slug($slug) {
			$slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin($slug));
			return $slug;
		}
		
		/**
		 * Register a repeating field group on Toolset
		 *
		 * @since 1.1.0
		 * 
		 * @param string $post_type Post type slug
		 * @param string $singular Singular post type name
		 * @param string $plural Plural post type name
		 * @param string $description Post type description
		 */
		private function register_repeating_field_group($post_type, $singular, $plural, $description) {
			$wpcf_custom_types = get_option('wpcf-custom-types', array());
			if ( !is_array($wpcf_custom_types) ) {
				$wpcf_custom_types = array();
			}
			if ( is_numeric($post_type) ) {
				// The post type must not be entirely numeric
				$post_type = '_' . $post_type;
			}
			$post_type = substr($post_type, 0, 20); // Post type is limited to 20 characters
			if ( empty($wpcf_custom_types) || !isset($wpcf_custom_types[$post_type]) ) {
				$wpcf_custom_type = array(
					$post_type => array(
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'add_new' => 'Add New',
							'add_new_item' => 'Add New %s',
							'edit_item' => 'Edit %s',
							'new_item' => 'New %s',
							'view_item' => 'View %s',
							'search_items' => 'Search %s',
							'not_found' => 'No %s found',
							'not_found_in_trash' => 'No %s found in Trash',
							'parent_item_colon' => 'Parent %s',
							'all_items' => '%s',
						),
						'slug' => $post_type,
						'description' => $description,
						'public' => '',
						'capabilities' => array(),
						'menu_position' => 0,
						'menu_icon' => '',
						'taxonomies' => array(),
						'supports' => array(
							'title' => 1,
							'author' => 1,
							'custom-fields' => 1,
						),
						'rewrite' => array(
							'enabled' => 1,
							'slug' => '',
							'with_front' => 1,
							'feeds' => 1,
							'pages' => 1,
						),
						'has_archive' => 1,
						'show_ui' => false,
						'show_in_menu' => 1,
						'show_in_menu_page' => false,
						'publicly_queryable' => false,
						'exclude_from_search' => 1,
						'hierarchical' => false,
						'query_var_enabled' => 1,
						'query_var' => '',
						'can_export' => 1,
						'show_rest' => false,
						'rest_base' => '',
						'show_in_nav_menus' => false,
						'register_meta_box_cb' => false,
						'permalink_epmask' => 'EP_PERMALINK',
						'is_repeating_field_group' => 1,
					),
				);
				
				$wpcf_custom_types = array_merge($wpcf_custom_types, $wpcf_custom_type);
				update_option('wpcf-custom-types', $wpcf_custom_types);
			}
		}
		
		/**
		 * Return a unique value
		 * 
		 * @since 1.1.5
		 * 
		 * @param string $value Value
		 * @param array $value_count Array counting the number of values
		 * @param string $parent Parent (to make the value unique inside the same parent)
		 * @return string Value
		 */
		private function unique($value, &$value_count, $parent='') {
			$key = $value;
			if ( !empty($parent) ) {
				$key = $parent . '-' . $key;
			}
			if ( !isset($value_count[$key]) ) {
				$value_count[$key] = 0;
			}
			$value_count[$key]++;
			if ( $value_count[$key] > 1 ) {
				$value .= '-' . $value_count[$key];
			}
			return $value;
		}
		
		/**
		 * Create a repeating field group
		 * 
		 * @since 1.1.0
		 * 
		 * @param string $fields_group_title Fields group title
		 * @param string $fields_group_name Fields group name
		 * @param array $subfields Subfield slugs
		 * @return int Field group post ID
		 */
		private function create_repeating_field_group($fields_group_title, $fields_group_name, $subfields) {
			
			$subfield_list = implode(',', $subfields);
			
			// Create the fields group (in post table)
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'hidden',
				'post_title'		=> $fields_group_title,
				'post_name'			=> $fields_group_name,
				'post_type'			=> 'wp-types-group',
			);
			$fields_group_post_id = wp_insert_post($new_post, true);
			if ( !is_wp_error($fields_group_post_id) ) {
				add_post_meta($fields_group_post_id, '_fgd2wp_old_group_name', $fields_group_name, true);
				add_post_meta($fields_group_post_id, 'types_field_group_purpose', 'for_repeating_field_group', true);
				add_post_meta($fields_group_post_id, '_types_repeatable_field_group_post_type', $fields_group_name, true);
				add_post_meta($fields_group_post_id, '_wp_types_group_fields', $subfield_list, true);
			}
			return $fields_group_post_id;
		}
		
		/**
		 * Import the value of a collection field
		 * 
		 * @since 1.1.0
		 * 
		 * @param int $parent_id WP Post or User ID
		 * @param string $field_name Field name
		 * @param string $post_type Post type
		 * @param array $field Field data
		 * @param array $field_values Field values
		 * @param date $parent_date Date of the parent node
		 */
		public function import_collection_field_values($parent_id, $field_name, $post_type, $field, $field_values, $parent_date) {
			if ( $this->plugin->cpt->is_repeating_fields_supported() && ($field['type'] == 'collection') ) {
				
				// Repeater field
				if ( $this->plugin->cpt_format == 'toolset' ) {
					// Toolset
					$group_slug = $this->build_group_slug($field_name, $post_type);
				} else {
					// ACF
					$repeater_field_name = 'collection-' . $field_name;
					if ( $post_type == 'user' ) {
						$this->plugin->cpt->set_custom_user_field($parent_id, $field_name, $field, array(''), $parent_date);
					} else {
						$this->plugin->cpt->set_custom_post_field($parent_id, $field_name, $field, array(''), $parent_date);
					}
				}
				
				foreach ( $field_values as $order => $collection ) {
					$collection_id = $collection[$field['columns']['value']];
					
					if ( $this->plugin->cpt_format == 'toolset' ) {
						// Toolset
						
						if ( $post_type == 'user' ) {
							continue; // No repeating field for the users in Toolset
						}
						
						// Create the post
						$post_id = $this->create_collection_field_post($group_slug, $field);
						if ( !is_wp_error($post_id) ) {
							add_post_meta($post_id, '_fgd2wp_old_collection_id', $collection_id, true);

							// Create the post meta with the subfields
							foreach ( $field['collection'] as $subfield ) {
								$subfield_values = $this->plugin->get_custom_field_values($collection_id, '', $subfield, 'field_collection_item');
								$subfield_slug = preg_replace('/^field_/', '', $subfield['field_name']);
								$subfield_slug = $this->registered_subfields[$field_name . '-' . $subfield_slug];
								$this->plugin->cpt->set_custom_post_field($post_id, $subfield_slug, $subfield, $subfield_values, $parent_date);
							}

							// Create the Toolset association
							$this->plugin->cpt->set_post_association($post_id, $field_name, $parent_id, $post_type, $field['type']);
						}
					}
					
					elseif ( $this->plugin->cpt_format == 'acf' ) {
						// ACF
						
						// Create the post meta with the subfields
						foreach ( $field['collection'] as $subfield ) {
							$subfield_values = $this->plugin->get_custom_field_values($collection_id, '', $subfield, 'field_collection_item');
							// Add a prefix in order to replace the target ID with the WP ID
							if ( in_array($subfield['type'], array('node_reference', 'entityreference')) ) {
								foreach ( $subfield_values as &$subfield_value ) {
									foreach ( $subfield_value as &$value ) {
										$value = 'target_id:' . $value;
									}
								}
							}
							$subfield_slug = preg_replace('/^field_/', '', $subfield['field_name']);
							$full_slug = $repeater_field_name . '_' . $order . '_' . $subfield_slug;
							if ( $post_type == 'user' ) {
								$this->plugin->cpt->set_custom_user_field($parent_id, $full_slug, $subfield, $subfield_values, $parent_date);
							} else {
								$this->plugin->cpt->set_custom_post_field($parent_id, $full_slug, $subfield, $subfield_values, $parent_date);
							}
						}
					}
					
				}
				
				if ( $this->plugin->cpt_format == 'acf' ) {
					// ACF
					// Update the last index of the repeater field
					if ( $post_type == 'user' ) {
						update_user_meta($parent_id, $repeater_field_name, count($field_values));
					} else {
						update_post_meta($parent_id, $repeater_field_name, count($field_values));
					}
				}
			}
		}
		
		/**
		 * Create the post containing the collection container
		 * 
		 * @global object $wpdb
		 * @param string $field_name Field name
		 * @param array $field Field data
		 * @return int WP post ID
		 */
		private function create_collection_field_post($field_name, $field) {
			global $wpdb;
			
			$next_id = $wpdb->get_var('SELECT MAX(ID) FROM ' . $wpdb->posts) + 1;
			$post_title = $field['label'] . ' ' . $next_id;
			$new_post = array(
				'post_content'		=> '',
				'post_status'		=> 'publish',
				'post_title'		=> $post_title,
				'post_name'			=> sanitize_title($post_title),
				'post_type'			=> $field_name,
			);
			$new_post_id = wp_insert_post($new_post, true);
			return $new_post_id;
		}
		
		/**
		 * Replace the target IDs in the postmeta
		 * 
		 * @since 2.1.0
		 */
		public function replace_target_ids() {
			global $wpdb;
			
			$rows = $this->get_collection_postmeta_with_target_id();
			foreach ( $rows as $row ) {
				$target_id = preg_replace('/^target_id:/', '', $row->meta_value);
				$post_id = $this->plugin->get_wp_post_id_from_drupal_id($target_id);
				if ( !empty($post_id) ) {
					// Replace the Drupal ID by the WP ID
					$wpdb->update($wpdb->postmeta, array('meta_value' => $post_id), array('meta_id' => $row->meta_id));
				}
			}
		}
		
		/**
		 * Get the WP postmeta containing a target_id
		 * 
		 * @since 2.1.0
		 * 
		 * @return array postmeta rows
		 */
		private function get_collection_postmeta_with_target_id() {
			global $wpdb;
			
			$sql = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE 'collection-%' AND meta_value LIKE 'target_id:%'";
			$results = $wpdb->get_results($sql);
			return $results;
		}
		
	}
}
