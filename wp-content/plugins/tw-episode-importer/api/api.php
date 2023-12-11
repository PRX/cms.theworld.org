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
				'before' => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
				'after'  => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
				'on'     => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
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
				'before' => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
				'after'  => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
					'sanitize_callback' => 'tw_episode_importer_args_date_sanitization_callback',
				),
				'on'     => array(
					'validate_callback' => 'tw_episode_importer_args_date_validation_callback',
					'type'              => 'string',
					'required'          => false,
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

	register_rest_route(
		TW_API_ROUTE_BASE,
		TW_EPISODE_IMPORTER_API_ENDPOINT . '/taxonomies',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tw_episode_importer_api_route_taxonomies',
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
	$options            = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$api_url            = $options[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ];
	$after_date_string  = $request->get_param( 'after' );
	$before_date_string = $request->get_param( 'before' );
	$on_date_string     = $request->get_param( 'on' );
	$on_date            = $on_date_string ? new DateTime( $on_date_string ) : new DateTime();
	$one_day            = new DateInterval( 'P1D' );
	$after              = $after_date_string ? $after_date_string : $on_date->format( 'Y-m-d' );
	$before             = $before_date_string ? $before_date_string : $on_date->add( $one_day )->format( 'Y-m-d' );
	$api_response       = wp_remote_get( $api_url . '?after=' . $after . '&before=' . $before . '&per=365' );
	$status             = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {

		$body     = json_decode( wp_remote_retrieve_body( $api_response ) );
		$episodes = tw_episode_importer_parse_api_items( $body, 'episode' );

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
		$episode = tw_episode_importer_parse_api_item( $body, 'episode' );

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
	$options            = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$api_url            = $options[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ];
	$after_date_string  = $request->get_param( 'after' );
	$before_date_string = $request->get_param( 'before' );
	$on_date_string     = $request->get_param( 'on' );
	$on_date            = $on_date_string ? new DateTime( $on_date_string ) : new DateTime();
	$one_day            = new DateInterval( 'P1D' );
	$after              = $after_date_string ? $after_date_string : $on_date->format( 'Y-m-d' );
	$before             = $before_date_string ? $before_date_string : $on_date->add( $one_day )->format( 'Y-m-d' );
	$api_response       = wp_remote_get( $api_url . '?after=' . $after . '&before=' . $before . '&per=3650' );
	$status             = wp_remote_retrieve_response_code( $api_response );

	$response = array(
		'status' => $status,
		'data'   => array(),
	);

	if ( 200 === $status ) {

		$body     = json_decode( wp_remote_retrieve_body( $api_response ) );
		$segments = tw_episode_importer_parse_api_items( $body, 'segment' );

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
		$segment = tw_episode_importer_parse_api_item( $body, 'segment' );

		$response['status'] = 200;
		$response['data']   = $segment ?? array();
	}

	return tw_episode_importer_get_response( $response );
}

/**
 * Get taxonomies data.
 *
 * @return WP_REST_Response
 */
function tw_episode_importer_api_route_taxonomies() {

	// Get taxonomies.
	$taxonomies = get_taxonomies(
		array(
			'public' => true,
		),
		'objects'
	);
	$data       = array_map(
		fn( $taxonomy ) => array(
			'name'  => $taxonomy->name,
			'label' => $taxonomy->label,
		),
		$taxonomies
	);

	unset( $data['post_format'] );
	unset( $data['license'] );
	unset( $data['resource_development'] );
	unset( $data['story_format'] );

	$response = array(
		'status' => 200,
		'data'   => $data,
	);

	return tw_episode_importer_get_response( $response );
}

/**
 * Parse API item into normalized data.
 *
 * @param array  $api_item Item from API request.
 * @param string $post_type Post type to check for existing data.
 * @return array Normalized item data as associative array.
 */
function tw_episode_importer_parse_api_item( $api_item, $post_type ) {

	$guid           = $api_item->guid;
	$title          = $api_item->title;
	$date_published = $api_item->publishedAt; // phpcs:ignore
	$post  = tw_episode_importer_get_post( $post_type, $guid, $title, $date_published, $api_item->_links->enclosure->href ); // phpcs:ignore
	$item           = array(
		'post'          => $post,
		'wasImported'   => $post ? $post['guid'] === $api_item->guid : false,
		'id'            => $api_item->id,
		'guid'          => $guid,
		'title'         => $title,
		'excerpt'       => $api_item->subtitle ?? null,
		'content'       => $api_item->description ?? null,
		'datePublished' => $date_published,
		'dateUpdated'   => $api_item->updatedAt ?? null, // phpcs:ignore
		'author'        => $api_item->author && property_exists( $api_item->author, 'name' ) ? (array) $api_item->author : null,
		'enclosure'     => (array) $api_item->_links->enclosure ?? null,
	);

	if ( $item['author'] ) {
		$contributor_terms = get_terms(
			array(
				'taxonomy' => 'contributor',
				'name'     => $item['author']['name'],
			)
		);
		$contributor_term  = ! empty( $contributor_terms ) ? $contributor_terms[0] : null;

		if ( $contributor_term ) {
			$contributor_image    = get_field( 'image', 'contributor_' . $contributor_term->term_id );
			$item['author']['id'] = $contributor_term->term_id;
			if ( $contributor_image ) {
				$item['author']['image'] = $contributor_image['url'];
			}
		}
	}

	$categories         = $api_item->categories;
	$item['categories'] = ! $categories || empty( $categories ) ? null : array_map(
		function ( $term_name ) {
			$result = array(
				'name' => $term_name,
			);

			// Get existing terms by the same name.
			$args  = array(
				'name' => trim( $term_name ),
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
		// Ensure terms with comma separated terms are broken out into separate terms.
		// TODO: Remove this split logic when feeder is updated to treat comma separated input as separate terms.
		array_unique(
			array_reduce(
				$categories,
				fn( $carry, $term_name ) => array_merge( $carry, explode( ',', $term_name ) ),
				array()
			)
		)
	);

	return $item;
}

/**
 * Parse API response body into normalized data.
 *
 * @param array  $api_body Response body from API request.
 * @param string $post_type Post type to check for existing data.
 * @return array Normalized item data as associative arrays.
 */
function tw_episode_importer_parse_api_items( $api_body, $post_type ) {

	$items = $api_body->_embedded->{'prx:items'};

	if ( ! $items || empty( $items ) ) {
		return null;
	}

	$episodes = array_map(
		function ( $item ) use ( $post_type ) {
			return tw_episode_importer_parse_api_item( $item, $post_type );
		},
		$items
	);

	return $episodes;
}

/**
 * Get post id using GUID.
 *
 * @param string $post_type Post type to lookup ID for.
 * @param string $guid Post guid to lookup ID for.
 * @param string $title Post title to lookup ID for.
 * @param string $date_published_string Post publish date string to lookup ID for.
 * @param string $audio_url Audio url to lookup ID for.
 * @return array|null
 */
function tw_episode_importer_get_post( $post_type, $guid, $title, $date_published_string, $audio_url ) {
	global $wpdb;

	$cache_key = 'post_id_for_guid:' . $audio_url;
	$id        = wp_cache_get( $cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );
	$post      = null;
	$result    = null;

	if ( ! $id ) {
		$date_published = $date_published_string ? new DateTime( $date_published_string ) : new DateTime();
		$one_day        = new DateInterval( 'P1D' );
		$from_date      = gmdate( 'Y-m-d H:i:s', strtotime( $date_published->format( 'Y-m-d' ) ) );
		$to_date        = gmdate( 'Y-m-d H:i:s', strtotime( $date_published->add( $one_day )->format( 'Y-m-d' ) ) );
		$prepared_query = $wpdb->prepare(
			"SELECT p.*
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pmbd ON pmbd.post_id = p.ID
				AND pmbd.meta_key = 'broadcast_date'
			LEFT JOIN {$wpdb->postmeta} pma ON pma.post_id = p.ID
				AND pma.meta_key = 'audio'
			LEFT JOIN {$wpdb->postmeta} pmaou ON pmaou.post_id = pma.meta_value
				AND pmaou.meta_key = 'original_uri'
			WHERE pmbd.meta_value BETWEEN %s AND %s
			AND p.post_type = %s
			AND (p.guid = %s OR p.post_title = %s OR pmaou.meta_value = %s);",
			array( $from_date, $to_date, $post_type, $guid, $title, $audio_url )
		);
		// phpcs:ignore
		$row = $wpdb->get_row( $prepared_query );
		if ( $row ) {
			error_log( $row->ID . ' >>> ' . $row->post_title );
			$id = $row->ID;
			wp_cache_set( $cache_key, $row->ID, TW_EPISODE_IMPORTER_CACHE_GROUP );
		}
	}

	if ( $id ) {
		$post = get_post( $id );
	}

	if ( $post ) {
		$audio_id       = get_field( 'audio', $post->ID );
		$audio_post     = get_post( $audio_id );
		$audio_metadata = get_metadata( 'post', $audio_id );
		$result         = array(
			'guid'       => $post->guid,
			'databaseId' => $post->ID,
			'audio'      => $audio_post ? array(
				'databaseId' => $audio_post->ID,
				'url'        => $audio_metadata['original_uri'][0],
			) : null,
		);
	}

	return $result;
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
