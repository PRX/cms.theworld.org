<?php
/**
 * Define and activate global plugins.
 *
 * @package the_world_site_config
 */

define(
	'DEVELOPMENT_PLUGINS',
	array(
		'wp-filesystem-ssh2/wp-filesystem-ssh2.php',
		'pri-migration-helper/pri-migration-helper.php',
		'fg-drupal-to-wp-premium/fg-drupal-to-wp-premium.php',
		'fg-drupal-to-wp-premium-entityreference-module/fg-drupal-to-wp-entityreference.php',
		'fg-drupal-to-wp-premium-fieldcollection-module/fg-drupal-to-wp-fieldcollection.php',
		'fg-drupal-to-wp-premium-mediaprovider-module/fg-drupal-to-wp-mediaprovider.php',
		'fg-drupal-to-wp-premium-metatag-module/fg-drupal-to-wp-metatag.php',
	),
);
