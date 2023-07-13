<?php
/**
 * WP CLI module.
 *
 * Commands: (base) tw-fix
 * 1. get_unprocessed_images
 * 2. get_images_count
 * 3. image_fix [all|new] [limit]
 *
 */

// Add 'tw-media-fix-all' command to wp-cli.
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	require_once PMH_CLI_DIR . '/includes/class-pmh-worker.php';

	$o_pmh_worker = new PMH_Worker();

	WP_CLI::add_command( 'tw-fix', $o_pmh_worker );
}
