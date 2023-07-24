<?php
/**
 * Define global plugins.
 *
 * @package the_world_site_config
 */

define(
	'GLOBAL_PLUGINS',
	array(
		'wp-native-php-sessions/pantheon-sessions.php',
		'advanced-custom-fields-pro/acf.php',
		'acf-to-rest-api/class-acf-to-rest-api.php',
		'disable-comments/disable-comments.php',
		'genesis-custom-blocks/genesis-custom-blocks.php',
		'PATCH-external-media-without-import/external-media-without-import.php',
		'pwa/pwa.php',
		'redirection/redirection.php',
		'user-role-editor/user-role-editor.php',
		'tw-contributors/tw-contributors.php',
		'tw-custom-tags/tw-custom-tags.php',
		'tw-disable-yoast-indexables/tw-disable-yoast-indexables.php',
		'tw-episodes/tw-episodes.php',
		'tw-media/tw-media.php',
		'tw-endpoint-helper/tw-endpoint-helper.php',
		'tw-programs/tw-programs.php',
		'tw-qa-block/tw-qa-block.php',
		'tw-resource-development-tags/tw-resource-development-tags.php',
		'tw-segments/tw-segments.php',
		'tw-story-format/tw-story-format.php',
		'tw-import-post-types/tw-import-post-types.php', # This is the plugin that creates the custom post types for the import. Can be removed after the import is complete.
		'wordpress-importer/wordpress-importer.php',
		'wordpress-seo/wp-seo.php',
    	'wp-all-export-pro/wp-all-export-pro.php',
		'wp-cfm-path/wp-cfm-path.php',
		'wp-cfm/wp-cfm.php',
		'wp-rest-menu/wp-rest-menus.php',
		'xml-sitemap-feed/xml-sitemap.php',
		'wp-redis/wp-redis.php',
	)
);
