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

		$this->id          = $alias_object->id;
		$this->type        = $alias_object->type;

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

			switch ( $this->type ) {
				// bundle: audio or file
				case 'media':
					$prefix = 'media';
					break;
				case 'episode':
					$prefix = 'episode';
					break;
				case 'segment':
					$prefix = 'segment';
					break;
				// bundle: story
				// Post
				case 'post':
					$prefix = 'posts';
					break;
				// bundle: page
				case 'page':
					$prefix = 'pages';
					break;
				// bundle: taxonomies
				// Post Tag
				case 'post_tag':
					$prefix = 'tags';
					break;
				// Category
				case 'person':
					$prefix = 'contributor';
					break;
				case 'category':
					$prefix = 'categories';
					break;
				case 'city':
				case 'continent':
				case 'contributor':
				case 'country':
				case 'license':
				case 'program':
				case 'province_or_state':
				case 'region':
				case 'social_tags':
				case 'story_format':
				case 'resource_development':
					$prefix = $this->type;
					break;

				default:
					$prefix = '';
					break;
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
	public function get_external_response_array() {

		if ( ! $this->is_external() ) {
			return false;
		}

		$response = array(
			'status' => 301,
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
