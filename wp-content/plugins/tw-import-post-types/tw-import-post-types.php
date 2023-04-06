<?php
/**
 * Plugin Name: TW Import Post Types
 * Description: Creates the Custom Post Types necessary to import content from the old site. Can be REMOVED after the import is complete.
 *
 * @package tw_import_post_types
 */

 function cptui_register_my_cpts() {

	/**
	 * Post Type: Persons.
	 */

	$labels = [
		"name" => esc_html__( "Persons", "newspack" ),
		"singular_name" => esc_html__( "Person", "newspack" ),
		"all_items" => esc_html__( "Persons", "newspack" ),
		"add_new" => esc_html__( "Add New", "newspack" ),
		"add_new_item" => esc_html__( "Add New Person", "newspack" ),
		"edit_item" => esc_html__( "Edit Person", "newspack" ),
		"new_item" => esc_html__( "New Person", "newspack" ),
		"view_item" => esc_html__( "View Person", "newspack" ),
		"search_items" => esc_html__( "Search Person", "newspack" ),
		"not_found" => esc_html__( "No Person found", "newspack" ),
		"not_found_in_trash" => esc_html__( "No Person found in Trash", "newspack" ),
	];

	$args = [
		"label" => esc_html__( "Persons", "newspack" ),
		"labels" => $labels,
		"description" => "Use person to create a page for each person associated with a story, episode, etc.",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "person-post",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => false,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "person-post", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "person", $args );

	/**
	 * Post Type: Programs.
	 */

	$labels = [
		"name" => esc_html__( "Programs", "newspack" ),
		"singular_name" => esc_html__( "Program", "newspack" ),
		"all_items" => esc_html__( "Programs", "newspack" ),
		"add_new" => esc_html__( "Add New", "newspack" ),
		"add_new_item" => esc_html__( "Add New Program", "newspack" ),
		"edit_item" => esc_html__( "Edit Program", "newspack" ),
		"new_item" => esc_html__( "New Program", "newspack" ),
		"view_item" => esc_html__( "View Program", "newspack" ),
		"search_items" => esc_html__( "Search Program", "newspack" ),
		"not_found" => esc_html__( "No Program found", "newspack" ),
		"not_found_in_trash" => esc_html__( "No Program found in Trash", "newspack" ),
	];

	$args = [
		"label" => esc_html__( "Programs", "newspack" ),
		"labels" => $labels,
		"description" => "Use programs to create a page for each program.",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "program-post",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => false,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "program-post", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "program", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
