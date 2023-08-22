<?php
/**
 * Theme functions file.
 *
 * @package the-world
 */

if ( ! function_exists( 'the_world_styles' ) ) :
	/**
	 * Setup theme styles and scripts.
	 *
	 * @uses wp_enqueue_scripts() Enqueue scripts hook.
	 * @uses wp_enqueue_styles() Registers style css file.
	 */
	function the_world_styles() {
		wp_enqueue_style( 'style', get_stylesheet_uri(), array(), 1.0 );
	}
endif;
add_action( 'wp_enqueue_scripts', 'the_world_styles' );

if ( ! function_exists( 'tw_allowed_redirect_hosts' ) ) :
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
endif;
add_filter( 'allowed_redirect_hosts', 'tw_allowed_redirect_hosts' );

if ( ! function_exists( 'tw_redirect_logged_out_to_frontend' ) ) :
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
endif;
add_action( 'template_redirect', 'tw_redirect_logged_out_to_frontend' );

if ( ! function_exists( 'tw_admin_styles' ) ) :
	/**
	 * Add styles to admin.
	 *
	 * @return void
	 */
	function tw_admin_styles() {
		// Fixes layout heights of some accordion containers after WP 6.3 update.
		echo '<style>
			.components-panel__body { display: grid; }
			.wpseo-meta-section, .wpseo-meta-section-react { min-height: unset; }
		</style>';
	}
endif;
add_action( 'admin_head', 'tw_admin_styles' );

// Remove Windows Live Writer manifest link
remove_action( 'wp_head', 'wlwmanifest_link' );
