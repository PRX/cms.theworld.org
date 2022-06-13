<?php

// Migration Filters.
require_once PMH_MIGRATION_DIR . '/node_filters/node-story-post_type-post.php';


/**
 * Hook for doing other actions after inserting the post
 *
 * @param int    $new_post_id  The new post ID.
 * @param array  $node         Array of info from Drupal Node.
 * @param string $post_type    Target post type.
 * @param string $entity_type  Drupal post type?
 * @return void
 */
function pri_migration_fgd2wp_post_insert_post( $new_post_id, $node, $post_type, $entity_type ) {
	/*
	$new_post_id
		int(392)

	$node
		array(9) {
			["nid"]       => "3913"
			["title"]     => "Baitz's "Other Desert Cities"
			["type"]      => "story"
			["status"]    => "1"
			["created"]   => "1376504783"
			["language"]  => "und"
			["vid"]       => "3954"
			["sticky"]    => "0"
			["uid"]       => "5"
		}

	$post_type
		string(4) "post"

	$entity_type
		string(4) "node"
	 */

}
add_action( 'fgd2wp_post_insert_post', 'pri_migration_fgd2wp_post_insert_post', 10, 4 );

/**
 * Hook when about to get featured image.
 *
 * @param array $featured_image  Featured image info.
 * @param array $node            Array of info from Drupal Node.
 * @return array $featured_image  Featured image info.
 */
function pri_migration_fgd2wp_get_featured_image( $featured_image, $node ) {
	/*
	$featured_image
		array(7) {
			fid       => 229582
			alt       => NULL
			title     => NULL
			filename  => tw-globe-bg-3000.jpg
			uri       => public://images/2020/04/tw-globe-bg-3000.jpg
			filemime  => image/jpeg
			timestamp => 1588173755
		}
	 */

	return $featured_image;
}
add_filter( 'fgd2wp_get_featured_image', 'pri_migration_fgd2wp_get_featured_image', 10, 2 );

/**
 * Hook when about to work with each custom post fields.
 *
 * @param array $args  Featured image info.
 * @return array $args  Featured image info.
 */
function pri_migration_import_node_fields( $args ) {

	/*
		This args will later be used for list() function as:

		array(3) {

			// custom_field_name
			[0]=>
			string(5) "image"

			// custom_field
			[1]=>
			array(14) {
				["field_name"]=>
				string(11) "field_image"
				["node_type"]=>
				string(5) "story"
				["table_name"]=>
				string(22) "field_data_field_image"
				["module"]=>
				string(5) "image"
				["columns"]=>
				array(5) {
				["fid"]=>
				string(15) "field_image_fid"
				["alt"]=>
				string(15) "field_image_alt"
				["title"]=>
				string(17) "field_image_title"
				["width"]=>
				string(17) "field_image_width"
				["height"]=>
				string(18) "field_image_height"
				}
				["label"]=>
				string(5) "Image"
				["type"]=>
				string(13) "media_generic"
				["description"]=>
				string(167) "Upload an image to go with this story. This is the primary image for this story and will be displayed wherever this story is shown. Use a 16:9 image whenever possible."
				["default_value"]=>
				string(0) ""
				["required"]=>
				int(0)
				["cardinality"]=>
				string(1) "1"
				["repetitive"]=>
				bool(false)
				["entity_type"]=>
				string(4) "node"
				["order"]=>
				int(3)
			}

			// custom_field_values
			[2]=>
			array(1) {
				[0]=>
				array(6) {
				["fid"]=>
				string(6) "229582"
				["filename"]=>
				string(20) "tw-globe-bg-3000.jpg"
				["uri"]=>
				string(44) "public://images/2020/04/tw-globe-bg-3000.jpg"
				["timestamp"]=>
				string(10) "1588173755"
				["alt"]=>
				NULL
				["title"]=>
				NULL
				}
			}
		}
	 */

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	$filter_name = wp_sprintf( 'pmh_filter__node_%s__field_%s', $custom_field['node_type'], $custom_field_name );

	if ( function_exists( $filter_name ) ) {
		add_filter( $filter_name, $filter_name );
	}

	$args = apply_filters( $filter_name, $args );

	/* Debug
	echo "<pre>";
	var_dump( $filter_name );
	var_dump( $custom_field_name );
	var_dump( $custom_field );
	var_dump( $custom_field_values );
	var_dump( $args );
	echo "</pre>";
	 */

	return $args;
}
add_filter( 'fgd2wp_import_node_fields', 'pri_migration_import_node_fields' );


function pri_migration_test_codes() {

	/* Debug
	 */
	echo "<pre>";
	var_dump( get_post_meta( $_GET['post'], 'image', true ) );
	echo "</pre>";
	exit;
}
// add_action( 'init', 'pri_migration_test_codes' );
