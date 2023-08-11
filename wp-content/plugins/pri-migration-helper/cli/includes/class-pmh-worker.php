<?php
// Use wpcli util.
use WP_CLI\Utils;

/**
 * Class PMH_Worker
 */
class PMH_Worker {

	/**
	 * The last ID processed.
	 *
	 * @var int
	 */
	private $i_last_id = 0;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		ini_set( 'display_errors', true ); // Display the errors that may happen (ex: Allowed memory size exhausted)
		if ( ! defined( 'WP_ADMIN' ) ) {
			define( 'WP_ADMIN', true ); // To execute the actions done when is_admin() (ex: Register Types post types)
		}
	}

	/**
	 * Get unprocessed images.
	 */
	public function get_unprocessed_images() {

		// Print start message.
		WP_CLI::log( __( 'Getting total unprocessed images.', 'dinkuminteractive' ) );

		$a_media_ids = f_pmh_get_media_ids( 1, 100 );
		$i_media_ids = count( $a_media_ids );

		WP_CLI::success( __( 'Total of 100 found unprocessed images: ', 'dinkuminteractive' ) . $i_media_ids );
		WP_CLI::success( __( 'Unprocessed image IDs: ', 'dinkuminteractive' ) . implode( ', ', $a_media_ids ) );
	}

	/**
	 * Get the images count.
	 *
	 * @return int
	 */
	public function get_images_count() {

		// Print start message.
		WP_CLI::log( __( 'Getting total images.', 'dinkuminteractive' ) );

		// Get attachment array.
		$a_attachment_counts = wp_count_attachments();

		// Calculate all the total of all images.
		$i_images_count = 0;

		foreach ( $a_attachment_counts as $s_key => $i_count ) {

			// Skip the non-image attachment counts.
			if ( 'image' !== substr( $s_key, 0, 5 ) ) {
				continue;
			}

			$i_images_count += (int) $i_count;
		}

		// Print the total of all images.
		WP_CLI::log( wp_sprintf( 'Total Images: %s', $i_images_count ) );

		// Also return the total of all images.
		return $i_images_count;
	}

	/**
	 * Process images. Fix sizes based on Drupal meta data.
	 */
	public function image_fix( $a_args ) {

		// All or New.
		$s_all_or_new = isset( $a_args[0] ) && 'all' === $a_args[0] ? 'all' : 'new';

		// Per process limit. Default to 50. Check if argument is passed and an integer.
		$i_per_process_limit = isset( $a_args[1] ) && intval( $a_args[1] ) ? (int) $a_args[1] : 50;

		// Start from.
		$i_start_from = isset( $a_args[2] ) && intval( $a_args[2] ) ? (int) $a_args[2] : 0;

		if ( 'all' === $s_all_or_new ) {

			WP_CLI::log( 'Processing all images..' );

			// Get total images count.
			$i_images_count = $this->get_images_count();

			// Create progress bar.
			$s_message      = __( 'Fixing all images.', 'dinkuminteractive' );
			$o_progress_cli = \WP_CLI\Utils\make_progress_bar( $s_message, $i_images_count );

			// Set the minimum post ID
			$this->i_last_id = $i_start_from;

			// Process images in batches.
			do {
				// Query and process images.
				$processed_count = $this->process_images_by_asc_id( $i_per_process_limit );

				// Advance the progress bar.
				$o_progress_cli->tick( $processed_count );

			} while ( $processed_count > 0 );

			// Finish the progress bar.
			$o_progress_cli->finish();

			// Print success message.
			WP_CLI::success( __( 'All images fixed. Last ID is: ', 'dinkuminteractive' ) . $this->i_last_id );
		}

		if ( 'new' === $s_all_or_new ) {

			WP_CLI::log( 'Processing new images..' );

			// Set total images count.
			$i_total_new_images_count = 0;

			// Create progress bar.
			$s_message      = __( 'Fixing new images.', 'dinkuminteractive' );
			$o_progress_cli = \WP_CLI\Utils\make_progress_bar( $s_message, 100 );

			// Process new images.
			do {
				// Query and process images.
				$processed_count = $this->process_new_images( $i_per_process_limit );

				// Increment total images count.
				$i_total_new_images_count += $processed_count;

				// Advance the progress bar.
				$o_progress_cli->tick();

			} while ( $processed_count > 0 );

			// Finish the progress bar.
			$o_progress_cli->finish();

			// Print success message.
			WP_CLI::success( __( 'All new images fixed. Total count: ', 'dinkuminteractive' ). $i_total_new_images_count );
		}
	}

	/**
	 * Process images in batches.
	 *
	 * @param int $per_process_limit The number of images to process in each batch.
	 *
	 * @return int The number of images processed in the batch.
	 */
	public function process_images_by_asc_id( $per_process_limit ) {

		// Query all the images.
		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'post_parent'    => null,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => $per_process_limit,
			'no_found_rows'  => true,
		);

		// Add filter to query.
		add_filter( 'posts_where', array( $this, 'image_fix_all_wp_query' ) );

		// Do the query.
		$query = new WP_Query($args);

		// Get the posts.
		$post_ids = $query->get_posts();

		// Remove filter.
		remove_filter( 'posts_where', array( $this, 'image_fix_all_wp_query' ) );

		return $this->process_images( $post_ids );
	}

	/**
	 * Support for image_fix_all wp query.
	 */
	public function image_fix_all_wp_query( $where ) {

		// Set the minimum post ID
		$min_id = $this->i_last_id;

		// Append to the WHERE clause
		$where .= " AND ID > " . $min_id;

		return $where;
	}

	/**
	 * Process new images.
	 */
	public function process_new_images( $i_limit ) {

		$a_new_image_ids = f_pmh_get_media_ids( 1, $i_limit );

		return $this->process_images( $a_new_image_ids );
	}

	/**
	 * Process images.
	 */
	public function process_images( $post_ids ) {

		// Process only if there are posts.
		if ( $post_ids ) {

			// Set the last ID.
			$this->i_last_id = (int) $post_ids[ count( $post_ids ) - 1 ];

			// Get Drupal Metadata.
			$a_drupal_media_metadatas = f_pmh_get_drupal_file_metadata( $post_ids );

			// Process the images here.
			foreach ( $post_ids as $i_post_id ) {

				// Get image metadata.
				$a_attachment_metadata = wp_get_attachment_metadata( $i_post_id );

				$a_keys_to_change = array( 'width', 'height' );

				$b_updated = false;

				foreach ( $a_keys_to_change as $s_key_to_change ) {

					if (
						isset( $a_drupal_media_metadatas[ $i_post_id ][ $s_key_to_change ] )
						&&
						$a_drupal_media_metadatas[ $i_post_id ][ $s_key_to_change ]
						&&
						$a_drupal_media_metadatas[ $i_post_id ][ $s_key_to_change ] != $a_attachment_metadata[ $s_key_to_change ]
					) {

						$b_updated = true;

						// File metadata.
						$a_attachment_metadata[ $s_key_to_change ] = $a_drupal_media_metadatas[ $i_post_id ][ $s_key_to_change ];

						// File 'full' size metadata.
						if ( isset( $a_attachment_metadata['sizes']['full'][ $s_key_to_change ] ) ) {

							$a_attachment_metadata['sizes']['full'][ $s_key_to_change ] = $a_drupal_media_metadatas[ $i_post_id ][ $s_key_to_change ];
						}
					}
				}

				// Drupal file metadata found.
				if ( $b_updated ) {

					wp_update_attachment_metadata( $i_post_id, $a_attachment_metadata );
				}

				// Drupal file metadata not found and not corrected.
				elseif ( ! $b_updated && ! isset( $a_drupal_media_metadatas[ $i_post_id ] ) ) {

					// Get the attachment URL.
					$s_attachment_url = wp_get_attachment_url( $i_post_id );

					// Get the image dimensions.
					$a_image_sizes = $this->clean_url_get_imagesize( $s_attachment_url );

					// If the image dimensions are found.
					if ( $a_image_sizes ) {

						list( $i_width, $i_height ) = $a_image_sizes;

						// Update the attachment metadata.
						$a_attachment_metadata['width']  = $i_width;
						$a_attachment_metadata['height'] = $i_height;

						// Update fullsize metadata.
						if ( isset( $a_attachment_metadata['sizes']['full'] ) ) {

							$a_attachment_metadata['sizes']['full']['width']  = $i_width;
							$a_attachment_metadata['sizes']['full']['height'] = $i_height;
						}

						// Update the attachment metadata.
						wp_update_attachment_metadata( $i_post_id, $a_attachment_metadata );
					}
				}

				f_pmh_flag_object_corrected( $i_post_id, 'post' );
			}
		}

		return count( $post_ids );
	}

	/**
	 * Escape URL and Get image size array.
	 *
	 * @param string $s_image_url Image URL.
	 *
	 * @return array|bool
	 */
	public function clean_url_get_imagesize( $s_image_url ) {

		// Escape the URL.
		$s_target_url    = esc_url( $s_image_url );
		$s_file_name     = basename( $s_target_url );
		$s_valid_url     = filter_var( $s_target_url, FILTER_VALIDATE_URL );
		$s_clean_url	= $s_valid_url ? $s_target_url : str_replace( $s_file_name, urlencode( $s_file_name ), $s_image_url );

		return getimagesize( $s_clean_url );
	}

	/**
	 * Run posts content media fix.
	 *
	 * @param array $a_args Arguments [limit, comma_ids]
	 */
	public function posts_content_media_fix( $a_args ) {

		// Set limit and ids.
		$i_perpage_process = isset( $a_args[0] ) ? (int) $a_args[0] : 50;
		$a_per_ids         = isset( $a_args[1] ) ? explode( ',', $a_args[1] ) : array();

		// Get posts and media ids.
		$a_posts_ids = f_pmh_get_posts_ids( 1, $i_perpage_process, $a_per_ids );
		$i_posts_ids = count( $a_posts_ids );
		$a_media_ids = f_pmh_get_media_ids( 1, 1 );
		$i_media_ids = count( $a_media_ids );

		// Run only if $i_media_ids = 0.
		if ( $i_media_ids ) {

			WP_CLI::error( "Aborting. Unfixed media is still found." );

		} else {

			// Run only if $a_posts_ids is not empty.
			if ( $i_posts_ids ) {

				// Show log.
				WP_CLI::log( "Processing {$i_posts_ids} posts." );

				// Do while $a_posts_ids is not empty single_post_content_media_fix.
				do {

					// Loop through posts ids.
					foreach ( $a_posts_ids as $i_post_id ) {

						// Show log.
						WP_CLI::log( "Processing post ID: {$i_post_id}." );

						// Run single_post_content_media_fix.
						$b_updated = $this->single_post_content_media_fix( array( $i_post_id ) );

						// If updated echo success.
						if ( $b_updated ) {

							// Show log.
							WP_CLI::success( "Post ID: {$i_post_id} updated." );

						} else {

							// Show log.
							WP_CLI::log( "Post ID: {$i_post_id} not updated." );
						}
					}

					$a_posts_ids = f_pmh_get_posts_ids( 1, $i_perpage_process );

				} while ( $a_posts_ids );

			} else {

				// Show log.
				WP_CLI::log( "No posts to process." );
			}
		}
	}

	/**
	 * Run post content media fix.
	 *
	 * @param array $a_args Arguments [0] = Post ID.
	 */
	public function single_post_content_media_fix( $a_args ) {

		/**
		 * Post ID to check.
		 * - 638516
		 */

		// Get target post ID.
		$target_wp_post_id = $a_args[0];

		// Try to get the post.
		$o_post = get_post( $target_wp_post_id );

		// Update status.
		$updated = false;

		// Check if post exists and post type is post.
		if ( $o_post && 'post' === $o_post->post_type ) {

			$updated = f_pmh_process_posts_content( $target_wp_post_id );

			if ( $updated ) {

				WP_CLI::success( "Post with ID: {$target_wp_post_id} is updated." );

			} else {

				WP_CLI::error( "Post with ID: {$target_wp_post_id} is not updated." );
			}
		} else {

			WP_CLI::error( "Post with ID: {$target_wp_post_id} does not exist." );
		}

		// Return.
		return $updated;
	}
}
