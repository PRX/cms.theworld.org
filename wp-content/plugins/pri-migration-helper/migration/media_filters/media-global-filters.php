<?php
/**
 * Migration filters
 *
 * @package WordPress
 */

/**
 * Import media from Drupal by file ID.
 *
 * @param mixed $identifier String for file name or Int for fid.
 * @return int $attachment_id
 */
function pmh_custom_import_media( $identifier ) {

	global $fgd2wpp;

	$attachment_id = false;

	$sql_condition = '';

	if ( is_int( $identifier ) ) {

		$sql_condition = "fm.fid = {$identifier}";
	}

	if ( is_string( $identifier ) ) {

		$sql_condition = "fm.uri = \"{$identifier}\"";
	}

	if ( ! $sql_condition ) {
		return false;
	}

	$sql_file_managed = "
	SELECT
		fm.fid,
		fm.filename,
		fm.uri,
		fm.timestamp,
		fm.type,
		f.field_description_value AS description

	FROM
		file_managed fm

	LEFT JOIN
		field_data_field_description f
		ON fm.fid = f.entity_id

	WHERE
		{$sql_condition}

	ORDER BY
		f.delta
	";

	$sql_file_managed_results = $fgd2wpp->drupal_query( $sql_file_managed );
	$sql_file_managed_result  = isset( $sql_file_managed_results[0] ) ? $sql_file_managed_results[0] : false;

	if ( $sql_file_managed_result ) {

		$date = date( 'Y-m-d H:i:s' );

		// Import media
		$file_date        = isset( $sql_file_managed_result['timestamp'] ) ? date( 'Y-m-d H:i:s', $sql_file_managed_result['timestamp'] ) : $date;
		$file_date        = apply_filters( 'fgd2wp_get_custom_field_file_date', $file_date, $date );
		$image_attributes = array(
			'description' => isset( $sql_file_managed_result['description'] ) ? $sql_file_managed_result['description'] : '',
		);

		if ( 'audio' === $sql_file_managed_result['type'] ) {

			$sql_file_managed_result = apply_filters( 'pmh_get_file_attributes_audio', $sql_file_managed_result );
		}

		if ( 'image' === $sql_file_managed_result['type'] || 'video' === $sql_file_managed_result['type'] ) {

			$sql_file_managed_result = apply_filters( 'pmh_get_file_attributes_images', $sql_file_managed_result );
		}

		$image_attributes = apply_filters( 'pmh_image_attributes', $image_attributes, $sql_file_managed_result );

		$filename = preg_replace( '/\..*$/', '', basename( $sql_file_managed_result['filename'] ) );

		$attachment_id = $fgd2wpp->import_media( $filename, $fgd2wpp->get_path_from_uri( $sql_file_managed_result['uri'] ), $file_date, $image_attributes );
	}

	/*
	 Debug
	echo "<pre>";
	var_dump( 'pmh_custom_import_media' );
	var_dump( $sql_file_managed_result );
	var_dump( $attachment_id );
	echo "</pre>";
	 */

	return $attachment_id;
}

/**
 * Import a media
 *
 * @param string $name Image name.
 * @param string $filename Image URL.
 * @param date   $date Date.
 * @param array  $attributes Image attributes (image_alt, image_caption).
 * @param array  $options Options.
 * @return int attachment ID or false
 */
function pmh_import_media( $name, $filename, $date = '0000-00-00 00:00:00', $attributes = array(), $options = array() ) {

	/*
	 Debug
	echo "<pre>pmh_import_media";
	var_dump( $filename );
	var_dump( pmh_get_wp_media_id_by_fid( $attributes['fid'] ) );
	echo "</pre>";
	 */

	// Return early if file was already imported.
	if ( isset( $attributes['fid'] ) ) {

		$wp_media = pmh_get_wp_media_id_by_fid( $attributes['fid'] );

		if ( $wp_media ) {

			return $attributes['fid'];
		}
	}

	// Import and setup media without download.
	$true_file_path = $filename;

	$attributes['original_uri'] = isset( $attributes['uri'] ) ? $attributes['uri'] : '';

	$attachment_id = pmh_add_external_media_without_import( $true_file_path, $attributes, $options );

	return $attachment_id;
}

/**
 * Get image and images extra attributes.
 *
 * @param array $attributes File attributes.
 * @return array $attributes File attributes.
 */
