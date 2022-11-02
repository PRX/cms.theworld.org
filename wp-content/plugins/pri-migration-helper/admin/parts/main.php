<?php
/**
 * Main display.
 *
 * @package WordPress
 */

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Post Worker', 'pmh' ); ?></h2>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="pmh-post-worker-object-type"><?php esc_html_e( 'Object Type', 'pmh' ); ?></label></th>
				<td>
					<select name="pmh-post-worker-object-type" id="pmh-post-worker-object-type">
						<option value=""><?php esc_html_e( 'Select Object Type', 'pmh' ); ?></option>
						<option value="post-type"><?php esc_html_e( 'Post Type', 'pmh' ); ?></option>
						<option value="taxonomy"><?php esc_html_e( 'Taxonomy', 'pmh' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="pmh-post-worker-object-name"><?php esc_html_e( 'Object Name', 'pmh' ); ?></label></th>
				<td>
					<select name="pmh-post-worker-object-name" id="pmh-post-worker-object-name">
						<option value=""><?php esc_html_e( 'Select Object Type', 'pmh' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="pmh-post-worker-object-name"><?php esc_html_e( 'Sample', 'pmh' ); ?></label></th>
				<td style="max-height: 240px; overflow: scroll; display: inline-block;">
					<table style="width: 320px;" id="pmh-post-worker-sample-table-content">
						<tr>
							<td style="border: 1px solid #000;"><strong>old_url</strong></td>
							<td style="border: 1px solid #000;"><strong>id</strong></td>
							<td style="border: 1px solid #000;"><strong>type</strong></td>
							<td style="border: 1px solid #000;"><strong>activated</strong></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="pmh-post-worker-process-post-total"><?php esc_html_e( 'Total Object', 'pmh' ); ?></label></th>
				<td>
					<input type="text" name="pmh-post-worker-process-post-total" id="pmh-post-worker-process-post-total" value="0" disabled>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="pmh-post-worker-paged-process"><?php esc_html_e( 'Paged Process', 'pmh' ); ?></label></th>
				<td>
					<input
						type="number"
						name="pmh-post-worker-paged-process"
						id="pmh-post-worker-paged-process"
						value="1"
						min="1"
					>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit"><input type="submit" name="pmh-post-worker-run-process" id="pmh-post-worker-run-process" class="button button-primary" value="Run Process"></p>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="pmh-post-worker-logs"><?php esc_html_e( 'Process Logs', 'pmh' ); ?></label></th>
				<td>
					<textarea name="pmh-post-worker-logs" id="pmh-post-worker-logs" cols="80" rows="10"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit"><input type="submit" name="pmh-post-worker-logs-delete" id="pmh-post-worker-logs-delete" class="button button-primary" value="Delete Logs"></p>
</div>
