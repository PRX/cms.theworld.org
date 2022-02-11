<?php
/*
 Plugin Name: Pantheon Site Config
 Plugin URI: https://pantheon.io/docs/environment-specific-config
 Description: Activates and deactivates plugins based on environment.
 Version: 0.1.1
 Author: Pantheon
 Author URI: https://pantheon.io/docs/contributors
*/
# Ensuring that this is on Pantheon
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
  # Symlink to the env-configs file
  require_once( 'site-config/live-specific-configs.php' );
}
else {

}
