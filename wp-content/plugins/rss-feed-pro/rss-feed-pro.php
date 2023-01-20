<?php
/** 
 * RSS Feed Pro for Wordpress
 * 
 * @package rss-feed-pro
 * @wordpress-plugin
 * 
 * Plugin Name: RSS Feed Pro 
 * Plugin URI: https://wordpress.org/plugins/rss-feed-pro/
 * Description: Display an RSS Feed in a widget, on a page or post by shortcode using any number of parameters. Provide users a means to sort your RSS feed archive by Category, Year, and by Author Name.
 * Version: 1.1.1
 * Author: Artios Media
 * Author URI: http://www.artiosmedia.com
 * Developer: Pawan Sindhwal
 * Copyright: © 2020-2022 Artios Media (email : steven@artiosmedia.com)
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rss-feed-pro
 * Domain Path: /languages
 * Tested up to: 6.0
 * PHP tested up to 8.1.5
 */

/**
 * ACKNOWLEDGEMENTS
 * Original/Idea: https://wordpress.org/plugins/rss-import/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 *
 * Detect WP-RSSImport
 * 
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'rss-import/rssimport.php' ) ) {
    include( plugin_dir_path( __FILE__ ) . 'inc/lib.php');
}


/**
 *
 * Custom Post Types
 * 
 */
include( plugin_dir_path( __FILE__ ) . 'inc/cpt.php');


/**
 *
 * Widgets
 * 
 */
include( plugin_dir_path( __FILE__ ) . 'inc/widgets.php');


/**
 *
 * Class RSSFP_RSSFeedPro
 * 
 */
class RSSFP_RSSFeedPro {


	/**
	 * 
	 * Constructor
	 *
	 */
	public function __construct() {

		add_filter( 'plugin_row_meta', array( $this, 'add_description_link' ), 10, 2 );
		add_filter( 'plugin_action_links_rss-feed-pro/rss-feed-pro.php', array( $this, 'link_to_archives' ) );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_ajaxurl' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );

		add_action( 'wp_body_open', array( $this, 'add_modal' ) );

		add_action( "admin_notices", array( $this, "ask_user_to_rate" ) );

		add_action( 'wp_ajax_rssfp_sort', array( $this, 'display_results' ) );
		add_action( 'wp_ajax_nopriv_rssfp_sort', array( $this, 'display_results' ) );
		add_action( "wp_ajax_rssfp_dismiss_notice", array( $this, "dismiss_notice" ) );

		add_shortcode( 'rss_feed_pro_sort', array( $this, 'show_filters' ) );
	} 


	/**
	 * 
	 * Enqueue css styles
	 * Enqueue scripts
	 *
	 */
	public static function enqueue_scripts_styles() {
		wp_enqueue_style( 'rss-feed-pro', plugins_url('assets/css/rss-feed-pro.css', __FILE__), array(), '1.0.0', 'all' );
		wp_enqueue_script( 'rss-feed-pro',  plugins_url('assets/js/rss-feed-pro.js', __FILE__), array('jquery'), '1.0.0', true );
	}


	/**
	 * 
	 * Enqueue admin scripts
	 * Enqueue css scripts
	 *
	 */
	public static function enqueue_admin_scripts_styles() {
		wp_enqueue_script( 'rss-feed-pro-admin',  plugins_url('assets/js/rss-feed-pro-admin.js', __FILE__), array('jquery'), '1.0.0', true );
		wp_enqueue_style( 'rss-feed-pro-admin', plugins_url('assets/css/rss-feed-pro-admin.css', __FILE__), array(), '1.0.0', 'all' );
	}


	/**
	 * 
	 * Localizes ajax url ( admin-ajax.php )
	 * Makes it available for the js script with id 'codeableform'
	 *
	 */
	public static function localize_ajaxurl() {
		$args = array(
			'url'      => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'rss-feed-pro', 'rssfp_object', $args );
	}


