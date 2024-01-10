<?php
/**
 * Plugin Name: TW Newsletters
 * Description: Manages the Newsletter custom post type
 *
 * @package tw_newsletters
 */

/**
 * Register Newsletter Post Type.
 *
 * @return void
 */
function tw_newsletters_post_type() {

	$labels = array(
		'name'                  => _x( 'Newsletters', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Newsletter', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Newsletters', 'text_domain' ),
		'name_admin_bar'        => __( 'Newsletters', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Newsletters', 'text_domain' ),
		'add_new_item'          => __( 'Add New Newsletter', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Newsletter', 'text_domain' ),
		'edit_item'             => __( 'Edit Newsletter', 'text_domain' ),
		'update_item'           => __( 'Update Newsletter', 'text_domain' ),
		'view_item'             => __( 'View Newsletter', 'text_domain' ),
		'view_items'            => __( 'View Newsletter', 'text_domain' ),
		'search_items'          => __( 'Search Newsletter', 'text_domain' ),
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
		'label'               => __( 'Newsletters', 'text_domain' ),
		'description'         => __( 'Manages the Newsletter custom post type', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'taxonomies'          => array(),
		'rewrite'             => array(
			'slug'       => 'newsletters',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-email',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'capability_type'     => array( 'newsletter', 'newsletters' ),
		'capabilities'        => array(
			'create_posts' => 'create_newsletter',
		),
		'map_meta_cap'        => true,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'newsletter',
		'graphql_plural_name' => 'newsletters',
	);
	register_post_type( 'newsletter', $args );
}
add_action( 'init', 'tw_newsletters_post_type', 0 );
