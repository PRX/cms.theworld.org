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

	$guid                     = $api_item->guid;
	$title                    = $api_item->title;
	$date_published           = $api_item->publishedAt; //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$date_updated             = $api_item->updatedAt; //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$audio_url                = $api_item->_links->enclosure->href;
	$audio_url_segments       = explode( '/', $audio_url );
	$audio_filename           = array_pop( $audio_url_segments );
	list($audio_name)         = explode( '.', $audio_filename );
	$episode_key              = null;
	$audio_key                = null;
	$audio_year               = null;
	$audio_month              = null;
	$audio_day                = null;
	$audio_segment            = null;
	$audio_version            = 'original';
	$audio_broadcast_date_key = null;
	$audio_broadcast_date     = null;
	$audio_name_matches       = array();

	if ( preg_match( '~^((\d{4})[_-]?(\d{2})[_-]?(\d{2})(?:[_-](\d{2}|seg[_-]?\d|full|.+)))(?:[_-]?(.+))?$~i', $audio_name, $audio_name_matches ) ) {
		if ( count( $audio_name_matches ) > 6 ) {
			list(, $audio_key, $audio_year, $audio_month, $audio_day, $audio_segment, $audio_version) = $audio_name_matches;
		} else {
			list(, $audio_key, $audio_year, $audio_month, $audio_day, $audio_segment) = $audio_name_matches;
		}

		$episode_key_segments = array(
			$audio_year,
			$audio_month,
			$audio_day,
		);

		if ( is_numeric( $audio_segment ) || preg_match( '~^seg[_-]?\d|full$~i', $audio_segment ) ) {
			$episode_key_segments[] = 'TW';
		} else {
			$episode_key_segments[] = strtoupper( $audio_segment );
		}

		$episode_key = implode( '_', $episode_key_segments );

		$audio_broadcast_date_key = $audio_year . '-' . $audio_month . '-' . $audio_day;
		$audio_broadcast_date     = new DateTime( $audio_broadcast_date_key );
		$audio_query              = new WP_Query(
			array(
				'post_type' => 'attachment',
				'name'      => $audio_key,
			)
		);
		$audio_post               = $audio_query->have_posts() ? reset( $audio_query->posts ) : null;
	}

	$posts              = tw_episode_importer_get_existing_post_data( $post_type, $guid, $audio_key ); //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$audio              = is_array( $posts ) && isset( $posts['audio'] ) ? $posts['audio'] : null;
	$posts              = is_array( $posts ) && isset( $posts['posts'] ) ? $posts['posts'] : null;
	$post               = $posts ? array_filter(
		$posts,
		function ( $p ) use ( $post_type ) {
			return $p['type'] === $post_type;
		}
	) : null;
	$post               = is_array( $post ) && ! empty( $post ) ? $post[0] : null;
	$was_imported       = is_array( $post ) && isset( $post['guid'] ) ? $post['guid'] === $guid : false;
	$audio_is_different = is_array( $audio ) && isset( $audio['url'] ) && $api_item->_links->enclosure->href !== $audio['url'];
	$has_updated_audio  = is_array( $audio ) &&
		isset( $audio['dateUpdated'] ) &&
		$audio_is_different;

	$item = array(
		'existingPosts'   => $posts,
		'existingPost'    => $post,
		'existingAudio'   => $audio,
		'wasImported'     => $was_imported,
		'hasUpdatedAudio' => $has_updated_audio,
		'id'              => $api_item->id,
		'guid'            => $guid,
		'title'           => $title,
		'excerpt'         => $api_item->subtitle ?? null,
		'content'         => $api_item->description ?? null,
		'datePublished'   => $date_published,
		'dateUpdated'     => $date_updated ?? null,
		'dateBroadcast'   => $audio_broadcast_date ? $audio_broadcast_date->format( 'c' ) : null,
		'dateKey'         => $audio_broadcast_date_key,
		'author'          => $api_item->author && property_exists( $api_item->author, 'name' ) ? (array) $api_item->author : null,
		'enclosure'       => array_merge(
			(array) $api_item->_links->enclosure,
			array(
				'episodeKey' => $episode_key,
				'audioKey'   => $audio_key,
				'segment'    => (int) $audio_segment,
				'version'    => $audio_version,
			)
		),
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
 * @param string $audio_key Audio key to lookup ID for.
 * @return array|null
 */
function tw_episode_importer_get_existing_post_data( $post_type, $guid, $audio_key ) {
	global $wpdb;

	$post_ids_cache_key   = 'post_ids_for_guid:' . $guid;
	$audio_id_cache_key   = 'audio_id_for_guid:' . $guid;
	$ids                  = wp_cache_get( $post_ids_cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );
	$audio_id             = wp_cache_get( $audio_id_cache_key, TW_EPISODE_IMPORTER_CACHE_GROUP );
	$audio_broadcast_date = null;
	$audio_post           = null;
	$post                 = null;
	$result               = null;

	if ( $audio_id ) {
		$audio_post = get_post( $audio_id );
	}

	// Attempt to get imported audio by guid.
	if ( is_null( $audio_post ) ) {
		$audio_query = new WP_Query(
			array(
				'post_type' => 'attachment',
				'guid'      => $guid,
			)
		);
		$audio_post  = $audio_query->have_posts() ? reset( $audio_query->posts ) : null;
	}

	// Attempt to get existing audio by using audio key derived from filename.
	if ( is_null( $audio_post ) && $audio_key ) {
			$audio_query = new WP_Query(
				array(
					'post_type' => 'attachment',
					'name'      => $audio_key,
				)
			);
			$row         = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT *
					FROM wp_posts
					WHERE post_name LIKE %s;',
					array( preg_replace( '~(?<=^\d{4}[_-]\d{2})[_-]?(?=\d\d[_-])~', '%', $audio_key ) . '%' ),
				)
			);
			// $audio_post  = $audio_query->have_posts() ? reset( $audio_query->posts ) : null;
			$audio_post = $row ? $row : null;
	}

	if ( ! is_null( $audio_post ) ) {
		$audio_metadata  = get_metadata( 'post', $audio_post->ID );
		$result['audio'] = array(
			'guid'          => $audio_post->guid,
			'databaseId'    => $audio_post->ID,
			'editLink'      => get_edit_post_link( $audio_post, 'link' ),
			'datePublished' => $audio_post->post_date,
			'dateUpdated'   => $audio_post->post_modified,
			'url'           => $audio_metadata['original_uri'][0],
		);
		wp_cache_set( $audio_id_cache_key, $audio_post->ID, TW_EPISODE_IMPORTER_CACHE_GROUP );
	}

	// Get ids of posts that audio is attached to via their 'audio' meta field.
	if ( ! $ids && ! is_null( $audio_post ) ) {
		$posts_query = new WP_Query(
			array(
				// Audio will always be attached to the episode or segment post,
				// but may also be attached to a story post.
				'post_type'  => array( $post_type, 'post' ),
				'meta_key'   => 'audio',
				'meta_value' => $audio_post->ID,
				'fields'     => 'ids',
				'orderby'    => 'type',
			)
		);

		if ( $posts_query->have_posts() ) {
			$ids = $posts_query->posts;
			wp_cache_set( $post_ids_cache_key, $ids, TW_EPISODE_IMPORTER_CACHE_GROUP );
		}
	}

	if ( is_array( $ids ) ) {
		foreach ( $ids as $id ) {
			$post = get_post( $id );

			if ( $post ) {
				$audio_id       = get_field( 'audio', $post->ID );
				$audio_post     = get_post( $audio_id );
				$audio_metadata = get_metadata( 'post', $audio_id );

				$result['posts'][] = array(
					'guid'          => $post->guid,
					'databaseId'    => $post->ID,
					'type'          => $post->post_type,
					'editLink'      => get_edit_post_link( $post, 'link' ),
					'datePublished' => $post->post_date,
					'dateUpdated'   => $post->post_modified,
					'audio'         => $audio_post ? array(
						'guid'          => $audio_post->guid,
						'databaseId'    => $audio_post->ID,
						'editLink'      => get_edit_post_link( $audio_post, 'link' ),
						'datePublished' => $audio_post->post_date,
						'dateUpdated'   => $audio_post->post_modified,
						'url'           => $audio_metadata['original_uri'][0],
					) : null,
				);
			}
		}
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
