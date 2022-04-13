<?php
/*
Plugin Name: TW Calls to Action
Description: Defines the Sidebars that house Call to Actions
*/

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function tw_calls_to_action_widgets_init() {

	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'tw_calls_to_action' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your sidebar.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title accent-header"><span>',
			'after_title'   => '</span></h2>',
      'show_in_rest'  => true,
      'show_instance_in_rest' => true,
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Slide-out Sidebar', 'tw_calls_to_action' ),
			'id'            => 'header-1',
			'description'   => esc_html__( 'Add widgets here to appear in an off-screen sidebar when it is enabled under the Customizer Header Settings.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="below-content widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
      'show_in_rest'  => true,
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'tw_calls_to_action' ),
			'id'            => 'footer-1',
			'description'   => __( 'Add widgets here to appear in your footer.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
      'show_in_rest'  => true,
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Above Copyright', 'tw_calls_to_action' ),
			'id'            => 'footer-2',
			'description'   => __( 'Add widgets here to appear below the footer, above the copyright information.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'show_in_rest'  => true,
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Article above content', 'tw_calls_to_action' ),
			'id'            => 'article-1',
			'description'   => __( 'Add widgets here to appear above article content.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="above-content widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'show_in_rest'  => true,
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Article below content', 'tw_calls_to_action' ),
			'id'            => 'article-2',
			'description'   => __( 'Add widgets here to appear below article content.', 'tw_calls_to_action' ),
			'before_widget' => '<section id="%1$s" class="below-content widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'show_in_rest'  => true,
		)
	);

}
add_action( 'widgets_init', 'tw_calls_to_action_widgets_init' );
