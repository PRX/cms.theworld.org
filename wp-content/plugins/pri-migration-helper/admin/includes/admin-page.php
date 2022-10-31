<?php
/**
 * Admin helper
 *
 * @package WordPress
 */

/**
 * Add admin option page.
 *
 * @return void
 */
function test_json_diff_option_menu() {

	add_menu_page(
		'Admin Test',
		'Admin Test',
		'manage_options',
		'pmh-admin-test',
		'pmh_admin_page',
		'dashicons-admin-generic',
		30
	);

	add_submenu_page(
		'pmh-admin-test',
		'JSON Diff',
		'JSON Diff',
		'manage_options',
		'pmh-admin-test-json-diff',
		'pmh_admin_page_json_diff',
		30
	);

	add_submenu_page(
		'pmh-admin-test',
		'Terms checker',
		'Terms checker',
		'manage_options',
		'pmh-admin-terms-checker',
		'pmh_admin_terms_checker',
		30
	);
}
add_action( 'admin_menu', 'test_json_diff_option_menu' );

/**
 * Add admin option page.
 *
 * @return void
 */
function pmh_admin_page() {

	echo wp_sprintf( '<h2>%s</h2>', __( 'Admin Test' ) );

	require_once PMH_ADMIN_DIR . '/parts/main.php';
}


/**
 * Add admin option page.
 *
 * @return void
 */
function pmh_admin_page_json_diff() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Don\'t have permission to view the page' );
	}

	$options           = get_option( 'json_diff' );
	$json_diff_results = get_transient( 'json_diff_results_transient' );
	$json_url_1        = null;
	$json_url_2        = null;

	if ( $options != '' ) {
		$json_url_1 = $options['json_url_1'];
		$json_url_2 = $options['json_url_2'];
	}

	if ( $json_diff_results ) {
		$json_diff_results = array_reverse( $json_diff_results );
	}

	echo wp_sprintf( '<h2>%s</h2>', __( 'JSON Diff' ) );

	require_once PMH_ADMIN_DIR . '/parts/json-diff.php';

}

/**
 * Add admin option page.
 *
 * @return void
 */
function pmh_admin_terms_checker() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Don\'t have permission to view the page' );
	}

	if ( isset( $_POST['checker_form_submitted'] ) ) {

		$hidden_field          = esc_html( $_POST['checker_form_submitted'] );
		$checker_form_taxonomy = '';
		$duplicated_term       = array();

		if ( $hidden_field == 'Y' ) {
			$checker_form_taxonomy = $_POST['checker_form_taxonomy'];

			$terms = get_terms(
				array(
					'taxonomy'   => $checker_form_taxonomy,
					'parent'     => 0,
					'hide_empty' => false,
				)
			);

			$hashmap_term = array();
			foreach ( $terms as $term ) {
				$hash = $term->name;
				if ( ! array_key_exists( $hash, $hashmap_term ) ) {
					$hashmap_term[ $hash ] = array();
				}
				$hashmap_term[ $hash ][] = $term;
			}
			foreach ( $hashmap_term as $entry ) {
				if ( count( $entry ) > 1 ) {
					foreach ( $entry as $term ) {
						array_push( $duplicated_term, $term );
					}
				}
			}
		}
	}
	$args       = array(
		'object_type' => array(
			'post',
		),
	);
	$taxonomies = get_taxonomies( $args, 'objects' );

	echo wp_sprintf( '<h2>%s</h2>', __( 'Terms checker' ) );

	require_once PMH_ADMIN_DIR . '/parts/terms-checker.php';
}

