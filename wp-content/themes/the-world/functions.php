<?php
/**
 * Theme functions file.
 *
 * @package the-world
 */

if ( getenv( 'TW_PREVIEWS_SECRET_KEY' ) ) {
	define( 'GRAPHQL_JWT_AUTH_SECRET_KEY', getenv( 'TW_PREVIEWS_SECRET_KEY' ) );
}

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
add_action( 'admin_enqueue_scripts', 'the_world_styles' );

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
		if ( in_array( getenv( 'WP_ENVIRONMENT_TYPE' ), array( 'staging', 'production' ), true ) ) {
			$my_hosts = array(
				'theworld.org',
			);
		} else {
			$my_hosts = array(
				'localhost:3000',
			);
		}
		return array_merge( $hosts, $my_hosts );
	}
endif;
add_filter( 'allowed_redirect_hosts', 'tw_allowed_redirect_hosts' );

if ( ! function_exists( 'tw_preview_post_link' ) ) {
	/**
	 * Update preview post link to go to frontend using permalink pattern.
	 * Should replace post name segment with preview segments.
	 *
	 * @param string  $preview_link Current preview link.
	 * @param WP_Post $post Post object preview link is being created for.
	 *
	 * @return string New post link URL.
	 */
	function tw_preview_post_link( $preview_link, $post ) {

		if ( ! function_exists( 'get_sample_permalink' ) ) {
			// Filter being called in a context without the function we need.
			// Just return the current link without any changes.
			return $preview_link;
		}

		$post_id = $post->ID;

		if ( wp_revisions_enabled( $post ) ) {
			$revision = wp_get_latest_revision_id_and_total_count( $post );

			if ( ! is_wp_error( $revision ) && $revision['latest_id'] ) {
				$post_id = $revision['latest_id'];
			}
		}

		list( $permalink ) = get_sample_permalink( $post );
		$permalink_ready   = str_replace( array( '%postname%', '%pagename%' ), "preview/{$post_id}", $permalink );

		return $permalink_ready;
	}
}
add_filter( 'preview_post_link', 'tw_preview_post_link', 100, 2 );

/**
 * Add filter hooks to extend those provided by Faust plugin to include our custom post types.
 */

if ( function_exists( 'WPE\FaustWP\Replacement\\post_link' ) ) {
	add_filter( 'post_type_link', 'WPE\FaustWP\Replacement\\post_link', 1000 );
}

if ( function_exists( 'WPE\FaustWP\Replacement\\preview_link_in_rest_response' ) ) {
	add_filter( 'rest_prepare_episode', 'WPE\FaustWP\Replacement\\preview_link_in_rest_response', 10, 2 );
	add_filter( 'rest_prepare_newsletter', 'WPE\FaustWP\Replacement\\preview_link_in_rest_response', 10, 2 );
	add_filter( 'rest_prepare_segment', 'WPE\FaustWP\Replacement\\preview_link_in_rest_response', 10, 2 );
}

if ( ! function_exists( 'tw_init_set_auth_cookie' ) ) {
	/**
	 * Set cookie to store auth token. SHould be an http cookies.
	 * Assume frontend will be served from the same domain.
	 *
	 * @return void
	 */
	function tw_init_set_auth_cookie() {
		$auth       = new WPGraphQL\JWT_Authentication\Auth();
		$secret_key = $auth->get_secret_key();

		if ( $secret_key && ! isset( $_COOKIE['wp_can_preview'] ) ) {
			$hostname = wp_parse_url( get_site_url(), PHP_URL_HOST );
			// NOTE: Regex assumes front-end domains will use single segment TLD's.
			$domain = trim( preg_replace( '~.*?\.?((?:\.?[\w_-]+){2})$~', '$1', $hostname ), '.' );
			$token  = $auth->get_refresh_token( wp_get_current_user() );

			setcookie(
				'wp_can_preview',
				$token,
				array(
					'expires'  => 0,
					'path'     => '/',
					'domain'   => $domain,
					'httponly' => true,
				)
			);
		}
	}
}
add_filter( 'admin_init', 'tw_init_set_auth_cookie' );

