<?php

if (!class_exists('TaxoPress_Pro_Auto_Links')) {
    /**
     * class TaxoPress_Pro_Auto_Links
     */
    class TaxoPress_Pro_Auto_Links
    {
        // class instance
        static $instance;

        /**
         * Construct the TaxoPress_Pro_Auto_Links class
         */
        public function __construct()
        {
            
            add_action('taxopress_autolinks_after_html_exclusions', [$this, 'taxopress_pro_autolinks_after_html_exclusions'], 10, 2);
        }


        /** Singleton instance */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        public function taxopress_pro_autolinks_after_html_exclusions($current, $ui){

            $html_exclusions_customs = (!empty($current['html_exclusion_customs_entry']) && is_array($current['html_exclusion_customs_entry'])) ? $current['html_exclusion_customs_entry'] : [];

            
            // add line break
            echo '<tr valign="top" class="html-exclusions-customs-row"><th style="padding: 0;" scope="row"><hr /></th><td style="padding: 0;"><hr /></td></tr>';

            if (!empty($html_exclusions_customs)) : 
                foreach ($html_exclusions_customs as $html_exclusions_custom) :
                    echo '<tr valign="top" class="html-exclusions-customs-row"><th scope="row"><label for="' . esc_attr($html_exclusions_custom) . '">' . esc_html($html_exclusions_custom) . '</label></th><td>';
                    echo '<input type="hidden" name="html_exclusion_customs_entry[]" value="' . esc_attr($html_exclusions_custom) . '" />';

                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $ui->get_check_input([
                        'checkvalue' => $html_exclusions_custom,
                        'checked'    => (!empty($current['html_exclusion_customs']) && is_array($current['html_exclusion_customs']) && in_array(
                            $html_exclusions_custom,
                            $current['html_exclusion_customs'],
                            true
                        )) ? 'true' : 'false',
                        'name'       => esc_attr($html_exclusions_custom),
                        'namearray'  => 'html_exclusion_customs',
                        'textvalue'  => esc_attr($html_exclusions_custom),
                        'labeltext'  => esc_html($html_exclusions_custom),
                        'labeldescription' => true,
                        'add_delete' => true,
                        'wrap'       => false,
                    ]);

                    echo '</td></tr>';
                endforeach;
            endif;

            //add new form
            echo '<tr valign="top" class="html-exclusions-customs-row html-exclusions-customs-form" style="display: none;"><th style="padding: 0;" scope="row"><br />' . esc_html__('Element tag', 'taxopress-pro') . '</th><td style="padding: 0;display: flex;"><input style="width: 100%;margin-top: 15px;" type="text" class="element-name" placeholder="E.g: blockquote" /> <button class="new-element-submit button" style="margin-top: 15px;">' . esc_html__('Add', 'taxopress-pro') . '</button></td></tr>';

            //add new button
            echo '<tr valign="top" class="html-exclusions-customs-row html-exclusions-customs-add"><th style="padding: 0;" scope="row"><br />' . esc_html__('Add Element', 'taxopress-pro') . '</th><td style="padding: 0;text-align: right;"><br /><button class="button show-autolink-custom-html-exclusions">' . esc_html__('New Element', 'taxopress-pro') . '</button></td></tr>';


        }
    }
}
