<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Group_Label_Control' ) )
{
	class Molongui_Customize_Group_Label_Control extends WP_Customize_Control
	{
		public $type = 'molongui-group-label';
		public function enqueue()
		{
			wp_enqueue_style(
				'molongui-custom-controls',
				plugin_dir_url( dirname( __FILE__ ) ).'css/styles.min.css',
				array(),
				false,
				'all'
			);
		}
		public function render_content()
		{
			?>
			<div class="molongui-group-label-control">

				<?php if ( !empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<?php if ( !empty( $this->description ) ) : ?>
					<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>

			</div> <!-- !.molongui-group-label-control -->
			<?php
		}
	}
}