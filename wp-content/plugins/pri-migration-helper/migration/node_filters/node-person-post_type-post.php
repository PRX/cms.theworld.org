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
function pri_person_fgd2wp_get_custom_fields( $custom_fields, $node_type ) {
	if ( 'person' === $node_type ) {
		foreach ( $custom_fields as $field_name => $field ) {
			if ( pri_person_field_to_exclude( $field_name ) ) {
				unset( $custom_fields[ $field_name ] );
			}
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_custom_fields', 'pri_person_fgd2wp_get_custom_fields', 10, 2 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_person_field( $default, $custom_field_name ) {
	if ( pri_person_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return nil to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_person_field', 'pri_fgd2wp_import_person_field', 10, 2 );

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_person_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'google_authorship_link',
		'links_contact',
		'links_restricted',
	);
	return in_array( $field_name, $do_not_import_fields, true );
}


/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
/*
function pmh_filter__node_person__field( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {

		case 'featured_stories':
			$custom_field_name = 'featured_stories';
			break;

		default:
			// code...
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}
*/
/**
 * Hook for creating the term in case there were no custom fields to process.
 *
 * @param int    $new_post_id  The new post ID.
 * @param array  $node         Array of info from Drupal Node.
 * @param string $post_type    Target post type.
 * @param string $entity_type  Drupal post type?.
 * @return void
 */
function pri_migration_fgd2wp_person_insert_term( $new_post_id, $node, $post_type, $entity_type ) {
	if ( 'person' !== $post_type || empty( $new_post_id ) ) {
		return;
	}
	$new_term_id = pri_create_term( $new_post_id, $post_type, 'contributor', $node['nid'] );
	if ( ( ! $new_term_id || is_wp_error( $new_term_id ) ) && defined( 'WP_CLI' ) ) {
		error_log( "[ERROR] Term Taxonomy {$post_type} could not be created for Post ID {$new_post_id} and Node ID {$node['nid']}." );
	}
}
// The priority is 99 because it needs to be called after all the insert post methods were executed.
add_action( 'fgd2wp_post_insert_post', 'pri_migration_fgd2wp_person_insert_term', 90, 4 );

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
function pri_pmh_person_set_custom_post_field( $new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date, $plugin_cpt ) {
	if ( empty( $new_post_id ) || 'person' !== get_post_type( $new_post_id ) ) {
		return;
	}
	// ** Convert CPT 'person' to Taxonomy 'person' */
	$new_term_id = pri_create_term( $new_post_id, 'person', 'contributor' );
	if ( $new_term_id && ! is_wp_error( $new_term_id ) ) {
		if ( false !== strpos( $custom_field_name, 'collection-social_links_0_' ) ) {
			pri_pmh_person_set_social_links( $new_term_id, $custom_field_name, $custom_field_values );
		} elseif ( 'ref_program' === $custom_field_name ) {
			// If this is a reference field.
			pri_pmh_person_set_program_taxonomy( $new_term_id, $custom_field_name, $custom_field_values );
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
add_action( 'pmh_set_custom_post_field', 'pri_pmh_person_set_custom_post_field', 10, 6 );

function pri_pmh_person_set_social_links( $new_term_id, $custom_field_name, $custom_field_values ) {
	// field name like "collection-social_links_0_blog".
	$term_meta_key = str_replace( 'collection-social_links_0_', '', $custom_field_name );
	if ( in_array( $term_meta_key, array( 'blog', 'facebook', 'get_in_touch', 'podcast', 'tumblr', 'twitter', 'website', 'rss' ), true ) && ! empty( $custom_field_values ) && is_array( $custom_field_values ) && ! empty( $custom_field_values[0]['url'] ) ) {
		// Get in touch will be converted into email field.
		$term_meta_key = 'get_in_touch' === $term_meta_key ? 'email' : $term_meta_key;
		// Values stored in this array attribute $custom_field_values[0]['url'].
		update_term_meta( $new_term_id, $term_meta_key, $custom_field_values[0]['url'] );
	}
}

function pri_pmh_person_set_program_taxonomy( $person_term_id, $custom_field_name, $custom_field_values ) {
	$term_meta_key = 'program';
	if ( ! is_array( $custom_field_values ) ) {
		$custom_field_values = array( $custom_field_values );
	}
	$term_meta_value = array();
	foreach ( $custom_field_values as $related_post_id ) {
		// Look for the Program term id stored in the post.
		$program_term_id = get_post_meta( intval( $related_post_id ), '_pri_new_wp_term_program_id', true );
		if ( $program_term_id ) {
			$term_meta_value[] = $program_term_id;
		}
	}

	if ( $term_meta_value ) {
		update_term_meta( $person_term_id, $term_meta_key, $term_meta_value );
		// after associating the person with the progam we will associate the progam to this person.
		// foreach ( $term_meta_value as $program_term_id ) {
		// pri_pmh_program_set_person_taxonomy( $program_term_id, $person_term_id );
		// }
		return 1;
	}
	return 0;
}

