<?php
/*
Plugin Name: TW Resource Development Tags
Description: Creates the Resource Development custom taxonomy
*/

// Register Custom Taxonomy
function tw_resource_development_tags_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Resource Development Tags', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Resource Development Tag', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Resource Development Tags', 'text_domain' ),
		'all_items'                  => __( 'All Resource Development Tags', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Resource Development Tag Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Resource Development Tag', 'text_domain' ),
		'edit_item'                  => __( 'Edit Resource Development Tag', 'text_domain' ),
		'update_item'                => __( 'Update Resource Development Tag', 'text_domain' ),
		'view_item'                  => __( 'View Resource Development Tag', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Resource Development Tags with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Resource Development Tags', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Resource Development Tags', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'Resource Development Tag', array( 'post' ), $args );

}
add_action( 'init', 'tw_resource_development_tags_taxonomy', 0 );
