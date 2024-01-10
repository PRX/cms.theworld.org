<?php
/**
 * Plugin Name: External Media without Import (PATCHED)
 * Description: Add external images to the media library without importing, i.e. uploading them to your WordPress site. (PATCH note: Removed bits requiring a height, width, and mime/type for imported files.)
 * Version: 1.1.2
 * Author: Zhixiang Zhu
 * Author URI: http://zxtechart.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
 *
 * External Media without Import is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * External Media without Import is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with External Media without Import. If not, see
 * https://www.gnu.org/licenses/gpl-3.0-standalone.html.
 *
 * @package PATCH-external-media-without-import
 */

namespace emwi;

function init_emwi() {
	wp_enqueue_media();
	$style    = 'emwi-css';
	$css_file = plugins_url( '/external-media-without-import.css', __FILE__ );
	wp_register_style( $style, $css_file );
	wp_enqueue_style( $style );
	$script  = 'emwi-js';
	$js_file = plugins_url( '/external-media-without-import.js', __FILE__ );
	wp_register_script( $script, $js_file, array( 'jquery' ) );
	wp_enqueue_script( $script );
}
add_action( 'admin_enqueue_scripts', 'emwi\init_emwi' );

add_action( 'post-plupload-upload-ui', 'emwi\post_upload_ui' );
add_action( 'post-html-upload-ui', 'emwi\post_upload_ui' );
add_action( 'wp_ajax_add_external_media_without_import', 'emwi\wp_ajax_add_external_media_without_import' );

/**
 * This filter is to make attachments added by this plugin pass the test
 * of wp_attachment_is_image. Otherwise issues with other plugins such
 * as WooCommerce occur:
 *
 * https://github.com/zzxiang/external-media-without-import/issues/10
 * https://wordpress.org/support/topic/product-gallery-image-not-working/
 * http://zxtechart.com/2017/06/05/wordpress/#comment-178
 * http://zxtechart.com/2017/06/05/wordpress/#comment-192
 */
add_filter(
	'get_attached_file',
	function ( $file, $attachment_id ) {
		if ( empty( $file ) ) {
			$post = get_post( $attachment_id );
			return $post->guid;
		}
		return $file;
	},
	10,
	2
);

function post_upload_ui() {
	?>
	<div id="emwi-in-upload-ui">
		<div class="row1">
			<?php echo __( 'or' ); ?>
		</div>
		<div class="row2">
		<button id="emwi-show" class="button button-large">
		<?php echo __( 'Add External Media without Import' ); ?>
		</button>
		<?php print_media_new_panel(); ?>
		</div>
	</div>
	<?php
}

function print_media_new_panel() {
	?>
	<div id="emwi-media-new-panel" style="display: none">
		<label id="emwi-urls-label"><?php echo __( 'Add media from URL\'s' ); ?></label>
		<textarea id="emwi-urls" rows="10" name="urls" required placeholder="<?php echo __( "Please fill in the media URL's.\nProvide multiple URL's each on its own line." ); ?>" value="
																						<?php
																						if ( isset( $_GET['urls'] ) ) {
																							echo esc_url( $_GET['urls'] );}
																						?>
		"></textarea>
		<div id="emwi-hidden" style="display: none">
			<span id="emwi-error">
			<?php
			if ( isset( $_GET['error'] ) ) {
				echo esc_html( $_GET['error'] );}
			?>
			</span>
	</div>
		<div id="emwi-buttons-row">
		<input type="hidden" name="action" value="add_external_media_without_import">
		<span class="spinner"></span>
		<input type="button" id="emwi-clear" class="button" value="<?php echo __( 'Clear' ); ?>">
		<input type="submit" id="emwi-add" class="button button-primary" value="<?php echo __( 'Add' ); ?>">
		<input type="button" id="emwi-cancel" class="button" value="<?php echo __( 'Cancel' ); ?>">
		</div>
	</div>
	<?php
}

