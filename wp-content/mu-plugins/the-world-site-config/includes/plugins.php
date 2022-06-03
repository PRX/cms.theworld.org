<?php
/**
 * Define helper functions to manage plugins.
 *
 * @package the_world_site_config
 */

/**
 * Require WP includes.
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/screen.php';

/**
 * Helper to activate a list of plugins. Only activates inactive plugins.
 *
 * @param string[] $plugins Paths to plugin files relative to plugins directory.
 */
function tw_activate_plugins( $plugins ) {
	foreach ( $plugins as $plugin ) {
		if ( is_plugin_inactive( $plugin ) ) {
			activate_plugin( $plugin );
		}
	}
}

/**
 * Helper to deactivate a list of plugins. Only deactivates active plugins.
 *
 * @param string[] $plugins Paths to plugin files relative to plugins directory.
 */
function tw_deactivate_plugins( $plugins ) {
	deactivate_plugins( $plugins );
}
