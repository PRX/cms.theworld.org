<?php
/**
 * TW - Endpoint Helper
 *
 * @package       PEH
 * @author        dinkuminteractive
 *
 * @wordpress-plugin
 * Plugin Name:   TW Endpoint Helper
 * Plugin URI:    https://www.dinkuminteractive.com/
 * Description:   Endpoint helper.
 * Version:       1.0.2
 * Author:        dinkuminteractive
 * Author URI:    https://www.dinkuminteractive.com/
 * Update URI:    https://www.dinkuminteractive.com/
 * Text Domain:   peh
 */

/**
 * Get includes.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-peh-alias-object.php';

/**
 * Route definitions.
 */
define( 'PEH_MAIN_ROUTE', 'tw/v2' );
define( 'PEH_ROUTE_ALIAS', '/alias' );

/**
 * Initialize custom REST.
 *
 * @return void
 */
function peh_rest_api_init() {

	$args_alias = array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'peh_route_alias',
		'permission_callback' => '__return_true',
		'args'     => array(
			// slug is the alias.
			'slug' => array(
				'validate_callback' => 'peh_args_validation_callback',
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'peh_data_arg_sanitize_callback',
			),
        ),
	);

	register_rest_route(
		PEH_MAIN_ROUTE,
		PEH_ROUTE_ALIAS,
		$args_alias
	);
}
add_action( 'rest_api_init', 'peh_rest_api_init' );

/**
 * Validate param.
 *
 * @param string $value
 * @param WP_REST_Request $request
 * @param string $key
 * @return bool
 */
function peh_args_validation_callback( $value, $request, $key ) {

	$value = sanitize_text_field( $value );

	// If the 'filter' argument is not a string return an error.
    if ( ! is_string( $value ) || ! $value ) {
        return new WP_Error( 'rest_invalid_param', esc_html__( 'TW - Alias or type supplied is required or invalid.', 'peh' ), array( 'status' => 400 ) );
    }

	return true;
}

/**
 * Sanitize function.
 *
 * @param string $value
 * @param WP_REST_Request $request
 * @param string $param
 * @return void
 */
function peh_data_arg_sanitize_callback( $value, $request, $param ) {
    // It is as simple as returning the sanitized value.
    return sanitize_text_field( $value );
}

/**
 * Custom rest callback.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function peh_route_alias( WP_REST_Request $request ) {

	$response = array(
		'status' => 200,
		'data'   => array(),
	);

	// Slug is the alias.
	$slug = $request->get_param( 'slug' );

	if ( empty( $slug ) ) {

		$response['status'] = 400;

	} else {

		// Check from redirect table.
		$alias_object = _peh_get_object( $slug );
		if ( $alias_object instanceof Peh_Alias_Object ) {

			if ( $alias_object->is_external() ) {

				$response = $alias_object->get_external_response_array();

			} else {

				// Load entity.
				$route = $alias_object->get_rest_route();
				if ( $route ) {

					try {

						// Do WP REST request.
						$request  = new WP_REST_Request( 'GET', $route );
						$response = rest_do_request( $request );
						// Return the response as it is since it is a WordPress API response or a WordPress error from its API.
						return rest_ensure_response( $response );

					} catch ( \Exception $e ) {

						$response['exception'] = 'true';

						if ( $e && method_exists( $e, 'getCode' ) ) {

							$response['status'] = $e->getCode();

						} else {

							$response['status'] = 500;
						}
					}
				} else {

					$response['status'] = 500;
				}
			}
		} else {

			$response['status'] = 404;
		}
	}

	return peh_get_response( $response );
}

/**
 * Get standardized object of alias.
 *
 * @param string $slug
 * @return mixed
 */
function _peh_get_object( $slug ) {

	$alias_object = apply_filters( 'peh_get_object_filters', false, $slug );

	// Check from redirect table.
	// $object = _peh_get_object_by_redirect_db( $slug );

	// if ( ! $object ) {

	// 	// Check from posts.
	// 	$object = _peh_get_object_by_slug( $slug );

	// 	if ( ! $object ) {

	// 		// Check from taxonomy.
	// 		$object = _peh_get_object_by_taxonomy( $slug );
	// 	}
	// }

	if ( isset( $alias_object->id, $alias_object->type ) ) {

		$alias_object = new Peh_Alias_Object( $alias_object );
	}

   return $alias_object;
}

/**
 * Filter object.
 *
 * @param mixed $object
 * @param string $slug
 * @return mixed
 */
function peh_maybe_object_wp_migrated_legacy_redirect_db( $object, $slug ) {

	return $object ? $object : _peh_get_object_by_wp_migrated_legacy_redirect_db( $slug );
}
add_filter( 'peh_get_object_filters', 'peh_maybe_object_wp_migrated_legacy_redirect_db', 0, 2 );

