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
		'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="black"><path d="M168-96q-29.7 0-50.85-21.17Q96-138.34 96-168.07v-432.41q0-22.52 13.5-41.02Q123-660 144-668l539-196 25 68-342 124h425.96Q822-672 843-650.84t21 50.88v432.24Q864-138 842.85-117T792-96H168Zm0-72h624v-264H168v264Zm143.77-48Q352-216 380-243.77q28-27.78 28-68Q408-352 380.23-380q-27.78-28-68-28Q272-408 244-380.23q-28 27.78-28 68Q216-272 243.77-244q27.78 28 68 28ZM168-504h480v-72h72v72h72v-96H168v96Zm0 336v-264 264Z"/></svg>' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => array( 'station', 'stations' ),
		'capabilities'        => array(
			'create_posts' => 'create_stations',
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
