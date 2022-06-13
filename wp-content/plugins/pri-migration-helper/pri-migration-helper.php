<?php
/**
Plugin Name: PRI Migration Helper
Plugin URI: https://www.dinkuminteractive.com/
Description: Migration helper.
Version: 1.0.0
Author: Dinkum Interactive
Author URI: https://www.dinkuminteractive.com/
Text Domain: dinkuminteractive
 */

/**
 * Define constants
 *
 * @return void
 */
function pmh_define_constants() {

	define( 'PMH_DIR', plugin_dir_path( __FILE__ ) );
	define( 'PMH_TEST_DIR', plugin_dir_path( __FILE__ ) . '/test' );
	define( 'PMH_MIGRATION_DIR', plugin_dir_path( __FILE__ ) . '/migration' );
}
pmh_define_constants();

// Migration helper.
require_once plugin_dir_path( __FILE__ ) . 'migration/migration.php';

// JSON tester.
require_once plugin_dir_path( __FILE__ ) . 'test/test.php';
