<?php
/**
 * Migration filters
 *
 * @package WordPress
 */


/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_page__field( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {
		case 'teaser':
			$custom_field_name = 'excerpt';
			break;
		default:
			// code...
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}

/**
 * Exclude the custom fields we don't want to import from the list of custom fields to be created in ACF
 *
 * @param array  $custom_fields Custom fields.
 * @param string $node_type Drupal node type.
 * @return array Custom fields
 */
function pri_page_fgd2wp_get_custom_fields( $custom_fields, $node_type ) {
	if ( 'page' === $node_type ) {
		foreach ( $custom_fields as $field_name => $field ) {
			if ( pri_page_field_to_exclude( $field_name ) ) {
				unset( $custom_fields[ $field_name ] );
			}
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_custom_fields', 'pri_page_fgd2wp_get_custom_fields', 10, 2 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_page_field( $default, $custom_field_name ) {
	if ( pri_page_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return nil to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_page_field', 'pri_fgd2wp_import_page_field', 10, 2 );

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_page_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'terms_sections',
		'images',
		'categories',
		'terms_verticals',
		'tags',
		'rss_link',
	);
	return in_array( $field_name, $do_not_import_fields, true );
}
