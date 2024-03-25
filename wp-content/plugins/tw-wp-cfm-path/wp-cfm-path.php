<?php
/*
Plugin Name: TW WP-CFM config path alter
Description: Alters the wpcfm config path for local / dev / live environments
Version: 0.1
*/
// Tell wp-cfm where our config files live

function set_multi_env() {

	// If we are in a Pantheon environment, set the 3 instances slugs out of the box.
	$environments = array( 'dev', 'live' );

	return $environments;
}
add_filter( 'wpcfm_multi_env', 'set_multi_env' );

/**
 * @param string $env - Default is an empty string ''.
 * @return string
 */
function set_current_env( $env ) {
	// Detect with your own code logic the current environment the WordPress site is running.
	// Generally this will be defined in a constant inside `$_ENV` or `$_SERVER` super-globals.
	// ...

	if ( defined( 'PANTHEON_ENVIRONMENT' ) && in_array( PANTHEON_ENVIRONMENT, array( 'test', 'live' ) ) ) {
		$env = 'live';
	} else {
		$env = 'dev';
	}

	return $env;
}
add_filter( 'wpcfm_current_env', 'set_current_env' );

/**
 * @param string $config_dir - Default is "<root>/wp-content/config"
 * @return string
 */
function change_config_dir( $config_dir ) {
	// Change default path to $config_dir if lando.
	if ( defined( 'PANTHEON_ENVIRONMENT' ) ) {
		// Set the Pantheon environment to test or live
		if ( in_array( PANTHEON_ENVIRONMENT, array( 'lando' ) ) ) {
			$config_dir = $_SERVER['DOCUMENT_ROOT'] . '/private/config/' . WPCFM_CURRENT_ENV;
		}
	}

	return $config_dir;
}
add_filter( 'wpcfm_config_dir', 'change_config_dir' );

/**
 * @param string $config_url - Default is "<domain>/wp-content/config"
 * @return string
 */
function change_config_url( $config_url ) {
	// Change default URL to $config_url if lando
	if ( defined( 'PANTHEON_ENVIRONMENT' ) ) {
		// Set the Pantheon environment to test or live
		if ( in_array( PANTHEON_ENVIRONMENT, array( 'lando' ) ) ) {
			$config_url = $_SERVER['DOCUMENT_ROOT'] . '/private/config/' . WPCFM_CURRENT_ENV;
		}
	}
	return $config_url;
}
add_filter( 'wpcfm_config_url', 'change_config_url' );
