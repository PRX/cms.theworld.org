<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Text_Radio_Button_Control' ) )
{
	class Molongui_Customize_Text_Radio_Button_Control extends WP_Customize_Control
	{
		public $type = 'molongui-text-radio';
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
			if ( empty( $this->choices ) ) return;
            $is_premium  = !empty( $this->input_attrs['premium']  ) ? true : false;
            $is_disabled = !empty( $this->input_attrs['disabled'] ) ? true : false;
			$input_id         = '_customize-input-' . $this->id;
			$description_id   = '_customize-description-' . $this->id;
			$describedby_attr = ( !empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
			$string  = 'molongui-compact-';
			$compact = ( substr( $this->type, 0, strlen( $string ) ) === $string ? true : false );

			?>
            <div class="molongui-text-radio-button-control <?php echo ( $is_disabled ? 'is-disabled' : '' ); ?>">

			    <?php if ( !$compact ) : ?>

                    <?php if ( !empty( $this->label ) ) : ?>
                        <span class="customize-control-title">
                            <?php echo esc_html( $this->label ); ?>
                            <?php if ( !$is_disabled and $is_premium ) : ?>
                                <img class="molongui-premium-setting-label" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( !empty( $this->description ) ) : ?>
                        <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                    <?php endif; ?>

                <?php else : ?>

                    <div class="molongui-compact-setting">
                        <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                        <div class="molongui-compact-setting-input">

                <?php endif; ?>

                <div class="molongui-text-radio-buttons">
                    <?php foreach ( $this->choices as $key => $value ) : ?>
                        <?php if ( !$value['display'] ) continue; ?>
                        <label class="molongui-text-radio-button-label">
                            <input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" data-molongui-setting="<?php echo ( $value['premium'] ? 'premium' : 'free' ); ?>" <?php checked( esc_attr( $key ), $this->value() ); ?> <?php $this->link(); ?> />
                            <span><?php echo esc_attr( $value['label'] ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php if ( $compact ) : ?>

                        </div> <!-- !.molongui-compact-setting-input -->
                    </div> <!-- !.molongui-compact-setting -->

                <?php endif; ?>

            </div> <!-- !.molongui-text-radio-button-control -->
			<?php
		}
	}
}