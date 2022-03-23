<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Toggle_Control' ) )
{
	class Molongui_Customize_Toggle_Control extends WP_Customize_Control
	{
		public $type = 'molongui-toggle';
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
			$input_id         = '_customize-input-' . $this->id;
			$description_id   = '_customize-description-' . $this->id;
			$describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
			$string  = 'molongui-compact-';
			$compact = ( substr( $this->type, 0, strlen( $string ) ) === $string ? true : false );
			$style = explode( '-', $this->type );
			$style = end( $style );

			?>
            <div class="molongui-range-control">

            <?php switch( $this->type )
            {
				case 'light':
					?>
                    <div class="checkbox_switch">

                        <?php if ( $compact ) : ?>
                            <div class="molongui-compact-setting">
                                    <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                                    <div class="molongui-compact-setting-input">
                        <?php endif; ?>

                        <div class="onoffswitch">
                            <input type="checkbox" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" <?php echo $describedby_attr; ?> class="onoffswitch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
                            <label class="onoffswitch-label" for="<?php echo esc_attr($this->id); ?>"></label>
                        </div>

                        <?php if ( $compact ) : ?>

                                </div> <!-- !.molongui-compact-setting-input -->
                            </div> <!-- !.molongui-compact-setting -->

                        <?php else : ?>

                            <span class="customize-control-title onoffswitch_label">
                                <?php echo esc_html( $this->label ); ?>
                                <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                    <img class="molongui-premium-setting-label" style="width: 18px;" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                                <?php endif; ?>
                            </span>

                            <?php if( !empty( $this->description ) ) : ?>
                                <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                            <?php endif; ?>

                        <?php endif; ?>

                    </div>
					<?php
				break;

				case 'label':
                case 'flat':
                default:
                    ?>


				        <?php if ( $compact ) : ?>
                            <div class="molongui-compact-setting">
                                <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                                <div class="molongui-compact-setting-input">
			            <?php endif; ?>

                        <div class="molongui-toggle-switch">
                            <input type="checkbox" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" <?php echo $describedby_attr; ?> class="molongui-toggle-switch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
                            <label class="molongui-toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
                                <span class="molongui-toggle-switch-inner <?php echo ( $this->type == 'label' ? 'molongui-toggle-switch-inner-label' : '' ); ?>"></span>
                                <span class="molongui-toggle-switch-switch"></span>
                            </label>
                        </div>

                        <?php if ( $compact ) : ?>

                                </div> <!-- !.molongui-compact-setting-input -->
                            </div> <!-- !.molongui-compact-setting -->

                        <?php else : ?>

                            <span class="customize-control-title">
                                <?php echo esc_html( $this->label ); ?>
                                <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                    <img class="molongui-premium-setting-label" style="width: 18px;" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                                <?php endif; ?>
                            </span>

                            <?php if( !empty( $this->description ) ) : ?>
                                <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                            <?php endif; ?>

                        <?php endif; ?>

                    </div>
                    <?php
                break;
			}
			?>
		    </div> <!-- !.molongui-toggle-control -->
	        <?php
        }
    }
}