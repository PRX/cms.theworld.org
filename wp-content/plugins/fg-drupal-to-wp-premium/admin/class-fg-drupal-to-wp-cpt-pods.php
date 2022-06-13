<?php

/**
 * Pods methods
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      3.28.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_CPT_Pods', false) ) {

	/**
	 * Pods class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_CPT_Pods {
		
		private $plugin;
		private $custom_fields = array();
		
		/**
		 * Constructor
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
			
			add_action('fgd2wp_post_register_custom_fields', array($this, 'set_custom_fields'));
			add_action('fgd2wp_post_register_post_types', array($this, 'setup_content_types'));
			add_action('fgd2wp_post_import', array($this, 'flush_pods_cache'), 99);
		}
		
		/**
		 * Check if Pods is activated
		 */
		public function check_required_plugins() {
			if ( !defined('PODS_VERSION') ) {
				$this->plugin->display_admin_warning(sprintf(__('The <a href="%s" target="_blank">Pods plugin</a> is required to manage the custom fields.', 'fgd2wpp'), 'https://wordpress.org/plugins/pods/'));
			}
		}
		
		/**
		 * Delete the data
		 */
		public function delete_data() {
			global $wpdb;
			$wpdb->query("TRUNCATE {$wpdb->prefix}podsrel");
			$this->flush_pods_cache();
		}
		
		/**
		 * Flush the Pods cache
		 */
		public function flush_pods_cache() {
			if ( function_exists('pods_api') ) {
				pods_api()->cache_flush_pods();
			}
		}
		
		/**
		 * Register the content types on WordPress
		 */
		public function setup_content_types() {
			if ( ! class_exists( 'PodsInit' ) ) {
				pods_init();
			}
			if ( function_exists('pods_init') ) {
				pods_init()->setup_content_types(true);
			}
		}
		
		/**
		 * Set the custom fields in an array
		 */
		public function set_custom_fields() {
			$posts = get_posts(array(
				'post_type' => '_pods_field',
				'numberposts' => -1,
				'order' => 'ASC',
			));
			foreach ( $posts as $post ) {
				$this->custom_fields[$post->post_name] = $post;
			}
		}
		
		/**
		 * Check if the repeating fields are supported
		 * 
		 * @since 3.31.0
		 * 
		 * @return bool Repeating fields supported
		 */
		public function is_repeating_fields_supported() {
			return true;
		}
		
		/**
		 * Get the field prefix
		 * 
		 * @return string Field prefix
		 */
		public function get_field_prefix() {
			return '';
		}
		
		/**
		 * Register a builtin post type
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular post type name
		 * @param string $plural Plural post type name
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 */
		public function register_builtin_post_type($post_type, $singular, $plural, $description, $taxonomies) {
			$this->register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, array());
		}
		
		/**
		 * Register a post type on Pods
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular label
		 * @param string $plural Plural label
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 * @param array $parent_post_types Parent post types
		 */
		public function register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, $parent_post_types) {
			$options = array(
				'supports_title' => true,
				'support_editor' => true,
				'supports_thumbnail' => true,
				'supports_author' => true,
				'supports_excerpt' => true,
			);
			foreach ( $taxonomies as $taxonomy ) {
				$options['built_in_taxonomies_' . $taxonomy] = true;
			}
			$this->register_pod($post_type, 'post_type', $singular, $plural, $description, $options);
		}
		
		/**
		 * Register a builtin taxonomy on Pods
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_builtin_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical) {
			$this->register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical);
		}
		
		/**
		 * Register a taxonomy on Pods
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical) {
			$options = array(
				'hierarchical' => $hierarchical,
			);
			foreach ( $post_types as $post_type ) {
				$options['built_in_post_types_' . $post_type] = true;
			}
			$this->register_pod($taxonomy, 'taxonomy', $singular, $plural, $description, $options);
		}
		
		/**
		 * Register a pod
		 * 
		 * @param string $slug Pod slug
		 * @param string $type post_type | taxonomy
		 * @param string $singular Singular label
		 * @param string $plural Plural label
		 * @param string $description Description
		 * @param array $options Options
		 * @return int Pod ID
		 */
		public function register_pod($slug, $type, $singular, $plural, $description='', $options=array()) {
			$pod_id = $this->get_pod_id_from_slug($slug);
			if ( !$pod_id ) {
				switch ( $type ) {
					case 'post_type':
						$create_extend = post_type_exists($slug)? 'extend' : 'create';
						break;
					case 'taxonomy':
						$create_extend = taxonomy_exists($slug)? 'extend' : 'create';
						break;
					case 'user':
						$create_extend = 'extend';
						break;
					default:
						$create_extend = 'create';
				}
				if ( function_exists('pods_api') ) {
					if ( $create_extend == 'create' ) {
						$pod_params = array(
							'create_extend' => 'create',
							'create_pod_type' => $type,
							'create_name' => $slug,
							'create_label_plural' => $plural, 
							'create_label_singular' => $singular,
						);
					} else {
						$pod_params = array(
							'create_extend' => 'extend',
							'extend_pod_type' => $type,
						);
						if ( $type == 'post_type' ) {
							$pod_params['extend_post_type'] = $slug;
						}
						if ( $type == 'taxonomy' ) {
							$pod_params['extend_taxonomy'] = $slug;
						}
					}
					$pod_id = pods_api()->add_pod($pod_params);
					if ( $pod_id && !is_wp_error($pod_id) ) {
						// Specific options
						foreach ( $options as $key => $value ) {
							add_post_meta($pod_id, $key, $value);
						}
					}
				}
			}
			return $pod_id;
		}
		
		/**
		 * Create a Pods group
		 * 
		 * @param int $pod_id Parent pod ID
		 * @param string $type post_type | taxonomy
		 * @return int Group ID
		 */
		private function create_pods_group($pod_id, $type) {
			$group_data = array(
				'post_title' => __('More Fields', $this->plugin->get_plugin_name()),
				'post_type' => '_pods_group',
				'post_parent' => $pod_id,
				'post_status' => 'publish',
				'numberposts' => 1,
			);
			$posts = get_posts($group_data);
			if ( is_array($posts) && (count($posts) > 0) ) {
				// Get the existing group
				$group_id = $posts[0]->ID;
			} else {
				$group_id = wp_insert_post($group_data, true);
				if ( !is_wp_error($group_id) ) {
					add_post_meta($group_id, 'object_type', 'group');
					add_post_meta($group_id, 'object_storage_type', $type);
					add_post_meta($group_id, 'parent', $pod_id);
				}
			}
			return $group_id;
		}
		
		/**
		 * Register the custom fields for a post type
		 *
		 * @param array $custom_fields Custom fields
		 * @param string $post_type Post type
		 * @return int Number of fields imported
		 */
		public function register_custom_post_fields($custom_fields, $post_type) {
			$fields_count = 0;
			if ( !empty($custom_fields) ) {
				
				// Add the fields group
				$pod_id = $this->get_pod_id_from_slug($post_type);
				$fields_group_id = $this->create_pods_group($pod_id, 'post_type');

				// Create the Pods fields
				foreach ( $custom_fields as $field_slug => $field ) {
					if ( in_array($field_slug, array('body', 'excerpt')) ) {
						continue; // Don't register the body and excerpt fields
					}
					$custom_fields_count = apply_filters('fgd2wp_register_custom_post_field', 0, $field_slug, $field, $post_type, $pod_id); // Allow the add-ons to intercept the creation of the field
					if ( $custom_fields_count == 0 ) {
						$field_id = $this->register_custom_field($field_slug, $field, $post_type, $pod_id, $fields_group_id);
						if ( !is_wp_error($field_id) ) {
							$fields_count++;
						}
					} else {
						$fields_count += $custom_fields_count;
					}
				}
			}
			return $fields_count;
		}
		
		/**
		 * Get the Pod ID from the Pod slug
		 * 
		 * @param string $slug Pod slug
		 * @return int Pod ID
		 */
		private function get_pod_id_from_slug($slug) {
			global $wpdb;
			return $wpdb->get_var("SELECT `ID` FROM {$wpdb->posts} WHERE `post_type` = '_pods_pod' AND `post_name` = '$slug'");
		}
		
		/**
		 * Create an Pods field (version 5)
		 * 
		 * @param string $field_slug Field slug
		 * @param array $field Field data
		 * @param string $post_type Post type
		 * @param int $pod_id Pod ID
		 * @param int $fields_group_id Fields group ID
		 * @return int Field ID
		 */
		public function register_custom_field($field_slug, $field, $post_type, $pod_id, $fields_group_id) {
			$post_parent = $pod_id;
			$title = $field['label'];
			$module = isset($field['module'])? $field['module'] : '';
			$field_type = $this->plugin->map_custom_field_type($field['type'], $field['label'], $module);
			$pods_field_type = $this->map_pods_field_type($field_type, $field);
			if ( isset($field['taxonomy']) ) {
				$field_slug = $field['taxonomy'] . '-' . $field_slug;
			}
			
			if ( isset($field['target_type']) && ($field['target_type'] == 'taxonomy_term') ) {
				// Don't import the taxonomy relationships as a field
				return;
			}
			
			$order = isset($field['order'])? $field['order'] : 0;
			
			// Multiple select
			$multiple = isset($field['cardinality']) && ($field['cardinality'] != 1);
			
			// Choices
			if ( isset($field['options']) && !empty($field['options']) ) {
				if ( is_array($field['options']) ) {
					$choices_array = array();
					foreach ( $field['options'] as $key => $value ) {
						$choices_array[] = "$key|$value";
					}
					$choices = implode("\n", $choices_array);
				} else {
					$choices = $field['options'];
				}
			}
			
			$field_id = $this->insert_pods_field($title, $field_slug, $order, $post_type, $post_parent);
			
			if ( !is_wp_error($field_id) ) {
				// Meta data
				update_post_meta($field_id, 'type', $pods_field_type);
				update_post_meta($field_id, 'group', $fields_group_id);
				update_post_meta($field_id, 'required', isset($field['required']) && !empty($field['required'])? 1 : 0);
				if ( isset($field['default_value'][0]['value']) ) {
					update_post_meta($field_id, 'default_value', $field['default_value'][0]['value']);
				}
				if ( isset($field['description']) ) {
					update_post_meta($field_id, 'text_placeholder', $field['description']);
				}
				if ( $pods_field_type == 'pick' ) {
					update_post_meta($field_id, 'pick_format_type', $multiple? 'multi' : 'single');
					switch ( $field_type ) {
						case 'checkboxes':
							$pick_format_single = 'checkbox';
							break;
						case 'radio':
							$pick_format_single = 'radio';
							break;
						case 'select':
							$pick_format_single = 'dropdown';
							break;
					}
					switch ( $field_type ) {
						case 'checkboxes':
						case 'radio':
						case 'select':
							update_post_meta($field_id, 'pick_object', 'custom-simple');
							update_post_meta($field_id, 'pick_format_single', $pick_format_single);
							update_post_meta($field_id, 'pick_format_multi', 'checkbox');
							update_post_meta($field_id, 'pick_custom', $choices);
							break;
						case 'nodereference':
							update_post_meta($field_id, 'pick_object', 'post_type');
							update_post_meta($field_id, 'pick_format_single', 'autocomplete');
							update_post_meta($field_id, 'pick_format_multi', 'autocomplete');
							$pick_val = isset($field['referenceable_types'][0])? $this->plugin->map_post_type($field['referenceable_types'][0]) : 'post';
							update_post_meta($field_id, 'pick_val', $pick_val);
							break;
					}
				}
				if ( $pods_field_type == 'file' ) {
					update_post_meta($field_id, 'file_format_type', $multiple? 'multi' : 'single');
				}
			}
			return $field_id;
		}
		
		/**
		 * Map the Drupal field type to an Pods field type
		 * 
		 * @param string $field_type Field type
		 * @param array $field Field
		 * @return string Pods field type
		 */
		public function map_pods_field_type($field_type, $field) {
			switch ( $field_type ) {
				case 'textfield':
					$pods_type = 'text';
					break;
				case 'image':
					$pods_type = 'file';
					break;
				case 'numeric':
					$pods_type = 'number';
					break;
				case 'url':
				case 'video':
					$pods_type = 'website';
					break;
				case 'checkbox':
					$pods_type = 'boolean';
					break;
				case 'checkboxes':
				case 'radio':
				case 'select':
				case 'nodereference':
					$pods_type = 'pick';
					break;
				default:
					$pods_type = $field_type;
			}
			$pods_type = apply_filters('fgd2wp_map_pods_field_type', $pods_type, $field_type, $field);
			return $pods_type;
		}
		
		/**
		 * Insert a Pods field
		 * 
		 * @param string $title Field title
		 * @param string $field_slug Field slug
		 * @param int $order Order
		 * @param string $post_type Post type
		 * @param int $parent_id Parent ID
		 * @return int Field ID
		 */
		private function insert_pods_field($title, $field_slug, $order, $post_type, $parent_id) {
			$meta_key = '_fgd2wp_old_field_name';
			$meta_value = $post_type . '-' . $field_slug;
			
			// Check if the field already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $meta_value);
			
			if ( empty($new_post_id) ) {

				// Insert the post
				$new_post = array(
					'post_title'		=> $title,
					'post_name'			=> $field_slug,
					'post_type'			=> '_pods_field',
					'post_parent'		=> $parent_id,
					'menu_order'		=> $order,
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $meta_value, true); // To avoid importing the same field
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Register a custom taxonomy field
		 * 
		 * @param string $custom_field_name Custom field name
		 * @param array $custom_field_data Custom field data
		 */
		public function register_custom_taxonomy_field($custom_field_name, $custom_field_data) {
			$taxonomy = $this->plugin->map_taxonomy($custom_field_data['taxonomy']);
			$pod_id = $this->get_pod_id_from_slug($taxonomy);
			// Create the Pods group
			$fields_group_id = $this->create_pods_group($pod_id, 'taxonomy');
			// Create the field
			$field_slug = sanitize_title(preg_replace('/^field_/', '', $custom_field_data['field_name']));
			$this->register_custom_field($field_slug, $custom_field_data, $taxonomy, $pod_id, $fields_group_id);
		}
		
		/**
		 * Register the user fields
		 * 
		 * @param array $custom_fields Custom user fields
		 * @return array Fields IDs
		 */
		public function register_custom_user_fields($custom_fields) {
			$fields_ids = array();
			if ( count($custom_fields) > 0 ) {
				// Register the "User" pod
				$pod_id = $this->register_pod('user', 'user', __('User'), __('Users'));
				$fields_group_id = $this->create_pods_group($pod_id, 'user');
				foreach ( $custom_fields as $field_slug => $custom_field_data ) {
					// Create the field
					$this->register_custom_field($field_slug, $custom_field_data, 'user', $pod_id, $fields_group_id);
				}
			}
			return $fields_ids;
		}
		
		/**
		 * Register the post types relationships
		 * 
		 * @param array $relationships Node Types Relationships
		 */
		public function register_post_types_relationships($relationships) {
			// Already registered as custom fields
		}
		
		/**
		 * Get the registered custom post types
		 * 
		 * @return array Custom post types
		 */
		public function get_custom_post_types() {
			return $this->get_pods('post_type');
		}
		
		/**
		 * Get the custom taxonomies
		 * 
		 * @return array Custom taxonomies
		 */
		public function get_custom_taxonomies() {
			return $this->get_pods('taxonomy');
		}
		
		/**
		 * Get the registered pods
		 * 
		 * @param string $pod_type Pod type
		 * @return array Pods
		 */
		private function get_pods($pod_type) {
			$pods = array();
			$posts = get_posts(array(
				'post_type' => '_pods_pod',
				'numberposts' => -1,
			));
			foreach ( $posts as $post ) {
				$type = get_post_meta($post->ID, 'type', true);
				if ( $type == $pod_type ) {
					$pods[$post->post_name] = array(
						'ID' => $post->ID,
						'label' => $post->post_title,
					);
				}
			}
			return $pods;
		}
		
		/**
		 * Get the post type name
		 * 
		 * @param array $post_type_object Post type object
		 * @return string Post type name
		 */
		public function get_post_type_name($post_type_object) {
			return $post_type_object['label'];
		}
		
		/**
		 * Get the taxonomy name
		 * 
		 * @param array $taxonomy_object Taxonomy object
		 * @return string Taxonomy name
		 */
		public function get_taxonomy_name($taxonomy_object) {
			return $taxonomy_object['label'];
		}
		
		/**
		 * Add a custom field value as a post meta
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_post_field($new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $new_post_id, $date);
			foreach ( $meta_values as $meta_value ) {
				if ( is_scalar($meta_value) ) {
					$meta_value = addslashes($this->plugin->replace_media_shortcodes(stripslashes($meta_value)));
				}
				add_post_meta($new_post_id, $custom_field_name, $meta_value);
			}
		}
		
		/**
		 * Add a custom field value as a term meta
		 * 
		 * @param int $new_term_id WordPress term ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 */
		public function set_custom_term_field($new_term_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_key = $custom_field_name;
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $new_term_id, $date);
			foreach ( $meta_values as $meta_value ) {
				add_term_meta($new_term_id, $meta_key, $meta_value);
			}
		}
		
		/**
		 * Add a custom field value as a user meta
		 * 
		 * @param int $new_user_id WordPress user ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_user_field($new_user_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_key = apply_filters('fgd2wp_get_user_meta_key', $custom_field_name, $custom_field);
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $new_user_id, $date);
			foreach ( $meta_values as $meta_value ) {
				if ( $meta_key == 'description' ) {
					// Unique field
					update_user_meta($new_user_id, $meta_key, $meta_value);
				} else {
					add_user_meta($new_user_id, $meta_key, $meta_value);
				}
			}
		}
		
		/**
		 * Convert custom field values to meta values
		 * 
		 * @param string $custom_field_name Custom field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param int $wp_id WordPress post, term or user ID
		 * @param date $date Date
		 * @return array Meta values
		 */
		private function convert_custom_field_to_meta_values($custom_field_name, $custom_field, $custom_field_values, $wp_id='', $date='') {
			$meta_values = array();
			$module = isset($custom_field['module'])? $custom_field['module'] : '';
			$field_type = $this->plugin->map_custom_field_type($custom_field['type'], $custom_field['label'], $module);
			$pods_field_type = $this->map_pods_field_type($field_type, $custom_field);
			switch ( $pods_field_type ) {
				// Date
				case 'date':
				case 'datetime':
					foreach ( $custom_field_values as $custom_field_value ) {
						if ( is_array($custom_field_value) ) {
							foreach ( $custom_field_value as $subvalue ) {
								$meta_values[] = $this->convert_to_date($subvalue);
							}
						} else {
							$meta_values[] = $this->convert_to_date($custom_field_value);
						}
					}
					break;

				// Image or file
				case 'file':
					if ( !$this->plugin->plugin_options['skip_media'] ) {
						foreach ( $custom_field_values as $file ) {
							// Import media
							$file_date = isset($file['timestamp'])? date('Y-m-d H:i:s', $file['timestamp']) : $date;
							$file_date = apply_filters('fgd2wp_get_custom_field_file_date', $file_date, $date);
							$image_attributs = array(
								'image_alt' => $this->plugin->get_image_attributes($file, 'alt'),
								'description' => $this->plugin->get_image_attributes($file, 'description'),
								'image_caption' => isset($file['caption'])? $file['caption'] : '',
							);
							$filename = preg_replace('/\..*$/', '', basename($file['filename']));
							$attachment_id = $this->plugin->import_media($filename, $this->plugin->get_path_from_uri($file['uri']), $file_date, $image_attributs);
							if ( $attachment_id ) {
								$this->plugin->media_count++;
								// Assign the media URL to the postmeta
								if ( !empty($wp_id) && ($custom_field['entity_type'] == 'node') ) {
									$set_featured_image = ($this->plugin->plugin_options['featured_image'] == 'featured') && !$this->plugin->thumbnail_is_set;
									$this->plugin->add_post_media($wp_id, array('post_date' => $file_date), array($attachment_id), $set_featured_image); // Attach the media to the post
									$this->plugin->thumbnail_is_set = true;
								}
								// Set the field value
								$meta_values[] = $attachment_id;
							}
						}
					}
					break;

				// URL
				case 'website':
					foreach ( $custom_field_values as $custom_field_value ) {
						$url = isset($custom_field_value['url'])? $custom_field_value['url'] : (isset($custom_field_value['uri'])? $custom_field_value['uri'] : '');
						$meta_values[] = $this->plugin->get_path_from_uri($url);
					}
					break;
					
				default:
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								$pods_value = implode("<br />\n", $custom_field_value);
							} else {
								$pods_value = $custom_field_value;
							}
							$pods_value = $this->plugin->replace_media_links($pods_value, $date);

							$meta_values[] = $pods_value;
						}
					} else {
						$meta_values[] = $custom_field_values;
					}
			}
			
			// Pods relationships
			if ( in_array($field_type, array('image', 'file', 'nodereference')) ) {
				foreach ( $meta_values as $meta_value ) {
					$this->insert_relationship($this->custom_fields[$custom_field_name]->post_parent, $this->custom_fields[$custom_field_name]->ID, $wp_id, $meta_value);
				}
			}
			return apply_filters('fgd2wp_convert_custom_field_to_meta_values', $meta_values, $custom_field_name, $custom_field, $custom_field_values, $wp_id, $date);
		}
		
		/**
		 * Convert a date with a MySQL format
		 * 
		 * @param mixed $date Date
		 * @return date Date
		 */
		private function convert_to_date($date) {
			if ( is_numeric($date) ) {
				$formatted_date = date('Y-m-d H:i:s', $date);
			} else {
				$formatted_date = preg_replace('/-00/', '-01', $date); // For dates with month=00 or day=00
				$formatted_date = preg_replace('/T/', ' ', $formatted_date); // For ISO date
			}
			return $formatted_date;
		}
		
		/**
		 * Set the user picture
		 * 
		 * @param int $user_id User ID
		 * @param int $image_id Image ID
		 */
		public function set_user_picture($user_id, $image_id) {
			add_user_meta($user_id, $this->get_field_prefix() . 'picture', $image_id);
		}
		
		/**
		 * Set a post relationship
		 * 
		 * @param int $post_id Post ID
		 * @param string $custom_field_name Custom field name
		 * @param int $related_id Related post ID
		 * @param array $custom_field Custom field
		 * @param string $relationship_slug Relationship slug (Toolset only)
		 */
		public function set_post_relationship($post_id, $custom_field_name, $related_id, $custom_field, $relationship_slug) {
			$this->set_custom_post_field($post_id, $custom_field_name, $custom_field, array($related_id));
		}
		
		/**
		 * Insert a Pods relationship
		 * 
		 * @param int $pod_id Pod ID
		 * @param int $field_id Field ID
		 * @param int $item_id Item ID
		 * @param int $related_item_id Related item ID
		 * @return int Pods relation ID
		 */
		private function insert_relationship($pod_id, $field_id, $item_id, $related_item_id) {
			global $wpdb;
			$podsrel_id = false;
			if ( $wpdb->insert($wpdb->prefix . 'podsrel', array(
				'pod_id' => $pod_id,
				'field_id' => $field_id,
				'item_id' => $item_id,
				'related_pod_id' => 0,
				'related_field_id' => 0,
				'related_item_id' => $related_item_id,
			)) ) {
				$podsrel_id = $wpdb->insert_id;
			}
			return $podsrel_id;
		}
		
	}
}
