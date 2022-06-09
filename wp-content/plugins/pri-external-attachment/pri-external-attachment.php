<?php
/**
 * Plugin Name: PRI External Attachment
 * Plugin URI: https://www.dinkuminteractive.com/
 * Description: Attachment helper.
 * Version: 1.0.0
 * Author: Dinkum Interactive
 * Author URI: https://www.dinkuminteractive.com/
 * Text Domain: dinkuminteractive
 *
 * @package WordPress
 */

/**
 * Filter attachment URL based on environment setting.
 *
 * @param string $url            Media URL.
 * @return string $url
 */
function pri_transform_attachment_url( $url ) {

	$base_url     = get_option( 'pri_media_url_base_url', 'https://media.pri.org' );
	$public_path  = get_option( 'pri_media_url_public_path', 's3fs-public/' );
	$private_path = get_option( 'pri_media_url_private_path', 's3fs-private/' );

	$url = str_replace( 'public://', trailingslashit( $base_url ) . $public_path, $url );
	$url = str_replace( 'private://', trailingslashit( $base_url ) . $private_path, $url );

	return $url;
}

if ( ! function_exists( 'pri_get_attachment_url' ) ) {
	/**
	 * Filter attachment URL based on environment setting.
	 *
	 * @param string $url            Media URL.
	 * @param int    $attachment_id  Media post ID.
	 * @return string $url
	 */
	function pri_get_attachment_url( $url, $attachment_id ) {

		$original_url = get_post_meta( $attachment_id, 'original_uri', true );

		return $original_url ? pri_transform_attachment_url( $original_url ) : $url;
	}
}
add_filter( 'wp_get_attachment_url', 'pri_get_attachment_url', 99, 2 );

if ( ! function_exists( 'pri_get_attachment_image_src' ) ) {
	/**
	 * Filter attachment URL based on environment setting.
	 *
	 * @param array|false  $image         {
	 *     Array of image data, or boolean false if no image is available.
	 *
	 *     @type string $0 Image source URL.
	 *     @type int    $1 Image width in pixels.
	 *     @type int    $2 Image height in pixels.
	 *     @type bool   $3 Whether the image is a resized image.
	 * }
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|int[] $size          Requested image size. Can be any registered image size name, or
	 *                                    an array of width and height values in pixels (in that order).
	 * @param bool         $icon          Whether the image should be treated as an icon.
	 * @return string $url
	 */
	function pri_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {

		$image_attr = image_downsize( $attachment_id, $size );

		if ( isset( $image[0] ) && $image_attr ) {
			$image[0] = apply_filters( 'wp_get_attachment_url', $image[0], $attachment_id );
		}

		return $image;
	}
}
add_filter( 'wp_get_attachment_image_src', 'pri_get_attachment_image_src', 99, 4 );

if ( ! function_exists( 'pri_get_external_attachment_acf_load_point' ) ) {
	/**
	 * Add local load folder.
	 *
	 * @param array $paths Array of ACF load paths.
	 * @return array $paths Array of ACF load paths.
	 */
	function pri_get_external_attachment_acf_load_point( $paths ) {

		$paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';

		return $paths;
	}
}
add_filter( 'acf/settings/load_json', 'pri_get_external_attachment_acf_load_point' );

if ( ! function_exists( 'pri_get_external_attachment_acf_save_point' ) ) {
	/**
	 * Add local load folder.
	 *
	 * @param string $path String of ACF load path.
	 * @return string $path String of ACF load path.
	 */
	function pri_get_external_attachment_acf_save_point( $path ) {

		$path = plugin_dir_path( __FILE__ ) . 'acf-json';

		return $path;
	}
}
// add_filter( 'acf/settings/save_json', 'pri_get_external_attachment_acf_save_point' );
