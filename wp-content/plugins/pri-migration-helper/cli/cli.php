<?php
/**
 * WP CLI module.
 *
 * Commands: (base) tw-fix
 * 1. get_unprocessed_images
 * 2. get_images_count
 * 3. image_fix [all|new, limit]
 * 4. posts_content_media_fix [limit, comma_separated_post_ids(optional)]
 * 5. single_post_content_media_fix [post_id]
 * 6. post_tags_fix_duplicate [start_page_number post_per_page]
 */

// Add 'tw-media-fix-all' command to wp-cli.
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	require_once PMH_CLI_DIR . '/includes/class-pmh-worker.php';

	$o_pmh_worker = new PMH_Worker();

	WP_CLI::add_command( 'tw-fix', $o_pmh_worker );
}
