<?php
/**
 * Register Country Taxonomy
 *
 * @package tw_custom_tags
 */

/**
 * Register Country Taxonomy
 *
 * @return void
 */
function tw_country_taxonomy() {
	/**
	 * Taxonomy: Countries.
	 */

	$labels = [
		"name" => esc_html__( "Countries", "newspack" ),
		"singular_name" => esc_html__( "Country", "newspack" ),
		"name" => esc_html__( "Countries", "newspack" ),
		"singular_name" => esc_html__( "Country", "newspack" ),
		"search_items" => esc_html__( "Search Countries", "newspack" ),
		"popular_items" => esc_html__( "Popular Countries", "newspack" ),
		"all_items" => esc_html__( "All Countries", "newspack" ),
		"parent_item" => esc_html__( "Parent Country", "newspack" ),
		"parent_item_colon" => esc_html__( "Parent Country:", "newspack" ),
		"edit_item" => esc_html__( "Edit Country", "newspack" ),
		"view_item" => esc_html__( "View Country", "newspack" ),
		"update_item" => esc_html__( "Update Country", "newspack" ),
		"add_new_item" => esc_html__( "Add New Country", "newspack" ),
		"new_item_name" => esc_html__( "New Country Name", "newspack" ),
		"separate_items_with_commas" => esc_html__( "Separate Countries with commas", "newspack" ),
		"add_or_remove_items" => esc_html__( "Add or remove Countries", "newspack" ),
		"choose_from_most_used" => esc_html__( "Choose from the most used Countries", "newspack" ),
		"not_found" => esc_html__( "No Countries found.", "newspack" ),
		"no_terms" => esc_html__( "No Countries", "newspack" ),
		"items_list_navigation" => esc_html__( "Countries list navigation", "newspack" ),
		"items_list" => esc_html__( "Countries list", "newspack" ),
		"menu_name" => esc_html__( "Countries", "newspack" ),
		"name_admin_bar" => esc_html__( "Countries", "newspack" ),
	];


	$args = [
		"label" => esc_html__( "Countries", "newspack" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'country', 'with_front' => true,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "country",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "country", [ "post" ], $args );
}
add_action( 'init', 'tw_country_taxonomy', 0 );
