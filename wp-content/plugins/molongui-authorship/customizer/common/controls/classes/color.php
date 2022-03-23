<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Color_Control' ) )
{
	class Molongui_Customize_Color_Control extends WP_Customize_Control
	{
		public $type = 'molongui-color';
		public $palette;
		public $show_opacity;
		public function enqueue()
		{
			wp_enqueue_script(
				'molongui-custom-controls',
				plugin_dir_url( dirname( __FILE__ ) ).'js/scripts.min.js',
				array( 'jquery', 'wp-color-picker' ),
				false,
				true
			);

			wp_enqueue_style(
				'molongui-custom-controls',
				plugin_dir_url( dirname( __FILE__ ) ).'css/styles.min.css',
				array( 'wp-color-picker' ),
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
			if ( is_array( $this->palette ) )
			{
				$palette = implode( '|', $this->palette );
			}
			else
			{
				$palette = ( false === $this->palette || 'false' === $this->palette ) ? 'false' : 'true';
			}
			$show_opacity = ( false === $this->show_opacity || 'false' === $this->show_opacity ) ? 'false' : 'true';

			?>
            <div class="molongui-color-control">

	            <?php if ( !$compact ) : ?>

		            <?php if ( !empty( $this->label ) ) : ?>
                        <label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title">
                            <?php echo esc_html( $this->label ); ?>
                            <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                <img class="molongui-premium-setting-label" src="<?php echo MOLONGUI_AUTHORSHIP_URL; ?>assets/img/common/lock.png" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>" />
                            <?php endif; ?>
                        </label>
                    <?php endif; ?>

                    <?php if ( !empty( $this->description ) ) : ?>
                        <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                    <?php endif; ?>

                <?php else : ?>

                    <div class="molongui-compact-setting molongui-compact-color-setting">
                        <div class="molongui-compact-setting-label"><?php echo $this->label; ?></div>
                        <div class="molongui-compact-setting-input">
			                <?php if ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) : ?>
                                <div class="molongui-premium-setting-label" title="<?php _e( 'Premium setting. Only preview available.', 'molongui-authorship' ); ?>"></div>
			                <?php endif; ?>

                <?php endif; ?>

                            <input id="<?php echo esc_attr( $input_id ); ?>" <?php echo $describedby_attr; ?> data-molongui-setting="<?php echo ( ( isset( $this->input_attrs['premium'] ) and $this->input_attrs['premium'] ) ? 'premium' : 'free' ); ?>" class="alpha-color-control" type="text" data-show-opacity="<?php echo $show_opacity; ?>" data-palette="<?php echo esc_attr( $palette ); ?>" <?php $this->link(); ?>  />

                <?php if ( $compact ) : ?>

                        </div> <!-- !.molongui-compact-setting-input -->
                    </div> <!-- !.molongui-compact-setting -->

                <?php endif; ?>

            </div> <!-- !.molongui-select-control -->
			<?php
		}
	}
}