<?php
/**
 * Migration Taxonomy filters
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
function pri_category_fgd2wp_get_custom_fields( $custom_fields ) {
	foreach ( $custom_fields as $field_name => $field ) {
		if ( pri_category_field_to_exclude( $field_name ) ) {
			unset( $custom_fields[ $field_name ] );
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_taxonomies_custom_fields', 'pri_category_fgd2wp_get_custom_fields', 11 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_category_field( $default, $custom_field_name ) {
	if ( pri_category_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return false to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_term_category_field', 'pri_fgd2wp_import_category_field', 10, 2 );

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_category_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'field_sort_by',
		'file_vertical_logo',
		'links_contact',
		'vertical_assoc',
		'background_image',
		'feed_name',
		'featured_story',
		'featured_link2',
		'featured_link',
		'featured_link_icon',
		'featured_link2_icon',
		'third_featured_link',
		'third_featured_link_icon',
		'city-latitude',
		'city-longitude',
		'city-containedbystate',
		'city-containedbycounty',
		'company-ticker',
		'company-legalname',
		'country-latitude',
		'country-longitude',
		'country-containedbystate',
		'country-containedbycounty',
		'province_or_state-latitude',
		'province_or_state-longitude',
		'province_or_state-containedbystate',
		'province_or_state-containedbycounty',
		'collections-body',
		'section-body',
		'series-background_image',
		'series-featured_link',
		'series-featured_link2',
		'series-featured_link2_icon',
		'series-featured_link_icon',
		'series-featured_story',
		'series-file_vertical_logo',
		'series-links_contact',
		'series-text_tagline',
		'series-third_featured_link',
		'series-third_featured_link_icon',
		'series-vertical_assoc',
		'series-featured_stories',
		'interactive_type-interactive_type_id',
		'section-feed_name',
		'licence-groups',
		'series-links_sponsors',
		'vertical-freeform_block_select',
		'series-image_banner',
		'section-image_banner',
		'syndicator-syndication_badge',
		'category-featured_link',
		'category-featured_link2',
		'category-featured_link2_icon',
		'category-featured_link_icon',
		// 'category-featured_story',
		'category-feed_name',
		'category-file_vertical_logo',
		'category-links_contact',
		'category-third_featured_link',
		'category-third_featured_link_icon',
		'category-vertical_assoc',
		'category-background_image',
		'category-sort_by',
	);
	return in_array( $field_name, $do_not_import_fields, true );
}

/**
 * Get the taxonomies related to a node type
 *
 * @param array  $taxonomies Taxonomies
 * @param string $node_type Node type
 * @return array Taxonomies
 */
function pri_get_node_type_taxonomies( $taxonomies, $node_type ) {
	if ( is_array( $taxonomies ) ) {
		foreach ( $taxonomies as $key => $taxonomy ) {
			if ( ! pri_taxonomies_to_include( $taxonomy ) ) {
				unset( $taxonomies[ $key ] );
			}
		}
	}
	return $taxonomies;
}
add_filter( 'fgd2wp_get_node_type_taxonomies', 'pri_get_node_type_taxonomies', 10, 2 );

/**
 * Compare if a taxonomy should be included.
 *
 * @since 1.1.3
 *
 * @param string $taxonomy_name Taxonomy name.
 * @return bool
 */
function pri_taxonomies_to_include( $taxonomy_name ) {
	// $taxonomies_to_exclude = array( 'collections', 'legacy_tags', 'migration_flags', 'section', 'series', 'story_tone', 'vertical' );
	$import_taxonomies = array(
		'category',
		'categories',
		'city',
		'continent',
		'country',
		'province_or_state',
		'region',
		'resource_development',
		'social_tags',
		'tags',
		'story_format',
		'person',
		'license',
	);
	return in_array( $taxonomy_name, $import_taxonomies, true );
}

/**
 * Removed those taxonomies we don't want to process.
 *
 * @since 1.1.3
 *
 * @param array $taxonomies List of taxonomies to be processed.
 * @return array
 */
function pri_migration_fgd2wp_get_custom_taxonomies( $taxonomies ) {

	foreach ( $taxonomies as $index => $taxonomy ) {
		if ( isset( $taxonomy['machine_name'] ) && ! pri_taxonomies_to_include( $taxonomy['machine_name'] ) ) {
			unset( $taxonomies[ $index ] );
		}
	}
	return $taxonomies;
}
add_filter( 'fgd2wp_get_custom_taxonomies', 'pri_migration_fgd2wp_get_custom_taxonomies' );


/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pri_fgd2wp_import_term_custom_fields( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {
		case 'category-category_deprecated':
			$custom_field_name = 'archive_category';
			break;
		case 'category-text_tagline':
			$custom_field_name = 'teaser';
			break;
		case 'category-featured_stories':
			$custom_field_name          = 'related_stories';
			$custom_field['repetitive'] = false;
			$custom_field['is_array']   = true;
			break;
		case 'category-image_banner':
			$custom_field_name = 'image_banner';
			break;
		case 'category-links_sponsors':
			$custom_field_name = 'sponsor_links';
			break;
		case 'category-ref_editor':
			$custom_field_name          = 'editors';
			$custom_field['repetitive'] = false;
			$custom_field['is_array']   = true;
			break;
		default:
			// code...
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}

add_filter( 'fgd2wp_import_term_custom_fields', 'pri_fgd2wp_import_term_custom_fields', 11 );

/**
 * Intercet term values before being inserted.
 *
 * @since 1.1.3
 *
 * @param array  $custom_field_values Custom field value.
 * @param  int    $new_term_id Term id inserted.
 * @param string $custom_field_name WordPress custom field name.
 * @param array  $custom_field Drupal field.
 * @return array
 */
function pri_fgd2wp_pre_insert_taxonomy_term( $custom_field_values, $new_term_id, $custom_field_name, $custom_field ) {
	switch ( $custom_field_name ) {
		case 'category-body':
			if ( $custom_field_values && ! empty( $custom_field_values[0] ) ) {
				$cat_description = get_term_field( 'description', $new_term_id, 'category', 'raw' );
				if ( ! $cat_description && ! empty( $custom_field_values[0]['field_body_value'] ) ) {
					$args['description'] = $custom_field_values[0]['field_body_value'];
					wp_update_term( $new_term_id, 'category', $args );
				}

				$custom_field_values = array();
			}
			break;
		case 'category-ref_editor':
			if ( $custom_field_values ) {
				$custom_field_values = pri_pmh_category_get_contributor_taxonomy( $custom_field_values );
			}
			break;
	}
	return $custom_field_values;
}
add_filter( 'fgd2wp_pre_import_term_custom_field_values', 'pri_fgd2wp_pre_insert_taxonomy_term', 10, 4 );

/**
 * Relate the story with the correspondant contributor taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $story_post_id New story post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_category_get_contributor_taxonomy( $meta_values ) {
	if ( $meta_values ) {
		foreach ( $meta_values as $key => $custom_field_value ) {
			// As we already have contributtors migrated we can relate them here.
			$contributor_term_id = get_post_meta( pmh_get_post_id_by_meta( '_fgd2wp_old_node_id', $custom_field_value['field_ref_editor_target_id'] ), '_pri_new_wp_term_person_id', true );
			if ( $contributor_term_id ) {
				$meta_values[ $key ]['field_ref_editor_target_id'] = $contributor_term_id;
			}
		}
	}
	return $meta_values;
}