	/**
	 * 
	 * Add descrition link: Donation for...
	 *
	 */
	public function add_description_link( $links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$row_meta = array(
				'donation' => '<a href="' . esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=E7LS2JGFPLTH2' ) . '" target="_blank">' . esc_html__( 'Donation for Homeless', 'rss-feed-pro' ) . '</a>'
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	/**
	 * 
	 * Add this next to "Deactivate". Helps with shortcodes
	 *
	 */
	public static function link_to_archives ( $links ) {

		$url = esc_url( add_query_arg(
			'post_type',
			'rssfp-shortcode',
			get_admin_url() . 'edit.php'
		) );

		$settings_link = "<a href='$url'>" . __( 'Shortcodes', 'rss-feed-pro' ) . '</a>';

		array_push(
			$links,
			$settings_link
		);
		return $links;
	}


	/**
	 * 
	 * Sort function
	 *
	 */
	public static function show_filters( $atts ){

	    $args = [];
		$args = shortcode_atts( 
		    array(
		        'id' => '',
		    ), 
		    $atts
		);

		$shortcode_id    = $args['id'];
		$feedurl         = get_post_meta( $shortcode_id, 'rssfp-meta-feed-url', true );
		$sort_mode       = get_post_meta( $shortcode_id, 'rssfp-meta-sort-by', true ); 
		$feed_items      = RSSFP_RSSFeedPro::get_feed_items( $feedurl, $sort_mode );
		$base_year       = $feed_items['base-year'];
		$item_categories = $feed_items['item-categories'];
		$unique_authors  = $feed_items['unique-authors'];
		$item_years      = $feed_items['item-years'];

		$opts = array(
			'sort-mode'       => $sort_mode,
			'shortcode-id'    => $shortcode_id,
			'base-year'       => $base_year,
			'item-categories' => $item_categories,
			'unique-authors'  => $unique_authors,
			'item-years'      => $item_years,
		);

		ob_start();

		echo self::render_options( $opts );

		return ob_get_clean();
	}


	/**
	 * 
	 * Display filter results
	 *
	 */
	public static function display_results() {

		$data  = [];
		$nonce = sanitize_text_field( $_POST['nonce'] );

		if ( !wp_verify_nonce( $nonce, 'rssfp_sort' ) ) {

			ob_start();
			?>
			<div id="rfp-replaceable-div">
				<?php esc_html_e( 'Invalid request!', 'rss-feed-pro' ); ?>
			</div>

			<?php
			$html            = ob_get_clean();
			$data['html']    = $html;
			$data['success'] = false;
			wp_send_json( $data );
		}

		if ( isset( $_POST['page_num'] ) ) {
			$page_num        = sanitize_text_field( $_POST['page_num'] );
		}else{
			$page_num        = 1;
		}

		$shortcode_id        = sanitize_text_field( $_POST['shortcode_id'] );
		$sort_value          = urldecode( sanitize_text_field ( $_POST['sort_value'] ) );
		$sort_mode           = sanitize_text_field ( $_POST['sort_mode'] );
	    $feedurl             = get_post_meta( $shortcode_id, 'rssfp-meta-feed-url', true );
		$feed_items          = RSSFP_RSSFeedPro::get_feed_items( $feedurl, $sort_mode );
		$items               = $feed_items['items'];


		ob_start();

		?>
		<div id="rfp-replaceable-div">
			<input type="hidden" id="rfp-ajax-shortcode-id" value="<?php echo esc_html( $shortcode_id ); ?>">
			<?php wp_kses_post( self::display_items( $items[$sort_value], $sort_mode, $sort_value, $page_num ) ); ?>
		</div>
		<?php

		$html            = ob_get_clean();
		$data['html']    = $html;
		$data['success'] = true;
		wp_send_json( $data );
	}


	/**
	 * 
	 * Display items function
	 *
	 */
	public static function display_items( $array_items, $sorb_by, $sort_value, $page_num ) {

		if ( empty( $array_items ) || count( $array_items ) < 1 ) {
			esc_html_e('Sorry. No items were found for this criteria.', 'rss-feed-pro');
			return;
		}

		global $wp;
		global $post;
		$date_format  = get_option( 'date_format' );
        $page_slug    = $post->post_name;
		$per_page     = 30;
		$total_items  = count( $array_items );
		$total_pages  = ceil( $total_items/$per_page );
		$page_num     = max( $page_num, 1); //get 1 page when get_query_var('paged') <= 0
		$page_num     = min( $page_num, $total_pages ); //get last page when get_query_var('paged') > $total_pages
		$offset       = ( $page_num - 1) * $per_page;

		if( $offset < 0 ){ 
			$offset = 0; 
		}
		$items = array_slice( $array_items, $offset, $per_page );
		?>

		<ul id="rft-search-results">
			<?php foreach ( $items as $item ): ?>
				<li>
					<span class="rft-item-date">
						<?php echo date_i18n( $date_format, strtotime( $item['date'] ) ); ?>
					</span>
					<a href="<?php echo esc_html( $item['link'] ); ?>" target="_blank">
						<?php echo esc_html( $item['title'] ); ?>
					</a>
				</li>
			<?php endforeach ?>
		</ul>

		<?php $query_vars = $page_slug.'?rss-feed-pro-sortby='.$sorb_by.'&sort-value='.$sort_value; ?>

		<?php if ( ceil( $total_pages*$per_page ) > $per_page ): ?>

			<div class="rfp-pagination" id="rfp-pagination">
				<?php

				$add_args = array(
				    'rss-feed-pro-sortby' => $sorb_by,
				    'sort-value'          => urldecode( $sort_value ),
				);

				$big = 999999999;// Needs an unlikely integer

				echo paginate_links( array(
				    'base'     => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				    'current'  => $page_num,
				    'format'   => '?paged=%#%',
				    'total'    => $total_pages,
				    'add_args' => $add_args,
				    'type'     => 'list'
				) );

				?>
			</div>

		<?php endif ?>
	<?php 
	}


	/**
	 * 
	 * Get the feed items
	 *
	 */
	public static function get_feed_items( $feedurl, $sort_mode ) {

		$rss = fetch_feed( $feedurl ); 

		if ( is_wp_error( $rss ) ) {
			'<p>'.esc_html_e( 'Error fetching the feed. May be the feed url is invalid.', 'rss-feed-pro' ).'</p>';
			return;
		}

		$items                     = $rss->get_items(0, 0);
		$years                     = [];
		$last_item                 = count( $items )-1;
		$base_year                 = date_i18n( 'Y', strtotime( $rss->get_items()[$last_item]->get_date() ) );
		$last_year_in_loop         = '';
		$last_category_in_loop     = '';
		$current_year_in_loop      = '';
		$feed_items                = [];
		$feed_items                = [];
		$item_categories           = [];
		$item_authors              = [];
		$item_years                = [];
		$unique_authors            = [];

		foreach ( $items as $key => $item ) {

			$date                 = $item->get_date();
			$title                = $item->get_title();
			$link                 = home_url('play-podcast/?id='.$item->get_id(true) );
			$link                 = $item->get_permalink();


			/**
			 * 
			 * If sorting by YEAR
			 *
			 */
			if ( $sort_mode == 'year' ) {

				$current_year_in_loop                             = date_i18n( 'Y', strtotime( $date ) );
				$feed_items[$current_year_in_loop][$key]['title'] = $title;
				$feed_items[$current_year_in_loop][$key]['date']  = $date;
				$feed_items[$current_year_in_loop][$key]['link']  = $link;

				if ( !in_array( $current_year_in_loop, $item_years ) ) {
					array_push( $item_years, $current_year_in_loop );
				}


			/**
			 * 
			 * If sorting by CATEGORY
			 *
			 */
			}elseif( $sort_mode == 'category' ) {

				$categories = $item->get_categories();
				if ( !empty( $categories ) && count( $categories ) >= 1 ) {
					foreach ( $categories as $category ) {
						$current_category_in_loop = $category->term;
						if ( !in_array( $current_category_in_loop, $item_categories )) {
							array_push( $item_categories, $current_category_in_loop );
						}
					}
				}

				if ( !empty( $categories ) && count( $categories ) >= 1 ) {
					foreach ( $categories as $category ) {
						$current_category_in_loop = $category->term;
						if ( in_array( $current_category_in_loop, $item_categories )) {
							$feed_items[$current_category_in_loop][$key]['title'] = $title;
							$feed_items[$current_category_in_loop][$key]['date']  = $date;
							$feed_items[$current_category_in_loop][$key]['link']  = $link;
						}
					}
				}


			/**
			 * 
			 * If sorting by AUTHOR NAME
			 *
			 */
			}elseif ( $sort_mode == 'author' ) {

				$item_authors = $item->get_authors();

				if ( !empty( $item_authors ) && count( $item_authors ) >= 1 ) {
					foreach ( $item_authors as $author ) {
						$item_author_in_loop = $author->get_name();
						if ( !in_array( $item_author_in_loop, $unique_authors )) {
							array_push( $unique_authors, $item_author_in_loop );
						}
					}
				}

				if ( !empty( $item_authors ) && count( $item_authors ) >= 1 ) {
					foreach ( $item_authors as $author ) {
						$item_author_in_loop = $author->get_name();
						if ( in_array( $item_author_in_loop, $unique_authors )) {
							$feed_items[$item_author_in_loop][$key]['title'] = $title;
							$feed_items[$item_author_in_loop][$key]['date']  = $date;
							$feed_items[$item_author_in_loop][$key]['link']  = $link;
						}
					}
				}
			}

		}

		$items['items']           = $feed_items;
		$items['base-year']       = $base_year;
		$items['item-categories'] = $item_categories;
		$items['unique-authors']  = $unique_authors;
		$items['item-years']      = $item_years;

		return $items;
	}


	/**
	 * 
	 * Add modal
	 *
	 */
	public function add_modal(){
		?>
		<?php wp_nonce_field( 'rssfp_sort', '_rssf_pagination_nonce' ); ?>
		<div class="rfp-modal">
			<div class="rfp-modal-dialog">
				<div class="rfp-modal-content">

					<h3 id="rfp-sort-title">
						<?php esc_html_e('Sorting by ', 'rss-feed-pro');?> 
						<span id="rfp-sort-title-ext"></span>
					</h3>

					<div class="rfp-x-out">
						<span id="rfp-x-out-btn">
							&times;
						</span>
					</div>

					<div class="rfp-loader"></div>

					<div id="rfp-replaceable-div">
					</div>

				</div>
			</div>
		</div>

		<div class="rfp-pg-backdrop">
		</div>
		<?php 
	}


	/**
	 * 
	 * Render Sort Options
	 *
	 */
	public static function render_options( $opts = array() ) {

		$sort_mode       = $opts['sort-mode'];
		$shortcode_id    = $opts['shortcode-id'];
		$base_year       = $opts['base-year'];    
		$item_categories = $opts['item-categories'];
		$unique_authors  = $opts['unique-authors'];
		$item_years      = $opts['item-years'];

		$current_year    = date_i18n( 'Y', time() );

		ob_start();

		?>

		<?php if ( $sort_mode == 'year' ): ?>
			<form method="GET" class="rfp-sorting-form" action="">
				<input type="hidden" class="rfp-sort-mode" value="<?php esc_html_e( 'year', 'rss-feed-pro' ); ?>"> 
				<input type="hidden" name="rfp-short-code-id" value="<?php echo esc_html( $shortcode_id ); ?>" class="rfp-shortcode-id">
				<select name="sort-value">
					<option value=""><?php esc_html_e('Select by Year'); ?></option>
					<?php foreach ( $item_years as $item_year ): ?>
						<option value="<?php echo esc_html( $item_year ); ?>"><?php echo esc_html( $item_year ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="rssp_nonce" class="rssfp-nonce" value="<?php echo esc_attr( wp_create_nonce('rssfp_sort') ); ?>">
			</form>
		<?php elseif( $sort_mode == 'category' ): ?>
			<form method="GET" class="rfp-sorting-form" action="">
				<input type="hidden" class="rfp-sort-mode" value="<?php esc_html_e( 'category', 'rss-feed-pro' ); ?>"> 
				<input type="hidden" name="rfp-short-code-id" value="<?php echo esc_html( $shortcode_id ); ?>" class="rfp-shortcode-id">
				<select name="sort-value">
					<option value=""><?php esc_html_e('Select by Category'); ?></option>
					<?php foreach ( $item_categories as $category ): ?>
						<option value="<?php echo esc_html( $category ); ?>"><?php echo esc_html( $category ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="rssp_nonce" class="rssfp-nonce" value="<?php echo esc_attr( wp_create_nonce('rssfp_sort') ); ?>">
			</form>
		<?php elseif( $sort_mode == 'author' ): ?>
			<form method="GET" class="rfp-sorting-form" action="">
				<input type="hidden" class="rfp-sort-mode" value="<?php esc_html_e( 'author', 'rss-feed-pro' ); ?>"> 
				<input type="hidden" name="rfp-short-code-id" value="<?php echo esc_html( $shortcode_id ); ?>" class="rfp-shortcode-id">
				<select name="sort-value">
					<option value=""><?php esc_html_e('Select by Author'); ?></option>
					<?php foreach ( $unique_authors as $author ): ?>
						<option value="<?php echo esc_html( $author ); ?>" ><?php echo esc_html( $author ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="rssp_nonce" class="rssfp-nonce" value="<?php echo esc_attr( wp_create_nonce('rssfp_sort') ); ?>">
			</form>
		<?php endif; ?>
		<?php

		return ob_get_clean();
	}


	public static function ask_for_rating_on(){
		update_option( "rssfp_ask_for_rating_on", date( 'Y-m-d', strtotime( '+30 days' ) ) );
	}

	public function ask_user_to_rate(){

		$date = get_option( "rssfp_ask_for_rating_on" );
		if ( $date && current_time( 'timestamp' ) >= strtotime( $date ) ) {
			?>
			<div class="notice notice-info is-dismissible" id="rfp-ask-for-rating">
			<p>
				<?php esc_html_e("How do you like","rss-feed-pro"); ?> <strong>RSS Feed Pro</strong>? <?php esc_html_e("Your feedback assures the continued maintenance of this plugin","rss-feed-pro" ); ?>!
				<a class="button button-primary" href="#" target="_blank">
					<?php esc_html_e( "Leave Feedback","rss-feed-pro" ); ?>
				</a>
			</p>
			<?php wp_nonce_field( 'rssfp_dismiss_notice', '_rssfp_userfeedback' ); ?>
			</div>
			<?php
		}
	}

	public function dismiss_notice() {

		$data  = [];
		$nonce = sanitize_text_field( $_POST['nonce'] );

		if ( !wp_verify_nonce( $nonce, 'rssfp_dismiss_notice' ) ) {

			ob_start();
			?>
			<div>
				<?php esc_html_e( 'Invalid request!', 'rss-feed-pro' ); ?>
			</div>

			<?php
			$html            = ob_get_clean();
			$data['html']    = $html;
			$data['success'] = false;
			wp_send_json( $data );
		}
		
		$is_final = '';

		if ( current_user_can( "manage_options" ) ) {
			$is_final = sanitize_text_field( $_POST["final"] );
			if ( isset( $is_final ) && !empty( $is_final && $is_final == true ) ) {
				delete_option( "rssfp_ask_for_rating_on" );
			}else {
				update_option( "rssfp_ask_for_rating_on", date( 'Y-m-d', strtotime( '+30 days' ) ) );
			}
			$data['success'] = true;
			wp_send_json( $data );
		}
	}




}//End of class RSSFP_RSSFeedPro


/*		
*
* RSSFP_RSSFeedPro Object
*
*/
$RSSFP_RSSFeedProObject = new RSSFP_RSSFeedPro();


/*
*
* PLUGIN ACTIVATION HOOKS
*
*/
register_activation_hook( __FILE__, array( 'RSSFP_RSSFeedPro', 'ask_for_rating_on' ) );