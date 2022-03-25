<?php
/*
Plugin Name: TW Contributors
Description: Creates the Contributors custom taxonomy
*/

// Register Custom Taxonomy
function tw_contributors_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Contributors', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Contributor', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Contributors', 'text_domain' ),
		'all_items'                  => __( 'All Contributors', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Contributor Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Contributor', 'text_domain' ),
		'edit_item'                  => __( 'Edit Contributor', 'text_domain' ),
		'update_item'                => __( 'Update Contributor', 'text_domain' ),
		'view_item'                  => __( 'View Contributor', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate contributors with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove contributors', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Contributors', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'description'	               => 'Biographical details of folks that work on content.',
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'contributor', [ 'post', 'attachment' ], $args );

}
add_action( 'init', 'tw_contributors_taxonomy', 0 );
