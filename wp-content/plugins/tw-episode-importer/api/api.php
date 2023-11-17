<?php
/**
 * Register API routes.
 *
 * @package tw_episode_importer
 */

define( 'TW_EPISODE_IMPORTER_API_ENDPOINT', 'episode-importer' );

/**
 * Initialize REST route for API episodes.
 *
 * @return void
 */
function tw_episode_importer_rest_api_init() {

	register_rest_route(
		TW_API_ROUTE_BASE,
		TW_EPISODE_IMPORTER_API_ENDPOINT . '/episodes',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tw_episode_importer_api_route_epsisodes',
			'permission_callback' => '__return_true',
			'args'                => array(
				'd' => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
			),
		)
	);
	register_rest_route(
		TW_API_ROUTE_BASE,
		TW_EPISODE_IMPORTER_API_ENDPOINT . '/episodes/(?P<id>[a-f0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tw_episode_importer_api_route_epsisode',
			'permission_callback' => '__return_true',
			'args'                => array(),
		)
	);

	register_rest_route(
		TW_API_ROUTE_BASE,
		TW_EPISODE_IMPORTER_API_ENDPOINT . '/segments',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tw_episode_importer_api_route_segments',
			'permission_callback' => '__return_true',
			'args'                => array(
				'd' => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
			),
		)
	);
	register_rest_route(
		TW_API_ROUTE_BASE,
		TW_EPISODE_IMPORTER_API_ENDPOINT . '/segments/(?P<id>[a-f0-9-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tw_episode_importer_api_route_segment',
			'permission_callback' => '__return_true',
			'args'                => array(),
		)
	);
}
add_action( 'rest_api_init', 'tw_episode_importer_rest_api_init' );

/**
 * Validate date param.
 *
 * @param string          $value    Value to validate.
 * @param WP_REST_Request $request  Request object.
 * @param string          $key      Parameter key.
 * @return bool
 */
// phpcs:ignore
function tw_episode_importer_args_date_validation_callback( $value, $request, $key ) {

	try {
		$after = new DateTime( $value );
	} catch ( \Throwable $th ) {
		return new WP_Error( 'rest_invalid_param', esc_html__( 'TW - Invalid date provided.', 'tw-text' ), array( 'status' => 400 ) );
	}

	return true;
}

/**
 * Sanitize function.
 *
 * @param string          $value    Value to validate.
 * @param WP_REST_Request $request  Request object.
 * @param string          $param      Parameter key.
 * @return string
 */
// phpcs:ignore
function tw_episode_importer_args_date_sanitization_callback( $value, $request, $param ) {
	// It is as simple as returning the sanitized value.
	return sanitize_text_field( $value );
}

/**
 * API Episodes route callback.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
// phpcs:ignore
function tw_episode_importer_api_route_epsisodes( WP_REST_Request $request ) {

	// Request API data.
	$options      = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$api_url      = $options[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ];
	$date_string  = $request->get_param( 'd' );
	$date         = new DateTime( $date_string );
	$one_day      = new DateInterval( 'P1D' );
	$after        = $date->format( 'Y-m-d' );
	$before       = $date->add( $one_day )->format( 'Y-m-d' );
	$api_response = wp_remote_get( $api_url . '?after=' . $after . '&before=' . $before );
	$status       = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {

		$body     = json_decode( wp_remote_retrieve_body( $api_response ) );
		$episodes = tw_episode_importer_parse_api_items( $body );

		$response['status'] = 200;
		$response['data']   = $episodes ?? array();

	}

	return tw_episode_importer_get_response( $response );
}

/**
 * API Epsiode route callback.
 *
 * @param WP_Request $request This function accepts a rest request to process data.
 * @return WP_REST_Response
 */
function tw_episode_importer_api_route_epsisode( $request ) {

	// Request API data.
	$options      = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$id           = $request['id'];
	$api_url      = $options[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] . '/' . $id;
	$api_response = wp_remote_get( $api_url );
	$status       = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {
		$body    = json_decode( wp_remote_retrieve_body( $api_response ) );
		$episode = tw_episode_importer_parse_api_item( $body );

		$response['status'] = 200;
		$response['data']   = $episode ?? array();
	}

	return tw_episode_importer_get_response( $response );
}

/**
 * API Segments route callback.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
// phpcs:ignore
function tw_episode_importer_api_route_segments( WP_REST_Request $request ) {

	// Request API data.
	$options      = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$api_url      = $options[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ];
	$date_string  = $request->get_param( 'd' );
	$date         = new DateTime( $date_string );
	$one_day      = new DateInterval( 'P1D' );
	$after        = $date->format( 'Y-m-d' );
	$before       = $date->add( $one_day )->format( 'Y-m-d' );
	$api_response = wp_remote_get( $api_url . '?after=' . $after . '&before=' . $before );
	$status       = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {

		$body     = json_decode( wp_remote_retrieve_body( $api_response ) );
		$segments = tw_episode_importer_parse_api_items( $body );

		$response['status'] = 200;
		$response['data']   = $segments ?? array();

	}

	return tw_episode_importer_get_response( $response );
}

/**
 * API Segment route callback.
 *
 * @param WP_Request $request This function accepts a rest request to process data.
 * @return WP_REST_Response
 */
