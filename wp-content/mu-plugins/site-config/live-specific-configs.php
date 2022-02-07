<?php

# List Dev Plugins
   $dev_plugins = array(
	'jetpack/jetpack.php',
	'pantheon-advanced-page-cache.php'
       );

   $live_plugins = array(
	'jetpack/jetpack.php',
	'pantheon-advanced-page-cache.php'
       );

# Live-specific configs
   if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'live' ) ) ) {

   # Disable Development Plugins
       require_once(ABSPATH . 'wp-admin/includes/plugin.php');
       foreach ($dev_plugins as $dev_plugin) {
           if(is_plugin_active($dev_plugin)) {
	            deactivate_plugins($dev_plugin);
           }
       }
       # Activate Live Plugins
       foreach ($live_plugins as $live_plugin) {
           if(is_plugin_inactive($live_plugin)) {
               activate_plugin($live_plugin);
           }
       }

   # Disable jetpack_development_mode
       add_filter( 'jetpack_development_mode', '__return_false' );
   }

   # Configs for All environments but Live
   else {
  	   # Disable Live Plugins
       require_once(ABSPATH . 'wp-admin/includes/plugin.php');
       foreach ($live_plugins as $live_plugin) {
           if(is_plugin_active($live_plugin)) {
              deactivate_plugins($live_plugin);
           }
       }

       # Activate Development Plugins
       foreach ($dev_plugins as $dev_plugin) {
           if(is_plugin_inactive($dev_plugin)) {
               activate_plugin($dev_plugin);
           }
       }
   # Enable development mode for jetpack
       add_filter( 'jetpack_development_mode', '__return_true' );
}
