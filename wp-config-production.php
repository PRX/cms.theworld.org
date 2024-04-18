<?php
/**
 * Production configuration.
 * !!! IMPORTANT: NEVER include wp-settings.php !!!
 *
 * @package tw
 */

// Ensure WP Environment Type constant is set.
if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	define( 'WP_ENVIRONMENT_TYPE', 'production' );
}

// Ensure debug mode is disabled.
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

// Configure S3 Uploads.
define( 'S3_UPLOADS_BUCKET', 'media-pri-org/s3fs-public' );
define( 'S3_UPLOADS_REGION', 'us-east-1' );
// Define the base bucket URL (without trailing slash).
define( 'S3_UPLOADS_BUCKET_URL', 'https://media.pri.org/s3fs-public' );

define( 'WP_MEMORY_LIMIT', '512M' );

define( 'COOKIE_DOMAIN', '.theworld.org' );
