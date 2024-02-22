<?php
/**
 * Plugin Name: TW Image Credit
 * Plugin URI: https://www.dinkuminteractive.com/
 * Description: Add image credit and credit field.
 * Version: 1.0.0
 */

/**
 * Adds the images media credit to rendered `core/image` blocks.
 *
 * @param  string  $block_content The block content about to be appended.
 * @param  mixed[] $block         The full block, including name and attributes.
 *
 * @return string
 *
 * @phpstan-param BlockMetaData $block
 */
function tw_add_media_credit_to_image_blocks( $block_content, array $block ) {

	// We only target standard images, and only when the credits are not displayed after the post content.
	if ( 'core/image' !== $block['blockName'] || ! isset( $block['attrs']['id'] ) ) {
		return $block_content;
	}

	// Retrieve image.
	$attachment = get_post( $block['attrs']['id'] );
	if ( empty( $attachment ) ) {
		// Not a valid attachment, let's bail.
		return $block_content;
	}

	// Get attachment credit.
	$attachment_credit = get_post_meta( $attachment->ID, '_media_credit', true );

	// Filter if needed.
	$block_content = apply_filters( 'tw_add_media_credit_to_image_blocks', $block_content, $attachment_credit );

	return $block_content;
}
add_filter( 'render_block', 'tw_add_media_credit_to_image_blocks', 10, 2 );

/**
 * Injects the credit into the caption markup of a `core/image` block.
 *
 * @param  string $block_content The block content.
 * @param  string $credit        The credit markup.
 *
 * @return string
 */
function tw_inject_credit_into_caption( $block_content, $credit ) {

	// If we have a credit, inject it into the caption markup.
	if ( $credit ) {

		// Replacement parts.
		$pattern     = '</figcaption>';
		$open        = '<span>';
		$credit = trim( $credit );
		$close       = '</span>';

		// Inject the credit into the caption markup.
		$block_content = str_replace( $pattern, "{$pattern}{$open}{$credit}{$close}", $block_content );
	}

	// Return the modified block content.
	return $block_content;
}
add_filter( 'tw_add_media_credit_to_image_blocks', 'tw_inject_credit_into_caption', 10, 2 );
