<?php
/**
 * Production configuration.
 * !!! IMPORTANT: NEVER include wp-settings.php !!!
 */

// Ensure debug mode is disabled.
if ( ! defined( 'WP_DEBUG' ) ) {
	define('WP_DEBUG', false);
}