function tw_episode_importer_api_route_segment( $request ) {

	// Request API data.
	$options      = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$id           = $request['id'];
	$api_url      = $options[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] . '/' . $id;
	$api_response = wp_remote_get( $api_url );
	$status       = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {
		$body    = json_decode( wp_remote_retrieve_body( $api_response ) );
		$segment = tw_episode_importer_parse_api_item( $body );

		$response['status'] = 200;
		$response['data']   = $segment ?? array();
	}

	return tw_episode_importer_get_response( $response );
}

/**
 * Parse API item into normalized data.
 *
 * @param array $api_item Item from API request.
 * @return array Normalized item data as associative array.
 */
function tw_episode_importer_parse_api_item( $api_item ) {

	$guid  = $api_item->guid;
	$title = $api_item->title;
	$post  = tw_episode_importer_get_post( $guid, $title );
	$item  = array(
		'post'          => $post,
		'wasImported'   => $post && $post->guid === $guid,
		'id'            => $api_item->id,
		'guid'          => $guid,
		'title'         => $title,
		'excerpt'       => $api_item->subtitle ?? null,
		'content'       => $api_item->description ?? null,
		'datePublished' => $api_item->publishedAt, // phpcs:ignore
		'dateUpdated'   => $api_item->updatedAt ?? null, // phpcs:ignore
		'author'        => $api_item->author->name ? (array) $api_item->author : null,
		'enclosure'     => (array) $api_item->_links->enclosure ?? null,
	);

	if ( $item['author'] ) {
		$contributor_term     = get_terms(
			array(
				'taxonomy' => 'contributor',
				'name'     => $item['author']['name'],
			)
		);
		$item['author']['id'] = $contributor_term && ! empty( $contributor_term ) ? $contributor_term[0]->term_id : null;
	}

	$categories         = $api_item->categories;
	$item['categories'] = ! $categories || empty( $categories ) ? null : array_map(
		function ( $term_name ) {
			$result = array(
				'name' => $term_name,
			);

			// Get existing terms by the same name.
			$args  = array(
				'name' => $term_name,
			);
			$terms = array_values( get_terms( $args ) );

			// Pick the needed props for the term and extend taxonomy data.
			if ( $terms && ! empty( $terms ) ) {
				$result['existingTerms'] = array_map(
					fn( $term ) => array_merge(
						array( 'id' => $term->term_id ),
						array_intersect_key( (array) $term, array_flip( array( 'name', 'taxonomy', 'count' ) ) ),
						array( 'taxonomy' => array_intersect_key( (array) get_taxonomy( $term->taxonomy ), array_flip( array( 'name', 'label' ) ) ) )
					),
					$terms
				);
			}

			return $result;
		},
		$categories
	);

	return $item;
}

/**
 * Parse API response body into normalized data.
 *
 * @param array $api_body Response body from API request.
 * @return array Normalized item data as associative arrays.
 */
function tw_episode_importer_parse_api_items( $api_body ) {

	$items = $api_body->_embedded->{'prx:items'};

	if ( ! $items || empty( $items ) ) {
		return null;
	}

	$episodes = array_map(
		'tw_episode_importer_parse_api_item',
		$items
	);

	return $episodes;
}

/**
 * Get post id using GUID.
 *
 * @param string $guid Post GUID to lookup ID for.
 * @param string $title Post title to lookup ID for.
 * @return array|null
 */
function tw_episode_importer_get_post( $guid, $title ) {
	global $wpdb;

	$cache_key = 'post_id_for_guid:' . $guid;
	$id        = wp_cache_get( $cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );

	if ( ! $id ) {
		// phpcs:ignore
		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE (guid=%s OR post_title='%s') AND post_type IN ('episode', 'segment');", array($guid, $title) ) );
		if ( $post ) {
			wp_cache_set( $cache_key, $post->ID, TW_EPISODE_IMPORTER_CACHE_GROUP );
		}
	} else {
		$post = get_post( $id );
	}

	return ! $post ? null : array(
		'guid'             => $post->guid,
		'databaseId'       => $post->ID,
		'title'            => $post->post_title,
		'slug'             => $post->post_name,
		'datePublished'    => $post->post_date,
		'datePublishedGmt' => $post->post_date_gmt,
	);
}

/**
 * Get post id by title. Convert title to slug before query.
 *
 * @param string $title Title to query posts for.
 * @return integer|null
 */
function tw_episode_importer_get_post_id_from_title( $title ) {
	$slug = sanitize_title( $title );
	$args = array(
		'title'       => $title,
		'post_type'   => array( 'episode', 'segment' ),
		'numberposts' => 1,
	);
	$post = get_posts( $args );

	return $posts ? $posts[0]->ID : null;
}

/**
 * Function to get response array based on $response argument.
 *
 * @param array $response   Repsonse object.
 * @return WP_REST_Response
 */
function tw_episode_importer_get_response( array $response ) {

	$status = $response['status'];

	switch ( $status ) {
		case 400:
			return new WP_Error( 'rest_no_route', __( 'TW - API URL not configured or could not be loaded.', 'tw-text' ), $response );

		case 403:
			return new WP_Error( 'rest_no_route', __( 'TW - Resource not found or forbiden.', 'tw-text' ), $response );

		case 404:
			return new WP_Error( 'rest_no_route', __( 'TW - No route was found matching the URL and request method.', 'tw-text' ), $response );

		case 500:
			return new WP_Error( 'rest_internal_error', __( 'TW - 500 Internal Error.', 'tw-text' ), $response );

		default:
			return new WP_REST_Response( $response['data'], $status );
	}
}
