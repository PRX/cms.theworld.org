<?php
/*
 Plugin Name: The World Site Config
 Plugin URI: https://pantheon.io/docs/environment-specific-config
 Description: Activates and deactivates plugins based on environment.
 Version: 1.0
 Author: Joe Tower
 Text Domain: the_world_site_config
 *
 * @package the_world_site_config
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

# Ensuring that this is on Pantheon or Lando (Pantheon recipe) environment
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {

  // Enable production specific config for the test and live environments.
  if ( in_array( PANTHEON_ENVIRONMENT, array('test', 'live' ) ) ){
    require_once( 'site-config/live-specific-configs.php' );
  }
  // Enable dev specific config for dev, multidevs, and lando environments.
  else {
    require_once( 'site-config/dev-specific-configs.php' );
  }
}
