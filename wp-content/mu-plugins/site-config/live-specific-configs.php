<?php

# List Development Plugins
   $plugins = array(
	'jetpack/jetpack.php',
	'pantheon-advanced-page-cache.php'
       );

# Live-specific configs
   if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'live' ) ) ) {

   # Disable Development Plugins
       require_once(ABSPATH . 'wp-admin/includes/plugin.php');
       foreach ($plugins as $plugin) {
           if(is_plugin_active($plugin)) {
	            deactivate_plugins($plugin);
           }
       }

   # Disable jetpack_development_mode
       add_filter( 'jetpack_development_mode', '__return_false' );
   }

   # Configs for All environments but Live
   else {

  	# Activate Development Plugins
       require_once(ABSPATH . 'wp-admin/includes/plugin.php');
       foreach ($plugins as $plugin) {
           if(is_plugin_inactive($plugin)) {
               activate_plugin($plugin);
           }
       }

   # Disable Live Plugins
       require_once(ABSPATH . 'wp-admin/includes/plugin.php');
       foreach ($plugins as $plugin) {
           if(is_plugin_active($plugin)) {
	            deactivate_plugins($plugin);
           }
       }
   # Enable development mode for jetpack
       add_filter( 'jetpack_development_mode', '__return_true' );
}
