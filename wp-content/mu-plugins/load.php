<?php
/**
 * Load MU Plugins organized in subdirectories.
 *
 * @package mu_plugins
 */

// Opens the must-use plugins directory.
$wpmu_plugin_dir = opendir( WPMU_PLUGIN_DIR );

// Lists all the entries in this directory.
$entry = readdir( $wpmu_plugin_dir );
while ( false !== $entry ) {
	$plugin_path = WPMU_PLUGIN_DIR . '/' . $entry;

	// Is the current entry a subdirectory?
	if ( '.' !== $entry && '..' !== $entry && is_dir( $plugin_path ) ) {
		// Includes the corresponding plugin.
		require $plugin_path . '/' . $entry . '.php';
	}

	$entry = readdir( $wpmu_plugin_dir );
}

// Closes the directory.
closedir( $wpmu_plugin_dir );
