<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Range_Control' ) )
{
	class Molongui_Customize_Range_Control extends WP_Customize_Control
    {
        public $type = 'molongui-range';
        public function enqueue()
        {
            wp_enqueue_script(
	            'molongui-custom-controls',
	            plugin_dir_url( dirname( __FILE__ ) ).'js/scripts.min.js',
                array( 'jquery' ),
                false,
                true
            );

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
	        $input_id         = '_customize-input-' . $this->id;
	        $description_id   = '_customize-description-' . $this->id;
	        $describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
	        $string  = 'molongui-compact-';
	        $compact = ( substr( $this->type, 0, strlen( $string ) ) === $string ? true : false );
            $style = explode( '-', $this->type );
            $style = end( $style );

            ?>
            <div class="molongui-range-control">

                <label for="<?php echo esc_attr( $input_id ); ?>" >

	                <?php if ( !$compact ) : ?>

                        <?php if ( !empty( $this->label ) ) : ?>
                            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                            <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                <img class="molongui-premium-setting-label" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ( !empty( $this->description ) ) : ?>
                            <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                        <?php endif; ?>

	                <?php else : ?>

                        <div class="molongui-compact-setting">
                            <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                            <div class="molongui-compact-setting-input">

                    <?php endif; ?>

                    <?php switch( $style )
	                {
		                case 'plain': ?>
                            <div class="molongui-range-slider-plain">
                                <input id="<?php echo esc_attr( $input_id ); ?>" <?php echo $describedby_attr; ?> class="molongui-range-slider-plain__input" data-input-type="range" type="range" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                                <div class="molongui-range-slider-plain__value"><?php echo esc_attr( $this->value() ).'%'; ?></div>
                            </div>
			            <?php break;

		                case 'flat': ?>
                            <div class="molongui-range-slider-flat" <?php if ( isset( $this->input_attrs['off'] ) and $this->input_attrs['off'] ) echo "data-off='true'" ?>>
                                <span class="molongui-range-slider-flat-wrapper">
                                    <input class="molongui-range-slider-flat__range" type="range" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->input_attrs(); $this->link(); ?>>
                                    <span class="molongui-range-slider-flat__value">0</span>
                                </span>
                            </div>
			            <?php break;

	                } ?>

	                <?php if ( $compact ) : ?>

                            </div> <!-- !.molongui-compact-setting-input -->
                        </div> <!-- !.molongui-compact-setting -->

                    <?php endif; ?>

                </label>

            </div> <!-- !.molongui-range-control -->
	        <?php
        }
    }
}