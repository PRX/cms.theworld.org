<?php
/**
 * Register Continent Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Continent Taxonomy
 *
 * @return void
 */
function tw_continent_taxonomy() {
	/**
	 * Taxonomy: Continents.
	 */

	$labels = [
		"name" => esc_html__( "Continents", "newspack" ),
		"singular_name" => esc_html__( "Continent", "newspack" ),
		"name" => esc_html__( "Continents", "newspack" ),
		"singular_name" => esc_html__( "Continent", "newspack" ),
		"search_items" => esc_html__( "Search Continents", "newspack" ),
		"popular_items" => esc_html__( "Popular Continents", "newspack" ),
		"all_items" => esc_html__( "All Continents", "newspack" ),
		"parent_item" => esc_html__( "Parent Continent", "newspack" ),
		"parent_item_colon" => esc_html__( "Parent Continent:", "newspack" ),
		"edit_item" => esc_html__( "Edit Continent", "newspack" ),
		"view_item" => esc_html__( "View Continent", "newspack" ),
		"update_item" => esc_html__( "Update Continent", "newspack" ),
		"add_new_item" => esc_html__( "Add New Continent", "newspack" ),
		"new_item_name" => esc_html__( "New Continent Name", "newspack" ),
		"separate_items_with_commas" => esc_html__( "Separate Continents with commas", "newspack" ),
		"add_or_remove_items" => esc_html__( "Add or remove Continents", "newspack" ),
		"choose_from_most_used" => esc_html__( "Choose from the most used Continents", "newspack" ),
		"not_found" => esc_html__( "No Continents found.", "newspack" ),
		"no_terms" => esc_html__( "No Continents", "newspack" ),
		"items_list_navigation" => esc_html__( "Continents list navigation", "newspack" ),
		"items_list" => esc_html__( "Continents list", "newspack" ),
		"menu_name" => esc_html__( "Continents", "newspack" ),
		"name_admin_bar" => esc_html__( "Continents", "newspack" ),
	];


	$args = [
		"label" => esc_html__( "Continents", "newspack" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'continent', 'with_front' => true,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "continent",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "continent", [ "post" ], $args );
}
add_action( 'init', 'tw_continent_taxonomy', 0 );
