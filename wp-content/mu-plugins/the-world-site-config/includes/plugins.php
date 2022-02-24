<?php
/**
 * Define helper functions to manage plugins.
 */

/**
 * Require WP plugins helpers.
 */
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

/**
 * Helper to activate a list of plugins. Only activates inactive plugins.
 */
function tw_activate_plugins($plugins) {
  foreach ($plugins as $plugin) {
    if(is_plugin_inactive($plugin)) {
        activate_plugin($plugin);
    }
  }
}

/**
 * Helper to deactivate a list of plugins. Only deactivates active plugins.
 */
function tw_deactivate_plugins($plugins) {
  foreach ($plugins as $plugin) {
    if(is_plugin_active($plugin)) {
       deactivate_plugins($plugin);
    }
  }
}
