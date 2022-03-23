<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Heading_Control' ) )
{
	class Molongui_Customize_Heading_Control extends WP_Customize_Control
	{
		public $type = 'molongui-heading';
		public function render_content()
		{
			?>
				<?php if ( !empty( $this->label ) ) : ?>
                    <h2 style="margin-top: 2em; margin-bottom: 0; border-bottom:1px solid lightgrey; padding-bottom:10px; <?php $this->input_attrs(); ?>">
                        <?php echo esc_html( $this->label ); ?>
                    </h2>
                    <?php if ( !empty( $this->description ) ) : ?>
                        <p style="margin-top:10px; margin-bottom:0; font-size:95%; color: #909090;"><?php echo esc_html( $this->description ); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
			<?php
		}
	}
}