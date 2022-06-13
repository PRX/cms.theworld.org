<?php
/**
 * Migration filters
 *
 * @package Wordpress
 */

/**
 * Migration filter
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_story__field_image( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	$custom_field_name = 'migrated_featured_image';

	$custom_field['module']  = 'text';
	$custom_field['type']    = 'text_textarea';
	$custom_field['type']    = 'text_textarea';
	$custom_field['label']   = 'Migrated Featured Image';
	$custom_field['columns'] = array(
		'value' => 'value_migrated_featured_image',
	);

	$custom_field_values = array(
		array(
			'value_migrated_featured_image' => wp_json_encode( $custom_field_values ),
		),
	);

	return array( $custom_field_name, $custom_field, $custom_field_values );
}