add_action( 'admin_enqueue_scripts', 'pmh_admin_enqueue' );
function pmh_admin_enqueue() {

	$allowed_pages = array(
		'pmh-admin-test',
		'pmh-admin-test-json-diff',
		'pmh-admin-terms-checker',
	);

	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $allowed_pages ) ) {

		wp_enqueue_style( 'admin-style', plugin_dir_url( __FILE__ ) . '/css/admin-styling-css.css', array(), '' );
		wp_enqueue_script( 'admin-script', plugin_dir_url( __FILE__ ) . '/js/admin-styling-js.js', array( 'jquery' ) );
		wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . '/js/json-diff-js.js', array( 'jquery' ) );
		wp_enqueue_script( 'post-worker', plugin_dir_url( __FILE__ ) . '/js/post-worker.js', array( 'jquery' ) );
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
}

function pmh_admin_json_diff_process() {

	$url_1    = (string) $_POST['url_1'];
	$url_2    = (string) $_POST['url_2'];
	$row      = (int) $_POST['row'];
	$next_row = $row + 1;

	$json1 = wp_remote_get( $url_1 );
	$obj1  = json_decode( wp_remote_retrieve_body( $json1 ) );

	$json2 = wp_remote_get( $url_2 );
	$obj2  = json_decode( wp_remote_retrieve_body( $json2 ) );

	$diff = pmh_get_json_diff( $obj1, $obj2 );

	$pmh_get_json_diff = test_json_diff( $url_1, $url_2 );
	$count             = $pmh_get_json_diff->getDiffCnt();
	$count_print       = ( 0 == $count ) ? '<div class="dashicons-before dashicons-yes"></div>' : '<div class="dashicons-before dashicons-no-alt"></div>';
	$patch             = ( 0 != $count ) ? '<div class="faq-drawer">
	<input class="faq-drawer__trigger" id="faq-drawer'.$next_row.'" type="checkbox" /><label class="faq-drawer__title" for="faq-drawer'.$next_row.'">See diff detail('.$count.')</label>
	<div class="faq-drawer__content-wrapper">
	  <div class="faq-drawer__content"><pre>'.var_export( $pmh_get_json_diff->getPatch()->jsonSerialize(), true ).'</pre></div>
      </div>
    </div>' : '<div class="dashicons-before dashicons-minus"></div>';

	$json_diff_result = array(
		'line'        => $next_row,
		'url_1'       => $url_1,
		'url_2'       => $url_2,
		'count_print' => $count_print,
		'patch'       => $patch,
	);

	$json_diff_results = get_transient( 'json_diff_results_transient' );
	$transient_time    = 12 * HOUR_IN_SECONDS;

	if ( ! $json_diff_results ) {

		$json_diff_results = array();
	}

	$json_diff_results[] = $json_diff_result;

	set_transient( 'json_diff_results_transient', $json_diff_results, $transient_time );

	$html_response =
	"<tr>
		<td>{$json_diff_result['line']}</td>
		<td>{$json_diff_result['url_1']}</td>
		<td>{$json_diff_result['url_2']}</td>
		<td>{$json_diff_result['count_print']}</td>
		<td>{$json_diff_result['patch']}</td>
	</tr>";

	$response = array(
		'html'     => $html_response,
		'next_row' => $next_row,
	);

	wp_send_json( $response );
}
add_action( 'wp_ajax_json_diff_process', 'pmh_admin_json_diff_process' );

function pmh_admin_json_diff_save_rows() {

	$url_1 = (string) $_POST['url_1'];
	$url_2 = (string) $_POST['url_2'];

	$opt = array(
		'json_url_1' => $url_1,
		'json_url_2' => $url_2,
	);

	update_option( 'json_diff', $opt );

	wp_die();
}
add_action( 'wp_ajax_json_diff_save_rows', 'pmh_admin_json_diff_save_rows' );

function pmh_admin_json_diff_clear() {

	delete_transient( 'json_diff_results_transient' );

	wp_die();
}
add_action( 'wp_ajax_json_diff_clear', 'pmh_admin_json_diff_clear' );

/**
 * Get object names.
 *
 * @return void
 */
