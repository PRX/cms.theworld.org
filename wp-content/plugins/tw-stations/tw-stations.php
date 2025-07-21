<?php
/**
 * Plugin Name: TW Stations
 * Description: Manages the station custom post type
 *
 * @package tw_stations
 */

/**
 * Register Station Post Type.
 *
 * @return void
 */
function tw_stations_post_type() {

	$labels = array(
		'name'                  => _x( 'Stations', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Station', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Stations', 'text_domain' ),
		'name_admin_bar'        => __( 'Stations', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All stations', 'text_domain' ),
		'add_new_item'          => __( 'Add New station', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New station', 'text_domain' ),
		'edit_item'             => __( 'Edit station', 'text_domain' ),
		'update_item'           => __( 'Update station', 'text_domain' ),
		'view_item'             => __( 'View station', 'text_domain' ),
		'view_items'            => __( 'View station', 'text_domain' ),
		'search_items'          => __( 'Search station', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args   = array(
		'label'               => __( 'Stations', 'text_domain' ),
		'description'         => __( 'Manages the list of Stations that air The World', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'taxonomies'          => array(),
		'rewrite'             => array(
			'slug'       => 'stations',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-location',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => array( 'station', 'stations' ),
		'capabilities'        => array(
			'create_posts' => 'create_station',
		),
		'map_meta_cap'        => true,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'station',
		'graphql_plural_name' => 'stations',
	);
	register_post_type( 'station', $args );
}
add_action( 'init', 'tw_stations_post_type', 0 );