/**
 * Filter object.
 *
 * @param mixed $object
 * @param string $slug
 * @return mixed
 */
function peh_maybe_object_wp_fg_redirect_db( $object, $slug ) {

	return $object ? $object : _peh_get_object_by_wp_fg_redirect_db( $slug );
}
add_filter( 'peh_get_object_filters', 'peh_maybe_object_wp_fg_redirect_db', 5, 2 );

/**
 * Filter object.
 *
 * @param mixed $object
 * @param string $slug
 * @return mixed
 */
function peh_maybe_object_post( $object, $slug ) {

	return $object ? $object : _peh_get_object_by_slug( $slug );
}
add_filter( 'peh_get_object_filters', 'peh_maybe_object_post', 10, 2 );

/**
 * Filter object.
 *
 * @param mixed $object
 * @param string $slug
 * @return mixed
 */
function peh_maybe_object_terms( $object, $slug ) {

	return $object ? $object : _peh_get_object_by_taxonomy( $slug );
}
add_filter( 'peh_get_object_filters', 'peh_maybe_object_terms', 15, 2 );

/**
 * Filter object for posts migrated as terms.
 *
 * @param mixed $object
 * @param string $slug
 * @return mixed
 */
function peh_maybe_post_moved_to_object_terms( $object ) {
	if ( isset( $object->type ) && in_array( $object->type, array( 'program', 'contributor', 'person' ) ) ) {
		$args = array(
			'hide_empty' => false, // also retrieve terms which are not used yet
			'meta_query' => array(
				array(
				   'key'       => "_pri_old_wp_{$object->type}_id",
				   'value'     => $object->id,
				   'compare'   => '='
				)
			),
			'fields'  => 'ids',
		);
		$terms = get_terms( $args );
		if ( $terms ) {
			$object->id   = $terms[0];
		}
	}
	return $object;
}
add_filter( 'peh_get_object_filters', 'peh_maybe_post_moved_to_object_terms', 20, 2 );

/**
 * Get post id from wp_fg_redirect table.
 *
 * @param array $slug string
 * @return bool|int
 */
function _peh_get_object_by_wp_migrated_legacy_redirect_db( $slug ) {

	global $wpdb;

	$row = $wpdb->get_row( "SELECT `uid` AS `id`, `type`, `redirect` FROM `wp_migrated_legacy_redirect` WHERE `source` = '$slug' LIMIT 1;" );
	if ( isset( $row->type ) && 'redirect' === $row->type && wp_http_validate_url( $row->redirect ) ) {
		$row->is_external = true;
	} elseif( $row && isset( $row->redirect ) && $row->redirect )  {
		$row = apply_filters( 'peh_get_object_filters', false, $row->redirect );
	}
	return $row;
}

/**
 * Get post id from wp_fg_redirect table.
 *
 * @param array $slug string
 * @return bool|int
 */
function _peh_get_object_by_wp_fg_redirect_db( $slug ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( "SELECT `id`, `type` FROM `wp_fg_redirect` WHERE `old_url` = '%s' LIMIT 1;", $slug ) );
}

/**
 * Get post by slug.
 *
 * @param string $slug
 * @return void
 */
function _peh_get_object_by_slug( $slug ) {

    $args = array(
        'name'           => $slug,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
    );

    $objects = get_posts( $args );

    if ( $objects ) {

		$object       = new stdClass();
		$object->id   = $objects[ 0 ]->ID;
		$object->type = $objects[ 0 ]->post_type;

        return $object;
    }

    return false;
}

/**
 * Get tax by slug.
 *
 * @param string $slug
 * @return void
 */
function _peh_get_object_by_taxonomy( $slug ) {

	$object = false;

	// depending of the resource type, check in taxonomy.
	if ( $term_id = term_exists( $slug ) ) {

		$taxonomy = get_term_field( 'taxonomy', $term_id );
		$object   = new stdClass();

		$object->id   = $term_id;
		$object->type = $taxonomy;
	}

	return $object;
}

/**
 * Function to get response array based on $response argument.
 *
 * @param array $response
 * @return array $response
 */
function peh_get_response( array $response ) {

    $status = $response['status'];

    $errors = array(
        400 => array( 'rest_no_route', 'TW - No argument supplied.' ),
        403 => array( 'rest_no_route', 'TW - Resource not found or forbiden.' ),
        404 => array( 'rest_no_route', 'TW - No route was found matching the URL and request method.' ),
        500 => array( 'rest_internal_error', 'TW - 500 Internal Error.' ),
	);

    $error = isset( $errors[$status] ) ?? false;

    return $error
        ? new WP_Error( $error[0], __( $error[1], 'peh'), $response )
        : new WP_REST_Response( $response['data'], $status );
}
