<?php
/**
 * Plugin Name: TW Call To Actions (CTA's)
 * Description: Creates the custom post types and taxonomies for CTA's.
 *
 * @package tw_call_to_actions
 */

/**
 * Import Taxonomy files
 */
require_once 'taxonomies/taxonomy--cta-region-type.php';

/**
 * Import Post Type files
 */
require_once 'post-types/post-type--cta.php';
require_once 'post-types/post-type--cta-region.php';
