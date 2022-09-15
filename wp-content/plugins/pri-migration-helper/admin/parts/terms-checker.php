<?php
  $default_tab = null;
  $tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : $default_tab;
?>
  <div class="wrap">

	<nav class="nav-tab-wrapper">
	  <a href="?page=pmh-admin-terms-checker" class="nav-tab 
	  <?php
		if ( $tab === null ) :
			?>
			nav-tab-active<?php endif; ?>">Taxonomy</a>
	  <a href="?page=pmh-admin-terms-checker&tab=other" class="nav-tab 
	  <?php
		if ( $tab === 'other' ) :
			?>
			nav-tab-active<?php endif; ?>">Other</a>
	</nav>

	<div class="tab-content">
	<?php
	switch ( $tab ) :
		case 'other':
			?>
		<div class="card">
			  <?php echo wp_sprintf( '<h2 class="title">%s</h2>', __( 'Other tab' ) ); ?>
			<p>Nothing to see here yet.</p>
		</div>
			<?php
			break;
		default:
			?>

		<div class="card">
			<h2 class="title"><?php esc_attr_e( 'Taxonomy term checker', 'pri-migration-helper' ); ?></h2>
			<form method="post" action="">
				<input type="hidden" value="Y" class="regular-text" name="checker_form_submitted" id="checker_form_submitted"/>
				<table class="form-table">
					<tr valign="top">
						<td scope="row"><label for="tablecell">
						<?php
						esc_attr_e(
							'Select taxonomy to check',
							'pri-migration-helper'
						);
						?>
								</label></td>
						<td>
							<select name="checker_form_taxonomy" id="checker_form_taxonomy">
								<option value="">Choose</option>
								  <?php
									if ( ! empty( $taxonomies ) ) :
										foreach ( $taxonomies as $taxonomy ) :
											?>
									<option <?php echo ( $checker_form_taxonomy == $taxonomy->name ) ? 'selected' : ''; ?> value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
											<?php
										endforeach;
								  endif;
									?>
							</select>
						</td>
					</tr>
				</table>
				<p>
					<input class="button-primary" type="submit" name="checker_form_input_submit" value="<?php esc_attr_e( 'Check', 'pri-migration-helper' ); ?>" />
				</p>
			</form>
		</div>
		
			<?php
			if ( $hidden_field == 'Y' ) :
				if ( ! empty( $duplicated_term ) ) :
					?>
			<br class="clear" />
			<div class="notice notice-error inline">
				<p>
						<?php
						printf(
							esc_html__( 'Duplicated term found: ', 'pri-migration-helper' )
						);
						?>
				</p>
			</div>
			<table class="widefat">
				<thead>
					<tr>
						<th class="row-title">ID</th>
						<th class="row-title">Name</th>
						<th class="row-title">Slug</th>
						<th class="row-title">Number of posts</th>
					</tr>
				</thead>
				<tbody>
						<?php foreach ( $duplicated_term as $term ) : ?>
					<tr>
						<td><?php echo $term->term_id; ?></td>
						<td><?php echo $term->name; ?></td>
						<td><?php echo $term->slug; ?></td>
						<td><?php echo $term->count; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<br class="clear" />
					<?php
					$edit_url = 'edit-tags.php?taxonomy=' . $checker_form_taxonomy;
					?>
			<a class="button-primary" href="<?php echo admin_url( $edit_url ); ?>">Go to taxonomy page</a>
					<?php
			else :
				?>
		<br class="clear" />
		<div class="notice notice-success inline">
			<p>
				  <?php
					printf(
						esc_html__( 'No duplicated term found.', 'pri-migration-helper' )
					);
					?>
			</p>
		</div>
				<?php
		  endif;
		  endif;
			?>
		
			<?php
			break;
	endswitch;
	?>
	</div>
  </div>
