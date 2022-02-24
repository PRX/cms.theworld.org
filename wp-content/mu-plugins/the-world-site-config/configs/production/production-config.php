<?php
/**
 * Configuration for production environments.
 */

# Disable jetpack_development_mode.
add_filter( 'jetpack_development_mode', '__return_false' );

/**
 * STOP production configuration here.
 */

/**
 * Load staging specific config now so it can override
 * production settings.
 */
if (WP_ENVIRONMENT_TYPE === 'staging') {
  require_once(dirname(__FILE__) . '/staging-config.php');
}
