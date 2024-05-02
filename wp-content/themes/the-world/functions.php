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

if ( ! function_exists( 'tw_admin_styles' ) ) :
	/**
	 * Add styles to admin.
	 *
	 * @return void
	 */
	function tw_admin_styles() {
		// Fixes layout heights of some accordion containers after WP 6.3 update.
		echo '<style>
			.wpseo-meta-section, .wpseo-meta-section-react { min-height: unset; }
		</style>';

		// Hides device options from preview menu dropdown. They are basically useless and confusing.
		echo '<style>
			.components-form-token-field__token-text { white-space: unset; }
		    .components-popover__content:has(.editor-preview-dropdown__button-external) .components-menu-group:not(:has(.editor-preview-dropdown__button-external)) { display: none; }
		    .components-popover__content:has(.editor-preview-dropdown__button-external) .components-menu-group:has([target="pp_revisions_copy"]) { display: none; }
			.components-popover__content:has(.editor-preview-dropdown__button-external) .components-menu-group + .components-menu-group:has(.editor-preview-dropdown__button-external) { border-top: none; margin-top: -8px; }
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

if ( ! function_exists( 'tw_add_edit_homepage_menu_link' ) ) :
	/**
	 * Add convenient link for editors to edit homepage.
	 *
	 * @uses add_menu_page() Add links to the menu.
	 */
	function tw_add_edit_homepage_menu_link() {
		add_menu_page( 'tw_edit_homepage_link', 'The World Homepage', 'publish_posts', '/term.php?taxonomy=program&tag_ID=12270&post_type=post', '', 'dashicons-admin-site-alt3', 8 );
	}
endif;
add_action( 'admin_menu', 'tw_add_edit_homepage_menu_link' );
