<?php
/**
 * Plugin Name: TW Call To Actions
 * Description: Manages the Call To Action custom post type
 *
 * @package tw_call_to_actions
 */

/**
 * Register Call To Action (CTA) Post Type.
 *
 * @return void
 */
function tw_call_to_actions_post_type_cta() {

	$labels = array(
		'name'                  => _x( 'Call To Actions', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Call To Action', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Call To Actions', 'text_domain' ),
		'name_admin_bar'        => __( 'Call To Actions', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Call To Actions', 'text_domain' ),
		'add_new_item'          => __( 'Add New Call To Action', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Call To Action', 'text_domain' ),
		'edit_item'             => __( 'Edit Call To Action', 'text_domain' ),
		'update_item'           => __( 'Update Call To Action', 'text_domain' ),
		'view_item'             => __( 'View Call To Action', 'text_domain' ),
		'view_items'            => __( 'View Call To Action', 'text_domain' ),
		'search_items'          => __( 'Search Call To Action', 'text_domain' ),
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
		'label'               => __( 'Call To Actions', 'text_domain' ),
		'description'         => __( 'Manages the Call To Action custom post type', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'taxonomies'          => array(),
		'rewrite'             => array(
			'slug'       => 'call_to_actions',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-align-center',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'capability_type'     => array( 'call_to_action', 'call_to_actions' ),
		'capabilities'        => array(
			'create_posts' => 'create_call_to_action',
		),
		'map_meta_cap'        => true,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'callToAction',
		'graphql_plural_name' => 'callToActions',
	);
	register_post_type( 'call_to_action', $args );

}
add_action( 'init', 'tw_call_to_actions_post_type_cta', 0 );
