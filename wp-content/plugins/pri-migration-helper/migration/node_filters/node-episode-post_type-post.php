<?php
/**
 * Migration filters
 *
 * @package WordPress
 */

/**
 * Exclude the custom fields we don't want to import from the list of custom fields to be created in ACF
 *
 * @param array  $custom_fields Custom fields.
 * @param string $node_type Drupal node type.
 * @return array Custom fields
 */
function pri_episode_fgd2wp_get_custom_fields( $custom_fields, $node_type ) {
	if ( 'episode' === $node_type ) {
		foreach ( $custom_fields as $field_name => $field ) {
			if ( pri_episode_field_to_exclude( $field_name ) ) {
				unset( $custom_fields[ $field_name ] );
			}
		}
	}
	return $custom_fields;
}
add_filter( 'fgd2wp_get_custom_fields', 'pri_episode_fgd2wp_get_custom_fields', 10, 2 );

/**
 * Migration fields filter to avoid calling database to get fields info
 * Good to check if necessary because we are filtering custom field creating when starting the import with the filter to fgd2wp_get_custom_fields
 *
 * @uses fgd2wp_get_custom_fields
 * @param bool   $default default value.
 * @param string $custom_field_name Custom field name.
 * @return bool true or false depending if the field should be processed or no.
 */
function pri_fgd2wp_import_episode_field( $default, $custom_field_name ) {
	if ( pri_episode_field_to_exclude( $custom_field_name ) ) {
		// If this field should not be imported, return nil to avoid inserting the ACF.
		return false;
	}
	return $default;
}
add_filter( 'fgd2wp_import_episode_field', 'pri_fgd2wp_import_episode_field', 10, 2 );

/**
 * Migration field redirection.
 *
 * @param array $args Custom field arguments.
 * @return array $args Custom field arguments.
 */
function pmh_filter__node_episode__field( $args ) {

	list( $custom_field_name, $custom_field, $custom_field_values ) = $args;

	// REMINDER: $custom_field_name is the field name without field_ prefix.
	switch ( $custom_field_name ) {
		case 'file_audio':
			$custom_field_name = 'audio';
			break;

		case 'date_broadcast':
			$custom_field_name = 'broadcast_date';
			break;

		case 'music_heard_on_air_body':
			$custom_field_name = 'music_heard_on_air';
			break;

		case 'teaser':
			$custom_field_name = 'excerpt';
			break;

		default:
			// code...
			break;
	}

	return array( $custom_field_name, $custom_field, $custom_field_values );
}

/**
 * Compare if a field should be excluded from a list of fields.
 *
 * @since 1.1.3
 *
 * @param string $field_name Field name.
 * @return bool
 */
function pri_episode_field_to_exclude( $field_name ) {
	$do_not_import_fields = array(
		'is_this_a_podcast_',
		'you_may_also_enjoy',
		'hashtag_or_header',
		'terms_migration_flags',
		'int_migrate_source_id',
	);
	return in_array( $field_name, $do_not_import_fields, true );
}

/**
 * Hook for creating the term in case there were no custom fields to process.
 *
 * @param int    $new_post_id  The new post ID.
 * @param array  $node         Array of info from Drupal Node.
 * @param string $post_type    Target post type.
 * @param string $entity_type  Drupal post type?.
 * @return void
 */
function pri_migration_fgd2wp_episode_merge_content( $new_post_id, $node, $content_type, $post_type, $entity_type ) {
	if ( 'episode' !== $post_type || empty( $new_post_id ) ) {
		return;
	}
	// $content = get_post_field( 'post_content', $new_post_id, 'raw' );
	// $spotify = pri_convert_spotify_tracks( $new_post_id );
	// if ( $spotify ) {
	// $content .= $spotify;
	// }
	// $amazon = pri_convert_amazon_items( $new_post_id );
	// if ( $amazon ) {
	// $content .= $amazon;
	// }
	// $music = get_post_meta( $new_post_id, 'music_heard_on_air_body', true );
	// if ( $music ) {
	// $content .= $music;
	// }

	if ( ! empty( $content ) ) {
		wp_update_post(
			array(
				'ID'           => $new_post_id,
				'post_content' => $content,
			)
		);
	}
}
// The priority is 99 because it needs to be called after all the insert post methods were executed.
// add_action( 'fgd2wp_post_import_post', 'pri_migration_fgd2wp_episode_merge_content', 90, 5 );


/**
 * Hook for doing other actions after inserting the post
 *
 * @param int                             $new_post_id  The new post ID.
 * @param string                          $custom_field_name  Field Name.
 * @param array                           $custom_field         Array of info from Drupal Field.
 * @param array                           $custom_field_values    Custom Field Values.
 * @param string                          $date  Date value.
 * @param \FG_Drupal_to_WordPress_CPT_ACF $plugin_cpt  ACF Plugin CPT value.
 * @return void
 */
