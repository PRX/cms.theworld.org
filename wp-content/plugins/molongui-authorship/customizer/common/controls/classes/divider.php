<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Divider_Control' ) )
{
	class Molongui_Customize_Divider_Control extends WP_Customize_Control
	{
		public $type = 'molongui-divider';
		public function render_content()
		{
			?>
                <hr>
			<?php
		}
	}
}