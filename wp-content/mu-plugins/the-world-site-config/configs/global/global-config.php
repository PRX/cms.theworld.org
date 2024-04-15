<?php
/**
 * Global configuration for all environments.
 *
 * @package the_world_site_config
 */


// Ensure Yoast optimization can be run in any environment.
add_filter( 'Yoast\WP\SEO\should_index_indexables', '__return_true' );