function pri_pmh_episode_convert_custom_post_field( $meta_values, $custom_field, $custom_field_values, $new_post_id, $date ) {
	if ( empty( $meta_values ) || ( isset( $custom_field['node_type'] ) && 'episode' !== $custom_field['node_type'] ) ) {
		return $meta_values;
	}
	switch ( $custom_field['field_name'] ) {
		case 'field_audio_explicit':
			foreach ( $meta_values as $key => $value ) {
				if ( 'yes' === $value[0] ) {
					$meta_values[ $key ][0] = 'explicit';
				} elseif ( 'no' === $value[0] ) {
					$meta_values[ $key ][0] = 'clean';
				}
			}
			// foreach ( $custom_field_values as $key => $value ) {
			// if ( 'yes' === $value['field_audio_explicit_value'] ) {
			// $custom_field_values[ $key ]['field_audio_explicit_value'] = 'explicit';
			// } elseif ( 'no' === $value['field_audio_explicit_value'] ) {
			// $custom_field_values[ $key ]['field_audio_explicit_value'] = 'clean';
			// }
			// }
			break;
		case 'field_ref_program':
			pri_pmh_episode_set_program_taxonomy( $new_post_id, $meta_values );
			break;
		case 'field_ref_episode_stories':
			pri_pmh_episode_set_ref_story( $new_post_id, $meta_values );
			break;
		case 'field_ref_hosts':
			pri_pmh_episode_set_taxonomy( $new_post_id, 'hosts', $meta_values, 'person' );
			break;
		case 'field_ref_producers':
			pri_pmh_episode_set_taxonomy( $new_post_id, 'producers', $meta_values, 'person' );
			break;
		case 'field_ref_guests':
			pri_pmh_episode_set_taxonomy( $new_post_id, 'guests', $meta_values, 'person' );
			break;
		case 'field_ref_reporters':
			pri_pmh_episode_set_taxonomy( $new_post_id, 'reporters', $meta_values, 'person' );
			break;

		default:
			return $meta_values;
	}
	return $meta_values;
}
add_filter( 'fgd2wp_convert_custom_field_to_meta_values', 'pri_pmh_episode_convert_custom_post_field', 10, 5 );


/**
 * Hook for doing other actions after inserting the post
 *
 * @param int                             $new_post_id  The new post ID.
 * @param string                          $custom_field_name  Field Name.
 * @param array                           $custom_field         Array of info from Drupal Field.
 * @param array                           $custom_field_values    Custom Field Values.
 * @param string                          $date  Date value.
 * @param \FG_Drupal_to_WordPress_CPT_ACF $plugin_cpt  ACF Plugin CPT value.
 * @return void
 */
function pri_pmh_episode_set_custom_post_field( $new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date, $plugin_cpt ) {
	if ( empty( $new_post_id ) || 'episode' !== $custom_field['node_type'] ) {
		return;
	}
	if ( 'ref_program' === $custom_field_name ) {
		// If this is a reference field.
		pri_pmh_episode_set_program_taxonomy( $new_post_id, $custom_field_name, $custom_field_values );
	}
}
// add_action( 'pmh_set_custom_post_field', 'pri_pmh_episode_set_custom_post_field', 10, 6 );

/**
 * Convert amazon items to track url;
 *
 * @since 1.1.3
 *
 * @param int $new_post_id Post ID to get Spotify tracks with the format spotify:track:TRACK_ID.
 * @return string
 */
function pri_convert_amazon_items( $new_post_id ) {
	$item_content = '';
	$amazon_items = get_field( 'collection-amazon_item', $new_post_id );
	if ( $amazon_items ) {
		foreach ( $amazon_items as $amazon_item ) {
			if ( empty( $amazon_item['amazon_item'] ) ) {
				continue;
			}
			ob_start();
			?>
				<?php echo esc_url( $amazon_item['amazon_item'] ); ?>
			<?php
			$item_content .= ob_get_contents();
		}
		ob_end_clean();
	}
	return $item_content;
}

/**
 * Convert spotify track to track url;
 *
 * @since 1.1.3
 *
 * @param int $new_post_id Post ID to get Spotify tracks with the format spotify:track:TRACK_ID.
 * @return string
 */
function pri_convert_spotify_tracks( $new_post_id ) {
	$track_content  = '';
	$spotify_tracks = get_field( 'collection-spotify_playlist', $new_post_id );
	if ( $spotify_tracks ) {
		foreach ( $spotify_tracks as $spotify_track ) {
			if ( empty( $spotify_track['spotify_playlist'] ) ) {
				continue;
			}
			$spotify_track_url = str_replace( 'spotify:track:', 'https://open.spotify.com/track/', $spotify_track['spotify_playlist'] );
			$track_content    .= sprintf(
				'<!-- wp:embed {"url":"%s","type":"rich","providerNameSlug":"spotify","responsive":true,"className":"wp-embed-aspect-21-9 wp-has-aspect-ratio"} -->
			<figure class="wp-block-embed is-type-rich is-provider-spotify wp-block-embed-spotify wp-embed-aspect-21-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
				%s
			</div></figure>
			<!-- /wp:embed -->',
				esc_url( $spotify_track_url ),
				esc_url( $spotify_track_url )
			);
		}
	}
	return $track_content;
}

