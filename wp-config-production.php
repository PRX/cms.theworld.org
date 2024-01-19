<?php
/**
 * Production configuration.
 * !!! IMPORTANT: NEVER include wp-settings.php !!!
 *
 * @package tw
 */

// Ensure debug mode is disabled.
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

// Configure S3 Uploads.
define( 'S3_UPLOADS_BUCKET', 'media-pri-org/s3fs-public' );
define( 'S3_UPLOADS_REGION', 'us-east-1' );

define( 'WP_MEMORY_LIMIT', '512M' );