function pmh_post_worker_select_object_type() {

	$object_names = array();
	$object_type  = sanitize_text_field( $_POST['objectType'] );

	switch ( $object_type ) {

		case 'taxonomy':
			$_object_names = get_taxonomies( array(), 'names' );

			if ( $_object_names ) {
				foreach ( $_object_names as $_object_name => $name ) {
					$object_names[] = $_object_name;
				}
			}
			break;

		case 'post-type':
			$_object_names = get_post_types( array(), 'names' );

			if ( $_object_names ) {
				foreach ( $_object_names as $_object_name => $name ) {
					$object_names[] = $_object_name;
				}
			}
			break;
	}

	$response = array(
		'object_names' => $object_names,
	);

	wp_send_json( $response );
}
add_action( 'wp_ajax_pmh_post_worker_select_object_type', 'pmh_post_worker_select_object_type' );

/**
 * Get object names.
 *
 * @return void
 */
function pmh_post_worker_get_sample() {

	$samples = array();
	$total   = 0;

	$object_type   = sanitize_text_field( $_POST['objectType'] );
	$object_name   = sanitize_text_field( $_POST['objectName'] );
	$paged_process = (int) sanitize_text_field( $_POST['pagedProcess'] );

	switch ( $object_type ) {

		case 'taxonomy':
			$total = wp_count_terms(
				$object_name,
				array(
					'hide_empty'=> false,
				)
			);

			$offset   = ( $paged_process - 1 ) * 50;
			$args     = array(
				'hide_empty' => false,
				'number'     => 50,
				'offset'     => $offset,
			);
			$_samples = get_terms( $object_name, $args );

			if ( $_samples ) {
				foreach ( $_samples as $_sample ) {

					switch ( $_sample->taxonomy ) {
						case 'program':
							$term_meta_key = '_fgd2wp_old_program_id';
							$post_prefix = 'node';
							break;
						case 'contributor':
							$term_meta_key = '_fgd2wp_old_person_id';
							$post_prefix = 'node';
							break;
						
						default:
							$term_meta_key = '_fgd2wp_old_taxonomy_id';
							$post_prefix = 'taxonomy/term';
							break;
					}

					$samples[] = array(
						'old_url'   => wp_sprintf( '%s/%s', $post_prefix, get_term_meta( $_sample->term_id, $term_meta_key, true ) ),
						'id'        => $_sample->term_id,
						'type'      => $object_name,
						'activated' => 1,
					);
				}
			}
			break;

		case 'post-type':
			$_total = wp_count_posts( $object_name );
			$total  = (int) $_total->publish + (int) $_total->draft;

			$args     = array(
				'post_type'      => $object_name,
				'posts_per_page' => 50,
				'paged'          => $paged_process,
			);
			$_samples = get_posts( $args );

			if ( $_samples ) {
				switch ( $object_name ) {
					case 'segment':
						$post_meta_key = 'fid';
						$post_prefix = 'file';
						break;
					
					default:
						$post_meta_key = 'nid';
						$post_prefix = 'node';
						break;
				}
				foreach ( $_samples as $_sample ) {
					
					$samples[] = array(
						'old_url'   => wp_sprintf( '%s/%s', $post_prefix, get_post_meta( $_sample->ID, $post_meta_key, true ) ),
						'id'        => $_sample->ID,
						'type'      => $object_name,
						'activated' => 1,
					);
				}
			}
			break;
	}

	$response = array(
		'samples' => $samples,
		'total'   => $total,
	);

	wp_send_json( $response );
}
add_action( 'wp_ajax_pmh_post_worker_get_sample', 'pmh_post_worker_get_sample' );


/**
 * Get object names.
 *
 * @return void
 */
