<?php
/**
 * Register Province or State Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Province or State Taxonomy
 *
 * @return void
 */
function tw_province_state_taxonomy() {
	/**
	 * Taxonomy: Province Or States.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Province Or States', 'newspack' ),
		'singular_name'              => esc_html__( 'Province Or State', 'newspack' ),
		'name'                       => esc_html__( 'Province Or States', 'newspack' ),
		'singular_name'              => esc_html__( 'Province Or State', 'newspack' ),
		'search_items'               => esc_html__( 'Search Province Or States', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Province Or States', 'newspack' ),
		'all_items'                  => esc_html__( 'All Province Or States', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent Province Or State', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent Province Or State:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit Province Or State', 'newspack' ),
		'view_item'                  => esc_html__( 'View Province Or State', 'newspack' ),
		'update_item'                => esc_html__( 'Update Province Or State', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New Province Or State', 'newspack' ),
		'new_item_name'              => esc_html__( 'New Province Or State Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Province Or States with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Province Or States', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Province Or States', 'newspack' ),
		'not_found'                  => esc_html__( 'No Province Or States found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Province Or States', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Province Or States list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Province Or States list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Province Or States', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Province Or States', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Province Or States', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'tags/provinces_or_states',
			'with_front'   => false,
			'hierarchical' => true,
		),
		'show_admin_column'     => false,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'tags/provinces_or_states',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'provinceOrState',
		'graphql_plural_name'   => 'provincesOrStates',
	);
	register_taxonomy( 'province_or_state', array( 'post', 'episode', 'segment' ), $args );
}
add_action( 'init', 'tw_province_state_taxonomy', 0 );
