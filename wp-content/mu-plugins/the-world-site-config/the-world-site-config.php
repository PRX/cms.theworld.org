<?php
/*
 Plugin Name: The World Site Config
 Plugin URI: https://pantheon.io/docs/environment-specific-config
 Description: Activates and deactivates plugins based on environment.
 Version: 1.0
 Author: Joe Tower
 *
 * @package the-world-site-config
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function the_world_site_config_multi_env_register( $environments ) {
    // Define an array containing the hosting environment names.
    // Or detect these with your own code logic if all are available in `$_ENV` or `$_SERVER` super-globals.
    // ...
    $environments = [
        'dev',
        'live'
    ];
    return $environments;
}
add_filter( 'the-world-site-config', 'the_world_site_config_multi_env_register' );

# Ensuring that this is on Pantheon or Lando (Pantheon recipe) environment
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {

  // Enable production specific config for the test and live environments.
  if ( in_array( PANTHEON_ENVIRONMENT, array('test', 'live' ) ) ){
    add_filter( 'the-world-site-config', 'live' );

    require_once( 'site-config/live-specific-configs.php' );
  }
  // Enable dev specific config for dev, multidevs, and lando environments.
  else {
    add_filter( 'the-world-site-config', 'dev' );

    require_once( 'site-config/dev-specific-configs.php' );
  }
}