/**
 * Filter to fix deprecation warning generated by WP GraphQL JWT Authentication plugin.
 */
add_filter(
	'graphql_jwt_auth_get_auth_header',
	function ( $auth_header ) {
		return $auth_header ? $auth_header : '';
	}
);

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

		// Hides device options from preview menu dropdown. They are basically useless and confusing.
		echo '<style>
			.edit-post-post-preview-dropdown .components-menu-group:first-child { display: none; }
			.edit-post-post-preview-dropdown .components-menu-group + .components-menu-group { border-top: none; margin-top: -8px; }
		</style>';
	}
endif;
add_action( 'admin_head', 'tw_admin_styles' );

if ( ! function_exists( 'tw_admin_init_editor_styles' ) ) {
	/**
	 * Set cookie to store auth token. SHould be an http cookies.
	 * Assume frontend will be served from the same domain.
	 *
	 * @return void
	 */
	function tw_admin_init_editor_styles() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'css/editor.css' );
	}
}
add_filter( 'admin_init', 'tw_admin_init_editor_styles' );

if ( ! function_exists( 'tw_allowed_block_types' ) ) :
	/**
	 * Set allowed Gutenberg blocks.
	 *
	 * @param bool|string[]           $allowed_blocks Array of allowed blocks. Boolean to enable/disable all.
	 * @param WP_Block_Editor_Context $editor_context Current block editor context.
	 *
	 * @return bool|string[] Array of allowed blocks. Boolean to enable/disable all.
	 */
	function tw_allowed_block_types( $allowed_blocks, $editor_context ) {
		$allowed_blocks = array(
			// Text...
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/list-item',
			'core/pullquote',
			'tw/qa-block',

			// Media...
			'core/audio',
			'core/image',
			'tw/scroll-gallery',
			'tw/scroll-gallery-slide',

			// Design...
			'core/separator',

			// Embeds...
			// Specific types of embeds are now variants of the embed block.
			// Variations can be enabled in `./js/blockembed.js`.
			'core/embed',
		);

		if ( 'page' === $editor_context->post->post_type ) {
			$allowed_blocks[] = 'core/shortcode';
		}

		return $allowed_blocks;
	}
endif;
add_action( 'allowed_block_types_all', 'tw_allowed_block_types', 25, 2 );

if ( ! function_exists( 'tw_block_editor_assets' ) ) :
	/**
	 * Setup block editor scripts and styles.
	 *
	 * @uses wp_enqueue_scripts() Enqueue scripts hook.
	 * @uses wp_enqueue_styles() Registers style css file.
	 */
	function tw_block_editor_assets() {
		wp_enqueue_script(
			'tw-deny-list-blocks',
			get_template_directory_uri() . '/js/blockembed.js',
			array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
			'1.0',
			array( 'in_footer' => true )
		);
		wp_enqueue_style(
			'tw-block-editor-styles',
			get_template_directory_uri() . '/css/block-editor.css',
			array(),
			'1.0'
		);
	}
endif;
add_action( 'enqueue_block_editor_assets', 'tw_block_editor_assets' );

if ( ! function_exists( 'tw_remove_core_block_patterns' ) ) :
	/**
	 * Remove core block patterns.
	 *
	 * @uses remove_theme_support() Allows a theme to de-register its support of a certain feature.
	 */
	function tw_remove_core_block_patterns() {
		remove_theme_support( 'core-block-patterns' );
	}
endif;
add_action( 'after_setup_theme', 'tw_remove_core_block_patterns' );

/**
 * Prevent remote block patterns from loading.
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );

// Remove Windows Live Writer manifest link.
remove_action( 'wp_head', 'wlwmanifest_link' );

if ( ! function_exists( 'tw_custom_menu_link' ) ) :
	function tw_custom_menu_link() {
		add_menu_page('tw_edit_hompage_link', 'The World Homepage', 'publish_posts', "/wp-admin/term.php?taxonomy=program&tag_ID=2&post_type=post", '', 'dashicons-admin-site-alt3', 8);
	}
endif;
add_action('admin_menu', 'tw_custom_menu_link');
