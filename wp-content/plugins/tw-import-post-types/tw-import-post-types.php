<?php
/**
 * Plugin Name: TW Import Post Types
 * Description: Creates the Custom Post Types necessary to import content from the old site. Can be REMOVED after the import is complete.
 *
 * @package tw_import_post_types
 */

/**
 * Register cusotm post type function.
 *
 * @return void
 */
function cptui_register_my_cpts() {

	/**
	 * Post Type: Persons.
	 */

	$labels = array(
		'name'               => esc_html__( 'Persons', 'newspack' ),
		'singular_name'      => esc_html__( 'Person', 'newspack' ),
		'all_items'          => esc_html__( 'Persons', 'newspack' ),
		'add_new'            => esc_html__( 'Add New', 'newspack' ),
		'add_new_item'       => esc_html__( 'Add New Person', 'newspack' ),
		'edit_item'          => esc_html__( 'Edit Person', 'newspack' ),
		'new_item'           => esc_html__( 'New Person', 'newspack' ),
		'view_item'          => esc_html__( 'View Person', 'newspack' ),
		'search_items'       => esc_html__( 'Search Person', 'newspack' ),
		'not_found'          => esc_html__( 'No Person found', 'newspack' ),
		'not_found_in_trash' => esc_html__( 'No Person found in Trash', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Persons', 'newspack' ),
		'labels'                => $labels,
		'description'           => 'Use person to create a page for each person associated with a story, episode, etc.',
		'public'                => true,
		'publicly_queryable'    => false,
		'show_ui'               => true,
		'show_in_rest'          => false,
		'rest_base'             => 'person-post',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'        => 'wp/v2',
		'has_archive'           => false,
		'show_in_menu'          => false,
		'show_in_nav_menus'     => false,
		'delete_with_user'      => false,
		'exclude_from_search'   => true,
		'capability_type'       => 'post',
		'map_meta_cap'          => true,
		'hierarchical'          => false,
		'can_export'            => true,
		'rewrite'               => array(
			'slug'       => 'person-post',
			'with_front' => true,
		),
		'query_var'             => true,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'author' ),
		'show_in_graphql'       => false,
	);

	register_post_type( 'person', $args );

	/**
	 * Post Type: Programs.
	 */

	$labels = array(
		'name'               => esc_html__( 'Programs', 'newspack' ),
		'singular_name'      => esc_html__( 'Program', 'newspack' ),
		'all_items'          => esc_html__( 'Programs', 'newspack' ),
		'add_new'            => esc_html__( 'Add New', 'newspack' ),
		'add_new_item'       => esc_html__( 'Add New Program', 'newspack' ),
		'edit_item'          => esc_html__( 'Edit Program', 'newspack' ),
		'new_item'           => esc_html__( 'New Program', 'newspack' ),
		'view_item'          => esc_html__( 'View Program', 'newspack' ),
		'search_items'       => esc_html__( 'Search Program', 'newspack' ),
		'not_found'          => esc_html__( 'No Program found', 'newspack' ),
		'not_found_in_trash' => esc_html__( 'No Program found in Trash', 'newspack' ),
	);

	$args = array(
		'label'                 => esc_html__( 'Programs', 'newspack' ),
		'labels'                => $labels,
		'description'           => 'Use programs to create a page for each program.',
		'public'                => true,
		'publicly_queryable'    => false,
		'show_ui'               => true,
		'show_in_rest'          => false,
		'rest_base'             => 'program-post',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'        => 'wp/v2',
		'has_archive'           => false,
		'show_in_menu'          => false,
		'show_in_nav_menus'     => false,
		'delete_with_user'      => false,
		'exclude_from_search'   => true,
		'capability_type'       => 'post',
		'map_meta_cap'          => true,
		'hierarchical'          => false,
		'can_export'            => true,
		'rewrite'               => array(
			'slug'       => 'program-post',
			'with_front' => true,
		),
		'query_var'             => true,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'author' ),
		'show_in_graphql'       => false,
	);

	register_post_type( 'program', $args );
}

add_action( 'init', 'cptui_register_my_cpts', 0 );

/**
 * This function is a filter that checks if the object is a Program or Person post type based on the URL query.
 * If the URL query contains 'program', it will search for the term slug in the 'program' taxonomy and return the object.
 * If the URL query contains 'person', it will search for the term slug in the 'person' taxonomy and return the object.
 * If the object is not found, it will return false.
 *
 * @param mixed $object The object to be returned.
 * @param array $url_query The URL query parameters.
 * @return mixed The object if found, false otherwise.
 */
function peh_get_object_maybe_program_person( $object, $url_query ) {

	// Maybe Program or Person post type?
	if ( false === $object && isset( $url_query['program'] ) ) {

		$term_slug = $url_query['program'];
		$term_tax  = 'program';

		if ( $term_slug ) {

			$object = _peh_get_object_by_taxonomy( $term_slug, $term_tax );
		}
	} elseif ( false === $object && isset( $url_query['person'] ) ) {

		$term_slug = $url_query['person'];
		$term_tax  = 'person';

		if ( $term_slug ) {

			$object = _peh_get_object_by_taxonomy( $term_slug, $term_tax );
		}
	}

	return $object;
}
add_filter( 'peh_get_object_wild', 'peh_get_object_maybe_program_person', 11, 2 );
