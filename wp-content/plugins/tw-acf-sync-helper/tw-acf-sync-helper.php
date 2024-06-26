<?php
/**
 * Plugin Name: TW ACF Sync Helper
 * Description: Syncs ACF fields semi automatically. Run "wp tw acf-sync-field-groups" to sync all ACF field groups.
 */

/**
 * example usage wp-cli: wp tw acf-sync-field-groups
 * example usage terminus: terminus wp the-world.{environemt} -- tw acf-sync-field-groups
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds a wp cli command to sync all ACF field groups. Use it like this:
 *
 * "wp rhau acf-sync-field-groups"
 *
 */
class ACFSyncFieldGroups {

	/**
	 * Init
	 */
	public static function init() {
		add_action( 'acf/init', array( __CLASS__, 'add_wp_cli_command' ) );
	}

	/**
	 * Conditional to check if inside WP_CLI
	 *
	 * @return boolean
	 */
	private static function is_wp_cli(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Add the WP_CLI command
	 *
	 * @return void
	 */
	public static function add_wp_cli_command(): void {
		if ( self::is_wp_cli() ) {
			\WP_CLI::add_command( 'tw acf-sync-field-groups', array( __CLASS__, 'wp_cli_acf_sync_field_groups' ) );
		}
	}

	/**
	 * Syncs all ACF field groups
	 *
	 * ## OPTIONS
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @return void
	 */
	public static function wp_cli_acf_sync_field_groups( array $args = array(), array $assoc_args = array() ): void {
		// Only allow this if invoked from WP CLI
		if ( ! self::is_wp_cli() ) return;

		acf_include( 'includes/admin/admin-internal-post-type-list.php' );

		if ( ! class_exists( 'ACF_Admin_Internal_Post_Type_List' ) ) {
			\WP_CLI::error( 'Some required ACF classes could not be found. Please update ACF to the latest version.' );
		}

		acf_include( 'includes/admin/post-types/admin-field-groups.php' );

		/**
		 * @var \ACF_Admin_Field_Groups $field_groups_class
		 */
		$field_groups_class = acf_get_instance( 'ACF_Admin_Field_Groups' );

		$field_groups_class->setup_sync();

		// Disable "Local JSON" controller to prevent the .json file from being modified during import.
		acf_update_setting( 'json', false );

		// Sync field groups and generate array of new IDs.
		$files = acf_get_local_json_files();

		foreach ( $field_groups_class->sync as $key => $field_group ) {

			$local_field_group = json_decode( file_get_contents( $files[$key] ), true );

			$local_field_group['ID'] = $field_group['ID'];

			$imported_field_group = acf_import_field_group( $local_field_group );

			\WP_CLI::success( "Synced ACF field group: {$imported_field_group["title"]}" );
		}
	}
}

/**
 * Initialize the plugin
 */
ACFSyncFieldGroups::init();
