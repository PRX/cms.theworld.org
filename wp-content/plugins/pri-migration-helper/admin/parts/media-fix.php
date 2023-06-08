<?php

?>
<p>This tool will attempt to fix media sizes and image tags in posts.</p>

<table id="pmh-media-fix-form" class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="pmh-media-fix-paged"><?php esc_html_e( 'Paged', 'pmh' ); ?></label></th>
			<td>
				<input type="number" name="pmh-media-fix-paged" value="1" min="1" readonly>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-media-fix-perpage"><?php esc_html_e( 'Per Page', 'pmh' ); ?></label></th>
			<td>
				<input type="number" name="pmh-media-fix-perpage" value="50" min="1">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-media-fix-ids"><?php esc_html_e( 'Per Ids', 'pmh' ); ?></label></th>
			<td>
				<textarea name="pmh-media-fix-ids" id="pmh-media-fix-ids" cols="80" rows="5"></textarea>
				<p><i>Separated by a newline. Leave empty to process all.</i></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-media-last-id"><?php esc_html_e( 'Last ID', 'pmh' ); ?></label></th>
			<td>
				<input type="number" name="pmh-media-last-id" value="<?php echo get_option( 'pmh_last_mediafix_id', 1 ); ?>" min="1" readonly>
				<p><i>The last media ID that was processed.</i></p>
			</td>
		</tr>

		<tr>
			<th scope="row">Run Process</th>
			<td>
				<!-- <input type="submit" name="pmh-post-worker-media-sample" id="pmh-post-worker-media-sample" class="button button-primary" value="View Sample"> -->
				<input type="submit" name="pmh-post-worker-media-fix" id="pmh-post-worker-media-fix" class="button button-primary" value="Fix Media Size">
				<p><i>Update media width and height metadata with Drupal media database.</i></p>
			</td>
		</tr>
		<tr>
			<th scope="row"></th>
			<td>
				<input type="submit" name="pmh-post-worker-posts-fix" id="pmh-post-worker-posts-fix" class="button button-primary" value="Fix Image Tags Inside Posts" disabled>
				<p><i>Update post content image tags. Warning: Run this only on posts with correct media sizes.</i></p>
			</td>
		</tr>
	</tbody>
</table>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="pmh-post-worker-logs"><?php esc_html_e( 'Process Logs', 'pmh' ); ?></label></th>
			<td>
				<textarea name="pmh-post-worker-logs" id="pmh-post-worker-logs" cols="80" rows="20"></textarea>
			</td>
		</tr>
	</tbody>
</table>
