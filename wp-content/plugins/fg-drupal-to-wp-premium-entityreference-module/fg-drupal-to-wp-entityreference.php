<?php
/**
 * Plugin Name: FG Drupal to WordPress Premium Entity Reference module
 * Depends:     FG Drupal to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-drupal-to-wordpress/
 * Description: A plugin to migrate the Entity Reference data (nodes/taxonomies relationships) from Drupal to WordPress
 *              Needs the plugin «FG Drupal to WordPress Premium» to work
 * Version:     1.13.0
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'fgd2wp_entityreference_test_requirements' );

if ( ! function_exists( 'fgd2wp_entityreference_test_requirements' ) ) {
	function fgd2wp_entityreference_test_requirements() {
		new fgd2wp_entityreference_requirements();
	}
}

if ( ! class_exists( 'fgd2wp_entityreference_requirements', false ) ) {
	class fgd2wp_entityreference_requirements {
		private $parent_plugin            = 'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php';
		private $required_premium_version = '3.21.0';

		public function __construct() {
			load_plugin_textdomain( 'fgd2wp_entityreference', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			if ( ! is_plugin_active( $this->parent_plugin ) ) {
				add_action( 'admin_notices', array( $this, 'error' ) );
			} else {
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->parent_plugin );
				if ( ! $plugin_data or version_compare( $plugin_data['Version'], $this->required_premium_version, '<' ) ) {
					add_action( 'admin_notices', array( $this, 'version_error' ) );
				}
			}
		}

		/**
		 * Print an error message if the Premium plugin is not activated
		 */
		function error() {
			echo '<div class="error"><p>[fgd2wp_entityreference] ' . __( 'The Entity Reference module needs the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong>.', 'fgd2wp_entityreference' ) . '<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>';
		}

		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function version_error() {
			printf( '<div class="error"><p>[fgd2wp_entityreference] ' . __( 'The Entity Reference module needs at least the <strong>version %1$s</strong> of the «FG Drupal to WordPress Premium» plugin to work. Please install and activate <strong>FG Drupal to WordPress Premium</strong> at least the <strong>version %2$s</strong>.', 'fgd2wp_entityreference' ) . '<br /><a href="https://www.fredericgilles.net/fg-drupal-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-drupal-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version );
		}
	}
}

if ( ! defined( 'WP_LOAD_IMPORTERS' ) && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) && ! defined( 'WP_CLI' ) ) {
	return;
}

add_action( 'plugins_loaded', 'fgd2wp_entityreference_load', 25 );

