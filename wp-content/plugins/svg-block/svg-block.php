<?php
/**
 * Plugin Name:       SVG Block
 * Description:       Display an SVG image as a block, which can be used for displaying icons, dividers, buttons...
 * Requires at least: 6.3
 * Requires PHP:      7.0
 * Version:           1.1.18
 * Author:            Phi Phan
 * Author URI:        https://boldblocks.net
 *
 * @package   SVGBlock
 * @copyright Copyright(c) 2022, Phi Phan
 */

namespace SVGBlock;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define constants.
define( 'SVG_BLOCK_ROOT', __FILE__ );
define( 'SVG_BLOCK_PATH', trailingslashit( plugin_dir_path( SVG_BLOCK_ROOT ) ) );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function svg_block_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', __NAMESPACE__ . '\\svg_block_block_init' );

// Load icons library.
require_once __DIR__ . '/includes/icon-library.php';

/**
 * Link the block to post.
 *
 * @param string   $block_content
 * @param array    $block
 * @param WP_Block $block_instance
 * @return string
 */
function svg_block_render_block( $block_content, $block, $block_instance ) {
	if ( $block['attrs']['linkToPost'] ?? false ) {
		if ( isset( $block_instance->context['postId'] ) ) {
			// Get post_id from the context first.
			$post_id = $block_instance->context['postId'];
		} else {
			// Fallback to the current post id.
			$post_id = get_queried_object_id();
		}

		$post_link = get_permalink( $post_id );
		if ( $post_link ) {
			$processor = new \WP_HTML_Tag_Processor( $block_content );
			if ( $processor->next_tag( 'a' ) ) {
				$processor->set_attribute( 'href', $post_link );

				$block_content = $processor->get_updated_html();
			}
		}
	}

	return $block_content;
}
add_action( 'render_block_boldblocks/svg-block', __NAMESPACE__ . '\\svg_block_render_block', 10, 3 );
