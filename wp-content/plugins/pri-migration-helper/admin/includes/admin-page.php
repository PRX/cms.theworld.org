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
