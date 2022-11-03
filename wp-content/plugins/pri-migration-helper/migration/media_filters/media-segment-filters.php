<?php
/**
 * Migration filters
 *
 * @package WordPress
 */

/**
 * Action runs after media creation.
 *
 * @param int   $attachment_id Media ID.
 * @param array $related_attachment_ids Related medias.
 * @return void
 */
function pmh_post_add_related_files( $attachment_id, $related_attachment_ids ) {

	if ( $related_attachment_ids ) {

		$segment_post_ids = array();

		foreach ( $related_attachment_ids as $related_attachment_id ) {

			$attachment          = get_post( $related_attachment_id );
			$attachment_metadata = wp_get_attachment_metadata( $related_attachment_id );
			$audio_node_id       = get_post_meta( $related_attachment_id, 'fid', true );

			$attachment_data = array(
				'post_type'   => 'segment',
				'post_title'  => get_post_meta( $related_attachment_id, 'audio_title', true ),
				'post_status' => 'publish',
			);

			if ( isset( $attachment->post_content ) ) {

				$attachment_data['post_content'] = $attachment->post_content;
			}

			$segment_post_id = wp_insert_post( $attachment_data );

			if ( ! is_wp_error( $segment_post_id ) ) {

				$segment_post_ids[] = $segment_post_id;

				update_post_meta( $segment_post_id, 'fid', $audio_node_id );
				update_post_meta( $segment_post_id, 'audio', $related_attachment_id );

				// File audio metas.
				update_post_meta( $segment_post_id, 'audio_title', get_post_meta( $related_attachment_id, 'audio_title', true ) );
				update_post_meta( $segment_post_id, 'audio_type', get_post_meta( $related_attachment_id, 'audio_type', true ) );
				update_post_meta( $segment_post_id, 'program', get_post_meta( $related_attachment_id, 'program', true ) );
				update_post_meta( $segment_post_id, 'broadcast_date', get_post_meta( $related_attachment_id, 'broadcast_date', true ) );
				update_post_meta( $segment_post_id, 'expiration_date', get_post_meta( $related_attachment_id, 'expiration_date', true ) );
				update_post_meta( $segment_post_id, 'related_files', get_post_meta( $related_attachment_id, 'related_files', true ) );
				update_post_meta( $segment_post_id, 'transcript', get_post_meta( $related_attachment_id, 'transcript', true ) );
				update_post_meta( $segment_post_id, '_media_credit', get_post_meta( $related_attachment_id, '_media_credit', true ) );

				// Contributor.
				$contributor = wp_get_post_terms( $related_attachment_id, 'contributor', array( 'fields' => 'ids' ) );
				wp_set_object_terms( $segment_post_id, $contributor, 'contributor', true );

				// Program.
				$program = (int) get_post_meta( $related_attachment_id, 'program', true );
				wp_set_object_terms( $segment_post_id, array( $program ), 'program', true );

				if ( isset( $attachment_metadata['file'] ) ) {

					update_post_meta( $segment_post_id, '_fgd2wp_old_file', $attachment_metadata['file'] );
				}
			}
		}

		if ( $segment_post_ids ) {

			update_post_meta( $attachment_id, 'segments_list', $segment_post_ids );
		}
	}
}
add_action( 'pmh_post_add_related_files', 'pmh_post_add_related_files', 10, 2 );
