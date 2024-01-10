<?php
/**
 * Register settings.
 *
 * @package tw_episode_importer
 */

/**
 * Register settings page.
 *
 * @return void
 */
function tw_episode_importer_settings_page() {
	add_options_page(
		'Episode Importer Settings', // title of the settings page.
		'Episode Importer Settings', // title of the submenu.
		'manage_options', // capability of the user to see this page.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE, // slug of the settings page.
		'tw_episode_importer_settings_page_html' // callback function when rendering the page.
	);
	add_action( 'admin_init', 'tw_episode_importer_settings_init' );
}
add_action( 'admin_menu', 'tw_episode_importer_settings_page' );

/**
 * Initialize settings and fields.
 *
 * @return void
 */
function tw_episode_importer_settings_init() {
	add_settings_section(
		TW_EPISODE_IMPORTER_SETTINGS_SECTION, // id of the section.
		'Episode Importer Settings', // title to be displayed.
		'', // callback function to be called when opening section, currently empty.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE // page on which to display the section.
	);

	// Register the setting.
	register_setting(
		'episode-importer-settings', // option group.
		TW_EPISODE_IMPORTER_SETTINGS_API,
		'tw_episode_importer_sanitize_api_settings',
	);

	// Register fields.
	add_settings_field(
		TW_EPISODE_IMPORTER_SETTINGS_API . '-' . TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY, // id of the settings field.
		'Episodes API URL', // title of the settings field.
		'tw_episode_importer_settings_episodes_api_url_cb', // callback function.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE, // page on which settings display.
		TW_EPISODE_IMPORTER_SETTINGS_SECTION // section on which to show settings.
	);
	add_settings_field(
		TW_EPISODE_IMPORTER_SETTINGS_API . '-' . TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY, // id of the settings field.
		'Segments API URL', // title of the settings field.
		'tw_episode_importer_settings_segments_api_url_cb', // callback function.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE, // page on which settings display.
		TW_EPISODE_IMPORTER_SETTINGS_SECTION // section on which to show settings.
	);
	add_settings_field(
		TW_EPISODE_IMPORTER_SETTINGS_API . '-' . TW_EPISODE_IMPORTER_AUTHOR_USER_ID_KEY, // id of the settings field.
		'Author of Created Content', // title of the settings field.
		'tw_episode_importer_settings_author_user_id_cb', // callback function.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE, // page on which settings display.
		TW_EPISODE_IMPORTER_SETTINGS_SECTION // section on which to show settings.
	);
	add_settings_field(
		TW_EPISODE_IMPORTER_SETTINGS_API . '-' . TW_EPISODE_IMPORTER_PROGRAM_ID_KEY, // id of the settings field.
		'Program of Created Content', // title of the settings field.
		'tw_episode_importer_settings_program_id_cb', // callback function.
		TW_EPISODE_IMPORTER_SETTINGS_PAGE, // page on which settings display.
		TW_EPISODE_IMPORTER_SETTINGS_SECTION // section on which to show settings.
	);
}

/**
 * Sanitize API settigs input.
 *
 * @param array $input Associative array of input values.
 * @return array
 */
function tw_episode_importer_sanitize_api_settings( $input ) {
	// Trim whitespace from values.
	$rgx_ending_non_word_or_slash                                = '~[^\w/]+$~';
	$sanitized_input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] = preg_replace( $rgx_ending_non_word_or_slash, '', trim( $input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] ) );
	$sanitized_input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] = preg_replace( $rgx_ending_non_word_or_slash, '', trim( $input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] ) );

	// Sanitize episode API URL.
	$sanitized_input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] = wp_http_validate_url( $sanitized_input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] );
	$sanitized_input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] = wp_http_validate_url( $sanitized_input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] );

	// Sanitize Author User ID.
	$sanitized_input[ TW_EPISODE_IMPORTER_AUTHOR_USER_ID_KEY ] = $input[ TW_EPISODE_IMPORTER_AUTHOR_USER_ID_KEY ];

	// Sanitize Program ID.
	$sanitized_input[ TW_EPISODE_IMPORTER_PROGRAM_ID_KEY ] = $input[ TW_EPISODE_IMPORTER_PROGRAM_ID_KEY ];

	if ( ! $sanitized_input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] ) {
		$sanitized_input[ TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY ] = '';
	}

	if ( ! $sanitized_input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] ) {
		$sanitized_input[ TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY ] = '';
	}

	return $sanitized_input;
}

