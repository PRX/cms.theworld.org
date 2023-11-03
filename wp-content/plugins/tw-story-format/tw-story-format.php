<?php
/**
 * Plugin Name: TW Story Types
 * Description: Creates the Story Type custom taxonomy
 *
 * @package tw_story_types
 */

/**
 * Register Story Types Taxonomy
 *
 * @return void
 */
function tw_story_types_taxonomy() {
	/**
	 * Taxonomy: Story Formats.
	 */

	$labels = array(
		'name'                       => esc_html__( 'Story Formats', 'newspack' ),
		'singular_name'              => esc_html__( 'Story Format', 'newspack' ),
		'search_items'               => esc_html__( 'Search Story Formats', 'newspack' ),
		'popular_items'              => esc_html__( 'Popular Story Formats', 'newspack' ),
		'all_items'                  => esc_html__( 'All Story Formats', 'newspack' ),
		'parent_item'                => esc_html__( 'Parent Story Format', 'newspack' ),
		'parent_item_colon'          => esc_html__( 'Parent Story Format:', 'newspack' ),
		'edit_item'                  => esc_html__( 'Edit Story Format', 'newspack' ),
		'view_item'                  => esc_html__( 'View Story Format', 'newspack' ),
		'update_item'                => esc_html__( 'Update Story Format', 'newspack' ),
		'add_new_item'               => esc_html__( 'Add New Story Format', 'newspack' ),
		'new_item_name'              => esc_html__( 'New Story Format Name', 'newspack' ),
		'separate_items_with_commas' => esc_html__( 'Separate Story Formats with commas', 'newspack' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Story Formats', 'newspack' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Story Formats', 'newspack' ),
		'not_found'                  => esc_html__( 'No Story Formats found.', 'newspack' ),
		'no_terms'                   => esc_html__( 'No Story Formats', 'newspack' ),
		'items_list_navigation'      => esc_html__( 'Story Formats list navigation', 'newspack' ),
		'items_list'                 => esc_html__( 'Story Formats list', 'newspack' ),
		'menu_name'                  => esc_html__( 'Story Formats', 'newspack' ),
		'name_admin_bar'             => esc_html__( 'Story Formats', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Story Formats', 'newspack' ),
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array(
			'slug'         => 'story_format',
			'with_front'   => true,
			'hierarchical' => true,
		),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'show_tagcloud'         => false,
		'rest_base'             => 'story_format',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rest_namespace'        => 'wp/v2',
		'show_in_quick_edit'    => true,
		'sort'                  => false,
		'show_in_graphql'       => true,
		'graphql_single_name'   => 'storyFormat',
		'graphql_plural_name'   => 'storyFormats',
	);
	register_taxonomy( 'story_format', array( 'post' ), $args );
}
add_action( 'init', 'tw_story_types_taxonomy', 0 );
