<?php
class Peh_Alias_Object {

	/**
	 * Resource ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Resource type.
	 *
	 * @var string
	 */
	public $type = null;


	/**
	 * Resource external.
	 *
	 * @var string
	 */
	public $is_external = false;

	/**
	 * Resource external.
	 *
	 * @var string
	 */
	public $external_url = '';

	/**
	 * Create object.
	 *
	 * @param stdClass $alias_object
	 */
	public function __construct( $alias_object ) {

		$this->id   = $alias_object->id;
		$this->type = $alias_object->type;

		if ( isset( $alias_object->is_external ) ) {

			$this->is_external  = $alias_object->is_external;
			$this->external_url = $alias_object->redirect;
		}
	}

	/**
	 * @TODO: How to determine external links?
	 *
	 * @return boolean
	 */
	public function is_external() {
		return $this->is_external;
	}


	/**
	 * Get formatted route.
	 *
	 * @return string
	 */
	public function get_rest_route() {

		$rest_route = '';

		if ( $this->type && $this->id ) {
			// No duplicated post type and taxonomy type will be allowed.
			// Priorityze taxonomy because we need to detect person and program taxonomy first because we are using them for the import.
			// bundle: taxonomies
				// Post Tag
				// 'post_tag':
				// Category
				// 'category':
				// Person
				// 'person':
				// 'city':
				// 'continent':
				// 'contributor':
				// 'country':
				// 'license':
				// 'person':
				// 'program':
				// 'province_or_state':
				// 'region':
				// 'social_tags':
				// 'story_format':
				// 'resource_development':
			$prefix = $this->get_taxonomy_rest_base( $this->type );
			if ( ! $prefix ) {
				// bundle: audio or file
				// 'media':
				// 'episode':
				// 'segment':
				// bundle: story
				// Post
				// 'post':
				// bundle: page
				// 'page':
				$prefix = $this->get_post_rest_base( $this->type );
			}
			$rest_route = $prefix ? "/wp/v2/{$prefix}/{$this->id}" : '';
		}

		return $rest_route;
	}

	/**
	 * Get formatted route.
	 *
	 * @return string
	 */
	public function get_post_rest_base( $type ) {
		$post_type = get_post_type_object( $type );
		if ( null !== $post_type ) {
			if ( ! empty( $post_type->rest_base ) ) {
				return $post_type->rest_base;
			}
			return $type;
		}
		return null;
	}

	/**
	 * Get formatted route.
	 *
	 * @return string
	 */
	public function get_taxonomy_rest_base( $taxonomy ) {
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( false !== $taxonomy_object ) {
			if ( ! empty( $taxonomy_object->rest_base ) ) {
				return $taxonomy_object->rest_base;
			}
			return $taxonomy;
		}
		return null;
	}


	/**
	 * Get formatted route.
	 *
	 * @return string
	 */
	public function get_external_response_array() {

		if ( ! $this->is_external() ) {
			return false;
		}

		$response = array(
			'status' => 200,
			'data'   => array(
				'type'       => 'redirect--external',
				'id'         => $this->id,
				'attributes' => array(
					'url' => $this->external_url,
				),
			),
		);

		return $response;
	}
}
