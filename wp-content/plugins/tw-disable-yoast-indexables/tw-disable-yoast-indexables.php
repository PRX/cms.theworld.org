<?php
/**
 * Plugin Name: TW Disable Yoast Indexables
 * Description: Disabling Indexables completely to speed up the import process.
 *
 */

// Add submenu below the Yoast SEO menu item.
function tw_add_submenu_page() {
	add_submenu_page(
		'wpseo_dashboard',
		'TW Disable Yoast Indexables',
		'TW Disable Yoast Indexables',
		'manage_options',
		'tw-disable-yoast-indexables',
		'tw_disable_yoast_indexables_page'
	);
}
add_action( 'admin_menu', 'tw_add_submenu_page' );

// Disable Yoast SEO Indexables page. Show toggleable option.
function tw_disable_yoast_indexables_page() {

	$i_option_disable_yoast_indexables = get_option( 'tw-disable-yoast-indexables', 1 );

	?>
	<div class="wrap">
		<h1><?php _e( 'TW Disable Yoast Indexables', 'tw-disable-yoast-indexables' ); ?></h1>

		<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">

			<table class="form-table">

				<tr>
					<th scope="row"><?php _e( 'Disable Yoast Indexables' ); ?></th>
					<td>
						<input
							name="tw-disable-yoast-indexables"
							id="tw-disable-yoast-indexables"
							type="checkbox"
							value="disable-yoast-indexables"
							<?php checked( $i_option_disable_yoast_indexables ); ?>
						>
					</td>
				</tr>

				<tr>
					<td></td>
					<td>
						<input type="submit" value="Submit" class="button button-primary">
						<input type="hidden" name="action" value="tw-save-setting-disable-yoast-indexables">
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
}

// Save the option.
function tw_save_setting_disable_yoast_indexables() {

	$i_option_disable_yoast_indexables = isset( $_POST['tw-disable-yoast-indexables'] ) ? 1 : 0;

	update_option( 'tw-disable-yoast-indexables', $i_option_disable_yoast_indexables );

	wp_redirect( admin_url( 'admin.php?page=tw-disable-yoast-indexables' ) );

	exit;
}
add_action( 'admin_post_tw-save-setting-disable-yoast-indexables', 'tw_save_setting_disable_yoast_indexables' );

// Apply the option.
function tw_apply_option_yoast_disable_indexables( $b_should_index_indexables ) {

	$i_option_disable_yoast_indexables = (int) get_option( 'tw-disable-yoast-indexables', 1 );

	if ( $i_option_disable_yoast_indexables ) {
		return false;
	}

	return $b_should_index_indexables;
}
add_filter( 'Yoast\WP\SEO\should_index_indexables', 'tw_apply_option_yoast_disable_indexables' );
