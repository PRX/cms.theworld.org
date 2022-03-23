<?php
defined( 'ABSPATH' ) or exit;
if ( !class_exists('WP_Customize_Control' ) ) include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
if ( !class_exists( 'Molongui_Customize_Checkbox_Control' ) )
{
    class Molongui_Customize_Checkbox_Control extends WP_Customize_Control
    {
        public $type = 'molongui-checkbox';
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
            $describedby_attr = ( !empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';
            $string  = 'molongui-compact-';
            $compact = ( substr( $this->type, 0, strlen( $string ) ) === $string ? true : false );

            ?>
            <div class="molongui-checkbox-control" data-type="<?php echo $this->type; ?>">

                <?php if ( !$compact ) : ?>

                    <?php if ( !empty( $this->label ) ) : ?>
                        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <?php endif; ?>

                    <?php if ( !empty( $this->description ) ) : ?>
                        <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
                    <?php endif; ?>

                <?php else : ?>

                    <div class="molongui-compact-setting">
                        <div class="molongui-compact-setting-label"><?php echo ''; ?></div>
                        <div class="molongui-compact-setting-input">

                <?php endif; ?>

                            <input
                                id="<?php echo esc_attr( $input_id ); ?>"
                                <?php echo $describedby_attr; ?>
                                type="checkbox"
                                value="<?php echo esc_attr( $this->value() ); ?>"
                                <?php $this->link(); ?>
                                <?php checked( $this->value() ); ?>
                            />
                            <label for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $this->label ); ?></label>

                <?php if ( $compact ) : ?>

                        </div> <!-- !.molongui-compact-setting-input -->
                    </div> <!-- !.molongui-compact-setting -->

            <?php endif; ?>

            </div> <!-- !.molongui-checkbox-control -->
            <?php
        }
    }
}