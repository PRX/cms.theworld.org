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
 * Define constant
 *
 * @param string $define_name Name.
 * @param string $define_val Value.
 * @return void
 */
function pmh_define_constant( $define_name, $define_val ) {
	if ( ! defined( $define_name ) ) {
		define( $define_name, $define_val );
	}
}

/**
 * Define constants
 *
 * @return void
 */
function pmh_define_constants() {

	pmh_define_constant( 'PMH_DIR', plugin_dir_path( __FILE__ ) );
	pmh_define_constant( 'PMH_ADMIN_DIR', plugin_dir_path( __FILE__ ) . '/admin' );
	pmh_define_constant( 'PMH_MIGRATION_DIR', plugin_dir_path( __FILE__ ) . '/migration' );
	pmh_define_constant( 'PMH_TEST_DIR', plugin_dir_path( __FILE__ ) . '/test' );
	pmh_define_constant( 'PMH_CLI_DIR', plugin_dir_path( __FILE__ ) . '/cli' );
}

pmh_define_constants();

// Admin helper.
require_once plugin_dir_path( __FILE__ ) . 'admin/admin.php';

// Migration helper.
require_once plugin_dir_path( __FILE__ ) . 'migration/migration.php';

// JSON tester.
require_once plugin_dir_path( __FILE__ ) . 'test/test.php';

// CLI Feature.
require_once plugin_dir_path( __FILE__ ) . 'cli/cli.php';
