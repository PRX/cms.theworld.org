<?php
/**
 * Migration helper
 *
 * @package WordPress
 */

// DEFINITIONS.
pmh_define_constant( 'PMH_MEDIA_BASE_URL', 'https://media.pri.org' );
pmh_define_constant( 'PMH_MEDIA_PUBLIC_URL', 's3fs-public/' );
pmh_define_constant( 'PMH_MEDIA_PRIVATE_URL', 's3fs-private/' );

// Migration Filters.
require_once PMH_MIGRATION_DIR . '/node_filters/node-global-filters.php';
require_once PMH_MIGRATION_DIR . '/node_filters/node-episode-post_type-post.php';
require_once PMH_MIGRATION_DIR . '/node_filters/node-page-post_type-post.php';
require_once PMH_MIGRATION_DIR . '/node_filters/node-person-post_type-post.php';
require_once PMH_MIGRATION_DIR . '/node_filters/node-program-post_type-post.php';
require_once PMH_MIGRATION_DIR . '/node_filters/node-story-post_type-post.php';
require_once PMH_MIGRATION_DIR . '/media_filters/media-global-filters.php';
require_once PMH_MIGRATION_DIR . '/media_filters/media-segment-filters.php';
require_once PMH_MIGRATION_DIR . '/taxonomy_filters/taxonomy-category.php';




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

	Real image URL: https://media.pri.org/s3fs-public/images/2020/04/tw-globe-bg-3000.jpg
	 */

	return $featured_image;
}
add_filter( 'fgd2wp_get_featured_image', 'pri_migration_fgd2wp_get_featured_image', 10, 2 );

/**
 * Hook when getting field value.
 *
 * @param array  $custom_field_values  Field Values.
 * @param int    $new_post_id Post ID inserted.
 * @param string $custom_field_name Custom field name that will be used in ACF.
 * @param array  $custom_field FIeld info from Drupal.
 * @param date   $date Created Date.
 * @return array $custom_field_values Updated Field Values.
 */
function pri_fgd2wp_pre_import_custom_field_values( $custom_field_values, $new_post_id, $custom_field_name, $custom_field, $date ) {
	$filter_name = wp_sprintf( 'pmh_filter__node_%s__fields', $custom_field['node_type'], $custom_field_name );

	if ( function_exists( $filter_name ) ) {
		add_filter( $filter_name, $filter_name );
	}

	$custom_field_values = apply_filters( $filter_name, $custom_field_values, $custom_field_name );

	/*
		This args will later be used for list() function as:
			# custom_field_name
			[0]=>
			string(5) "image"

			# custom_field
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

			# custom_field_values
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
	 */

	return $custom_field_values;
}
add_filter( 'fgd2wp_pre_import_custom_field_values', 'pri_fgd2wp_pre_import_custom_field_values', 10, 5 );

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

	// Filter for specific field name.
	$filter_field = $custom_field_name;
	$filter_field = str_replace( '-', '_', $filter_field );
	$filter_name  = wp_sprintf( 'pmh_filter__node_%s__field_%s', $custom_field['node_type'], $filter_field );

	if ( function_exists( $filter_name ) ) {
		add_filter( $filter_name, $filter_name );
	}

	$args = apply_filters( $filter_name, $args );

	// Filter for value redirection.
	$filter_name = wp_sprintf( 'pmh_filter__node_%s__field', $custom_field['node_type'] );

	if ( function_exists( $filter_name ) ) {
		add_filter( $filter_name, $filter_name );
	}

	$args = apply_filters( $filter_name, $args );

	/*
	 Debug
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

/**
 * Add media using external media without import.
 *
 * @param int    $post_id         The post ID.
 * @param string $meta_key        Meta key to insert.
 * @param array  $attachment_urls Media urls.
 * @return mixed
 */
