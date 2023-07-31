<?php
/**
 * Register Person Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Person Taxonomy
 *
 * @return void
 */
function tw_person_taxonomy() {
	/**
	 * Taxonomy: Persons.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Persons', 'newspack' ),
		'singular_name'              => esc_html__( 'Person', 'newspack' ),
		'name'                       => esc_html__( 'Persons', 'newspack' ),
		'singular_name'              => esc_html__( 'Person', 'newspack' ),
		'search_items'               => esc_html__( 'Search Persons', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Persons', 'newspack' ),
		'all_items'                  => esc_html__( 'All Persons', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent Person', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent Person:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit Person', 'newspack' ),
		'view_item'                  => esc_html__( 'View Person', 'newspack' ),
		'update_item'                => esc_html__( 'Update Person', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New Person', 'newspack' ),
		'new_item_name'              => esc_html__( 'New Person Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Persons with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Persons', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Persons', 'newspack' ),
		'not_found'                  => esc_html__( 'No Persons found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Persons', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Persons list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Persons list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Persons', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Persons', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Persons', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'tags/people',
			'with_front'   => false,
			'hierarchical' => true,
		),
		'show_admin_column'     => false,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'person',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'person',
		'graphql_plural_name'   => 'people',
	);
	register_taxonomy( 'person', array( 'post' ), $args );
}
add_action( 'init', 'tw_person_taxonomy', 0 );
