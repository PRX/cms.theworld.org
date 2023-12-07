<?php
/**
 * Setup admin page UI.
 *
 * @package tw_episode_importer
 */

define( 'TW_EPISODE_IMPORTER_APP_CONTAINER_ID', 'tw-episode-importer-app' );

/**
 * Register submenu page.
 *
 * @return void
 */
function tw_episode_importer_admin_page() {
	add_submenu_page(
		'edit.php?post_type=episode',
		__( 'Import Episode', 'tw-text' ),
		__( 'Import Episode', 'tw-text' ),
		'publish_posts',
		'import-episode',
		'tw_episode_importer_admin_page_html',
		1
	);
}
add_action( 'admin_menu', 'tw_episode_importer_admin_page' );

/**
 * Render HTML and enqueue scripts for admin page.
 *
 * @return void
 */
function tw_episode_importer_admin_page_html() {
	wp_enqueue_style( 'tw-episode-importer-admin-ui', TW_EPISODE_IMPORTER_URL . 'admin/ui/ui.css', array(), wp_rand() );
	wp_enqueue_script( 'tw-episode-importer-admin-ui', TW_EPISODE_IMPORTER_URL . 'admin/ui/dist/bundle.js', array(), wp_rand(), true );
	wp_localize_script(
		'tw-episode-importer-admin-ui',
		'appLocalizer',
		array(
			'appContainerId' => TW_EPISODE_IMPORTER_APP_CONTAINER_ID,
			'apiUrl'         => home_url( '/wp-json/' . TW_API_ROUTE_BASE . '/' . TW_EPISODE_IMPORTER_API_ENDPOINT . '/' ),
			'gqlUrl'         => trailingslashit( site_url() ) . 'index.php?' . \WPGraphQL\Router::$route,
			'nonce'          => wp_create_nonce( 'wp_rest' ),
		)
	);

	// HTML only needs to be a div that can be targeted for React to init app into.
	echo '<div id="' . esc_attr( TW_EPISODE_IMPORTER_APP_CONTAINER_ID ) . '"></div>';
}