function pmh_add_post_media( $post_id, $meta_key, $attachment_urls ) {

	$singular_media_names = array( 'story_featured_image' );

	if ( ! function_exists( 'emwi\add_external_media_without_import' ) ) {
		return false;
	}

	if ( $attachment_urls && is_array( $attachment_urls ) ) {

		$attachment_urls = implode( "\n", $attachment_urls );
	}

	$_POST['urls'] = $attachment_urls;

	$media_informations = emwi\add_external_media_without_import();

	if ( isset( $media_informations['attachment_ids'] ) && $media_informations['attachment_ids'] ) {

		if ( in_array( $meta_key, $singular_media_names, true ) && isset( $media_informations['attachment_ids'][0] ) ) {

			$meta_value = $media_informations['attachment_ids'][0];

			set_post_thumbnail( $post_id, $meta_value );

		} else {

			$meta_value = $media_informations['attachment_ids'];

			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	return $media_informations;
}

/**
 * Gets the address that the URL ultimately leads to.
 * Returns $url itself if it isn't a redirect.
 *
 * @param string $url
 * @return string
 */
function pmh_get_redirect_url( $url ) {

	/*
	string(44) "public://images/2022/06/tw-globe-bg-3000.jpg"
	string(38) "https://migration-pri9.pantheonsite.io"
	string(20) "sites/default/files/"
	string(28) "sites/default/files/private/"

	public://images/2022/06/tw-globe-bg-3000.jpg
	https://migration-pri9.pantheonsite.io/sites/default/files/images/2022/06/tw-globe-bg-3000.jpg
	https://media-pri-dev.s3.us-east-1.amazonaws.com/s3fs-public/styles/story_main/public/images/2022/06/tw-globe-bg-3000.jpg
	public://images/2022/06/tw-globe-bg-3000.jpg

	https://media-pri-dev.s3.us-east-1.amazonaws.com/s3fs-public/styles/thumbnail/public/migration/PriMigrationsDamanticWordpressAttachmentsImagesMigration/www.theworld.org/wp-content/uploads/egypt-clashes400.jpg?itok=6FrXWg0V
	https://media-pri-dev.s3.us-east-1.amazonaws.com/s3fs-public/styles/story_main/public/images/2022/06/egypt-clashes400b.jpg?itok=Eofw4529
	 */

	/*
	 Debug
	echo "<pre>";
	var_dump( 'pmh_get_redirect_url' );
	var_dump( $url );
	echo "</pre>";
	 */

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_HEADER, true );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

	$head = curl_exec( $ch );
	$url  = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );

	return $url;
}

/**
 * Get post ID by meta.
 *
 * @param string $meta_key Meta key.
 * @param string $meta_value Meta value.
 * @return int Attachment ID or false.
 */
function pmh_get_post_id_by_meta( $meta_key, $meta_value ) {
	global $wpdb;

	$supported_keys = array(
		'_fgd2wp_old_node_id' => 'node',
		'fgd2wp_old_node_id'  => 'node',
		'nid'                 => 'node',
		'_fgd2wp_old_file'    => 'file',
		'fgd2wp_old_file'     => 'file',
		'fid'                 => 'file',
	);

	if ( array_key_exists( $meta_key, $supported_keys ) ) {

		$support_table  = $wpdb->prefix . 'pmh_nodes';
		$query_type     = $supported_keys[ $meta_key ];

		$sql     = "SELECT post_id FROM {$support_table} WHERE type = '{$query_type}' AND node_id = '$meta_value' LIMIT 1";
		$post_id = $wpdb->get_var( $sql );

		if ( $post_id ) {
			return $post_id;
		}
	}

	$sql     = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key' AND meta_value = '$meta_value' LIMIT 1";
	$post_id = $wpdb->get_var( $sql );

	return $post_id;
}

/**
 * Get post by nid.
 *
 * @param int $nid Post ID or false.
 * @return int Post ID or false.
 */
function pmh_get_post_by_nid( $nid ) {

	return pmh_get_post_id_by_meta( 'nid', $nid );
}

/**
 * Get a media ID
 *
 * @param int $fid Attachment ID or false.
 * @return int Attachment ID or false.
 */
function pmh_get_wp_media_id_by_fid( $fid ) {

	return pmh_get_post_id_by_meta( 'fid', $fid );
}

