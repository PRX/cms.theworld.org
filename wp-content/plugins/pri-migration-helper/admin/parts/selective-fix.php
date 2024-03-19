<?php
if ( isset( $_POST['pmh-selective-fix'] ) && '1' === $_POST['pmh-selective-fix'] ) {

	// Save content types default.
	$set_selective_fix_content_types = array();

	// Save post node ids.
	if ( isset( $_POST['pmh-post-fix-ids'] ) ) {

		update_option( 'tw_get_nodes_story_target_ids', sanitize_textarea_field( $_POST['pmh-post-fix-ids'] ) );

		if (  ! empty( $_POST['pmh-post-fix-ids'] ) ) {

			$set_selective_fix_content_types[] = 'story';
		}
	}

	// Save episode node ids.
	if ( isset( $_POST['pmh-episode-fix-ids'] ) ) {

		update_option( 'tw_get_nodes_episode_target_ids', sanitize_textarea_field( $_POST['pmh-episode-fix-ids'] ) );

		if (  ! empty( $_POST['pmh-episode-fix-ids'] ) ) {

			$set_selective_fix_content_types[] = 'episode';
		}
	}

	// Save content types.
	update_option( 'tw_selective_fix_content_types', $set_selective_fix_content_types );
}

// Get node ids.
$post_node_ids = get_option( 'tw_get_nodes_story_target_ids' );
$episode_node_ids = get_option( 'tw_get_nodes_episode_target_ids' );

// Current selected content types.
$selective_fix_content_types = get_option( 'tw_selective_fix_content_types', array() );
$selective_fix_content_types_string = $selective_fix_content_types ? implode( ', ', $selective_fix_content_types ) : 'None';
?>
<p>This tool will scope the import process to selected IDs only.</p>
<p>Current selected content types: <?php echo $selective_fix_content_types_string; ?></p>

<form method="POST">
	<table id="pmh-episode-fix" class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="pmh-post-fix-ids"><?php esc_html_e( 'Story nid', 'pmh' ); ?></label></th>
				<td>
					<textarea name="pmh-post-fix-ids" id="pmh-post-fix-ids" cols="80" rows="5"><?php echo $post_node_ids; ?></textarea>
					<p><i>Separated by comma (,). Leave empty to process all.</i></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="pmh-episode-fix-ids"><?php esc_html_e( 'Episode nid', 'pmh' ); ?></label></th>
				<td>
					<textarea name="pmh-episode-fix-ids" id="pmh-episode-fix-ids" cols="80" rows="5"><?php echo $episode_node_ids; ?></textarea>
					<p><i>Separated by comma (,). Leave empty to process all.</i></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<input type="hidden" name="pmh-selective-fix" value="1">
		<input type="submit" class="button button-primary" value="Save Settings">
	</p>
</form>
