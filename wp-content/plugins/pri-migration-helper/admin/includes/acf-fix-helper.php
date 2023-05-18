<?php
function pri_get_field_key( $post_type ) {
	$args = array(
		'post_type'      => 'acf-field-group',
		'posts_per_page' => -1,
	);
	$fields = array();

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();

			// Get field group location rules
			$location_rules = get_field('location', get_the_ID());

			foreach ($location_rules as $location_group) {
				foreach ($location_group as $location_rule) {
					if ($location_rule['param'] == 'post_type' && $location_rule['operator'] == '==' && $location_rule['value'] == $post_type) {
						$fields[] = acf_get_fields(get_the_ID());
					}
				}
			}
		}
		wp_reset_postdata(); // Reset the post data to avoid conflicts with other loops
	}
	return $fields;
}
function pri_get_term_meta_keys_and_values( $type = null, $field = null ) {
	// the _ at the beginning of the name was added to match the way ACF stores the data.
	$meta_keys = array(
		'category' => array(
			'_teaser' => 'field_622a166c5d7e1',
			'_hosts' => 'field_622a3175fb705',
		),
		'program' => array(
			'_teaser' => 'field_622a166c5d7e1',
			'_hosts' => 'field_622a3175fb705',
		),
		'contributor' => array(
			'_teaser' => 'field_623ccb1dfcb75',
			'_blog' => 'field_623ccc3387beb',
			'_facebook' => 'field_623ccbb012bd9',
			'_get_in_touch' => 'field_623ccc4e94dbf',
			'_email' => 'field_623ccc4e94dbf',
			'_podcast' => 'field_623ccc238bbe9',
			'_tumblr' => 'field_623ccc15ce693',
			'_twitter' => 'field_623ccbd685718',
			'_website' => 'field_623ccc442086f',
			'_rss' => 'field_623ccc5cce3d4',
			'_program' => 'field_623cca1414076',
			'_hosts' => 'field_622a3175fb705',
		)
	);

	if ( $type && $field && isset( $meta_keys[$type][$field] ) ) {
		return $meta_keys[$type][$field];
	}

	return $meta_keys;

	// foreach ( pri_get_field_key( 'attachment' ) as $group) {
	// 	if ( is_array( $group ) ) {
	// 		foreach ( $group as $field ) {
	// 			if ( in_array( $field['name'], $meta_keys ) ) {
	// 				return array( 'name' => "_{$field['name']}", 'key' => $field['key'] );  );
	// 			}
	// 		}
	// 	}
	// }
	// return array();
}
function pri_get_post_meta_keys_and_values( $type = null, $field = null ) {
	$meta_keys = array(
		'images' => array(
			'_original_uri' => 'field_62b4862efaff1',
			'_fid' => 'field_62b48652faff2',
			'_image_title' => 'field_62d671c653456',
			'_hide_image' => 'field_622a6312b1b5d',
		),
		'audio' => array(
			'_original_uri' => 'field_62b4862efaff1',
			'_fid' => 'field_62b48652faff2',
			'_audio_title' => 'field_622a69ed0dfde',
			'_audio_type' => 'field_622a69fd0dfdf',
			'_broadcast_date' => 'field_622a6a928a4f6',
			'_program' => 'field_622a6a5bbd407',
			'_expiration_date' => 'field_622a6ac11faf8',
			'_transcript' => 'field_622a6ba1511d4',
		),
		'segment' => array(
			// '_audio_title', NO ACF FIELD
			// '_audio_type', NO ACF FIELD
			// '_program', NO ACF FIELD
			// '_media_credit', NO ACF FIELD
			// '_related_files', NO ACF FIELD
			'_audio' => 'field_62850f267d649',
			'_broadcast_date' => 'field_62850d45bcb78',
			'_expiration_date' => 'field_62850d71bcb79',
			'_transcript' => 'field_62850f67a80b5',
		),
		'episode' => array(
			'_hosts' => 'field_622a111626720',
			'_producers' => 'field_622a116e3e41b',
			'_guests' => 'field_622a11ceed329',
			'_reporters' => 'field_622a11a2747d2',
		),
	);

	if ( $type && $field ) {
		if ( isset( $meta_keys[$type][$field] ) ) {
			return $meta_keys[$type][$field];
		}
		return false;
	}

	return $meta_keys;
}