/**
 * Add support amazon for asin custom field.
 *
 * @since 1.1.3
 *
 * @param array  $default_columns Default table columns.
 * @param string $default_table_name Database table name in drupal database.
 * @param array  $data Field data from Drupal field, it is not content.
 * @param string $field_type The field type in Drupal.
 * @return array
 */
function pri_fgd2wp_get_field_amazon_columns( $default_columns, $default_table_name, $data, $field_type ) {
	if ( 'asin_text' === $field_type ) {
		$default_columns = array(
			'value' => str_replace( 'field_data_', '', $default_table_name ) . '_asin',
		);
	}
	return $default_columns;
}
add_filter( 'fgd2wp_get_field_columns', 'pri_fgd2wp_get_field_amazon_columns', 10, 4 );

/**
 * Add support amazon for spotify list custom field.
 *
 * @since 1.1.3
 *
 * @param array  $default_columns Default table columns.
 * @param string $default_table_name Database table name in drupal database.
 * @param array  $data Field data from Drupal field, it is not content.
 * @param string $field_type The field type in Drupal.
 * @return array
 */
function pri_fgd2wp_get_field_spotify_columns( $default_columns, $default_table_name, $data, $field_type ) {
	if ( 'spotify_play_button_single' === $field_type ) {
		$default_columns = array(
			'value' => str_replace( 'field_data_', '', $default_table_name ) . '_uri',
		);
	}
	return $default_columns;
}
add_filter( 'fgd2wp_get_field_columns', 'pri_fgd2wp_get_field_spotify_columns', 10, 4 );

/**
 * Set relationship with different taxonomies.
 *
 * @since 1.1.3
 *
 * @param int    $episode_post_id New episode post id inserted.
 * @param string $meta_key Field meta key for relationship.
 * @param array  $meta_values Meta values to convert into term id.
 * @param string $taxonomy Term taxonomy.
 * @return void
 */
function pri_pmh_episode_set_taxonomy( $episode_post_id, $meta_key, $meta_values, $taxonomy ) {
	if ( $meta_values ) {
		$term_meta_value = array();
		foreach ( $meta_values as $related_post_id ) {
			$program_term_id = get_post_meta( intval( $related_post_id ), "_pri_new_wp_term_{$taxonomy}_id", true );
			if ( $program_term_id ) {
				$term_meta_value[] = $program_term_id;
			}
		}
		if ( $term_meta_value ) {
			$current_values = get_post_meta( $episode_post_id, $meta_key, true );
			if ( $current_values && is_array( $current_values ) ) {
				$term_meta_value = array_unique( array_merge( $current_values, $term_meta_value ) );
			}
			update_post_meta( $episode_post_id, $meta_key, $term_meta_value );
		}
	}
}

/**
 * Relate the episode with the correspondant program taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $episode_post_id New episode post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_episode_set_program_taxonomy( $episode_post_id, $meta_values ) {
	if ( $meta_values ) {
		$program_term_id = get_post_meta( intval( $meta_values[0] ), '_pri_new_wp_term_program_id', true );
		if ( $program_term_id ) {
			wp_set_object_terms( $episode_post_id, intval( $program_term_id ), 'program', false );
		}
	}
}
/**
 * Relate the episode with the correspondant program taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $episode_post_id New episode post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_episode_set_ref_story( $episode_post_id, $meta_values ) {
	if ( $meta_values ) {
		$story_post_id = isset( $meta_values[0] ) ? $meta_values[0] : null;
		if ( $story_post_id ) {
			$related_stories = get_field( 'related_stories', $episode_post_id );
			if ( $related_stories && is_array( $related_stories ) ) {
				$related_stories[] = $story_post_id;
			} else {
				$related_stories = array( $story_post_id );
			}
			update_field( 'related_stories', $related_stories, $episode_post_id );
		}
	}
}

/**
 * collection field name.
 *
 * @since 1.1.3
 *
 * @param int   $episode_post_id New episode post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function episode_pmh_set_collection_field_name( $custom_field_name ) {
	if ( $custom_field_name && 'collection-amazon_item' === $custom_field_name ) {
		$custom_field_name = 'amazon_items';
	}
	if ( $custom_field_name && 'collection-spotify_playlist' === $custom_field_name ) {
		$custom_field_name = 'spotify_playlists';
	}
	return $custom_field_name;
}

add_filter( 'pmh_set_collection_field_name', 'episode_pmh_set_collection_field_name' );
