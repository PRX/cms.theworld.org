<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Notice_Control' ) )
{
	class Molongui_Customize_Notice_Control extends WP_Customize_Control
	{
		public $type = 'molongui-notice';
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
			$allowed_html = array(
				'a' => array
				(
					'href'   => array(),
					'target' => array(),
					'title'  => array(),
					'class'  => array(),
                    'style'  => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'i'      => array
				(
					'class' => array(),
					'style' => array(),
				),
				'span'   => array
				(
					'class' => array(),
					'style' => array(),
				),
				'code'   => array(),
			);
			?>
			<div class="molongui-notice-control" style="<?php echo ( !empty( $this->input_attrs['bg'] ) ? 'background:'.$this->input_attrs['bg'].';' : '' ); ?> <?php echo ( !empty( $this->input_attrs['color'] ) ? 'color:'.$this->input_attrs['color'].';' : '' ); ?>">
				<?php if( !empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				<?php if( !empty( $this->description ) ) : ?>
					<span class="customize-control-description"><?php echo wp_kses( $this->description, $allowed_html ); ?></span>
				<?php endif; ?>
			</div>
			<?php
		}
	}
}