/**
 * Render Episodes API URL setting value.
 *
 * @return void
 */
function tw_episode_importer_settings_episodes_api_url_cb() {
	tw_episode_importer_settings_render_field_input( TW_EPISODE_IMPORTER_EPISODES_API_URL_KEY );
}

/**
 * Render Segments API URL setting value.
 *
 * @return void
 */
function tw_episode_importer_settings_segments_api_url_cb() {
	tw_episode_importer_settings_render_field_input( TW_EPISODE_IMPORTER_SEGMENTS_API_URL_KEY );
}

/**
 * Render Author User ID setting value.
 *
 * @return void
 */
function tw_episode_importer_settings_author_user_id_cb() {
	tw_episode_importer_settings_render_user_field_input( TW_EPISODE_IMPORTER_AUTHOR_USER_ID_KEY );
}

/**
 * Render Program ID setting value.
 *
 * @return void
 */
function tw_episode_importer_settings_program_id_cb() {
	tw_episode_importer_settings_render_taxonomy_field_input( TW_EPISODE_IMPORTER_PROGRAM_ID_KEY, 'program' );
}

/**
 * Render field input HTML.
 *
 * @param string $option_key Key name to store value under.
 * @return void
 */
function tw_episode_importer_settings_render_field_input( $option_key ) {
	$options = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$id      = TW_EPISODE_IMPORTER_SETTINGS_API . '-' . $option_key;
	$value   = $options[ $option_key ];

	echo '<input type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( TW_EPISODE_IMPORTER_SETTINGS_API . '[' . $option_key . ']' ) . '" value="' . esc_attr( $value ) . '" style="width: 100%" />';
}

/**
 * Render user input HTML.
 *
 * @param string $option_key Key name to store value under.
 * @return void
 */
function tw_episode_importer_settings_render_user_field_input( $option_key ) {
	$options = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$id      = TW_EPISODE_IMPORTER_SETTINGS_API . '-' . $option_key;
	$value   = $options[ $option_key ];

	wp_dropdown_users(
		array(
			'name'             => TW_EPISODE_IMPORTER_SETTINGS_API . '[' . $option_key . ']',
			'id'               => $id,
			'role'             => array( 'editor' ),
			'selected'         => $value,
			'include_selected' => true,
			'show_option_none' => 'Select a user...',
		)
	);
}

/**
 * Render taxonomy input HTML.
 *
 * @param string $option_key Key name to store value under.
 * @param string $taxonomy Name of taxonomy.
 * @return void
 */
function tw_episode_importer_settings_render_taxonomy_field_input( $option_key, $taxonomy = 'category' ) {
	$options = get_option( TW_EPISODE_IMPORTER_SETTINGS_API );
	$id      = TW_EPISODE_IMPORTER_SETTINGS_API . '-' . $option_key;
	$value   = $options[ $option_key ];

	wp_dropdown_categories(
		array(
			'id'               => $id,
			'name'             => TW_EPISODE_IMPORTER_SETTINGS_API . '[' . $option_key . ']',
			'taxonomy'         => $taxonomy,
			'orderby'          => 'name',
			'selected'         => $value,
			'include_selected' => true,
			'show_option_none' => "Select a {$taxonomy}...",
		)
	);
}

/**
 * Render settings page HTML.
 *
 * @return void
 */
function tw_episode_importer_settings_page_html() {
	// check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
<div class="wrap">
	<?php settings_errors(); ?>
	<form method="POST" action="options.php">
		<?php settings_fields( TW_EPISODE_IMPORTER_SETTINGS_PAGE ); ?>
		<?php do_settings_sections( TW_EPISODE_IMPORTER_SETTINGS_PAGE ); ?>
		<?php submit_button(); ?>
	</form>
</div>
	<?php
}
