<div class="wrap">

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-1">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h2><span><?php esc_attr_e( 'URLs to compare', 'pri-migration-helper' ); ?></span></h2>

						<div class="inside">
							<form id="json-diff-form" method="post" action="">
								<input type="hidden" value="Y" class="regular-text" name="json_form_submitted" id="json_form_submitted"/>
								<table class="form-table">
									<tr valign="top">
										<td scope="row" class="row-title">
											<label for="tablecell"><?php esc_attr_e( 'URL 1:', 'pri-migration-helper' ); ?></label>
										</td>
										<td scope="row" class="row-title">
											<label for="tablecell"><?php esc_attr_e( 'URL 2:', 'pri-migration-helper' ); ?></label>
										</td>
									</tr>
									<tr valign="top">
										<td style="width: 50%;"><textarea id="json_url_1" name="json_url_1" rows="10" class="large-text"><?php echo $json_url_1; ?></textarea></td>
										<td style="width: 50%;"><textarea id="json_url_2" name="json_url_2" rows="10" class="large-text"><?php echo $json_url_2; ?></textarea></td>
									</tr>
									<tr>
										<td scope="row" colspan="2">
											<label class="row-title" for="tablecell"><?php esc_attr_e( 'Row: ', 'pri-migration-helper' ); ?></label>
											<input id="json_url_row" class="small-text" name="json_url_row" type="number" min="1" value="1">
										</td>
									</tr>
								</table>
								<p>
									<input class="button-primary" type="submit" name="input_json_url_submit" value="<?php esc_attr_e( 'Compare', 'pri-migration-helper' ); ?>" />
									<button id="input_json_url_clear" class="button-secondary">Clear Results</button>
								</p>
							</form>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>


						<br class="clear" />
						<div id="json-diff-notice">
						<?php if ( $json_diff_results ) : ?>
							<div class="notice notice-success inline">
								<p>Log</p>
							</div>
						<?php endif; ?>
						</div>
						<table class="widefat accordion">
							<thead>
								<tr>
									<th class="row-title">Line</th>
									<th class="row-title">URL 1</th>
									<th class="row-title">URL 2</th>
									<th class="row-title">Equals</th>
									<th class="row-title">Diff</th>
								</tr>
							</thead>
							<tbody id="json-diff-ajax-return">

								<?php if ( $json_diff_results ) : ?>

									<?php foreach ( $json_diff_results as $json_diff_result ) : ?>

										<tr>
											<td><?php echo $json_diff_result['line']; ?></td>
											<td><?php echo $json_diff_result['url_1']; ?></td>
											<td><?php echo $json_diff_result['url_2']; ?></td>
											<td><?php echo $json_diff_result['count_print']; ?></td>
											<td><?php echo $json_diff_result['patch']; ?></td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
						<br class="clear" />


			</div>
			<!-- post-body-content -->



		</div>
		<!-- #post-body .metabox-holder .columns-1 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
