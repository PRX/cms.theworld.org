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
		'PATCH-s3-uploads/s3-uploads.php',
		'microsoft-start/index.php',
		'pwa/pwa.php',
		'redirection/redirection.php',
		'user-role-editor/user-role-editor.php',
		'taxopress-pro/taxopress-pro.php',
		'tw-call-to-actions/tw-call-to-actions.php',
		'tw-contributors/tw-contributors.php',
		'tw-custom-tags/tw-custom-tags.php',
		'tw-disable-yoast-indexables/tw-disable-yoast-indexables.php',
		'tw-endpoint-helper/tw-endpoint-helper.php',
		'tw-episode-importer/tw-episode-importer.php',
		'tw-episodes/tw-episodes.php',
		'tw-graphql/tw-graphql.php',
		'tw-image-credit/tw-image-credit.php',
		'tw-import-post-types/tw-import-post-types.php', // This is the plugin that creates the custom post types for the import. Can be removed after the import is complete.
		'tw-media/tw-media.php',
		'tw-menus/tw-menus.php',
		'tw-newsletters/tw-newsletters.php',
		'tw-programs/tw-programs.php',
		'tw-qa-block/tw-qa-block.php',
		'tw-datawrapper-block/tw-datawrapper-block.php',
		'tw-resource-development-tags/tw-resource-development-tags.php',
		'tw-scroll-gallery/tw-scroll-gallery.php',
		'tw-segments/tw-segments.php',
		'tw-story-format/tw-story-format.php',
		'wordpress-importer/wordpress-importer.php',
		'wordpress-seo/wp-seo.php',
		'wp-cfm-path/wp-cfm-path.php',
		'wp-cfm/wp-cfm.php',
		'wp-rest-menu/wp-rest-menus.php',
		'xml-sitemap-feed/xml-sitemap.php',
		'wp-redis/wp-redis.php',
		'svg-block/svg-block.php',
		'wp-graphql/wp-graphql.php',
		'wp-graphql-acf/wp-graphql-acf.php',
		'wp-graphql-jwt-authentication/wp-graphql-jwt-authentication.php',
		'add-wpgraphql-seo/wp-graphql-yoast-seo.php',
		'custom-post-type-permalinks/custom-post-type-permalinks.php',
		'faustwp/faustwp.php',
	)
);
