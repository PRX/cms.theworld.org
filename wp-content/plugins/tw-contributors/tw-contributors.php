<?php
/**
 * Plugin Name: TW Contributors
 * Description: Creates the Contributors custom taxonomy
 *
 * @package tw_contributors
 */

/**
 * Register Contributors Taxonomy
 *
 * @return void
 */
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
	$args   = array(
		'labels'              => $labels,
		'description'         => 'Biographical details of folks that work on content.',
		'rewrite'             => array(
			'slug'       => 'contributors',
			'with_front' => false,
		),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_admin_column'   => true,
		'show_in_nav_menus'   => false,
		'show_tagcloud'       => false,
		'show_in_rest'        => true,
		'show_in_graphql'     => true,
		'graphql_single_name' => 'contributor',
		'graphql_plural_name' => 'contributors',
	);
	register_taxonomy( 'contributor', array( 'post', 'attachment', 'segment' ), $args );
}
add_action( 'init', 'tw_contributors_taxonomy', 0 );

/**
 * Change RSS feed author name.
 *
 * @param string $author
 *
 * @return string
 */
function tw_contributors_rss_author( $author ) {

	if ( is_feed() ) {

		global $post;

		$author = tw_contributors_get_post_contributors_string( $post->ID );
	}

	return $author;
}
add_filter( 'the_author', 'tw_contributors_rss_author' );

/**
 * Get author byline.
 *
 * @param int $post_id The post ID.
 *
 * @return string The author byline.
 */
function tw_contributors_get_post_contributors_string( $post_id ) {

	$contributors = '';

	// Get contributor taxonomy terms.
	$contributor_terms = get_the_terms( $post_id, 'contributor' );

	// Bail early if no contributor terms are found.
	if (
		! $contributor_terms
		||
		! is_array( $contributor_terms )
	) {
		return $contributors;
	}

	$contributor_titles = wp_list_pluck( $contributor_terms, 'name' );

	$author_byline = implode( ', ', $contributor_titles );

	return $author_byline;
}
