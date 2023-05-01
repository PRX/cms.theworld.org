<?php
/**
 * Plugin Name: TW Disable Yoast Indexables
 * Description: Disabling Indexables completely to speed up the import process.
 *
 */

 add_filter( 'Yoast\WP\SEO\should_index_indexables', '__return_false' );
