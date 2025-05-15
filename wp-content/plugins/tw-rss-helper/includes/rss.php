<?php
/**
 * Add support to site RSS feed
 *
 * @since 1.0.0
 */

/**
 * Add audio enclosure to RSS feed.
 *
 * This function will get post_rss_audio meta from a post.
 * WARNING: This function will only work if ACF is active.
 *
 * @since 1.0.0
 */
function tw_rss_helper_audio_enclosure() {

	// Check if ACF is active
	if ( !function_exists('get_field') ) {
		return false;
	}

	global $post;

	// Get audio metadata.
	$audio_id = get_post_meta( $post->ID, 'audio', true );

	$audio_media = [];

	$audio_url = wp_get_attachment_url( $audio_id );

	// Bail early if no audio URL is found.
	if ( $audio_url ) {

		$audio_media['url'] = $audio_url;

	} else {

		return false;
	}

	$audio_filesize = tw_rss_helper_get_external_audio_url_filesize( $audio_url );

	// If filesize is found, add it to the audio media array.
	if ( $audio_filesize ) {
		$audio_media['filesize'] = $audio_filesize;
	}

	// Build the default enclosure HTML.
	$enclosure_html = sprintf(
		'<enclosure url="%s" type="audio/mpeg" length="%s"/>',
		$audio_media['url'],
		$audio_media['filesize']
	);

	/**
	 * Filter the audio enclosure HTML
	 *
	 * @since 1.0.0
	 *
	 * @param  string $enclosure_html The default enclosure HTML.
	 * @param  int    $post_id        The post ID.
	 * @param  array  $audio_media    The audio media array.
	 *
	 * @return string The audio enclosure HTML.
	 */
	$filtered_html = apply_filters(
		'tw_rss_helper_audio_enclosure',
		$enclosure_html,
		$post->ID,
		$audio_media
	);

	echo wp_kses(
		$filtered_html,
		array(
			'enclosure' => array(
				'url' => true,
				'type' => true,
				'length' => true,
			),
		)
	);
}
add_action( 'rss2_item', 'tw_rss_helper_audio_enclosure' );

/**
 * Get external audio URL file size.
 *
 * @since 1.0.0
 *
 * @param  string $url The URL to get the file size of.
 *
 * @return int The file size of the URL.
 */
function tw_rss_helper_get_external_audio_url_filesize( $url ) {

	$filesize = 0;

	$headers = get_headers( $url, 1 );

	if ( isset( $headers['Content-Length'] ) ) {

		$content_length = $headers['Content-Length'];

		if ( is_array( $content_length ) ) {

			$filesize = max( array_map( 'intval', $content_length ) );

		} else {

			$filesize = intval( $content_length );
		}
	}

	return $filesize;
}
