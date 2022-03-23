<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Number_Control' ) )
{
	class Molongui_Customize_Number_Control extends WP_Customize_Control
	{
		public $type = 'molongui-number';
		public function render_content()
		{
			$input_id         = '_customize-input-' . $this->id;
			$description_id   = '_customize-description-' . $this->id;
			$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

			?>
			<?php if ( ! empty( $this->label ) ) : ?>
                <label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title">
                    <?php echo esc_html( $this->label ); ?>
                    <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                        <img class="molongui-premium-setting-label" style="width: 18px;" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                    <?php endif; ?>
                </label>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<input type="number" id="<?php echo esc_attr( $input_id ); ?>" <?php echo $describedby_attr; ?>
			       min="<?php echo $this->input_attrs['min']; ?>" max="<?php echo $this->input_attrs['max']; ?>" step="<?php echo $this->input_attrs['step']; ?>"
				   <?php if ( !isset( $this->input_attrs['value'] ) ) : ?>
						value="<?php echo esc_attr( $this->value() ); ?>"
				   <?php endif; ?>
				   <?php $this->link(); ?>
			/>

			<?php
		}
	}
}