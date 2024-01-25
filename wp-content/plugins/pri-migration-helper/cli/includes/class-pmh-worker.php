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

		// Get image size normally.
		$a_image_res = @getimagesize( $s_clean_url );

		if ( false === $a_image_res ) {

			$s_file_name = basename( $s_image_url );

			// Get image size with urlencode.
			$s_clean_url = str_replace( $s_file_name, urlencode( $s_file_name ), $s_image_url );
			$a_image_res = @getimagesize( $s_clean_url );

			if ( false === $a_image_res ) {

				// Get image size with rawurlencode.
				$s_clean_url = str_replace( $s_file_name,rawurlencode( $s_file_name ), $s_image_url );
				$a_image_res = @getimagesize( $s_clean_url );
			}
		}

		return $a_image_res;
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

					if ( ! $a_per_ids ) {
						$a_posts_ids = f_pmh_get_posts_ids( 1, $i_perpage_process );
					}

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

				WP_CLI::log( "Post with ID: {$target_wp_post_id} is not updated." );
			}
		} else {

			WP_CLI::log( "Post with ID: {$target_wp_post_id} does not exist." );
		}

		// Return.
		return $updated;
	}

	/**
	 * Check each post tag if it is duplicated in other custom taxonomies.
	 *
	 * @return void
	 */
	public function get_duplicated_post_tags_number( $a_args ) {

		// @todo Remove after testing.
		ob_start();

		// Get all post tags.
		$a_post_tags = get_terms( array( 'taxonomy' => 'post_tag', 'hide_empty' => false ) );

		// Get all the public custom taxonomies.
		$a_custom_taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		// Get the custom taxonomies that are not post_tag.
		$a_custom_taxonomies = array_filter(
			$a_custom_taxonomies,
			function( $o_custom_taxonomy ) {
				return 'post_tag' !== $o_custom_taxonomy->name;
			}
		);

		// Get the custom taxonomies slugs.
		$a_custom_taxonomies_slugs = array_map(
			function( $o_custom_taxonomy ) {
				return $o_custom_taxonomy->name;
			},
			$a_custom_taxonomies
		);

		// Duplicate container.
		$a_duplicates = array();

		// For each custom taxonomy.
		foreach ( $a_custom_taxonomies_slugs as $s_custom_taxonomies_slug ) {

			$a_duplicates[ $s_custom_taxonomies_slug ] = array();
		}

		// For each post_tag.
		foreach ( $a_post_tags as $o_post_tag ) {

			// Get the post_tag slug.
			$s_post_tag_slug = $o_post_tag->slug;

			// For each custom taxonomy.
			foreach ( $a_custom_taxonomies_slugs as $s_custom_taxonomies_slug ) {

				// Get term from custom taxonomy with the same slug as the post_tag.
				$o_term = get_term_by( 'slug', $s_post_tag_slug, $s_custom_taxonomies_slug );

				// If term is found.
				if ( $o_term ) {

					// Add post_tag id to the duplicates container.
					$a_duplicates[ $s_custom_taxonomies_slug ][] = $o_post_tag->term_id;
				}
			}
		}

		/* Debug: Dump the total duplicate for each custom taxonomy.
		 */
		foreach ( $a_duplicates as $s_custom_taxonomies_slug => $a_duplicate_ids ) {

			// @todo Remove after testing.
			WP_CLI::log( $s_custom_taxonomies_slug . ': ' . count( $a_duplicate_ids ) );
		}

		// @todo Remove after testing.
		$log = ob_get_clean();

		// @todo Remove after testing.
		WP_CLI::log( $log );
	}

	/**
	 * Get all custom taxonomies slugs.
	 *
	 * @return array
	 */
	public function get_custom_taxonomies() {

		global $a_custom_taxonomies_slugs;

		if ( $a_custom_taxonomies_slugs ) {
			return $a_custom_taxonomies_slugs;
		}

		// Get all custom taxonomy slugs.
		$a_custom_taxonomies_slugs = array_map(
			function( $o_custom_taxonomy ) {

				// skip post_tag.
				if ( 'post_tag' === $o_custom_taxonomy->name ) {
					return;
				}

				return $o_custom_taxonomy->name;
			},
			get_taxonomies( array( 'public' => true ), 'objects' )
		);

		// Remove empty values.
		$a_custom_taxonomies_slugs = array_filter( $a_custom_taxonomies_slugs );

		return $a_custom_taxonomies_slugs;
	}

	/**
	 * Get all post_tags in a post and compare them with the custom taxonomies.
	 *
	 * Example: wp tw-fix cleanup_post_tag 654016 remove
	 *
	 * @param [type] $a_args
	 * @return void
	 */
	public function cleanup_post_tag( $a_args ) {

		// @todo Remove after testing.
		ob_start();

		// Get post ID.
		$i_post_id = isset( $a_args[0] ) ? (int) $a_args[0] : 0;

		// Remove post_tags from the post.
		$b_remove_post_tags = isset( $a_args[1] ) && 'remove' === $a_args[1] ? true : false;

		// Get custom taxonomies.
		$a_custom_taxonomies = $this->get_custom_taxonomies();

		// Get post_tags.
		$a_post_tags = wp_get_post_tags( $i_post_id );

		// Begin.
		WP_CLI::log( '-------------------------------' );

		// Foreach post_tags check if that slug exists in the custom taxonomies.
		if ( $a_post_tags ) {

			// If post_tags found.
			WP_CLI::log( wp_sprintf( 'Post %s contains %s tags:', $i_post_id, count( $a_post_tags ) ) );

			// List post_tags.
			// foreach ( $a_post_tags as $o_post_tag ) {

			// 	WP_CLI::log( wp_sprintf( '- (%s) %s', $o_post_tag->term_id, $o_post_tag->slug ) );
			// }

			// Separator.
			WP_CLI::log( '- - - - - - - - - - - - - - - -' );

			foreach ( $a_post_tags as $o_post_tag ) {

				// Get post_tag ID.
				$i_post_tag_id = $o_post_tag->term_id;

				// Get the post_tag slug.
				$s_post_tag_slug = $o_post_tag->slug;

				// Custom taxonomy terms to relate.
				$a_custom_taxonomy_terms = array();

				// Show the post_tag slug.
				WP_CLI::log( '' );
				WP_CLI::log( wp_sprintf( 'Working on post_tags: (%s) %s', $i_post_tag_id, $s_post_tag_slug ) );

				// For each custom taxonomy.
				foreach ( $a_custom_taxonomies as $s_custom_taxonomies_slug ) {

					// Get term from custom taxonomy with the same slug as the post_tag.
					$o_term = get_term_by( 'slug', $s_post_tag_slug, $s_custom_taxonomies_slug );

					// If term is found.
					if ( $o_term ) {

						// Show the custom taxonomy where the post_tag was found.
						WP_CLI::log( wp_sprintf( '- Slug found in custom taxonomy %s with term ID %s.', $s_custom_taxonomies_slug, $o_term->term_id ) );

						// Add term from custom taxonomy.
						$a_custom_taxonomy_terms[ $s_custom_taxonomies_slug ][] = $o_term->term_id;

						// Remove post_tag from the post.
						if ( $b_remove_post_tags ) {
							// Create the array of the post_tag IDs to be removed from the post.
							$a_tags_to_remove[] = $o_post_tag->term_id;
						}
					}
				}

				// Relate the custom taxonomy terms.
				if ( $a_custom_taxonomy_terms ) {

					// Show the post ID and the custom taxonomy and terms to relate.
					WP_CLI::log( '' );
					WP_CLI::log( 'Relating terms to post ID: ' . $i_post_id );

					foreach ( $a_custom_taxonomy_terms as $s_custom_taxonomy_slug => $a_custom_term_ids ) {

						// Set custom terms to post.
						$terms_related = true; // @todo Remove after testing. Activate the one below
						// $terms_related = wp_set_object_terms( $i_post_id, $a_custom_term_ids, $s_custom_taxonomy_slug, true );

						// Report terms related.
						WP_CLI::log( wp_sprintf( '- Add terms from taxonomy %s to post. (%s) | %s',
							$s_custom_taxonomy_slug,
							$terms_related ? 'success' : 'failed',
							implode( ',', $a_custom_term_ids )
						) );

						if ( ! is_wp_error( $terms_related ) && $b_remove_post_tags && $a_tags_to_remove ) {

							// Remove the post_tag from the post only if the association to custom taxonomies worked.
							$term_removed = true; // @todo Remove after testing. Activate the one below
							// $term_removed = wp_remove_object_terms( $i_post_id, $a_tags_to_remove, 'post_tag' );

							// Report term removed.
							WP_CLI::log( wp_sprintf( '- Remove post_tag with term ID %s from post. (%s)', implode( ',', $a_tags_to_remove ), $term_removed ? 'success' : 'failed' ) );
						}
					}
				} else {

					// Show the post_tag slug.
					WP_CLI::log( wp_sprintf( '- No custom taxonomy terms to relate.' ) );
				}

				// Separator.
                WP_CLI::log( '- - - - - - - - - - - - - - - -' );
			}
		} else {

			// If no post_tags found.
			WP_CLI::log( 'Post %s does not contain any post_tags.', $i_post_id );
		}

		/* Debug
		echo "<pre>";
		var_dump( $a_custom_taxonomies );
		echo "</pre>";
		 */

		// @todo Remove after testing.
		$log = ob_get_clean();

		// @todo Remove after testing.
		WP_CLI::log( $log );

	}

	/**
	 * Loop through all post_tags, check each term if it is duplicated in other custom taxonomies.
	 * If it is duplicated, add posts related to the post_tag to the custom taxonomy term.
	 *
	 * @param array $args
	 * @param int $args[0] Start from page number.
	 * @param int $args[1] Limit.
	 *
	 * Example: wp tw-fix post_tags_fix_duplicate 1 10
	 */
	public function post_tags_fix_duplicate( $args ) {

		// Get all custom taxonomies.
		$a_custom_taxonomies = $this->get_custom_taxonomies();

		// Get allowed post types.
		$a_allowed_post_types = array( 'post', 'episode' );

		// Start looping.
		WP_CLI::log( '' );
		WP_CLI::log( 'Looping post tags with posts related to it.' );
		WP_CLI::log( '-------------------------------' );

		// If the first argument can be converted to integer, use it as the page number.
		$page = isset( $args[0] ) ? (int) $args[0] : 1;

		// If $page is not an integer, use the argument as the post_tag slug.
		if ( ! $page ) {
			$page = isset( $args[0] ) ? $args[0] : 1;
		}

		// Limit if needed.
		$i_limit = isset( $args[1] ) ? (int) $args[1] : false;

		do {

			// If $page is not an integer, use the argument as the post_tag slug.
			if ( ! is_int( $page ) ) {

				$term = get_term_by( 'slug', $page, 'post_tag' );

			} else {

				$term = $this->post_tags_fix_duplicate_page( array( $page ) );

			}

			if ( ! is_null( $term ) ) {

				WP_CLI::log( wp_sprintf(
					'Processing post_tags - (%s) %s',
					$term->term_id,
					$term->slug
				) );

				// Get post ids related to the post_tag.
				$a_post_ids = get_objects_in_term( $term->term_id, 'post_tag' );

				// For each custom taxonomy, check if the post_tag is duplicated.
				foreach ( $a_custom_taxonomies as $s_custom_taxonomies_slug ) {

					// Get term from custom taxonomy with the same slug as the post_tag.
					$o_term = get_term_by( 'slug', $term->slug, $s_custom_taxonomies_slug );

					// If term is found.
					if ( $o_term ) {

						// Show the custom taxonomy where the post_tag was found.
						WP_CLI::log( wp_sprintf( '- Slug found in taxonomy %s with term ID %s.', $s_custom_taxonomies_slug, $o_term->term_id ) );

						// Add posts related to the post_tag to the custom taxonomy term.
						$terms_related = false; // @todo Remove after testing.

						// For each post id related to the post_tag.
						foreach ( $a_post_ids as $i_post_id ) {

							// Only process post and episode.
							$s_post_type = get_post_type( $i_post_id );

							// Skip if post type is not allowed.
							if ( ! in_array( $s_post_type, $a_allowed_post_types ) ) {

								// Report terms related.
								WP_CLI::log( wp_sprintf( '  Post ID %s is not a (%s).', $i_post_id, implode( ',', $a_allowed_post_types ) ) );

								continue;
							}

							// Check if the post id is already related to the custom taxonomy term.
							$a_post_terms = get_the_terms( $i_post_id, $s_custom_taxonomies_slug );

							// If the post id is not related to the custom taxonomy term.
							if ( ! in_array( $o_term->term_id, wp_list_pluck( $a_post_terms, 'term_id' ) ) ) {

								// Default values.
								$terms_related = false;
								$term_removed  = false;

								// Add the post id to the custom taxonomy term.
								// $terms_related = true; // @todo Remove after testing.
								$terms_related = wp_set_object_terms( $i_post_id, $o_term->term_id, $s_custom_taxonomies_slug, true );

								if ( ! is_wp_error( $terms_related ) ) {
									// Remove the post_tag from the post.
									// $term_removed = true; // @todo Remove after testing.
									$term_removed = wp_remove_object_terms( $i_post_id, $term->term_id, 'post_tag' );
								}

								// Report terms related.
								WP_CLI::log( wp_sprintf( '  Relate post ID %s to term and remove from post_tag. (%s) (%s)',
									$i_post_id,
									$terms_related ? 'success' : 'failed',
									$term_removed ? 'success' : 'failed',
								) );

								// If error.
								if ( is_wp_error( $terms_related ) ) {

									// Report terms related.
									WP_CLI::log( wp_sprintf( '  Error: %s', $terms_related->get_error_message() ) );
								}
								if ( is_wp_error( $term_removed ) ) {

									// Report terms related.
									WP_CLI::log( wp_sprintf( '  Error: %s', $term_removed->get_error_message() ) );
								}

							} else {

								// Report terms related.
								WP_CLI::log( wp_sprintf( '  Post ID %s is already related to term.', $i_post_id ) );
							}
						}
					}
				}
			}

			// Separator.
			WP_CLI::log( '' );
			WP_CLI::log( wp_sprintf( '- - - - - - - - - - - - - - - - (%s)', $page ) );
			WP_CLI::log( '' );
			WP_CLI::log( '' );

			// Break if $page is not an integer.
			if ( ! is_int( $page ) ) {
				$term = null;
				break;
			}

			// Increment page.
			$page++;

			// Reduce limit.
			if ( $i_limit ) {

				// Reduce limit.
				$i_limit--;

				// Break if limit is 0.
				if ( 0 === $i_limit ) {
					$term = null;
					break;
				}
			}

		} while ( $term !== null );

		// Process finished.
		WP_CLI::log( '-------------------------------' );
		WP_CLI::log( 'Post tags fix duplicate process finished.' );
	}

	public function post_tags_fix_duplicate_page( $args ) {

		// Get page number.
		$number = is_array( $args ) ? $args[0] : 1;

		$a_post_tags = get_terms( array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => true,
			'offset'     => $number,
			'number'     => 1,
		) );

		// Return false if no post_tags found.
		return count( $a_post_tags ) > 0 ? $a_post_tags[0] : null;
	}
}

