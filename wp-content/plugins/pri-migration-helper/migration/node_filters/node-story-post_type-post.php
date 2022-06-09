<?php
/**
 * Migration filters
 *
 * @package WordPress
 */

/**
 * Exclude the custom fields we don't want to import from the list of custom fields to be created in ACF
 *
 * @param array  $custom_fields Custom fields.
 * @param string $node_type Drupal node type.
 * @return array Custom fields
 */
function pri_story_fgd2wp_get_custom_fields( $custom_fields, $node_type ) {
	if ( 'story' === $node_type ) {
		foreach ( $custom_fields as $field_name => $field ) {
			if ( pri_story_field_to_exclude( $field_name ) ) {
				unset( $custom_fields[ $field_name ] );
			}
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_custom_fields', 'pri_story_fgd2wp_get_custom_fields', 10, 2 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_story_field( $default, $custom_field_name ) {
	if ( pri_story_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return nil to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_story_field', 'pri_fgd2wp_import_story_field', 10, 2 );

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_story_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'multimedia_code_long',
		'files_additional',
		'terms_verticals',
		'ref_related_stories',
		'hashtag_or_header',
		'cross_links',
		'facebook_post',
		'program_facebook_post',
		'podcast_title_override',
		'podcast_teaser_override',
		'audio_explicit',
		'appear_in_homepage_stream',
		'display_main_image_on_stor',
		'hidden_elements',
		'terms_story_tone',
		'links',
		'multimedia_code',
		'no_image',
		'terms_migration_flags',
		'int_migrate_source_id',
		'terms_legacy_tags',
		'terms_sections',
		'terms_collections',
		'series',
		'exclusion',
		'fc_story_creators',
		'credits_json',

		// add to test only, comment later.
		// 'image',
		// 'images',
		// 'date_broadcast',
		// 'date_published',
		// 'teaser',
		// 'file_audio',
		// 'body',
		// 'updated',
		// 'search_optimized_title',
		// 'display_template',
		// 'pri_tweet',
		// 'program_tweet',
		// 'credits_json',
		// 'ref_program',
		// 'globalpost_byline',
		// 'byline',

	);
	return in_array( $field_name, $do_not_import_fields, true );
}

/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_story__field( $args ) {

	/*
	 Debug
	echo "<pre>";
	var_dump( 'pmh_filter__node_story__field' );
	var_dump( $args );
	echo "</pre>";
	 */

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {

		case 'images':
			$custom_field_name = 'additional_images';

			$custom_field['repetitive'] = false;
			$custom_field['is_array']   = true;
			break;

		case 'file_audio':
			$custom_field_name = 'audio';
			break;

		case 'video':
			// DINKUM: remove this if we are going to import multiple video files.
			$custom_field['repetitive'] = false;
			break;

		case 'pri_tweet':
			$custom_field_name = 'suggested_social_post';
			break;

		case 'program_tweet':
			$custom_field_name = 'suggested_tweet';
			break;

		case 'search_optimized_title':
			$custom_field_name = 'seo_title';
			break;

		case 'date_broadcast':
			$custom_field_name = 'broadcast_date';
			break;

		case 'updated':
			$custom_field_name = 'updated_date';
			break;

		case 'display_template':
			$custom_field_name = 'format';
			break;

		case 'teaser':
			$custom_field_name = 'excerpt';
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}


/**
 * Hook for doing other actions after inserting the post
 *
 * @param int                             $new_post_id  The new post ID.
 * @param string                          $custom_field_name  Field Name.
 * @param array                           $custom_field         Array of info from Drupal Field.
 * @param array                           $custom_field_values    Custom Field Values.
 * @param string                          $date  Date value.
 * @param \FG_Drupal_to_WordPress_CPT_ACF $plugin_cpt  ACF Plugin CPT value.
 * @return void
 */
function pri_pmh_story_convert_custom_post_field( $meta_values, $custom_field, $custom_field_values, $new_post_id, $date ) {
	if ( empty( $meta_values ) || ( isset( $custom_field['node_type'] ) && 'story' !== $custom_field['node_type'] ) ) {
		return $meta_values;
	}
	switch ( $custom_field['field_name'] ) {
		case 'field_ref_program':
			pri_pmh_story_set_program_taxonomy( $new_post_id, $meta_values );
			break;
		case 'field_globalpost_byline':
			pri_pmh_story_set_contributor_taxonomy_name( $new_post_id, $meta_values );
			break;
		case 'field_fc_story_creators':
		case 'field_byline':
			pri_pmh_story_set_contributor_taxonomy( $new_post_id, $meta_values );
			break;
		// case 'field_video':
		// $meta_values = pri_convert_video( $custom_field_values );
		// break;

		default:
			return $meta_values;
	}
	return $meta_values;
}
add_filter( 'fgd2wp_convert_custom_field_to_meta_values', 'pri_pmh_story_convert_custom_post_field', 10, 5 );

// function pri_pmh_map_acf_field_type( $acf_type, $field_type, $field ) {
// if ( 'Video' === $field['label'] && 'embed' === $field['type'] ) {
// return 'video';
// }

// return $acf_type;
// }
// add_filter( 'fgd2wp_map_acf_field_type', 'pri_pmh_map_acf_field_type', 10, 3 );

/**
 * Relate the story with the correspondant program taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $story_post_id New story post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_story_set_program_taxonomy( $story_post_id, $meta_values ) {
	if ( $meta_values ) {
		$program_term_id = get_post_meta( intval( $meta_values[0] ), '_pri_new_wp_term_program_id', true );
		if ( $program_term_id ) {
			wp_set_object_terms( $story_post_id, intval( $program_term_id ), 'program', false );
		}
	}
}

/**
 * Relate the story with the correspondant contributor taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $story_post_id New story post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_story_set_contributor_taxonomy( $story_post_id, $meta_values ) {
	if ( $meta_values ) {
		$contributor_term_id = get_post_meta( intval( $meta_values[0] ), '_pri_new_wp_term_person_id', true );
		if ( $contributor_term_id ) {
			wp_set_object_terms( $story_post_id, intval( $contributor_term_id ), 'contributor', true );
		}
	}
}
/**
 * Relate the story with the correspondant contributor taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $story_post_id New story post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_story_set_contributor_taxonomy_name( $story_post_id, $meta_values ) {
	if ( $meta_values ) {
		$new_term = term_exists( $meta_values[0], 'contributor' );
		if ( ! $new_term ) {
			$new_term = wp_insert_term( $meta_values[0], 'contributor' );
		}
		if ( ! is_wp_error( $new_term ) ) {
			wp_set_object_terms( $story_post_id, intval( $new_term['term_id'] ), 'contributor', true );
		}
	}
}
/**
 * fix to support story field relationship.
 *
 * @since 1.1.3
 *
 * @param array  $post_types
 * @param string $wp_post_type
 * @return array
 */
function pri_fgd2wp_get_drupal_post_types_from_wp_post_type( $post_types, $wp_post_type ) {
	if ( 'post' === $wp_post_type ) {
		$post_types = array( 'article', 'story', 'post' );
	}
	return $post_types;
}
add_filter( 'fgd2wp_get_drupal_post_types_from_wp_post_type', 'pri_fgd2wp_get_drupal_post_types_from_wp_post_type', 10, 2 );

/**
 * Convert spotify track to track url;
 *
 * @since 1.1.3
 *
 * @param int $new_post_id Post ID to get Spotify tracks with the format spotify:track:TRACK_ID.
 * @return string
 */
function pri_convert_video( $meta_values, $entity_id, $node_type, $custom_field, $entity_type ) {
	if ( $meta_values && 'story' === $node_type && 'field_video' === $custom_field['field_name'] ) {
		// DINKUM: As the video is only 1 in ACF field we will use the first uri for now.
		if ( is_array( $meta_values ) && isset( $meta_values[0]['uri'] ) ) {
			$meta_value = $meta_values[0]['uri'];
			$meta_value = str_replace( 'oembed://', '', $meta_value );
			$meta_value = str_replace( 'youtube://', 'https://www.youtube.com/', $meta_value );
			$meta_value = str_replace( 'vimeo://v/', 'https://vimeo.com/', $meta_value );
			if ( false !== strpos( $meta_value, 'public://' ) && function_exists( 'pri_transform_attachment_url' ) ) {
				$meta_value = pri_transform_attachment_url( $meta_value );
			}
			return array( $meta_value );
		}
	}
	return $meta_values;
}
add_filter( 'fgd2wp_get_custom_field_values', 'pri_convert_video', 10, 5 );

/**
 * Get the taxonomies terms associated with a node
 *
 * @param int    $node_id Node ID
 * @param string $taxonomy Taxonomy name (all by default)
 * @param string $taxonomy_module Taxonomy module (for Drupal 6 only)
 * @param string $entity_type Entity type (node, media)
 * @return array Taxonomies terms
 */
function get_node_primary_category( $node_id ) {
	$terms = array();
		// Drupal 7
	$sql   = "
				SELECT i.field_categories_primary_tid
				FROM field_data_field_categories_primary i
				WHERE i.nid = '$node_id'
			";
	$terms = $this->drupal_query( $sql );
	return $terms;
}

		/**
		 * Get the WordPress term ids corresponding to the Drupal terms
		 *
		 * @param array $terms Taxonomies terms
		 * @return array Taxonomies terms ids
		 */
function get_wp_taxonomies_terms_ids( $terms ) {
	$terms_ids = array();
	foreach ( $terms as $term ) {
		$term_id = apply_filters( 'fgd2wp_get_taxonomy_term_id', $term['tid'], $term );
		if ( isset( $this->imported_taxonomies[ $term_id ] ) ) {
			$terms_ids[] = (int) $this->imported_taxonomies[ $term_id ];
		}
	}
	return $terms_ids;
}


/**
 * Hook for associating the story as featured story in programs and person.
 *
 * @return void
 */
function pri_migration_featured_stories_relations() {
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM wp_termmeta WHERE meta_key = '_pri_featured_stories_nid'" );
	if ( $results ) {
		foreach ( $results as $term_info ) {
			$post_id = $term_info->meta_value;
			// $post_id = pmh_get_post_by_nid( $term_info->meta_value );
			if ( $post_id ) {
				$current_value = get_term_meta( $term_info->term_id, 'related_stories', true );
				if ( $current_value && ! is_array( $current_value ) ) {
					$current_value = array( $current_value );
				} elseif ( empty( $current_value ) ) {
					$current_value = array();
				}
				$current_value[] = $post_id;
				update_term_meta( $term_info->term_id, 'related_stories', array_unique( $current_value ) );
			}
		}
	}

}
// The priority is 99 because it needs to be called after all the insert post methods were executed.
add_action( 'fgd2wp_post_import_nodes_relations', 'pri_migration_featured_stories_relations', 90 );
