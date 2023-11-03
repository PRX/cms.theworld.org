<?php
/**
 * Plugin Name: TW Media
 * Description: Creates terms needed for image and audio files
 *
 * @package tw_media
 */

/**
 * Register License Taxonomy.
 *
 * @return void
 */
function tw_license_taxonomy() {

	$labels = array(
		'name'                       => _x( 'License', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'License', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'License', 'text_domain' ),
		'all_items'                  => __( 'All License', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Story Type Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Story Type', 'text_domain' ),
		'edit_item'                  => __( 'Edit Story Type', 'text_domain' ),
		'update_item'                => __( 'Update Story Type', 'text_domain' ),
		'view_item'                  => __( 'View Story Type', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate License with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove License', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search License', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args   = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_admin_column'   => false,
		'show_in_nav_menus'   => true,
		'show_tagcloud'       => false,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'license',
		'graphql_plural_name' => 'licenses',
	);
	register_taxonomy( 'license', array( 'attachment' ), $args );
}
add_action( 'init', 'tw_license_taxonomy', 0 );
