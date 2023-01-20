<?php
/**
 * Plugin Name: TW RSS Extend
 * Description: Extends the fields available to the RSS Feed.
 *
 * @package tw_rss_extend
 */

/**
 * Add Featured Image to RSS feed
 *
 * @return void
 */
function tw_rss_extend_featuredimage($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '<p>' . get_the_post_thumbnail($post->ID) .
		'</p>' . get_the_content();
	}
	return $content;
	}
add_filter('the_excerpt_rss', 'tw_rss_extend_featuredimage');
add_filter('the_content_feed', 'tw_rss_extend_featuredimage');
