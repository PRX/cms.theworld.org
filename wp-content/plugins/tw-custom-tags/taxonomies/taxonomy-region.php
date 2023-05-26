<?php
/**
 * Register Region Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Region Taxonomy
 *
 * @return void
 */
function tw_region_taxonomy() {
	/**
	 * Taxonomy: Regions.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Regions', 'newspack' ),
		'singular_name'              => esc_html__( 'Region', 'newspack' ),
		'name'                       => esc_html__( 'Regions', 'newspack' ),
		'singular_name'              => esc_html__( 'Region', 'newspack' ),
		'search_items'               => esc_html__( 'Search Regions', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Regions', 'newspack' ),
		'all_items'                  => esc_html__( 'All Regions', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent Region', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent Region:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit Region', 'newspack' ),
		'view_item'                  => esc_html__( 'View Region', 'newspack' ),
		'update_item'                => esc_html__( 'Update Region', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New Region', 'newspack' ),
		'new_item_name'              => esc_html__( 'New Region Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Regions with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Regions', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Regions', 'newspack' ),
		'not_found'                  => esc_html__( 'No Regions found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Regions', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Regions list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Regions list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Regions', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Regions', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Regions', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'region',
			'with_front'   => true,
			'hierarchical' => true,
		),
		'show_admin_column'     => false,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'region',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'region',
		'graphql_plural_name'   => 'regions',
	);
	register_taxonomy( 'region', array( 'post' ), $args );
}
add_action( 'init', 'tw_region_taxonomy', 0 );