function pmh_get_file_attributes_images( $attributes ) {

	global $fgd2wpp;

	$fid = $attributes['fid'];

	// Get file data.
	$sql_1 = "
	SELECT
		fm.fid AS fid,
		fm.uid AS uid,
		fm.filemime AS filemime,
		fm.filesize AS filesize,
		fm.type AS type,
		fd.field_description_value AS description,
		fiat.field_file_image_alt_text_value AS image_alt,
		fitt.field_file_image_title_text_value AS image_title,
		fc.field_credit_value AS credit,
		fhi.field_hide_image_value AS hide_image,
		ttd.name AS license,
		fcap.field_caption_value AS caption

	FROM
		file_managed AS fm

	LEFT JOIN
		field_data_field_description AS fd
	ON fm.fid = fd.entity_id

	LEFT JOIN
	field_data_field_file_image_alt_text AS fiat
	ON fm.fid = fiat.entity_id

	LEFT JOIN
	field_data_field_file_image_title_text AS fitt
	ON fm.fid = fitt.entity_id

	LEFT JOIN
		field_data_field_credit AS fc
	ON fm.fid = fc.entity_id

	LEFT JOIN
		field_data_field_hide_image AS fhi
	ON fm.fid = fhi.entity_id

	LEFT JOIN
		field_data_field_licence AS fl
	ON fm.fid = fl.entity_id

	LEFT JOIN
		taxonomy_term_data AS ttd
	ON fl.field_licence_tid = ttd.tid

	LEFT JOIN
		field_data_field_caption fcap
		ON fm.fid = fcap.entity_id

	WHERE
		fm.fid = {$fid}

	ORDER BY
		fd.delta

	LIMIT 1
	";

	$results_1 = $fgd2wpp->drupal_query( $sql_1 );

	if ( count( $results_1 ) > 0 ) {
		$attributes['fid']         = $results_1[0]['fid'];
		$attributes['uid']         = $results_1[0]['uid'];
		$attributes['filemime']    = $results_1[0]['filemime'];
		$attributes['filesize']    = $results_1[0]['filesize'];
		$attributes['type']        = $results_1[0]['type'];
		$attributes['description'] = $results_1[0]['description'];
		$attributes['image_alt']   = $results_1[0]['image_alt'];
		$attributes['image_title'] = $results_1[0]['image_title'];
		$attributes['credit']      = $results_1[0]['credit'];
		$attributes['hide_image']  = $results_1[0]['hide_image'];
		$attributes['license']     = $results_1[0]['license'];
		$attributes['caption']     = $results_1[0]['caption'];
	}

	return $attributes;
}
add_filter( 'pmh_get_file_attributes_images', 'pmh_get_file_attributes_images' );

/**
 * Get audio extra attributes.
 *
 * @param array $attributes File attributes.
 * @return array $attributes File attributes.
 */
function pmh_get_file_attributes_audio( $attributes ) {

	global $fgd2wpp;

	$fid = $attributes['fid'];

	// Get file data.
	$sql_1 = "
	SELECT
		fm.fid AS fid,
		fm.uid AS uid,
		fm.filemime AS filemime,
		fm.filesize AS filesize,
		fm.type AS type,
		fd.field_description_value AS description,
		fat.field_audio_title_value AS audio_title,
		fatp.field_audio_type_value AS audio_type,
		fap.field_audio_program_target_id AS program,
		fdb.field_date_broadcast_value AS broadcast_date,
		fde.field_date_expires_value AS expiration_date,
		faa.field_audio_author_target_id AS contributor,
		fc.field_credit_value AS credit,
		ft.field_transcript_value AS transcript


	FROM
		file_managed AS fm

	LEFT JOIN
		field_data_field_description AS fd
	ON fm.fid = fd.entity_id

	LEFT JOIN
		field_data_field_audio_title AS fat
	ON fm.fid = fat.entity_id

	LEFT JOIN
		field_data_field_audio_type AS fatp
	ON fm.fid = fatp.entity_id

	LEFT JOIN
		field_data_field_audio_program AS fap
	ON fm.fid = fap.entity_id

	LEFT JOIN
		field_data_field_date_broadcast AS fdb
	ON fm.fid = fdb.entity_id

	LEFT JOIN
		field_data_field_date_expires AS fde
	ON fm.fid = fde.entity_id

	LEFT JOIN
		field_revision_field_audio_author AS faa
	ON fm.fid = faa.entity_id

	LEFT JOIN
		field_data_field_credit AS fc
	ON fm.fid = fc.entity_id

	LEFT JOIN
		field_data_field_transcript AS ft
	ON fm.fid = ft.entity_id

	WHERE
		fm.fid = {$fid}

	ORDER BY
		fd.delta

	LIMIT 1
	";

	$results_1 = $fgd2wpp->drupal_query( $sql_1 );

	if ( count( $results_1 ) > 0 ) {
		$attributes['fid']             = $results_1[0]['fid'];
		$attributes['uid']             = $results_1[0]['uid'];
		$attributes['filemime']        = $results_1[0]['filemime'];
		$attributes['filesize']        = $results_1[0]['filesize'];
		$attributes['type']            = $results_1[0]['type'];
		$attributes['description']     = $results_1[0]['description'];
		$attributes['audio_title']     = $results_1[0]['audio_title'];
		$attributes['audio_type']      = $results_1[0]['audio_type'];
		$attributes['program']         = $results_1[0]['program'];
		$attributes['broadcast_date']  = $results_1[0]['broadcast_date'];
		$attributes['expiration_date'] = $results_1[0]['expiration_date'];
		$attributes['contributor']     = $results_1[0]['contributor'];
		$attributes['credit']          = $results_1[0]['credit'];
		$attributes['transcript']      = $results_1[0]['transcript'];
	}

	// Get extra data.
	$sql_extra = "
	SELECT
		field_segments_target_id AS segments_target_id
	FROM
		field_data_field_segments
	WHERE
		entity_id = {$fid}
	";

	$results_extra = $fgd2wpp->drupal_query( $sql_extra );

	$attributes['related_files'] = array();

	if ( count( $results_extra ) > 0 ) {

		$segments = array();

		foreach ( $results_extra as $result_extra ) {
			$segments[] = $result_extra['segments_target_id'];
		}

		$attributes['related_files'] = $segments;
	}

	return $attributes;
}
add_filter( 'pmh_get_file_attributes_audio', 'pmh_get_file_attributes_audio' );