/**
 * Should run after all media is imported. (work in progress)
 *
 * @return void
function post_import_all_media() {

	$related_files = get_post_meta( $post_id, 'related_files', true );

	// Update related files.
	if ( $related_files ) {

		$_related_files = array();

		foreach ( $related_files as $related_file ) {

			$_related_file_id = pmh_get_wp_media_id_by_fid( $related_file );

			if ( $_related_file_id ) {

				$_related_files[] = $_related_file_id;
			}
		}

		if ( $_related_files ) {

			update_post_meta( $post_id, 'related_files', $_related_files );
		}
	}
}
 */

function wpse70000_add_excerpt() {
	/*
	 Debug
	 */
	echo '<pre>';
	var_dump( get_post_meta( $_GET['post'] ) );
	echo '</pre>';
	exit;
}
// add_action( 'init', 'wpse70000_add_excerpt', 100 );

function pri_migration_test_codes() {
}
// add_action( 'init', 'pri_migration_test_codes' );

/**
 * Hook into fgd2wp_post_import and add media fix process.
 */
function pri_post_import_media_fix() {

	// Initialize media fix cli object.
	$o_media_fix_cli = new PMH_Worker();

	// Media fix arguments.
	$a_media_fix_arguments = array( 'new', 50 );

	// Run media fix.
	$o_media_fix_cli->image_fix( $a_media_fix_arguments );
}
// add_action( 'fgd2wp_post_import', 'pri_post_import_media_fix', 100 );

/**
 * Modify extra conditions for getting nodes.
 *
 * @param string $extra_conditions Extra conditions.
 * @param string $content_type Post type.
 * @param string $entity_type Entity type.
 *
 * @return string
 */
function pri_migration_tw_get_nodes_add_extra_conditions( $extra_conditions, $content_type, $entity_type ) {

	// Content types.
	$selective_fix_content_types = get_option( 'tw_selective_fix_content_types', array() );

	// Add extra conditions if content type matches.
	if ( in_array( $content_type, $selective_fix_content_types, true ) ) {

		// Get node ids.
		$node_ids = get_option( 'tw_get_nodes_' . $content_type . '_target_ids' );

		// If node ids exist.
		if ( $node_ids ) {

			// Add extra conditions.
			$extra_conditions = ' AND ';
			$extra_conditions .= "n.nid IN ($node_ids)";
		}
	}

	return $extra_conditions;
}
add_filter( 'tw_get_nodes_add_extra_conditions', 'pri_migration_tw_get_nodes_add_extra_conditions', 10, 3 );

/**
 * Add how many nodes to process when extra conditions are added.
 */
function pri_migration_tw_how_many_nodes_to_import( $how_many_nodes_to_import, $content_type ) {

	// Content types.
	$selective_fix_content_types = get_option( 'tw_selective_fix_content_types', array() );

	// Add extra conditions if content type matches.
	if ( in_array( $content_type, $selective_fix_content_types, true ) ) {

		// Get node ids.
		$node_ids = get_option( 'tw_get_nodes_' . $content_type . '_target_ids' );

		// If node ids exist.
		if ( $node_ids ) {

			// Add extra conditions.
			$how_many_nodes_to_import = count( explode( ',', $node_ids ) );
		}
	}

	return $how_many_nodes_to_import;
}
add_filter( 'tw_how_many_nodes_to_import', 'pri_migration_tw_how_many_nodes_to_import', 10, 2 );

/**
 * Allow and disallow HTML tags.
 *
 * @param bool $allow Allow HTML tags.
 *
 * @return void
 */
function pri_allow_html_term_description( bool $allow ) {

	if ( $allow ) {
		remove_filter( 'term_description', 'wp_kses_data' );
		remove_filter( 'pre_term_description', 'wp_filter_kses' );
		add_filter( 'term_description', 'wp_kses_post' );
		add_filter( 'pre_term_description', 'wp_filter_post_kses' );
	} else {
		add_filter( 'term_description', 'wp_kses_data' );
		add_filter( 'pre_term_description', 'wp_filter_kses' );
		remove_filter( 'term_description', 'wp_kses_post' );
		remove_filter( 'pre_term_description', 'wp_filter_post_kses' );
	}
}
