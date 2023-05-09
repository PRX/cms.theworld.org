<?php

?>
<p>This tool will attempt to fix ACF fields in WP objects.</p>

<table id="pmh-acf-fix-form" class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="pmh-acf-fix-paged"><?php esc_html_e( 'Paged', 'pmh' ); ?></label></th>
			<td>
				<input type="number" name="pmh-acf-fix-paged" value="1" min="1" readonly>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-acf-fix-perpage"><?php esc_html_e( 'Per Page', 'pmh' ); ?></label></th>
			<td>
				<input type="number" name="pmh-acf-fix-perpage" value="50" min="1">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-acf-fix-ids"><?php esc_html_e( 'Per Ids', 'pmh' ); ?></label></th>
			<td>
				<textarea name="pmh-acf-fix-ids" id="pmh-acf-fix-ids" cols="80" rows="5"></textarea>
				<p><i>Separated by comma (,). Leave empty to process all.</i></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-acf-fix-ids"><?php esc_html_e( 'Post Type', 'pmh' ); ?></label></th>
			<td>
				<select name="pmh-acf-fix-post-type" id="pmh-acf-fix-post-type" onchange="updateAcfFieldSelect()">
					<option value="audio">Post - audio</option>
					<option value="episode">Post - episode</option>
					<option value="images">Post - images</option>
					<option value="segment">Post - segment</option>
					<option value="category">Term - category</option>
					<option value="contributor">Term - contributor</option>
					<option value="program">Term - program</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pmh-acf-field"><?php esc_html_e( 'Field to fix', 'pmh' ); ?></label></th>
			<td>
			<select name="pmh-acf-field" id="pmh-acf-field">
				<!-- Options will be populated by JavaScript -->
			</select>
			</td>
		</tr>

		<tr>
			<th scope="row">Run Process</th>
			<td>
				<input type="submit" name="pmh-post-worker-acf-fix" id="pmh-post-worker-acf-fix" class="button button-primary" value="Fix ACF">
				<p><i>Add ACF meta fields to selected objects. This will expose ACF fields correctly in REST.</i></p>
			</td>
		</tr>
	</tbody>
</table>

<p class="submit">
</p>

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
