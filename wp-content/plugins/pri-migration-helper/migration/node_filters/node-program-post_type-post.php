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
function pri_program_fgd2wp_get_custom_fields( $custom_fields, $node_type ) {
	if ( 'program' === $node_type ) {
		foreach ( $custom_fields as $field_name => $field ) {
			if ( pri_program_field_to_exclude( $field_name ) ) {
				unset( $custom_fields[ $field_name ] );
			}
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_custom_fields', 'pri_program_fgd2wp_get_custom_fields', 10, 2 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_program_field( $default, $custom_field_name ) {
	if ( pri_program_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return nil to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_program_field', 'pri_fgd2wp_import_program_field', 10, 2 );

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_program_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'image',
		'ref_program',
		'syndication',
		'program_copyright',
		'featured_story',
		'is_this_a_podcast_',
		'program_source_url',
		'int_migrate_source_id',
		'square_content',
		'rss_feed',
		'social_links',
		'groups',
	);
	return in_array( $field_name, $do_not_import_fields, true );
}


/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_program__field( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {
		case 'podcast_logo':
			$custom_field_name = 'logo';
			break;

		case 'links_sponsors':
			$custom_field_name = 'sponsor_links';
			break;
		// case 'teaser':
		// $custom_field_name = 'excerpt';
		// break;

		default:
			// code...
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}

/**
 * Hook for creating the term in case there were no custom fields to process.
 *
 * @param int    $new_post_id  The new post ID.
 * @param array  $node         Array of info from Drupal Node.
 * @param string $post_type    Target post type.
 * @param string $entity_type  Drupal post type?
 * @return void
 */
function pri_migration_fgd2wp_program_insert_term( $new_post_id, $node, $post_type, $entity_type ) {
	if ( 'program' !== $post_type || empty( $new_post_id ) ) {
		return;
	}
	$new_term_id = pri_create_term( $new_post_id, $post_type, $post_type, $node['nid'] );
	if ( ( ! $new_term_id || is_wp_error( $new_term_id ) ) && defined( 'WP_CLI' ) ) {
		error_log( "[ERROR] Term Taxonomy {$post_type} could not be created for Post ID {$new_post_id} and Node ID {$node['nid']}." );
	}
}
// The priority is 99 because it needs to be called after all the insert post methods were executed.
add_action( 'fgd2wp_post_insert_post', 'pri_migration_fgd2wp_program_insert_term', 90, 4 );

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
function pri_pmh_program_set_custom_post_field( $new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date, $plugin_cpt ) {
	if ( empty( $new_post_id ) || 'program' !== get_post_type( $new_post_id ) ) {
		return;
	}
	// ** Convert CPT 'Program' to Taxonomy 'Program' */
	$new_term_id = pri_create_term( $new_post_id, 'program', 'program' );
	if ( $new_term_id && ! is_wp_error( $new_term_id ) ) {
		if ( 'ref_authors' === $custom_field_name ) {
			// If this is a reference field.
			pri_pmh_program_set_person_taxonomy( $new_term_id, $custom_field_values );
		} elseif ( 'featured_stories' === $custom_field_name ) {
			// Save featured stories nid to related to the term later.
			if ( $custom_field_values ) {
				if ( ! is_array( $custom_field_values ) ) {
					$custom_field_values = array( $custom_field_values );
				}
				foreach ( $custom_field_values as $value ) {
					add_term_meta( $new_term_id, '_pri_featured_stories_nid', $value );
				}
			}
		} else {
			$plugin_cpt->set_custom_term_field( $new_term_id, $custom_field_name, $custom_field, $custom_field_values );
		}
	}
}
add_action( 'pmh_set_custom_post_field', 'pri_pmh_program_set_custom_post_field', 10, 6 );

function pri_pmh_program_set_person_taxonomy( $program_term_id, $custom_field_values ) {

	$term_meta_key = 'hosts';
	if ( ! is_array( $custom_field_values ) ) {
		$custom_field_values = array( $custom_field_values );
	}
	$term_meta_value = array();
	foreach ( $custom_field_values as $related_post_id ) {
		// Look for the Program term id stored in the post.
		$person_term_id = get_post_meta( intval( $related_post_id ), '_pri_new_wp_term_person_id', true );
		if ( $person_term_id ) {
			$term_meta_value[] = $person_term_id;
		}
	}

	$current_values = get_term_meta( $program_term_id, $term_meta_key, true );
	if ( $current_values && is_array( $current_values ) ) {
		$term_meta_value = array_unique( array_merge( $current_values, $term_meta_value ) );
	}

	if ( $term_meta_value ) {
		update_term_meta( $program_term_id, $term_meta_key, $term_meta_value );
		return 1;
	}
	return 0;
}
// function pri_pmh_program_set_person_taxonomy( $program_term_id, $person_term_id ) {
// if ( $program_term_id ) {
// if ( ! is_array( $person_term_id ) ) {
// $person_term_id = array( $person_term_id );
// }
// $current_values = get_term_meta( $program_term_id, 'hosts', true );
// if ( $current_values && is_array( $current_values ) ) {
// $person_term_id = array_unique( array_merge( $current_values, $person_term_id ) );
// }
// }
// return update_term_meta( $program_term_id, 'hosts', $person_term_id );
// }
