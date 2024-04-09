<?php

/**
 * Function to check if the object is a post based on the URL query.
 *
 * @param object $object
 * @param array $url_query
 * @return object $object
 */
function peh_get_object_maybe_post( $object, $url_query ) {

	// Maybe post?
	// Exception added to avoid conflict with program post type.
	if ( false === $object && isset( $url_query['name'] ) && ! isset( $url_query['program'] ) && ! isset( $url_query['person'] ) ) {

		$object = _peh_get_object_by_slug( $url_query['name'], $url_query );

	}

	return $object;
}

add_filter( 'peh_get_object_wild', 'peh_get_object_maybe_post', 10, 2 );



/**
 * Function to check if the object is a category based on the URL query.
 *
 * @param object $object
 * @param array $url_query
 * @return object $object
 */
function peh_get_object_maybe_category( $object, $url_query ) {

	// Maybe Category?
	if ( false === $object && isset( $url_query['category_name'] ) ) {

		$term_slug = '';
		$term_tax  = '';

		if ( str_contains( $url_query['category_name'], '/' ) ) {

			$slug_parts = explode( '/', $url_query['category_name'] );

			if ( $slug_parts ) {

				$term_slug = end( $slug_parts );
				$term_tax  = 'category';
			}
		} else {

			$term_slug = $url_query['category_name'];
		}

		if ( $term_slug ) {

			$object = _peh_get_object_by_taxonomy( $term_slug, $term_tax );
		}
	}

	return $object;
}

add_filter( 'peh_get_object_wild', 'peh_get_object_maybe_category', 12, 2 );


/**
 * Function to check if the object is a post based on the URL query.
 * This function checks if the object is a post based on the URL query by looking for a term taxonomy and slug.
 *
 * @param object $object The object to check.
 * @param array $url_query The URL query to check against.
 * @return object $object The checked object.
 */
function peh_get_object_taxonomy( $object, $url_query ) {

	// Maybe post?
	// Exception added to avoid conflict with program post type.
	if ( false === $object  ) {

		$term_tax  = key( $url_query );
		if ( isset( $url_query[ $term_tax ] ) ) {
			$term_slug = $url_query[ $term_tax ];
			$object = _peh_get_object_by_taxonomy( $term_slug, $term_tax );
		}
	}

	return $object;
}

add_filter( 'peh_get_object_wild', 'peh_get_object_taxonomy', 13, 2 );

/**
 * Function to check if the object is a page based on the URL query.
 *
 * @param object $object
 * @param array $url_query
 * @return object $object
 */
function peh_get_object_maybe_any( $object, $url_query ) {

	// Maybe page?
	// Exception added to avoid conflict with program post type.
	if ( false === $object && isset( $url_query['pagename'] ) && $url_query['pagename'] && ! isset( $url_query['program'] ) && ! isset( $url_query['person'] ) ) {
		$url_query['post_type'] = 'any';
		$object = _peh_get_object_by_slug( $url_query['pagename'], $url_query );

	}
	return $object;
}

add_filter( 'peh_get_object_wild', 'peh_get_object_maybe_any', 15, 2 );
