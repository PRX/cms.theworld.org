<?php
/**
 * Plugin Name: TW Menus
 * Description: Register menus.
 *
 * @package tw_menus
 */

if ( ! function_exists( 'tw_menus_init' ) ) :
	/**
	 * Plugin init.
	 * Should register menus.
	 *
	 * @return void
	 */
	function tw_menus_init() {
		register_nav_menus(
			array(
				'header-menu'               => __( 'Header Menu', 'tw_menus' ),
				'main-menu'                 => __( 'Main Menu', 'tw_menus' ),
				'top-menu'                  => __( 'Top Menu', 'tw_menus' ),
				'social-menu'               => __( 'Social Links Menu', 'tw_menus' ),
				'footer-menu'               => __( 'Footer Menu', 'tw_menus' ),
				'homepage-quick-links-menu' => __( 'Homepage Quick Links Menu', 'tw_menus' ),
			)
		);
	}

endif;
add_action( 'init', 'tw_menus_init' );

if ( ! function_exists( 'tw_menus_graphql_resolve_field_menuitem_url' ) ) :
	/**
	 * Resolver for menu item url field. Should convert URL's that start with site URL to local paths.
	 *
	 * @param mixed           $result The result of the field resolution.
	 * @param mixed           $source The source passed down the Resolve Tree.
	 * @param array           $args The args for the field.
	 * @param AppContext      $context The AppContext passed down the ResolveTree.
	 * @param ResolveInfo     $info The ResolveInfo passed down the ResolveTree.
	 * @param string          $type_name The name of the type the fields belong to.
	 * @param string          $field_key The name of the field.
	 * @param FieldDefinition $field The Field Definition for the resolving field.
	 * @param mixed           $field_resolver The Field Definition for the resolving field.
	 * @return mixed
	 */
	// phpcs:ignore
	function tw_menus_graphql_resolve_field_menuitem_url( $result, $source, $args, $context, $info, $type_name, $field_key, $field, $field_resolver ) {

		if ( 'menuitem' === strtolower( $type_name ) && 'url' === $field_key ) {
			$site_url = site_url();
			return preg_replace( '~/$~', '', str_replace( $site_url, '', $result ) );
		}

		return $result;
	}
endif;
add_filter( 'graphql_resolve_field', 'tw_menus_graphql_resolve_field_menuitem_url', 10, 9 );
