<?php
/**
 * Plugin Name: TW Programs
 * Description: Creates the Programs custom taxonomy
 *
 * @package tw_programs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Register Program Taxonomy
 *
 * @return void
 */
function tw_programs_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Programs', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Program', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Programs', 'text_domain' ),
		'all_items'                  => __( 'All Programs', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Program Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Program', 'text_domain' ),
		'edit_item'                  => __( 'Edit Program', 'text_domain' ),
		'update_item'                => __( 'Update Program', 'text_domain' ),
		'view_item'                  => __( 'View Program', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate programs with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove programs', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Programs', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args   = array(
		'labels'              => $labels,
		'rewrite'             => array(
			'slug'       => 'programs',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_admin_column'   => true,
		'show_in_nav_menus'   => true,
		'show_tagcloud'       => false,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'program',
		'graphql_plural_name' => 'programs',
	);
	register_taxonomy( 'program', array( 'post', 'episode', 'segment' ), $args );
}
add_action( 'init', 'tw_programs_taxonomy', 1 );
