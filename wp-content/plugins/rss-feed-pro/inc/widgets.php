<?php 

// Adds widget: Widget
class RSSFP_RSSFeedProWidget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'rssfeedpro',
			esc_html__( 'RSS Feed Pro Archive', 'rss-feed-pro' ),
			array( 'description' => esc_html__( 'Display an RSS Feed on your blog by widget. Sort your RSS feed archive by Category, Year, and by Author Name', 'rss-feed-pro' ), )
		);
	}

	public function the_fields(){

		$field_data    = [];
		$widget_fields = [];

		$args          = array(
			'post_type'      => 'rssfp-shortcode',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ){
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$post_id             = get_the_ID();
				$post_title          = get_the_title();
				$field_data['id']    = $post_id.'_rssfp_widget_field';
				$field_data['type']  = 'checkbox';
				$field_data['label'] = $post_title;
				$field_data['value'] = $post_id;

				array_push( $widget_fields, $field_data );

			}
		}

		wp_reset_postdata();

		return $widget_fields;
	}


	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$shortcode_ids = array_filter( $instance );
		unset( $shortcode_ids['title'] );

		if ( !empty( $shortcode_ids ) && count( $shortcode_ids ) >=1 ) {

			$RSSFP_RSSFeedProObject = new RSSFP_RSSFeedPro();

			foreach ( $shortcode_ids as $shortcode_id ) {

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

				echo RSSFP_RSSFeedPro::render_options( $opts );
			}
		}

		echo $args['after_widget'];
	}

	public function field_generator( $instance ) {

		$widget_fields = self::the_fields();
		$output        = '';

		foreach ( $widget_fields as $widget_field ) {
			$default = '';
			if ( isset($widget_field['default']) ) {
				$default = $widget_field['default'];
			}
			$widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $default, 'rss-feed-pro' );
			switch ( $widget_field['type'] ) {
				case 'checkbox':
					$output .= '<p>';
					$output .= '<input class="checkbox" type="checkbox" '.checked( $widget_value, $widget_field['value'], false ).' id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" value="'.esc_attr( $widget_field['value'] ).'">';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'rss-feed-pro' ).'</label>';
					$output .= '</p>';
					break;
				default:
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'rss-feed-pro' ).':</label> ';
					$output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_field['value'] ).'">';
					$output .= '</p>';
			}
		}
		echo $output;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'rss-feed-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$this->field_generator( $instance );
	}

	public function update( $new_instance, $old_instance ) {

		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$widget_fields     = self::the_fields();

		foreach ( $widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				default:
					$instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
			}
		}
		return $instance;
	}
}

function register_new_widget() {
	register_widget( 'RSSFP_RSSFeedProWidget' );
}
add_action( 'widgets_init', 'register_new_widget' );