function wp_ajax_add_external_media_without_import() {
	$info           = add_external_media_without_import();
	$attachment_ids = $info['attachment_ids'];
	$attachments    = array();
	foreach ( $attachment_ids as $attachment_id ) {
		if ( $attachment = wp_prepare_attachment_for_js( $attachment_id ) ) {
			array_push( $attachments, $attachment );
		} else {
			$error = "There's an attachment sucessfully inserted to the media library but failed to be retrieved from the database to be displayed on the page.";
		}
	}
	$info['attachments'] = $attachments;
	if ( isset( $error ) ) {
		$info['error'] = isset( $info['error'] ) ? $info['error'] . "\nAnother error also occurred. " . $error : $error;
	}
	wp_send_json_success( $info );
}

function sanitize_and_validate_input() {
	$raw_urls = explode( "\n", $_POST['urls'] );
	$info     = array(
		'urls' => array(),
	);

	if ( empty( $raw_urls ) ) {
		$info['error'] = _( 'No URL\'s submitted.' );
		return $info;
	}

	foreach ( $raw_urls as $i => $raw_url ) {
		// Don't call sanitize_text_field on url because it removes '%20'.
		// Always use esc_url/esc_url_raw when sanitizing URLs. See:
		// https://codex.wordpress.org/Function_Reference/esc_url
		$info['urls'][] = esc_url_raw( trim( $raw_url ) );
	}

	return $info;
}

function add_external_media_without_import() {
	$info = sanitize_and_validate_input();

	if ( isset( $info['error'] ) ) {
		return $info;
	}

	$urls = $info['urls'];

	$attachment_ids = array();
	$failed_urls    = array();

	foreach ( $urls as $url ) {
		if ( ! $fp = fopen( $url, 'r' ) ) {
			array_push( $failed_urls, $url );
			continue;
		}

		$meta = stream_get_meta_data( $fp );

		// Close file stream.
		fclose( $fp );

		foreach ( $meta['wrapper_data'] as $index => $value ) {
			if ( preg_match( '~HTTP/\d+\.\d+ 200 OK~', $value ) === 1 ) {
				$ok_index = $index;
				break;
			}
		}

		$ok_headers = array_slice( $meta['wrapper_data'], $ok_index + 1 );
		$headers    = array_reduce(
			$ok_headers,
			function ( $c, $val ) {
				list($key, $val) = explode( ': ', $val );

				$c[ strtolower( $key ) ] = $val;

				return $c;
			},
			array()
		);
		$filename   = wp_basename( $url );
		$filesize   = $headers['content-length'];
		$mime_type  = $headers['content-type'];

		// Insert into database.
		$attachment    = array(
			'guid'           => $url,
			'post_mime_type' => $mime_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
		);
		$attachment_id = wp_insert_attachment( $attachment );

		// Add metadata.
		$attachment_metadata = array(
			'file'     => $filename,
			'filesize' => $filesize,
		);

		// Get metadata from file.
		$tmp_file = download_url( $url );
		if ( ! is_wp_error( $tmp_file ) ) {
			$tmp_attachment_metadata = array();

			if ( wp_attachment_is( 'image', $attachment_id ) ) {
				$tmp_attachment_metadata = wp_read_image_metadata( $tmp_file );
				if ( $image_size = wp_getimagesize( $tmp_file ) ) {
					list($width, $height)          = $image_size;
					$attachment_metadata['width']  = $width;
					$attachment_metadata['height'] = $height;
					$attachment_metadata['sizes']  = array( 'full' => $attachment_metadata );
				}
			} elseif ( wp_attachment_is( 'video', $attachment_id ) ) {
				$tmp_attachment_metadata = wp_read_video_metadata( $tmp_file );
			} elseif ( wp_attachment_is( 'audio', $attachment_id ) ) {
				$tmp_attachment_metadata = wp_read_audio_metadata( $tmp_file );
			}

			// Remove the blob of binary data from the array.
			unset( $tmp_attachment_metadata['image']['data'] );

			// Merge file metadata with initial values.
			$attachment_metadata = array_merge( $attachment_metadata, $tmp_attachment_metadata );

			// Remove temp file.
			unlink( $tmp_file );
		}

		// Update attachment with metadata.
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		// Add to our successful attachments.
		array_push( $attachment_ids, $attachment_id );
	}

	$info['attachment_ids'] = $attachment_ids;

	$failed_urls_string = implode( "\n", $failed_urls );
	$info['urls']       = $failed_urls_string;

	if ( ! empty( $failed_urls_string ) ) {
		$info['error'] = 'Failed to get info for these URL\'s. Please check them and try again.';
	}

	return $info;
}
