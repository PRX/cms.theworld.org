<?php
/**
 * Newspack Donations features management.
 *
 * @package Newspack
 */

namespace Newspack;

use \WP_Error, \WC_Product_Simple, \WC_Product_Subscription, \WC_Name_Your_Price_Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Handles donations functionality.
 */
class Donations {
	const DONATION_PRODUCT_ID_OPTION              = 'newspack_donation_product_id';
	const DONATION_SUGGESTED_AMOUNT_META          = 'newspack_donation_suggested_amount';
	const DONATION_UNTIERED_SUGGESTED_AMOUNT_META = 'newspack_donation_untiered_suggested_amount';
	const DONATION_TIERED_META                    = 'newspack_donation_is_tiered';
	const DONATION_PAGE_ID_OPTION                 = 'newspack_donation_page_id';
	const DONATION_NON_WC_SETTINGS_OPTION         = 'newspack_donations_settings';
	const DONATION_ORDER_META_KEYS                = [
		'referer_tags'       => [
			'label' => 'Post Tags',
		],
		'referer_categories' => [
			'label' => 'Post Categories',
		],
		'utm_source'         => [
			'label' => 'Campaign Source',
		],
		'utm_medium'         => [
			'label' => 'Campaign Medium',
		],
		'utm_campaign'       => [
			'label' => 'Campaign Name',
		],
		'utm_term'           => [
			'label' => 'Campaign Term',
		],
		'utm_content'        => [
			'label' => 'Campaign Content',
		],
	];

	/**
	 * Initialize hooks/filters/etc.
	 */
	public static function init() {
		if ( ! is_admin() ) {
			add_action( 'wp_loaded', [ __CLASS__, 'process_donation_form' ], 99 );
			add_action( 'woocommerce_checkout_update_order_meta', [ __CLASS__, 'woocommerce_checkout_update_order_meta' ] );
			add_filter( 'woocommerce_billing_fields', [ __CLASS__, 'woocommerce_billing_fields' ] );
		}
	}

