<?php
/**
 * Define helper functions to manage plugins.
 */

/**
 * Require WP includes.
 */
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once(ABSPATH . 'wp-admin/includes/screen.php');

/**
 * Helper to activate a list of plugins. Only activates inactive plugins.
 *
 * @param $plugins string[] Paths to plugin files relative to plugins directory.
 */
function tw_activate_plugins($plugins) {
  foreach ( $plugins as $plugin ) {
    if ( is_plugin_inactive($plugin) ) {
      try {
        activate_plugin($plugin);
      } catch (\Throwable $th) {
        // Deactivate plugin. It may have not fully initialized and cause buggy behavior.
        // TODO: Figure out how to display only on plugins page or only to admin users.
        do_action('tw_plugin_activation_notice', $plugin);
        deactivate_plugins($plugin);
        // TODO: Figure out how to log this error in a way it can be reviewed after login by admin.
      }
    }
  }
}

/**
 * Helper to deactivate a list of plugins. Only deactivates active plugins.
 *
 * @param $plugins string[] Paths to plugin files relative to plugins directory.
 */
function tw_deactivate_plugins($plugins) {
  try {
    deactivate_plugins($plugins);
  } catch (\Throwable $th) {
    // TODO: Figure out how to log this error in a way it can be reviewed after login by admin.
  }
}

/**
 * Action to display a notice message when a plugin failed to activate.
 *
 * @param $plugin string Path to plugin file relative to plugins directory.
 */
function tw_plugin_activation_notice__error($plugin) {
  $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin, FALSE, FALSE);
  $class = 'notice notice-error';
  $message = __( "Encountered error activating {$plugin_data['Name']}. Plugin has been disabled to avoid issues from incomplete activation." );

  printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
add_action('tw_plugin_activation_notice', 'tw_plugin_activation_notice__error', 10, 3);

