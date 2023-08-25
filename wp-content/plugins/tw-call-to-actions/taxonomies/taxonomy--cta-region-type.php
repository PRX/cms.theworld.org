<?php
/**
 * Register CTA Region Type Taxonomy
 *
 * @package tw_call_to_actions
 */

/**
 * Register Region Taxonomy
 *
 * @return void
 */
function tw_call_to_actions_taxonomy_cta_region_type() {
	/**
	 * Taxonomy: CTA Region Type.
	 */

	$labels = array(
		'name'                       => esc_html__( 'CTA Region Types', 'newspack' ),
		'singular_name'              => esc_html__( 'CTA Region Type', 'newspack' ),
		'name'                       => esc_html__( 'CTA Region Types', 'newspack' ),
		'singular_name'              => esc_html__( 'CTA Region Type', 'newspack' ),
		'search_items'               => esc_html__( 'Search CTA Region Types', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular CTA Region Types', 'newspack' ),
		'all_items'                  => esc_html__( 'All CTA Region Types', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent CTA Region Type', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent CTA Region Type:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit CTA Region Type', 'newspack' ),
		'view_item'                  => esc_html__( 'View CTA Region Type', 'newspack' ),
		'update_item'                => esc_html__( 'Update CTA Region Type', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New CTA Region Type', 'newspack' ),
		'new_item_name'              => esc_html__( 'New CTA Region Type Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate CTA Region Types with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove CTA Region Types', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used CTA Region Types', 'newspack' ),
		'not_found'                  => esc_html__( 'No CTA Region Types found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No CTA Region Types', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'CTA Region Types list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'CTA Region Types list', 'newspack' ),
		'menu_name'                  => esc_html__( 'CTA Region Types', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'CTA Region Types', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'CTA Region Types', 'newspack' ),
		'description'           => esc_html__( 'Allows for multile regions to be queries by type slug in a single request via the GraphQL API.', 'newspack' ),
		'labels'                => $labels,
		'public'                => false,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => '/cta_region_types',
			'with_front'   => false,
			'hierarchical' => true,
		),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'cta_region_types',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'ctaRegionType',
		'graphql_plural_name'   => 'ctaRegionTypes',
	);
	register_taxonomy( 'cta_region_type', array( 'cta_region' ), $args );
}
add_action( 'init', 'tw_call_to_actions_taxonomy_cta_region_type', 0 );
