<?php

/*
Plugin Name: S3 Uploads (PATCHED)
Description: Store uploads in S3
Author: Human Made Limited
Version: 3.0.3
Author URI: https://hmn.md
*/

// wp-content/plugins/s3-uploads/s3-uploads.php.
if ( ! class_exists( '\\Aws\\S3\\S3Client' ) ) {
	// Require AWS Autoloader file.
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/inc/namespace.php';

add_action( 'plugins_loaded', 'S3_Uploads\\init', 0 );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 's3-uploads', 'S3_Uploads\\WP_CLI_Command' );
}
