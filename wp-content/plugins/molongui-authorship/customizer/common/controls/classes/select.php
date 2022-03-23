<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Select_Control' ) )
{
	class Molongui_Customize_Select_Control extends WP_Customize_Control
	{
		public $type = 'molongui-select';
		public function render_content()
		{
			if ( empty( $this->choices ) ) return;
			$input_id         = '_customize-input-' . $this->id;
			$description_id   = '_customize-description-' . $this->id;
			$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
            $string  = 'molongui-compact-';
			$compact = ( substr( $this->type, 0, strlen( $string ) ) === $string ? true : false );

			?>
            <div class="molongui-select-control">

                <?php if ( !$compact ) : ?>

                    <?php if ( !empty( $this->label ) ) : ?>
                        <label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title">
                            <?php echo esc_html( $this->label ); ?>
                            <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                <img class="molongui-premium-setting-label" style="width: 18px;" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                            <?php endif; ?>
                        </label>
                    <?php endif; ?>

                    <?php if ( !empty( $this->description ) ) : ?>
                        <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                    <?php endif; ?>

                <?php else : ?>

                    <div class="molongui-compact-setting">
                        <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                        <div class="molongui-compact-setting-input">

                <?php endif; ?>

                            <select id="<?php echo esc_attr( $input_id ); ?>" <?php echo $describedby_attr; ?> <?php $this->link(); ?>>
                                <?php foreach ( $this->choices as $value => $choice ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $this->value(), $value, true ); ?> <?php echo ( ( isset( $choice['disabled'] ) and $choice['disabled'] ) ? 'disabled' : '' ); ?> ><?php echo esc_attr( $choice['label'] ); ?></option>
                                <?php endforeach; ?>
                            </select>

                <?php if ( $compact ) : ?>

                        </div> <!-- !.molongui-compact-setting-input -->
                    </div> <!-- !.molongui-compact-setting -->

		        <?php endif; ?>

            </div> <!-- !.molongui-select-control -->
            <?php
		}
	}
}