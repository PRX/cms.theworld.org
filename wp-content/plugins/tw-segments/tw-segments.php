<?php
/**
 * Plugin Name: TW Segments
 * Description: Manages the Segment custom post type
 *
 * @package tw_segments
 */

/**
 * Register Segments Post Type.
 *
 * @return void
 */
function tw_segments_post_type() {
	$labels = array(
		'name'                  => _x( 'Segments', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x(
			'Segments',
			'Post Type Singular Name',
			'text_domain'
		),
		'menu_name'             => __( 'Segments', 'text_domain' ),
		'name_admin_bar'        => __( 'Segments', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Segments', 'text_domain' ),
		'add_new_item'          => __( 'Add New Segment', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Segment', 'text_domain' ),
		'edit_item'             => __( 'Edit Segment', 'text_domain' ),
		'update_item'           => __( 'Update Segment', 'text_domain' ),
		'view_item'             => __( 'View Segment', 'text_domain' ),
		'view_items'            => __( 'View Segment', 'text_domain' ),
		'search_items'          => __( 'Search Segment', 'text_domain' ),
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
		'label'               => __( 'Segments', 'text_domain' ),
		'description'         => __(
			'Manages the Segment custom post type',
			'text_domain'
		),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'rewrite'             => array(
			'slug'       => 'segments',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'segment',
		'graphql_plural_name' => 'segments',
		'public'              => true,
		'publicly_queryable'  => true,
	);
	register_post_type( 'segment', $args );
}
add_action( 'init', 'tw_segments_post_type', 0 );
