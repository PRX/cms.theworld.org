<?php
/*
 Plugin Name: Pantheon WP CFM Site Config
 Plugin URI: https://pantheon.io/docs/mu-plugin#wp-cfm-compatibility
 Description: Enables WP CFM on pantheon environments.
 Version: 0.1.1
 Author: Pantheon
 Author URI: https://pantheon.io/docs/contributors
*/
add_filter( 'wpcfm_multi_env', function( $pantheon_envs ) {
  if ( !( in_array( PANTHEON_ENVIRONMENT, $pantheon_envs ) ) ) {
    $pantheon_envs[] = PANTHEON_ENVIRONMENT;
  }
return $pantheon_envs;
} );

add_filter( 'wpcfm_current_env', function( $pantheon_env ) {
    return PANTHEON_ENVIRONMENT;
} );