/**
 * Get the media caption
 *
 * @since 3.8.0
 *
 * @param array  $custom_field_values Values.
 * @param int    $entity_id             Values.
 * @param string $node_type          Values.
 * @param string $custom_field       Values.
 * @param string $entity_type        Values.
 * @return array Values
 */
function pmh_get_file_attributes( $custom_field_values, $entity_id, $node_type, $custom_field, $entity_type ) {

	$supported_module = array( 'file', 'image' );

	if ( isset( $custom_field['module'] ) && in_array( $custom_field['module'], $supported_module, true ) ) {

		global $fgd2wpp;

		$prefix = $fgd2wpp->plugin_options['prefix'];

		if ( is_array( $custom_field_values ) && $custom_field_values ) {

			foreach ( $custom_field_values as &$value ) {

				if ( isset( $value['fid'] ) ) {

					$fid = $value['fid'];

					// Image extra field.
					$image_table_names = array(
						'field_data_field_image',
						'field_data_field_images',
						'field_data_field_image_banner',
						'field_data_field_podcast_logo',
						'field_data_field_syndication_badge',
					);

					if ( in_array( $custom_field['table_name'], $image_table_names ) ) {

						$value = apply_filters( 'pmh_get_file_attributes_images', $value );
					}

					// Audio.
					if ( 'field_data_field_file_audio' === $custom_field['table_name'] ) {

						$value = apply_filters( 'pmh_get_file_attributes_audio', $value );
					}

					// Video.
					// if ( 'field_data_field_video' === $custom_field['table_name'] ) {

					// Get file data.
					// $sql_1 = "
					// SELECT
					// fm.fid AS fid,
					// fm.filemime AS filemime,
					// fm.filesize AS filesize,
					// fm.type AS type,
					// fd.field_description_value AS description,
					// fc.field_credit_value AS credit

					// FROM
					// file_managed AS fm

					// LEFT JOIN
					// field_data_field_description AS fd
					// ON fm.fid = fd.entity_id

					// LEFT JOIN
					// field_data_field_credit AS fc
					// ON fm.fid = fc.entity_id

					// WHERE
					// fm.fid = {$fid}

					// ORDER BY
					// fd.delta

					// LIMIT 1
					// ";

					// $results_1 = $fgd2wpp->drupal_query( $sql_1 );

					// if ( count( $results_1 ) > 0 ) {
					// $value['fid']         = $results_1[0]['fid'];
					// $value['filemime']    = $results_1[0]['filemime'];
					// $value['filesize']    = $results_1[0]['filesize'];
					// $value['type']        = $results_1[0]['type'];
					// $value['description'] = $results_1[0]['description'];
					// $value['credit']      = $results_1[0]['credit'];
					// }
					// }

					// Get file metadata.
					$sql_2 = "
						SELECT
							fmd.name AS name,
							fmd.value AS value

						FROM
							file_metadata AS fmd

						WHERE
							fmd.fid = {$fid}
					";

					$results_2 = $fgd2wpp->drupal_query( $sql_2 );

					if ( count( $results_2 ) > 0 ) {
						foreach ( $results_2 as $result_2 ) {
							$val                        = str_replace( 'i:', '', $result_2['value'] );
							$val                        = str_replace( ';', '', $val );
							$value[ $result_2['name'] ] = (int) $val;
						}
					}
				}
			}
		}
	}

	return $custom_field_values;
}
add_filter( 'fgd2wp_get_custom_field_values', 'pmh_get_file_attributes', 10, 5 );

