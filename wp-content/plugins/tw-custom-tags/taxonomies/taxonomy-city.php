<?php
/**
 * Register Cities Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Cities Taxonomy
 *
 * @return void
 */
function tw_city_taxonomy() {
	/**
	 * Taxonomy: Cities.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Cities', 'newspack' ),
		'singular_name'              => esc_html__( 'City', 'newspack' ),
		'search_items'               => esc_html__( 'Search Cities', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Cities', 'newspack' ),
		'all_items'                  => esc_html__( 'All Cities', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent City', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent City:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit City', 'newspack' ),
		'view_item'                  => esc_html__( 'View City', 'newspack' ),
		'update_item'                => esc_html__( 'Update City', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New City', 'newspack' ),
		'new_item_name'              => esc_html__( 'New City Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Cities with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Cities', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Cities', 'newspack' ),
		'not_found'                  => esc_html__( 'No Cities found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Cities', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Cities list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Cities list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Cities', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Cities', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Cities', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'tags/cities',
			'with_front'   => false,
			'hierarchical' => true,
		),
		'show_admin_column'     => false,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'tags/cities',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'city',
		'graphql_plural_name'   => 'cities',
	);
	register_taxonomy( 'city', array( 'post', 'episode', 'segment' ), $args );
}
add_action( 'init', 'tw_city_taxonomy', 0 );