	/**
	 * Check whether WooCommerce and associated extensions required for donations are active.
	 *
	 * @return bool|WP_Error True if active. WP_Error if not.
	 */
	public static function is_woocommerce_suite_active() {
		if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_Subscriptions_Product' ) || ! class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			return new WP_Error(
				'newspack_missing_required_plugin',
				esc_html__( 'The required plugins are not installed and activated. Install and/or activate them to access this feature.', 'newspack' ),
				[
					'status' => 400,
					'level'  => 'fatal',
				]
			);
		}

		return true;
	}

	/**
	 * Get the default donation settings.
	 *
	 * @param bool   $suggest_donations Whether to include suggested default donation amounts (Default: false).
	 * @param string $platform Selected reader revenue platform (Default: wc).
	 * @return array Array of settings info.
	 */
	protected static function get_donation_default_settings( $suggest_donations = false, $platform = 'wc' ) {
		return [
			'name'                    => __( 'Donate', 'newspack' ),
			'suggestedAmounts'        => $suggest_donations ? [ 7, 15.00, 30.00 ] : [],
			'suggestedAmountUntiered' => $suggest_donations ? 15.00 : 0,
			'tiered'                  => false,
			'image'                   => false,
			'created'                 => 'wc' !== $platform,
			'platform'                => $platform,
			'products'                => [
				'once'  => false,
				'month' => false,
				'year'  => false,
			],
		];
	}

	/**
	 * Get the donation currency symbol.
	 */
	private static function get_currency_symbol() {
		if ( self::is_platform_wc() ) {
			return \get_woocommerce_currency_symbol();
		} else {
			$currency = Stripe_Connection::get_stripe_data()['currency'];
			return newspack_get_currency_symbol( $currency );
		}
	}

	/**
	 * Get the donation settings.
	 *
	 * @return Array of donation settings or WP_Error if WooCommerce is not set up.
	 */
	public static function get_donation_settings() {
		$currency_symbol = html_entity_decode( self::get_currency_symbol() );

		if ( self::is_platform_nrh() ) {
			$saved_settings             = get_option( self::DONATION_NON_WC_SETTINGS_OPTION, [] );
			$defaults                   = self::get_donation_default_settings( true, 'nrh' );
			$settings                   = wp_parse_args( $saved_settings, $defaults );
			$settings['currencySymbol'] = $currency_symbol;
			return $settings;
		}

		$ready = self::is_woocommerce_suite_active();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}

		$settings                   = self::get_donation_default_settings( true );
		$settings['currencySymbol'] = $currency_symbol;

		$product_id = get_option( self::DONATION_PRODUCT_ID_OPTION, 0 );
		if ( ! $product_id ) {
			return $settings;
		}

		$product = \wc_get_product( $product_id );
		if ( ! $product || 'grouped' !== $product->get_type() ) {
			return $settings;
		}

		$settings['created'] = true;
		$settings['name']    = $product->get_name();
		$settings['image']   = [
			'id'  => $product->get_image_id(),
			'url' => $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'woocommerce_thumbnail' ) ) : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
		];

		$suggested_amounts = $product->get_meta( self::DONATION_SUGGESTED_AMOUNT_META, true );
		if ( is_array( $suggested_amounts ) ) {
			$suggested_amounts = array_map( 'wc_format_decimal', $suggested_amounts );
			sort( $suggested_amounts, SORT_NUMERIC );
			$settings['suggestedAmounts'] = $suggested_amounts;
		}

		$untiered_suggested_amount = $product->get_meta( self::DONATION_UNTIERED_SUGGESTED_AMOUNT_META, true );
		if ( is_numeric( $untiered_suggested_amount ) ) {
			$settings['suggestedAmountUntiered'] = wc_format_decimal( $untiered_suggested_amount );
		}

		$settings['tiered'] = (bool) $product->get_meta( self::DONATION_TIERED_META, true );

		// Add the product IDs for each frequency.
		foreach ( $product->get_children() as $child_id ) {
			$child_product = wc_get_product( $child_id );
			if ( ! $child_product || ! (bool) WC_Name_Your_Price_Helpers::is_nyp( $child_id ) ) {
				continue;
			}

			if ( 'subscription' === $child_product->get_type() ) {
				if ( 'year' === $child_product->get_meta( '_subscription_period', true ) ) {
					$settings['products']['year'] = $child_id;
				} elseif ( 'month' == $child_product->get_meta( '_subscription_period', true ) ) {
					$settings['products']['month'] = $child_id;
				}
			} elseif ( 'simple' === $child_product->get_type() ) {
				$settings['products']['once'] = $child_id;
			}
		}

		return $settings;
	}

	/**
	 * Set the donation settings.
	 *
	 * @param array $args Array of settings info.
	 * @return array Updated settings.
	 */
	public static function set_donation_settings( $args ) {
		if ( self::is_platform_nrh() ) {
			update_option( self::DONATION_NON_WC_SETTINGS_OPTION, $args );
			return self::get_donation_settings();
		}

		$ready = self::is_woocommerce_suite_active();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}

		$defaults = self::get_donation_default_settings();
		$args     = wp_parse_args( $args, $defaults );

		// Create the product if it hasn't been created yet.
		$product_id = get_option( self::DONATION_PRODUCT_ID_OPTION, 0 );
		if ( ! $product_id ) {
			self::create_donation_product( $args );
			return self::get_donation_settings();
		}

		// Re-create the product if the data is corrupted.
		$product = \wc_get_product( $product_id );
		if ( ! $product || 'grouped' !== $product->get_type() ) {
			self::create_donation_product( $args );
			return self::get_donation_settings();
		}

		// Update the existing product.
		self::update_donation_product( $args );
		return self::get_donation_settings();
	}

	/**
	 * Create new donations products.
	 *
	 * @param array $args Info that will be used to create the products.
	 */
	protected static function create_donation_product( $args ) {
		$defaults = self::get_donation_default_settings();
		$args     = wp_parse_args( $args, $defaults );

		// Parent product.
		$parent_product = new \WC_Product_Grouped();
		$parent_product->set_name( $args['name'] );
		$suggested_amounts = array_map( 'wc_format_decimal', $args['suggestedAmounts'] );
		sort( $suggested_amounts, SORT_NUMERIC );
		$parent_product->update_meta_data( self::DONATION_SUGGESTED_AMOUNT_META, $suggested_amounts );
		$parent_product->update_meta_data( self::DONATION_UNTIERED_SUGGESTED_AMOUNT_META, wc_format_decimal( $args['suggestedAmountUntiered'] ) );
		$parent_product->update_meta_data( self::DONATION_TIERED_META, (bool) $args['tiered'] );
		$parent_product->set_catalog_visibility( 'hidden' );
		$parent_product->set_virtual( true );
		$parent_product->set_downloadable( true );
		$parent_product->set_sold_individually( true );

		$default_price = $args['tiered'] ? wc_format_decimal( $args['suggestedAmounts'][ floor( count( $args['suggestedAmounts'] ) / 2 ) ] ) : wc_format_decimal( $args['suggestedAmountUntiered'] );

		// Monthly donation.
		$monthly_product = new \WC_Product_Subscription();
		/* translators: %s: Product name */
		$monthly_product->set_name( sprintf( __( '%s: Monthly', 'newspack' ), $args['name'] ) );
		$monthly_product->set_regular_price( $default_price );
		$monthly_product->update_meta_data( '_suggested_price', $default_price );
		$monthly_product->update_meta_data( '_hide_nyp_minimum', 'yes' );
		$monthly_product->update_meta_data( '_min_price', wc_format_decimal( 1.0 ) );
		$monthly_product->update_meta_data( '_nyp', 'yes' );
		$monthly_product->update_meta_data( '_subscription_price', wc_format_decimal( $default_price ) );
		$monthly_product->update_meta_data( '_subscription_period', 'month' );
		$monthly_product->update_meta_data( '_subscription_period_interval', 1 );
		$monthly_product->set_virtual( true );
		$monthly_product->set_downloadable( true );
		$monthly_product->set_catalog_visibility( 'hidden' );
		$monthly_product->set_sold_individually( true );
		$monthly_product->save();

		// Yearly donation.
		$yearly_product = new \WC_Product_Subscription();
		/* translators: %s: Product name */
		$yearly_product->set_name( sprintf( __( '%s: Yearly', 'newspack' ), $args['name'] ) );
		$yearly_product->set_regular_price( 12 * $default_price );
		$yearly_product->update_meta_data( '_suggested_price', 12 * $default_price );
		$yearly_product->update_meta_data( '_hide_nyp_minimum', 'yes' );
		$yearly_product->update_meta_data( '_min_price', wc_format_decimal( 1.0 ) );
		$yearly_product->update_meta_data( '_nyp', 'yes' );
		$yearly_product->update_meta_data( '_subscription_price', wc_format_decimal( 12 * $default_price ) );
		$yearly_product->update_meta_data( '_subscription_period', 'year' );
		$yearly_product->update_meta_data( '_subscription_period_interval', 1 );
		$yearly_product->set_virtual( true );
		$yearly_product->set_downloadable( true );
		$yearly_product->set_catalog_visibility( 'hidden' );
		$yearly_product->set_sold_individually( true );
		$yearly_product->save();

		// One-time donation.
		$once_product = new \WC_Product_Simple();
		/* translators: %s: Product name */
		$once_product->set_name( sprintf( __( '%s: One-Time', 'newspack' ), $args['name'] ) );
		$once_product->set_regular_price( 12 * $default_price );
		$once_product->update_meta_data( '_suggested_price', 12 * $default_price );
		$once_product->update_meta_data( '_hide_nyp_minimum', 'yes' );
		$once_product->update_meta_data( '_min_price', wc_format_decimal( 1.0 ) );
		$once_product->update_meta_data( '_nyp', 'yes' );
		$once_product->set_virtual( true );
		$once_product->set_downloadable( true );
		$once_product->set_catalog_visibility( 'hidden' );
		$once_product->set_sold_individually( true );
		$once_product->save();

		$parent_product->set_children(
			[
				$monthly_product->get_id(),
				$yearly_product->get_id(),
				$once_product->get_id(),
			]
		);
		$parent_product->save();
		update_option( self::DONATION_PRODUCT_ID_OPTION, $parent_product->get_id() );
	}

	/**
	 * Update the donations products.
	 *
	 * @param string $args Donations settings.
	 */
	protected static function update_donation_product( $args ) {
		$defaults   = self::get_donation_default_settings();
		$args       = wp_parse_args( $args, $defaults );
		$product_id = get_option( self::DONATION_PRODUCT_ID_OPTION, 0 );

		$parent_product = \wc_get_product( $product_id );
		$parent_product->set_name( $args['name'] );
		$suggested_amounts = array_map( 'wc_format_decimal', $args['suggestedAmounts'] );
		sort( $suggested_amounts, SORT_NUMERIC );
		$parent_product->update_meta_data( self::DONATION_SUGGESTED_AMOUNT_META, $suggested_amounts );
		$parent_product->update_meta_data( self::DONATION_UNTIERED_SUGGESTED_AMOUNT_META, wc_format_decimal( $args['suggestedAmountUntiered'] ) );
		$parent_product->update_meta_data( self::DONATION_TIERED_META, (bool) $args['tiered'] );
		$parent_product->set_status( 'publish' );
		$parent_product->save();

		$default_price = $args['tiered'] ? wc_format_decimal( $args['suggestedAmounts'][ floor( count( $args['suggestedAmounts'] ) / 2 ) ] ) : wc_format_decimal( $args['suggestedAmountUntiered'] );

		foreach ( $parent_product->get_children() as $child_id ) {
			$child_product = \wc_get_product( $child_id );
			if ( ! $child_product ) {
				continue;
			}

			$yearly_price = 12 * $default_price;
			$child_product->set_status( 'publish' );
			$child_product->set_regular_price( $default_price );
			$child_product->update_meta_data( '_suggested_price', $default_price );
			if ( 'subscription' === $child_product->get_type() ) {
				if ( 'year' === $child_product->get_meta( '_subscription_period', true ) ) {
					/* translators: %s: Product name */
					$child_product->set_name( sprintf( __( '%s: Yearly', 'newspack' ), $args['name'] ) );
					$child_product->update_meta_data( '_subscription_price', \wc_format_decimal( $yearly_price ) );
					$child_product->update_meta_data( '_suggested_price', \wc_format_decimal( $yearly_price ) );
					$child_product->set_regular_price( $yearly_price );
				} else {
					/* translators: %s: Product name */
					$child_product->set_name( sprintf( __( '%s: Monthly', 'newspack' ), $args['name'] ) );
					$child_product->update_meta_data( '_subscription_price', wc_format_decimal( $default_price ) );
				}
			} else {
				/* translators: %s: Product name */
				$child_product->set_name( sprintf( __( '%s: One-Time', 'newspack' ), $args['name'] ) );
				$child_product->set_regular_price( $yearly_price );
				$child_product->update_meta_data( '_suggested_price', $yearly_price );
			}
			$child_product->save();
		}
	}

	/**
	 * Remove all donation products from the cart.
	 */
	public static function remove_donations_from_cart() {
		$donation_settings = self::get_donation_settings();
		if ( ! $donation_settings['created'] || is_wp_error( self::is_woocommerce_suite_active() ) ) {
			return;
		}

		foreach ( \WC()->cart->get_cart() as $cart_key => $cart_item ) {
			if ( ! empty( $cart_item['product_id'] ) && in_array( $cart_item['product_id'], $donation_settings['products'] ) ) {
				\WC()->cart->remove_cart_item( $cart_key );
			}
		}
	}

	/**
	 * Is NRH the donation platform?
	 */
	public static function is_platform_nrh() {
		return 'nrh' === get_option( NEWSPACK_READER_REVENUE_PLATFORM );
	}

	/**
	 * Is WooCommerce the donation platform?
	 */
	public static function is_platform_wc() {
		$wc_configuration_manager = Configuration_Managers::configuration_manager_class_for_plugin_slug( 'woocommerce' );
		return 'wc' === get_option( NEWSPACK_READER_REVENUE_PLATFORM ) && $wc_configuration_manager->is_active();
	}

	/**
	 * Handle submission of the donation form.
	 */
	public static function process_donation_form() {
		$is_wc = self::is_platform_wc();

		$donation_form_submitted = filter_input( INPUT_GET, 'newspack_donate', FILTER_SANITIZE_NUMBER_INT );
		if ( ! $donation_form_submitted || ( $is_wc && is_wp_error( self::is_woocommerce_suite_active() ) ) ) {
			return;
		}

		$donation_settings = self::get_donation_settings();
		if ( ! $donation_settings['created'] ) {
			return;
		}

		// Parse values from the form.
		$donation_frequency = filter_input( INPUT_GET, 'donation_frequency', FILTER_SANITIZE_STRING );
		if ( ! $donation_frequency ) {
			return;
		}
		$donation_value = filter_input( INPUT_GET, 'donation_value_' . $donation_frequency, FILTER_SANITIZE_STRING );
		if ( ! $donation_value ) {
			$donation_value = filter_input( INPUT_GET, 'donation_value_' . $donation_frequency . '_untiered', FILTER_SANITIZE_STRING );

			if ( ! $donation_value ) {
				return;
			}
		}
		if ( 'other' === $donation_value ) {
			$donation_value = filter_input( INPUT_GET, 'donation_value_' . $donation_frequency . '_other', FILTER_SANITIZE_STRING );
			if ( ! $donation_value ) {
				return;
			}
		}

		$referer    = wp_get_referer();
		$params     = [];
		$parsed_url = wp_parse_url( $referer );

		// Get URL params appended to the referer URL.
		if ( ! empty( $parsed_url['query'] ) ) {
			wp_parse_str( $parsed_url['query'], $params );
		}

		if ( function_exists( 'wpcom_vip_url_to_postid' ) ) {
			$referer_post_id = wpcom_vip_url_to_postid( $referer );
		} else {
			$referer_post_id = url_to_postid( $referer ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid
		}
		$referer_tags       = [];
		$referer_categories = [];
		$tags               = get_the_tags( $referer_post_id );
		if ( $tags && ! empty( $tags ) ) {
			$referer_tags = array_map(
				function ( $item ) {
					return $item->slug;
				},
				$tags
			);
		}
		$categories = get_the_category( $referer_post_id );
		if ( $categories && ! empty( $categories ) ) {
			$referer_categories = array_map(
				function ( $item ) {
					return $item->slug;
				},
				$categories
			);
		}

		if ( $is_wc ) {
			// Add product to cart.
			$product_id = $donation_settings['products'][ $donation_frequency ];
			if ( ! $product_id ) {
				return;
			}

			self::remove_donations_from_cart();

			\WC()->cart->add_to_cart(
				$product_id,
				1,
				0,
				[],
				[
					'nyp' => (float) \WC_Name_Your_Price_Helpers::standardize_number( $donation_value ),
				]
			);
		}

		$query_args = [];

		if ( ! empty( $referer_tags ) ) {
			$query_args['referer_tags'] = implode( ',', $referer_tags );
		}
		if ( ! empty( $referer_categories ) ) {
			$query_args['referer_categories'] = implode( ',', $referer_categories );
		}

		// Pass through UTM params so they can be forwarded to the WooCommerce checkout flow.
		foreach ( $params as $param => $value ) {
			if ( 'utm' === substr( $param, 0, 3 ) ) {
				$param                = sanitize_text_field( $param );
				$query_args[ $param ] = sanitize_text_field( $value );
			}
		}

		$checkout_url = add_query_arg(
			$query_args,
			$is_wc ? \wc_get_page_permalink( 'checkout' ) : ''
		);

		// Redirect to checkout.
		\wp_safe_redirect( apply_filters( 'newspack_donation_checkout_url', $checkout_url, $donation_value, $donation_frequency ) );
		exit;
	}

	/**
	 * Create the donation page prepopulated with CTAs for the subscriptions.
	 *
	 * @return int Post ID of page.
	 */
	public static function create_donation_page() {
		$revenue_model = 'donations';

		$intro           = esc_html__( 'With the support of readers like you, we provide thoughtfully researched articles for a more informed and connected community. This is your chance to support credible, community-based, public-service journalism. Please join us!', 'newspack' );
		$content_heading = esc_html__( 'Donation', 'newspack' );
		$content         = esc_html__( "Edit and add to this content to tell your publication's story and explain the benefits of becoming a member. This is a good place to mention any special member privileges, let people know that donations are tax-deductible, or provide any legal information.", 'newspack' );

		$intro_block           = '
			<!-- wp:paragraph -->
				<p>%s</p>
			<!-- /wp:paragraph -->';
		$content_heading_block = '
			<!-- wp:heading -->
				<h2>%s</h2>
			<!-- /wp:heading -->';
		$content_block         = '
			<!-- wp:paragraph -->
				<p>%s</p>
			<!-- /wp:paragraph -->';

		$page_content = sprintf( $intro_block, $intro );
		if ( 'donations' === $revenue_model ) {
			$page_content .= self::get_donations_block();
		} elseif ( 'subscriptions' === $revenue_model ) {
			$page_content .= self::get_subscriptions_block();
		}
		$page_content .= sprintf( $content_heading_block, $content_heading );
		$page_content .= sprintf( $content_block, $content );

		$page_args = [
			'post_type'      => 'page',
			'post_title'     => __( 'Support our publication', 'newspack' ),
			'post_content'   => $page_content,
			'post_excerpt'   => __( 'Support quality journalism by joining us today!', 'newspack' ),
			'post_status'    => 'draft',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		];

		$page_id = wp_insert_post( $page_args );
		if ( is_numeric( $page_id ) ) {
			self::set_donation_page( $page_id );
		}
		return $page_id;
	}

	/**
	 * Get raw content for a pre-populated WC featured product block featuring subscriptions.
	 *
	 * @return string Raw block content.
	 */
	protected static function get_subscriptions_block() {
		$button_text   = __( 'Join', 'newspack' );
		$subscriptions = wc_get_products(
			[
				'limit'                           => -1,
				'only_get_newspack_subscriptions' => true,
				'return'                          => 'ids',
			]
		);
		$num_products  = count( $subscriptions );
		if ( ! $num_products ) {
			return '';
		}

		$id_list = esc_attr( implode( ',', $subscriptions ) );

		$block_format = '
		<!-- wp:woocommerce/handpicked-products {"columns":%d,"editMode":false,"contentVisibility":{"title":true,"price":true,"rating":false,"button":true},"orderby":"price_asc","products":[%s]} -->
			<div class="wp-block-woocommerce-handpicked-products is-hidden-rating">[products limit="%d" columns="%d" orderby="price" order="ASC" ids="%s"]</div>
		<!-- /wp:woocommerce/handpicked-products -->';

		$block = sprintf( $block_format, $num_products, $id_list, $num_products, $num_products, $id_list );
		return $block;
	}

	/**
	 * Get raw content for a pre-populated Newspack Donations block.
	 *
	 * @return string Raw block content.
	 */
	protected static function get_donations_block() {
		$block = '<!-- wp:newspack-blocks/donate /-->';
		return $block;
	}

	/**
	 * Set the donation page.
	 *
	 * @param int $page_id The post ID of the donation page.
	 */
	protected static function set_donation_page( $page_id ) {
		update_option( self::DONATION_PAGE_ID_OPTION, $page_id );
	}

	/**
	 * Get info about the donation page.
	 *
	 * @param int $page_id Optional ID of page to get info for. Default: saved donation page.
	 * @return array|bool Array of info, or false if page is not created.
	 */
	public static function get_donation_page_info( $page_id = 0 ) {
		if ( ! $page_id ) {
			$page_id = get_option( self::DONATION_PAGE_ID_OPTION, 0 );
		}
		if ( ! $page_id || 'page' !== get_post_type( $page_id ) ) {
			$page_id = self::create_donation_page();
		}

		return [
			'id'      => $page_id,
			'url'     => get_permalink( $page_id ),
			'editUrl' => html_entity_decode( get_edit_post_link( $page_id ) ),
			'status'  => get_post_status( $page_id ),
		];
	}

	/**
	 * Add a hidden billing fields with tags and categories.
	 *
	 * @param Array $form_fields WC form fields.
	 */
	public static function woocommerce_billing_fields( $form_fields ) {
		$params = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );

		if ( is_array( $params ) ) {
			foreach ( $params as $param => $value ) {
				if ( $value && in_array( $param, array_keys( self::DONATION_ORDER_META_KEYS ) ) ) {
					$form_fields[ sanitize_text_field( $param ) ] = [
						'type'    => 'text',
						'default' => sanitize_text_field( $value ),
						'class'   => [ 'hide' ],
					];
				}
			}
		}

		return $form_fields;
	}

	/**
	 * Update WC order with the client id from hidden form field.
	 *
	 * @param String $order_id WC order id.
	 */
	public static function woocommerce_checkout_update_order_meta( $order_id ) {
		$params = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		if ( is_array( $params ) ) {
			foreach ( $params as $param => $value ) {
				if ( in_array( $param, array_keys( self::DONATION_ORDER_META_KEYS ) ) ) {
					update_post_meta( $order_id, sanitize_text_field( $param ), sanitize_text_field( $value ) );
				}
			}
		}
	}
}
Donations::init();
