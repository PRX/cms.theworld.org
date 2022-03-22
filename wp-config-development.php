<?php
/**
 * Development configuration.
 * !!! IMPORTANT: NEVER include wp-settings.php !!!
 */

// Ensure debug mode is enabled.
if ( ! defined( 'WP_DEBUG' ) ) {
	define('WP_DEBUG', true);
}

// Configure S3 Uploads.
define( 'S3_UPLOADS_BUCKET', 'media-pri-dev/s3fs-public' );
define( 'S3_UPLOADS_REGION', 'us-east-1' );
