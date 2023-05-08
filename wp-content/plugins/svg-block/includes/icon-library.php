<?php
/**
 * The icon library
 *
 * @package   SVGBlock
 * @author    Phi Phan <mrphipv@gmail.com>
 * @copyright Copyright (c) 2022, Phi Phan
 */

namespace SVGBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( __NAMESPACE__ . '\IconLibrary' ) ) :
	/**
	 * The controller class for icon library.
	 */
	class IconLibrary {
		/**
		 * Plugin instance
		 *
		 * @var IconLibrary
		 */
		private static $instance;

		/**
		 * A dummy constructor
		 */
		private function __construct() {}

		/**
		 * Initialize the instance.
		 *
		 * @return IconLibrary
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof IconLibrary ) ) {
				self::$instance = new IconLibrary();
			}

			return self::$instance;
		}

		/**
		 * Run main hooks
		 *
		 * @return void
		 */
		public function run() {
			// Add rest api endpoint to query icon library.
			add_action( 'rest_api_init', [ $this, 'register_icon_library_endpoint' ] );

			// Load data for js.
			// add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
		}

		/**
		 * Enqueue data for js
		 *
		 * @return void
		 */
		public function enqueue_block_editor_assets() {
			$this->enqueue_localize_scripts( [ 'boldblocks-svg-block-editor-script' ] );
		}

		/**
		 * Enqueue localize scripts
		 *
		 * @return void
		 */
		public function enqueue_localize_scripts( $handles ) {
			// icons_version file path.
			$icons_version_file = SVG_BLOCK_PATH . 'data/icon-library/icons-version.json';

			// Bail if ther is no icons-version.json.
			if ( \file_exists( $icons_version_file ) ) {
				$icons_version = \file_get_contents( $icons_version_file );
				$icons_version = \json_decode( $icons_version, true );

				// Define localize sripts.
				$localization_scripts = [
					'iconsVersion' => $icons_version['version'] ?? '1.0.0',
				];

				// Register localize scripts.
				foreach ( $handles as $handle ) {
					wp_localize_script(
						$handle,
						'SVGBlockIconLibrary',
						$localization_scripts
					);
				}
			}
		}

		/**
		 * Build a custom endpoint to query icon library.
		 *
		 * @return void
		 */
		public function register_icon_library_endpoint() {
			register_rest_route(
				'svgblock/v1',
				'/getIconLibrary/',
				array(
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_icon_library' ],
					'permission_callback' => function () {
						return current_user_can( 'publish_posts' );
					},
				)
			);
		}

			/**
			 * Get icon library.
			 *
			 * @param WP_REST_Request $request The request object.
			 * @return void
			 */
		public function get_icon_library( $request ) {
			// icons file path.
			$icons_file = SVG_BLOCK_PATH . 'data/icon-library/icons.json';

			// Send the error if the icons file is not exists.
			if ( ! \file_exists( $icons_file ) ) {
				wp_send_json_error( __( 'The icons.json file is not exists.', 'svg-block' ), 500 );
			}

			// Parse json.
			$icons = wp_json_file_decode( $icons_file, [ 'associative' => true ] );

			wp_send_json(
				[
					'data'    => $icons,
					'success' => true,
				]
			);
		}
	}

	// Kick start.
	IconLibrary::get_instance()->run();
endif;