/**
 * Add extra image attributes.
 *
 * @param array $image_attributs image_attributs.
 * @param array $file file.
 * @return array $image_attributs image_attributs.
 */
function pmh_extra_image_attributes( $image_attributs, $file ) {

	$extra_attributes = array(
		'fid',
		'uid',
		'filemime',
		'filesize',
		'type',
		'description',
		'width',
		'height',
		'uri',
		'credit',
		'hide_image',
		'license',
		'audio_title',
		'audio_type',
		'program',
		'broadcast_date',
		'expiration_date',
		'contributor',
		'transcript',
		'related_files',
		'image_alt',
		'image_title',
		'caption',
	);

	foreach ( $extra_attributes as $key ) {

		if ( isset( $file[ $key ] ) ) {

			$image_attributs[ $key ] = is_string( $file[ $key ] ) ? wp_strip_all_tags( $file[ $key ] ) : $file[ $key ];
		}
	}

	return $image_attributs;
}
add_filter( 'pmh_image_attributes', 'pmh_extra_image_attributes', 10, 2 );


/**
 * Create a media without downloading.
 *
 * @param string $url Image URL.
 * @param array  $attributes Image attributes (image_alt, image_caption).
 * @param array  $options Options.
 * @return int attachment ID or false
 */
function pmh_add_external_media_without_import( $url, $attributes = array(), $options = array() ) {
	global $wpdb;
	$filename        = wp_basename( $url );
	$filesize        = isset( $attributes['filesize'] ) ? $attributes['filesize'] : '';
	$mime_type       = isset( $attributes['filemime'] ) ? $attributes['filemime'] : '';
	$width           = isset( $attributes['width'] ) ? $attributes['width'] : '';
	$height          = isset( $attributes['height'] ) ? $attributes['height'] : '';
	$description     = isset( $attributes['description'] ) ? $attributes['description'] : '';
	$image_caption   = isset( $attributes['image_caption'] ) ? $attributes['image_caption'] : '';
	$original_uri    = isset( $attributes['original_uri'] ) ? $attributes['original_uri'] : '';
	$fid             = isset( $attributes['fid'] ) ? $attributes['fid'] : '';
	$credit          = isset( $attributes['credit'] ) ? $attributes['credit'] : '';
	$hide_image      = isset( $attributes['hide_image'] ) ? $attributes['hide_image'] : '';
	$license         = isset( $attributes['license'] ) ? $attributes['license'] : '';
	$audio_title     = isset( $attributes['audio_title'] ) ? $attributes['audio_title'] : '';
	$audio_type      = isset( $attributes['audio_type'] ) ? $attributes['audio_type'] : '';
	$program         = isset( $attributes['program'] ) ? $attributes['program'] : '';
	$broadcast_date  = isset( $attributes['broadcast_date'] ) ? $attributes['broadcast_date'] : '';
	$expiration_date = isset( $attributes['expiration_date'] ) ? $attributes['expiration_date'] : '';
	$contributor     = isset( $attributes['contributor'] ) ? $attributes['contributor'] : '';
	$transcript      = isset( $attributes['transcript'] ) ? $attributes['transcript'] : '';
	$related_files   = isset( $attributes['related_files'] ) ? $attributes['related_files'] : array();
	$image_title     = isset( $attributes['image_title'] ) ? $attributes['image_title'] : '';
	$alt             = isset( $attributes['alt'] ) ? $attributes['alt'] : '';
	$caption		 = isset( $attributes['caption'] ) ? $attributes['caption'] : '';

	if ( empty( $alt ) ) {
		$alt = isset( $attributes['image_alt'] ) ? $attributes['image_alt'] : '';
	}

	/*
	 Debug
	var_dump( 'pmh_add_external_media_without_import' );
	var_dump( $url );
	var_dump( $attributes );
	var_dump( '============================' );
	 */

	// Insert into database.
	$attachment    = array(
		'post_mime_type' => $mime_type,
		'post_title'     => sanitize_title( preg_replace( '/\.[^.]+$/', '', $filename ) ),
		'post_content'   => wp_strip_all_tags( $description ),
		'post_excerpt'   => wp_strip_all_tags( $caption ),
	);
	$attachment    = apply_filters( 'fgd2wp_pre_insert_post', $attachment, $attributes );
	$attachment_id = wp_insert_attachment( $attachment );

	if ( ! is_wp_error( $attachment_id ) ) {

		// Add metadata.
		$attachment_metadata = array(
			'file'     => $filename,
			'filesize' => $filesize,
		);

		// Image dimension.
		if ( wp_attachment_is( 'image', $attachment_id ) ) {

			// Set width and height to 0.
			$attachment_metadata['width']  = $width;
			$attachment_metadata['height'] = $height;

			// If one of the dimension is missing.
			if ( ! $width || ! $height ) {

				// Get image size.
				$image_sizes = f_pmh_get_drupal_image_sizes( $fid );

				// If image sizes is found and isset.
				if (
					$image_sizes
					&&
					isset( $image_sizes['width'] )
					&&
					isset( $image_sizes['height'] )
				) {

					// Set width and height.
					$attachment_metadata['width']  = $image_sizes['width'];
					$attachment_metadata['height'] = $image_sizes['height'];
				}
			}

			$attachment_metadata['sizes']  = array( 'full' => $attachment_metadata );
		}

		/*
		Debug
		var_dump( 'pmh_add_external_media_without_import' );
		var_dump( $attachment );
		var_dump( $attachment_metadata );
		*/

		// Update attachment with metadata.
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
		if ( $alt ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
		}
		if ( $image_title ) {
			update_post_meta( $attachment_id, 'image_title', $image_title );
		}
		if ( $original_uri ) {
			update_post_meta( $attachment_id, 'original_uri', $original_uri );
		}
		if ( $fid ) {
			update_post_meta( $attachment_id, 'fid', $fid );
		}
		if ( $credit ) {
			update_post_meta( $attachment_id, '_media_credit', wp_strip_all_tags( $credit ) );
		}
		if ( $hide_image ) {
			update_post_meta( $attachment_id, 'hide_image', $hide_image );
		}
		if ( $license ) {
			update_post_meta( $attachment_id, 'license', $license );
		}
		if ( $audio_title ) {
			update_post_meta( $attachment_id, 'audio_title', $audio_title );
		}
		if ( $audio_type ) {
			update_post_meta( $attachment_id, 'audio_type', $audio_type );
		}
		if ( $broadcast_date ) {
			update_post_meta( $attachment_id, 'broadcast_date', $broadcast_date );
		}
		if ( $expiration_date ) {
			update_post_meta( $attachment_id, 'expiration_date', $expiration_date );
		}
		if ( $contributor ) {
			pri_pmh_media_set_contributor_taxonomy( $attachment_id, $contributor );
		}
		if ( $transcript ) {
			update_post_meta( $attachment_id, 'transcript', $transcript );
		}

		// Update attachment license.
		if ( $license ) {
			wp_set_object_terms( $attachment_id, array( $license ), 'license' );
		}

		// Update attachment program.
		if ( $program ) {

			$program_id = pmh_get_post_by_nid( $program );

			$program_term_id = get_post_meta( intval( $program_id ), '_pri_new_wp_term_program_id', true );

			update_post_meta( $attachment_id, 'program', $program_term_id );
		}

		// Process related files.
		if ( $related_files ) {

			$related_attachment_ids = array();

			foreach ( $related_files as $related_file ) {

				$related_attachment_id = pmh_custom_import_media( (int) $related_file );

				if ( $related_attachment_id ) {

					$related_attachment_ids[] = $related_attachment_id;
				}
			}

			if ( $related_attachment_ids ) {

				update_post_meta( $attachment_id, 'related_files', $related_attachment_ids );

				// Late trigger for media creation.
				do_action( 'pmh_post_add_related_files', $attachment_id, $related_attachment_ids );
			}
		}
	}

	// Update attachment guid
	// $wpdb->update( $wpdb->posts, array( 'guid' => $url ), array( 'ID' => $attachment_id ) );

	return $attachment_id;
}


/**
 * Relate the media with the correspondant contributor taxonomy.
 *
 * @since 1.1.3
 *
 * @param int   $media_post_id New story post id inserted.
 * @param array $meta_values Meta values to convert into term id.
 * @return void
 */
function pri_pmh_media_set_contributor_taxonomy( $media_post_id, $contributor_post_id ) {
	if ( $contributor_post_id ) {
		$contributor_term_id = get_post_meta( pmh_get_post_id_by_meta( '_fgd2wp_old_node_id', $contributor_post_id ), '_pri_new_wp_term_person_id', true );
		if ( $contributor_term_id ) {
			wp_set_object_terms( $media_post_id, intval( $contributor_term_id ), 'contributor', true );
		}
	}
}
