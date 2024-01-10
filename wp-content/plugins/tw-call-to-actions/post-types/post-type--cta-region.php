<?php
/**
 * Plugin Name: TW CTA Regions
 * Description: Manages the CTA Region custom post type
 *
 * @package tw_call_to_actions
 */

/**
 * Register CTA Regions Post Type.
 *
 * @return void
 */
function tw_call_to_actions_post_type_cta_region() {

	$labels = array(
		'name'                  => _x( 'CTA Regions', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'CTA Region', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'CTA Regions', 'text_domain' ),
		'name_admin_bar'        => __( 'CTA Regions', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All CTA Regions', 'text_domain' ),
		'add_new_item'          => __( 'Add New CTA Region', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New CTA Region', 'text_domain' ),
		'edit_item'             => __( 'Edit CTA Region', 'text_domain' ),
		'update_item'           => __( 'Update CTA Region', 'text_domain' ),
		'view_item'             => __( 'View CTA Region', 'text_domain' ),
		'view_items'            => __( 'View CTA Region', 'text_domain' ),
		'search_items'          => __( 'Search CTA Region', 'text_domain' ),
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
		'label'               => __( 'CTA Regions', 'text_domain' ),
		'description'         => __( 'Manages the CTA Region custom post type', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'excerpt' ),
		'taxonomies'          => array( 'cta_region_types' ),
		'rewrite'             => array(
			'slug'       => 'cta_regions',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-align-right',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'capability_type'     => array( 'cta_region', 'cta_regions' ),
		'capabilities'        => array(
			'create_posts' => 'create_cta_region',
		),
		'map_meta_cap'        => true,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'ctaRegion',
		'graphql_plural_name' => 'ctaRegions',
	);
	register_post_type( 'cta_region', $args );
}
add_action( 'init', 'tw_call_to_actions_post_type_cta_region', 0 );