if ( ! function_exists( 'fgd2wp_entityreference_load' ) ) {
	function fgd2wp_entityreference_load() {
		if ( ! defined( 'FGD2WPP_LOADED' ) ) {
			return;
		}

		load_plugin_textdomain( 'fgd2wp_entityreference', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		global $fgd2wpp;
		new fgd2wp_entityreference( $fgd2wpp );
	}
}

if ( ! class_exists( 'fgd2wp_entityreference', false ) ) {
	class fgd2wp_entityreference {

		private $plugin;
		private $user_relation_fields = array();
		private $imported_posts       = array();

		/**
		 * Sets up the plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

			add_filter( 'fgd2wp_pre_display_admin_page', array( $this, 'process_admin_page' ), 11, 1 );
			add_filter( 'fgd2wp_get_node_reference_fields', array( $this, 'get_entityreference_fields' ), 10, 1 );
			add_filter( 'fgd2wp_get_referenceable_types', array( $this, 'get_referenceable_types' ), 10, 2 );
			add_filter( 'fgd2wp_get_node_type_taxonomies', array( $this, 'get_node_type_taxonomies' ), 10, 2 );
			add_filter( 'fgd2wp_get_drupal7_custom_fields', array( $this, 'get_drupal7_custom_fields' ), 10, 4 );
			add_filter( 'fgd2wp_post_get_custom_field', array( $this, 'get_drupal8_custom_fields' ), 10, 4 );
			add_filter( 'fgd2wp_get_custom_field_values_sql', array( $this, 'get_custom_field_values_sql' ), 10, 4 );
			add_filter( 'fgd2wp_pre_import_term_custom_field_values', array( $this, 'not_import_entityreference_values' ), 10, 4 );
			add_action( 'fgd2wp_post_register_user_fields', array( $this, 'get_user_relation_fields' ), 10, 1 );
			add_action( 'fgd2wp_post_import', array( $this, 'import_users_relationships' ), 20, 1 );
			add_action( 'fgd2wp_pre_import_terms_relations', array( $this, 'get_imported_posts' ) );
			add_action( 'fgd2wp_import_term_relations', array( $this, 'import_term_relationships' ), 10, 4 );
		}

		/**
		 * Add information to the admin page
		 *
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page( $data ) {
			$data['title']       .= ' ' . __( '+ Entity Reference module', __CLASS__ );
			$data['description'] .= '<br />' . __( 'The Entity Reference module will also import the Drupal Entity Reference data (nodes and taxonomies relationships) to WordPress.', __CLASS__ );

			return $data;
		}

		/**
		 * Get the Entity Reference fields
		 *
		 * @param array $fields Node reference fields
		 * @return array Node reference fields with Entity Reference fields
		 */
		public function get_entityreference_fields( $fields ) {
			$entityreference_fields = array();
			$prefix                 = $this->plugin->plugin_options['prefix'];

			if ( $this->plugin->drupal_version == 7 ) {
				// Version 7
				$sql                    = "
					SELECT f.field_name, f.data, fi.bundle AS type_name, fi.data AS instance_data, f.cardinality
					FROM ${prefix}field_config f
					INNER JOIN ${prefix}field_config_instance fi ON fi.field_name = f.field_name
					WHERE f.type = 'entityreference'
					AND f.deleted = 0
				";
				$entityreference_fields = $this->plugin->drupal_query( $sql );
				foreach ( $entityreference_fields as &$field ) {
					$field['data']          = unserialize( $field['data'] );
					$field['instance_data'] = unserialize( $field['instance_data'] );
				}
			} elseif ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) {
				// Version 8
				$drupal8_fields = $this->plugin->get_drupal_config_like( 'field.field.node.%.field_%' );
				foreach ( $drupal8_fields as $data ) {
					if ( isset( $data['field_type'] ) && isset( $data['entity_type'] ) && ( $data['field_type'] == 'entity_reference' ) && ( $data['entity_type'] == 'node' ) && ( $data['settings']['handler'] != 'default:taxonomy_term' ) ) {
						$data_storage             = $this->plugin->get_drupal8_storage( $data['field_name'] );
						$cardinality              = isset( $data_storage['cardinality'] ) ? $data_storage['cardinality'] : 1;
						$entityreference_fields[] = array(
							'field_name'  => $data['field_name'],
							'data'        => $data,
							'type_name'   => $data['bundle'],
							'cardinality' => $cardinality,
						);
					}
				}
			}
			$fields = array_merge( $fields, $entityreference_fields );
			return $fields;
		}

		/**
		 * Get the referenceable types from the field settings
		 *
		 * @param array  $types Referenceable types
		 * @param string $settings Field settings
		 * @return array Referenceable types
		 */
		public function get_referenceable_types( $types, $settings ) {
			if ( isset( $settings['target_type'] ) && ( $settings['target_type'] == 'user' ) ) {
				// User relationship
				$types[] = 'user';

			} elseif ( isset( $settings['handler_settings']['target_bundles'] ) ) {
				// Simple/default mode
				foreach ( $settings['handler_settings']['target_bundles'] as $key => $value ) {
					if ( ! empty( $value ) ) {
						$types[] = $key;
					}
				}
			} elseif ( isset( $settings['handler_settings']['view']['view_name'] ) ) {
				// Views mode
				$display_options = $this->get_default_views_display_options( $settings['handler_settings']['view']['view_name'] );
				if ( empty( $display_options ) ) {
					// Try to get the view by its display name
					if ( isset( $settings['handler_settings']['view']['display_name'] ) ) {
						$display_options = $this->get_views_display_options_by_display_name( $settings['handler_settings']['view']['display_name'] );
					}
				}
				if ( isset( $display_options['filters']['type']['value'] ) ) {
					foreach ( $display_options['filters']['type']['value'] as $key => $value ) {
						if ( ! empty( $value ) ) {
							$types[] = $key;
						}
					}
				}
			}
			return $types;
		}

		/**
		 * Get the "default" views display options
		 *
		 * @since 1.3.0
		 *
		 * @param string $view_name View name
		 * @return array Display options
		 */
		private function get_default_views_display_options( $view_name ) {
			$view = $this->get_default_view( $view_name );
			return $this->get_display_options( $view );
		}

		/**
		 * Get the view display options by a display name
		 *
		 * @since 1.7.0
		 *
		 * @param string $display_name View display name
		 * @return array Display options
		 */
		private function get_views_display_options_by_display_name( $display_name ) {
			$view = $this->get_view_by_display_name( $display_name );
			return $this->get_display_options( $view );
		}

		/**
		 * Get the display options of a view
		 *
		 * @since 1.7.0
		 *
		 * @param array $view View data
		 * @return array Display options
		 */
		private function get_display_options( $view ) {
			$display_options = array();
			if ( $this->plugin->drupal_version == 7 ) {
				if ( isset( $view['display_options'] ) ) {
					$display_options = $view['display_options'];
				} elseif ( isset( $view['filters'] ) ) {
					$display_options = $view;
				}
			} elseif ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) {
				if ( isset( $view['display']['default']['display_options'] ) ) {
					$display_options = $view['display']['default']['display_options'];
				} elseif ( isset( $view['filters'] ) ) {
					$display_options = $view;
				}
			}
			return $display_options;
		}

		/**
		 * Get the "default" view
		 *
		 * @since 1.4.0
		 *
		 * @param string $view_name View name
		 * @return array View data
		 */
		private function get_default_view( $view_name ) {
			$data   = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( $this->plugin->drupal_version == 7 ) {
				// Drupal 7
				$sql     = "
					SELECT vd.display_options
					FROM ${prefix}views_display vd
					INNER JOIN ${prefix}views_view v ON v.vid = vd.vid
					WHERE v.name = '$view_name'
					AND vd.display_plugin = 'default'
					LIMIT 1
				";
				$results = $this->plugin->drupal_query( $sql );
				if ( count( $results ) > 0 ) {
					$data = unserialize( $results[0]['display_options'] );
				}
			} elseif ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) {
				// Drupal 8
				$views = $this->plugin->get_drupal_config_like( "views.view.$view_name" );
				if ( count( $views ) > 0 ) {
					$data = array_shift( $views );
				}
			}
			return $data;
		}

		/**
		 * Get the view by its display name
		 *
		 * @since 1.7.0
		 *
		 * @param string $display_name View display name
		 * @return array View data
		 */
		private function get_view_by_display_name( $display_name ) {
			$data   = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( $this->plugin->drupal_version == 7 ) {
				// Drupal 7
				$sql     = "
					SELECT vd.display_options
					FROM ${prefix}views_display vd
					WHERE vd.id = '$display_name'
					LIMIT 1
				";
				$results = $this->plugin->drupal_query( $sql );
				if ( count( $results ) > 0 ) {
					$data = unserialize( $results[0]['display_options'] );
				}
			} elseif ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) {
				// Drupal 8
				$views = $this->plugin->get_drupal_config_like( "views.view.$display_name" );
				if ( count( $views ) > 0 ) {
					$data = array_shift( $views );
				}
			}
			return $data;
		}

		/**
		 * Get the taxonomies related to a node type
		 *
		 * @param array  $taxonomies Taxonomies
		 * @param string $node_type Node type
		 * @return array Taxonomies
		 */
		public function get_node_type_taxonomies( $taxonomies, $node_type ) {
			$matches                = array();
			$entityreference_fields = $this->get_entityreference_fields_for_node_type( $node_type );
			foreach ( $entityreference_fields as $field ) {
				$data = $field['data'];
				if ( isset( $data['settings']['handler_settings']['view']['view_name'] ) ) {
					// Views mode
					$view = $this->get_default_view( $data['settings']['handler_settings']['view']['view_name'] );
					if ( isset( $view['base_table'] ) && ( $view['base_table'] == 'taxonomy_term_field_data' ) ) {
						if ( isset( $view['dependencies']['config'] ) ) {
							// Get the taxonomies related to the view
							foreach ( $view['dependencies']['config'] as $related_taxonomy ) {
								if ( preg_match( '/^taxonomy\.vocabulary\.(.*)/', $related_taxonomy, $matches ) ) {
									$taxonomy = $matches[1];
									if ( ! empty( $taxonomy ) ) {
										$taxonomies[] = $taxonomy;
									}
								}
							}
						} else {
							$taxonomies[] = $view['id'];
						}
					}
				} elseif ( isset( $data['settings']['target_type'] ) && ( $data['settings']['target_type'] == 'taxonomy_term' ) && isset( $data['settings']['handler_settings']['target_bundles'] ) ) {
					// Simple/default mode
					$taxonomies = array_merge( $taxonomies, array_keys( $data['settings']['handler_settings']['target_bundles'] ) );
				}
			}
			return $taxonomies;
		}

		/**
		 * Get the Entity Reference fields for a node type
		 *
		 * @param string $node_type Node type
		 * @return array Node reference fields
		 */
		private function get_entityreference_fields_for_node_type( $node_type ) {
			$entityreference_fields = array();
			$prefix                 = $this->plugin->plugin_options['prefix'];
			if ( $this->plugin->drupal_version == 7 ) {
				// Version 7
				$sql                    = "
					SELECT f.field_name, f.data, fi.bundle AS type_name
					FROM ${prefix}field_config f
					INNER JOIN ${prefix}field_config_instance fi ON fi.field_name = f.field_name
					WHERE f.type = 'entityreference'
					AND f.deleted = 0
					AND fi.bundle = '$node_type'
				";
				$entityreference_fields = $this->plugin->drupal_query( $sql );
				foreach ( $entityreference_fields as &$field ) {
					$field['data'] = unserialize( $field['data'] );
				}
			} elseif ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) {
				// Version 8
				$fields = $this->plugin->get_drupal_config_like( "field.field.node.$node_type.field_%" );
				foreach ( $fields as $data ) {
					if ( isset( $data['field_type'] ) && isset( $data['entity_type'] ) && ( $data['field_type'] == 'entity_reference' ) && ( $data['entity_type'] == 'node' ) && ( $data['settings']['handler'] != 'default:taxonomy_term' ) ) {
						$entityreference_fields[] = array(
							'field_name' => $data['field_name'],
							'data'       => $data,
							'type_name'  => $data['bundle'],
						);
					}
				}
			}
			return $entityreference_fields;
		}

		/**
		 * Get the custom fields for a node type
		 *
		 * @param array $custom_fields Custom fields
		 * @param array $row custom field
		 * @param array $data Custom field data
		 * @param array $data_instance Custom field data instance
		 * @return array Custom fields
		 */
		public function get_drupal7_custom_fields( $custom_fields, $row, $data, $data_instance ) {
			if ( ( $row['module'] == 'entityreference' ) ) {
				$table_name = 'field_data_' . $row['field_name'];
				$columns    = array( 'target_id' => $row['field_name'] . '_target_id' );
				$type       = 'entityreference';
				$field      = array(
					'field_name'      => $row['field_name'],
					'table_name'      => $table_name,
					'module'          => $row['module'],
					'columns'         => $columns,
					'label'           => $data_instance['label'],
					'type'            => $type,
					'description'     => isset( $data_instance['description'] ) ? $data_instance['description'] : '',
					'default_value'   => isset( $data_instance['default_value'] ) ? $data_instance['default_value'] : '',
					'required'        => isset( $data_instance['required'] ) ? $data_instance['required'] : false,
					'repetitive'      => ( $row['cardinality'] != 1 ) && ( $row['module'] != 'list' ),
					'do_not_register' => true,
				);
				if ( isset( $data['settings']['allowed_values'] ) ) {
					$field['options'] = $data['settings']['allowed_values'];
				}
				$referenceable_types = $this->plugin->get_referenceable_types( $data );
				if ( ! empty( $referenceable_types ) ) {
					$field['referenceable_types'] = $referenceable_types;
				}
				if ( isset( $field['columns']['value'] ) || isset( $field['columns']['fid'] ) || isset( $field['referenceable_types'] ) ) {
					// Get only the standard types and the referenceable types
					$field_slug                   = sanitize_key( FG_Drupal_to_WordPress_Tools::convert_to_latin( remove_accents( $data_instance['label'] ) ) );
					$custom_fields[ $field_slug ] = $field;
				}
			}

			return $custom_fields;
		}

		/**
		 * Get the custom fields for a node type
		 *
		 * @param array $custom_fields Custom fields
		 * @param array $field custom field
		 * @param array $data_storage Custom field data storage
		 * @param array $data Custom field data
		 * @return array Custom fields
		 */
		public function get_drupal8_custom_fields( $custom_fields, $field, $data_storage, $data ) {
			if ( $field['type'] == 'entity_reference' ) {
				$field_slug = sanitize_key( FG_Drupal_to_WordPress_Tools::convert_to_latin( remove_accents( $data['label'] ) ) );
				if ( isset( $field['taxonomy'] ) ) {
					$field_slug = $field['taxonomy'] . '-' . $field_slug;
				}
				$field['do_not_register']     = true;
				$custom_fields[ $field_slug ] = $field;
			}
			return $custom_fields;
		}

		/**
		 * Modify the SQL request for Entity Reference fields
		 *
		 * @since 1.10.0
		 *
		 * @param string $sql SQL
		 * @param int    $entity_id Entity ID
		 * @param string $node_type Node type
		 * @param array  $custom_field Custom field
		 * @return string SQL
		 */
		public function get_custom_field_values_sql( $sql, $entity_id, $node_type, $custom_field ) {
			if ( ( $custom_field['type'] == 'entity_reference' ) && ( $custom_field['entity_type'] != 'node' ) && ! isset( $custom_field['taxonomy'] ) && ( version_compare( $this->plugin->drupal_version, '8', '>=' ) ) ) {
				$prefix     = $this->plugin->plugin_options['prefix'];
				$field_name = $custom_field['columns']['target_id'];
				$sql        = preg_replace( '/' . preg_quote( $field_name ) . '/', 'd.title AS value', $sql );
				$sql        = preg_replace( '/WHERE/', "INNER JOIN {$prefix}node_field_data d ON d.nid = f.$field_name\nWHERE", $sql );
			}
			return $sql;
		}

		/**
		 * Do not import the relationships fields between a taxonomy term and a node
		 * The relationship will be imported in a second step
		 *
		 * @since 1.13.0
		 *
		 * @param array  $custom_field_values Custom field values
		 * @param int    $new_term_id WP term ID
		 * @param string $custom_field_name Custom field name
		 * @param array  $custom_field Custom field
		 * @return array Custom field values
		 */
		public function not_import_entityreference_values( $custom_field_values, $new_term_id, $custom_field_name, $custom_field ) {
			if ( ( $custom_field['type'] == 'entity_reference' ) && isset( $custom_field['taxonomy'] ) ) {
				$custom_field_values = array();
			}
			return $custom_field_values;
		}

		/**
		 * Get the users relation fields
		 *
		 * @since 1.12.0
		 *
		 * @param array $custom_fields Custom fields
		 */
		public function get_user_relation_fields( $custom_fields ) {
			$this->user_relation_fields = array();
			foreach ( $custom_fields as $field_name => $field ) {
				if ( $field['type'] == 'entityreference' ) {
					$this->user_relation_fields[ $field_name ] = $field;
				}
			}
		}

		/**
		 * Import the users relationships
		 *
		 * @since 1.12.0
		 */
		public function import_users_relationships() {
			if ( count( $this->user_relation_fields ) > 0 ) {
				$imported_users = $this->plugin->get_imported_drupal_users();
				$message        = __( 'Importing users relationships...', __CLASS__ );
				if ( defined( 'WP_CLI' ) ) {
					$progress_cli = \WP_CLI\Utils\make_progress_bar( $message, count( $imported_users ) );
				} else {
					$this->plugin->log( $message );
				}

				foreach ( $imported_users as $drupal_user_id => $wp_user_id ) {
					$user = array( 'uid' => $drupal_user_id );
					foreach ( $this->user_relation_fields as $custom_field_name => $custom_field ) {
						$custom_field_values = $this->plugin->get_user_custom_field_values( $user, $custom_field );
						$this->plugin->cpt->set_custom_user_field( $wp_user_id, $custom_field_name, $custom_field, $custom_field_values );
					}

					if ( defined( 'WP_CLI' ) ) {
						$progress_cli->tick( 1 );
					}
				}
				if ( defined( 'WP_CLI' ) ) {
					$progress_cli->finish();
				}
			}
		}

		/**
		 * Get the imported posts
		 *
		 * @since 1.13.0
		 */
		public function get_imported_posts() {
			$this->imported_posts = $this->plugin->get_imported_drupal_posts_with_post_type();
		}

		/**
		 * Import the relationships of a term
		 *
		 * @since 1.13.0
		 *
		 * @param array  $term Term
		 * @param int    $drupal_term_id Drupal term ID
		 * @param string $field_slug Field slug
		 * @param array  $custom_field Custom field
		 */
		public function import_term_relationships( $term, $drupal_term_id, $field_slug, $custom_field ) {
			// if ( $custom_field['type'] == 'entity_reference' || $custom_field['type'] == 'entityreference' || $custom_field['type'] == 'node_reference' ) {
			if ( $custom_field['type'] == 'entity_reference' || $custom_field['type'] == 'node_reference' ) {
				$drupal_term         = array(
					'tid' => $drupal_term_id,
				);
				$custom_field_values = $this->plugin->get_term_custom_field_values( $drupal_term, $custom_field );
				list($field_slug, $custom_field, $custom_field_values) = apply_filters( 'fgd2wp_import_term_custom_fields', array( $field_slug, $custom_field, $custom_field_values ) );
				$new_custom_field_values                               = array();
				foreach ( $custom_field_values as $custom_field_value ) {
					if ( isset( $custom_field['columns']['target_id'] ) || isset( $custom_field['columns']['nid'] ) ) {

						$target_id = isset( $custom_field['columns']['target_id'] ) ? $custom_field['columns']['target_id'] : '';
						if ( empty( $target_id ) && isset( $custom_field['columns']['nid'] ) ) {
							$target_id = $custom_field['columns']['nid'];
						}
						if ( isset( $custom_field_value[ $target_id ] ) ) {
							$node_id = $custom_field_value[ $target_id ];
							if ( isset( $this->imported_posts[ $node_id ]['post_id'] ) ) {
								$new_custom_field_values[] = $this->imported_posts[ $node_id ]['post_id'];
							}
						}
					}
				}
				if ( $new_custom_field_values ) {
					if ( isset( $custom_field['repetitive'] ) && ! $custom_field['repetitive'] ) {
						$this->plugin->cpt->set_custom_term_field( $term['term_id'], $field_slug, $custom_field, $new_custom_field_values );
					} else {
						foreach ( $new_custom_field_values as $post_id ) {
							$this->plugin->cpt->set_custom_term_field( $term['term_id'], $field_slug, $custom_field, $post_id );
						}
					}
				}
			}
		}

	}
}
