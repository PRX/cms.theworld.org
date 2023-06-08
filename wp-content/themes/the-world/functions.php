<?php
/**
 * Theme functions file.
 *
 * @package the-world
 */

/**
 * Setup theme styles and scripts.
 *
 * @uses wp_enqueue_scripts() Enqueue scripts hook.
 * @uses wp_enqueue_styles() Registers style css file.
 */
function the_world_styles() {
	wp_enqueue_style( 'style', get_stylesheet_uri(), array(), 1.0 );
}
add_action( 'wp_enqueue_scripts', 'the_world_styles' );

/**
 * Add allowed redirect hosts.
 *
 * @uses allowed_redirect_hosts() Allowed redirect hosts filter.
 *
 * @param string[] $hosts Array of existing allowed hosts.
 * @return string[] New array of allowed hosts.
 */
function tw_allowed_redirect_hosts( $hosts ) {
	$my_hosts = array(
		'theworld.org',
	);
	return array_merge( $hosts, $my_hosts );
};
add_filter( 'allowed_redirect_hosts', 'tw_allowed_redirect_hosts' );

/**
 * Redirect logged out users to the frontend site.
 * Protects from accidental sharing of preview URL's.
 *
 * @uses template_redirect() Template Redirect action hook
 * @uses is_user_logged_in() Is user logged in conditional tag
 * @uses wp_redirect() WP Redirect function
 */
function tw_redirect_logged_out_to_frontend() {
	global $wp;

	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( "https://theworld.org/{$wp->request}" );
		exit();
	}
}
add_action( 'template_redirect', 'tw_redirect_logged_out_to_frontend' );