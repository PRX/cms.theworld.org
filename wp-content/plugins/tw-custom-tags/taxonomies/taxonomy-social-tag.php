<?php
/**
 * Register Social Tag Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Social Tag Taxonomy
 *
 * @return void
 */
function tw_social_tag_taxonomy() {
	/**
	 * Taxonomy: Social Tags.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Social Tags', 'newspack' ),
		'singular_name'              => esc_html__( 'Social Tag', 'newspack' ),
		'name'                       => esc_html__( 'Social Tags', 'newspack' ),
		'singular_name'              => esc_html__( 'Social Tag', 'newspack' ),
		'search_items'               => esc_html__( 'Search Social Tags', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Social Tags', 'newspack' ),
		'all_items'                  => esc_html__( 'All Social Tags', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent Social Tag', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent Social Tag:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit Social Tag', 'newspack' ),
		'view_item'                  => esc_html__( 'View Social Tag', 'newspack' ),
		'update_item'                => esc_html__( 'Update Social Tag', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New Social Tag', 'newspack' ),
		'new_item_name'              => esc_html__( 'New Social Tag Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Social Tags with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Social Tags', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Social Tags', 'newspack' ),
		'not_found'                  => esc_html__( 'No Social Tags found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Social Tags', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Social Tags list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Social Tags list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Social Tags', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Social Tags', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Social Tags', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'tags/social_tags',
			'with_front'   => false,
			'hierarchical' => true,
		),
		'show_admin_column'     => false,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'tags/social_tags',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'socialTag',
		'graphql_plural_name'   => 'socialTags',
	);
	register_taxonomy( 'social_tags', array( 'post', 'episode', 'segment' ), $args );
}
add_action( 'init', 'tw_social_tag_taxonomy', 0 );
