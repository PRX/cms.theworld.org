<?php
/**
 * Migration helper
 *
 * @package WordPress
 */



/**
 * Add a custom field value action.
 *
 * @param int                            $post_id WordPress post ID
 * @param string                         $custom_field_name Field name
 * @param array                          $custom_field Field data
 * @param array                          $custom_field_values Field values
 * @param date                           $date Date
 * @param FG_Drupal_to_WordPress_CPT_ACF $fg_cpt Date
 * @return void
 */
function pmh_set_custom_post_field( $post_id, $custom_field_name, $custom_field, $custom_field_values, $date, $fg_cpt ) {

	/*
	 Debug
	echo "<pre>";
	var_dump( 'pmh_set_custom_post_field' );
	var_dump( $custom_field_values );
	echo "</pre>";
	 */

	switch ( $custom_field_name ) {

		case 'date_published':
			// DINKUM: we don't need this anymore because the date will be the post_date.
			update_post_meta( $post_id, 'date_published_timezone', pmh_custom_field_get_date_published_timezone( $custom_field_values ) );
			update_post_meta( $post_id, 'date_published_offset', pmh_custom_field_get_date_published_offset( $custom_field_values ) );
			break;
	}
}
add_action( 'pmh_set_custom_post_field', 'pmh_set_custom_post_field', 10, 6 );

/**
 * Get field value.
 *
 * @param array $custom_field_values
 * @return mixed $custom_field_value
 */
function pmh_custom_field_get_date_published_timezone( $custom_field_values ) {

	$custom_field_value = null;

	if ( isset( $custom_field_values[0]['field_date_published_timezone'] ) ) {
		$custom_field_value = $custom_field_values[0]['field_date_published_timezone'];
	}

	return $custom_field_value;
}

/**
 * Get field value.
 *
 * @param array $custom_field_values
 * @return mixed $custom_field_value
 */
function pmh_custom_field_get_date_published_offset( $custom_field_values ) {

	$custom_field_value = null;

	if ( isset( $custom_field_values[0]['field_date_published_offset'] ) ) {
		$custom_field_value = $custom_field_values[0]['field_date_published_offset'];
	}

	return $custom_field_value;
}

/**
 * Method used to create a term from a post type.
 *
 * @since 1.1.3
 *
 * @param int    $new_post New post ID.
 * @param string $post_type Post type to validate it is the same as the created post.
 * @param string $taxonomy Term taxonomy to be created.
 * @param int    $node_id Drupal Node ID.
 * @return int
 */
function pri_create_term( $new_post, $post_type, $taxonomy, $node_id = null ) {
	if ( empty( $new_post ) || empty( $post_type ) || empty( $taxonomy ) ) {
		return null;
	}
	$new_post = get_post( $new_post );
	if ( $post_type !== $new_post->post_type ) {
		return null;
	}

	$new_term = term_exists( $new_post->post_title, $taxonomy );
	if ( $new_term ) {
		return intval( $new_term['term_id'] );
	}

	$new_term = wp_insert_term( $new_post->post_title, $taxonomy, array( 'description' => $new_post->post_content ) );
	if ( ! is_wp_error( $new_term ) ) {
		$new_term_id = intval( $new_term['term_id'] );
		// We need to import this as a custom field because the teaser is the post excerpt by default .
		update_term_meta( $new_term_id, 'teaser', $new_post->post_excerpt );

		update_term_meta( $new_term_id, "_pri_old_wp_{$new_post->post_type}_id", $new_post->ID );
		update_term_meta( $new_term_id, "_fgd2wp_old_{$new_post->post_type}_id", $node_id ? $node_id : get_post_meta( $new_post->ID, '_fgd2wp_old_node_id', true ) );
		update_post_meta( $new_post->ID, "_pri_new_wp_term_{$new_post->post_type}_id", $new_term_id );
		return $new_term_id;
	}

	return $new_term;
}

/**
 * Hook for doing other actions after inserting the post
 *
 * @param int    $new_post_id  The new post ID.
 * @param array  $node         Array of info from Drupal Node.
 * @param string $post_type    Target post type.
 * @param string $entity_type  Drupal post type?
 * @return void
 */
function pmh_post_insert_post_media( $new_post_id, $node, $post_type, $entity_type ) {

	// Save legacy ID.
	if ( isset( $node['nid'] ) ) {
		update_post_meta( $new_post_id, 'nid', $node['nid'] );
	}
}
add_action( 'fgd2wp_post_insert_post', 'pmh_post_insert_post_media', 10, 4 );
