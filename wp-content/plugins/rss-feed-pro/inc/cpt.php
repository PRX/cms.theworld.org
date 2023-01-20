<?php

add_action( 'init', 'rssfp_shortcode_CPT' );
function rssfp_shortcode_CPT() {
$labels = array(
	'name'                  => _x( 'RSS Archives', 'Post Type General Name', 'rss-feed-pro' ),
	'singular_name'         => _x( 'RSS Archive', 'Post Type Singular Name', 'rss-feed-pro' ),
	'menu_name'             => __( 'RSS Archives', 'rss-feed-pro' ),
	'name_admin_bar'        => __( 'RSS Archive', 'rss-feed-pro' ),
	'archives'              => __( 'RSS Archive Archives', 'rss-feed-pro' ),
	'attributes'            => __( 'RSS Archive Attributes', 'rss-feed-pro' ),
	'parent_item_colon'     => __( 'Parent RSS Archive:', 'rss-feed-pro' ),
	'all_items'             => __( 'All RSS Archives', 'rss-feed-pro' ),
	'add_new_item'          => __( 'Add New RSS Archive', 'rss-feed-pro' ),
	'add_new'               => __( 'Add New', 'rss-feed-pro' ),
	'new_item'              => __( 'New RSS Archive', 'rss-feed-pro' ),
	'edit_item'             => __( 'Edit RSS Archive', 'rss-feed-pro' ),
	'update_item'           => __( 'Update RSS Archive', 'rss-feed-pro' ),
	'view_item'             => __( 'View RSS Archive', 'rss-feed-pro' ),
	'view_items'            => __( 'View RSS Archives', 'rss-feed-pro' ),
	'search_items'          => __( 'Search RSS Archive', 'rss-feed-pro' ),
	'not_found'             => __( 'Not found', 'rss-feed-pro' ),
	'not_found_in_trash'    => __( 'Not found in Trash', 'rss-feed-pro' ),
	'featured_image'        => __( 'Featured Image', 'rss-feed-pro' ),
	'set_featured_image'    => __( 'Set featured image', 'rss-feed-pro' ),
	'remove_featured_image' => __( 'Remove featured image', 'rss-feed-pro' ),
	'use_featured_image'    => __( 'Use as featured image', 'rss-feed-pro' ),
	'insert_into_item'      => __( 'Insert into item', 'rss-feed-pro' ),
	'uploaded_to_this_item' => __( 'Uploaded to this item', 'rss-feed-pro' ),
	'items_list'            => __( 'RSS Archives list', 'rss-feed-pro' ),
	'items_list_navigation' => __( 'RSS Archives list navigation', 'rss-feed-pro' ),
	'filter_items_list'     => __( 'Filter items list', 'rss-feed-pro' ),
);
$args = array(
	'label'                 => __( 'RSS Archive', 'rss-feed-pro' ),
	'description'           => __( 'RSS Archives', 'rss-feed-pro' ),
	'labels'                => $labels,
	'supports'              => array( 'title', 'editor', 'thumbnail'),
	'hierarchical'          => false,
	'public'                => true,
	'show_ui'               => true,
	'show_in_menu'          => true,
	'show_in_admin_bar'     => true,
	'show_in_nav_menus'     => true,
	'can_export'            => true,
	'has_archive'           => true,		
	'exclude_from_search'   => false,
	'publicly_queryable'    => true,
	'capability_type'       => 'page',
	'menu_icon'             => 'dashicons-rss',
	"rewrite" => array( "slug" => "rssfp-shortcode", "with_front" => true ),
	'supports' => array('title','author'),
);

register_post_type( 'rssfp-shortcode', $args );
}


add_action( 'add_meta_boxes', 'rssfp_add_custom_box' );
function rssfp_add_custom_box(  ) {
	add_meta_box(
        'rssfp-id',// Unique ID
        'Shortcode Details',// Box title
        'rssfp_box_html',// Content callback, must be of type callable
        ['rssfp-shortcode']// Post type
    );
}

function rssfp_box_html() {
    $post_id = get_the_ID();
	?>
	<table>
		<tbody>
			<tr>
				<td>
					<label><?php echo esc_html_e( 'Feed url', 'rss-feed-pro' ); ?></label>
				</td>
				<td>
					<input type="text" name="rssfp-meta-feed-url" value="<?php echo get_post_meta( $post_id, 'rssfp-meta-feed-url', true ); ?>">
				</td>
			</tr>
			<tr>
				<td>
					<label><?php echo esc_html_e( 'Sort by', 'rss-feed-pro' ); ?></label>
				</td>
				<td>
					<select name="rssfp-meta-sort-by">
						<?php $selected = get_post_meta( $post_id, 'rssfp-meta-sort-by', true ); ?>
						<option>---</option>
						<option value="year" <?php selected( $selected, 'year' ); ?> >
							<?php echo esc_html_e( 'Year', 'rss-feed-pro' ); ?>
						</option>
						<option value="category" <?php selected( $selected, 'category' ); ?>>
							<?php echo esc_html_e( 'Category', 'rss-feed-pro' ); ?>
						</option>
						<option value="author" <?php selected( $selected, 'author' ); ?>>
							<?php echo esc_html_e( 'Author', 'rss-feed-pro' ); ?>
						</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php if ( !empty( $post_id ) ): ?>
		<p>
			<strong><?php echo esc_html_e( 'Your Shortcode:', 'rss-feed-pro' ); ?></strong><br>
		    <?php echo "[rss_feed_pro_sort id='".$post_id."']"; ?>
		</p>
	<?php endif ?>
	<?php
}


add_action( 'save_post', 'rssfp_save_postdata' );
function rssfp_save_postdata( $post_id ){

	$post_id = get_the_ID();

	if ( !empty( $post_id ) && isset( $_POST['rssfp-meta-feed-url'] ) && isset( $_POST['rssfp-meta-sort-by'] )) {
		update_post_meta(
			$post_id,
			'rssfp-meta-feed-url',
			sanitize_text_field( $_POST['rssfp-meta-feed-url'] )
		);
		update_post_meta(
			$post_id,
			'rssfp-meta-sort-by',
			sanitize_text_field( $_POST['rssfp-meta-sort-by'] )
		);
	}
	
}