function pmh_post_worker_run_process() {

	global $wpdb;

	$object_type   = sanitize_text_field( $_POST['objectType'] );
	$object_name   = sanitize_text_field( $_POST['objectName'] );
	$paged_process = (int) sanitize_text_field( $_POST['pagedProcess'] );

	$next_paged_process = $paged_process + 1;

	$paged = $paged_process;

	// Log.
	$log = "\n";

	switch ( $object_type ) {

		case 'taxonomy':
			$offset = ( $paged - 1 ) * 50;
			$args   = array(
				'hide_empty' => false,
				'number'     => 50,
				'offset'     => $offset,
			);
			$terms  = get_terms( $object_name, $args );

			if ( $terms ) {
				foreach ( $terms as $term ) {

					$term_id = $term->term_id;
					switch ( $term->taxonomy ) {
						case 'program':
							$term_meta_key = '_fgd2wp_old_program_id';
							$post_prefix = 'node';
							break;
						case 'contributor':
							$term_meta_key = '_fgd2wp_old_person_id';
							$post_prefix = 'node';
							break;
						
						default:
							$term_meta_key = '_fgd2wp_old_taxonomy_id';
							$post_prefix = 'taxonomy/term';
							break;
					}
					
					$nid     = get_term_meta( $term_id, $term_meta_key, true );
					$old_url = "$post_prefix/$nid";
					$result  = $wpdb->get_results( "SELECT * FROM wp_fg_redirect WHERE old_url = '$old_url'" );

					if ( ! $result ) {
						$insert = $wpdb->insert(
							'wp_fg_redirect',
							array(
								'old_url'   => wp_sprintf( '%s/%s', $post_prefix, get_term_meta( $term_id, $term_meta_key, true ) ),
								'id'        => $term_id,
								'type'      => $object_name,
								'activated' => 1,
							),
							array(
								'%s',
								'%d',
								'%s',
								'%d',
							),
						);

						$insert_log = $insert ? "Success - $term_id (pg-$paged)" : "Failed - $term_id (pg-$paged)";
					} else {

						$insert_log = "Duplicate - $term_id (pg-$paged)";
					}

					$log .= "\n$insert_log";
				}
			} else {
				$next_paged_process = false;
			}
			break;

		case 'post-type':
			$_total = wp_count_posts( $object_name );
			$total  = (int) $_total->publish + (int) $_total->draft;

			$args     = array(
				'post_status'    => 'all',
				'post_type'      => $object_name,
				'posts_per_page' => 50,
				'paged'          => $paged,
			);
			$posts = get_posts( $args );

			if ( $posts ) {
				foreach ( $posts as $post ) {

					$post_id = $post->ID;

					$result = $wpdb->get_results( "SELECT * FROM wp_fg_redirect WHERE id = $post_id" );

					if ( ! $result ) {
						switch ( $object_name ) {
							case 'segment':
								$post_meta_key = 'fid';
								$post_prefix = 'file';
								break;
							
							default:
								$post_meta_key = 'nid';
								$post_prefix = 'node';
								break;
						}
						$nid = get_post_meta( $post_id, $post_meta_key, true );

						if ( $nid ) {

							$insert = $wpdb->insert(
								'wp_fg_redirect',
								array(
									'old_url'   => wp_sprintf( '%s/%s', $post_prefix, $nid ),
									'id'        => $post_id,
									'type'      => $object_name,
									'activated' => 1,
								),
								array(
									'%s',
									'%d',
									'%s',
									'%d',
								),
							);

							$insert_log = $insert ? "Success - $post_id (pg-$paged)" : "Failed - $post_id (pg-$paged)";

						} else {

							$insert_log = "No NID - $post_id (pg-$paged)";
						}
					} else {

						$insert_log = "Duplicate - $post_id (pg-$paged)";
					}

					$log .= "\n$insert_log";
				}
			} else {
				$next_paged_process = false;
			}
			break;
	}

	$response = array(
		'log'                => $log,
		'next_paged_process' => $next_paged_process,
	);

	wp_send_json( $response );
}
add_action( 'wp_ajax_pmh_post_worker_run_process', 'pmh_post_worker_run_process' );
