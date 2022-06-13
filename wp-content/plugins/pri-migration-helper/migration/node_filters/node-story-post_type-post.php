<?php
/**
 * Migration filters
 *
 * @package WordPress
 */

/**
 * Migration filter
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_story__field_image( $args ) {

	// Destination custom field name.
	$new_custom_field_name  = 'story_featured_image';
	$new_custom_field_label = 'Migrated Featured Image';

	// Break down args.
	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// Setup new custom field name.
	$custom_field_name = $new_custom_field_name;

	// Setup ACF field.
	$custom_field['module']  = 'text';
	$custom_field['type']    = 'text_textarea';
	$custom_field['label']   = $new_custom_field_label;
	$custom_field['columns'] = array(
		'value' => 'featured_image',
	);

	// Setup Meta value.
	$custom_field_values = array(
		array(
			'featured_image' => wp_json_encode( $custom_field_values ),
		),
	);

	return array( $custom_field_name, $custom_field, $custom_field_values );
}

/**
 * Migration filter
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_story__field_images( $args ) {

	// Destination custom field name.
	$new_custom_field_name  = 'story_featured_images';
	$new_custom_field_label = 'Migrated Featured Images';

	// Break down args.
	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// Setup new custom field name.
	$custom_field_name = $new_custom_field_name;

	// Setup ACF field.
	$custom_field['module']     = 'text';
	$custom_field['type']       = 'text_textarea';
	$custom_field['repetitive'] = false;
	$custom_field['label']      = $new_custom_field_label;
	$custom_field['columns']    = array(
		'value' => 'featured_images',
	);

	// Setup Meta value.
	$custom_field_values = array(
		array(
			'featured_images' => wp_json_encode( $custom_field_values ),
		),
	);

	return array( $custom_field_name, $custom_field, $custom_field_values );
}
