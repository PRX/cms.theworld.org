<?php
/**
 * Development configuration.
 * !!! IMPORTANT: NEVER include wp-settings.php !!!
 *
 * @package tw
 */

// Ensure debug mode is enabled.
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
}

// Configure S3 Uploads.
define( 'S3_UPLOADS_BUCKET', 'media-pri-org/s3fs-public' );
define( 'S3_UPLOADS_REGION', 'us-east-1' );

define( 'GRAPHQL_DEBUG', true );

define( 'COOKIE_DOMAIN', 'the-world-wp.lndo.site' );
