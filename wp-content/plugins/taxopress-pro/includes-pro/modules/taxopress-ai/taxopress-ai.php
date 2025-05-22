<?php

if (!class_exists('TaxoPress_Pro_AI_Module')) {
    /**
     * class TaxoPress_Pro_AI_Module
     */
    class TaxoPress_Pro_AI_Module
    {
        // class instance
        static $instance;

        /**
         * Construct the TaxoPress_Pro_AI_Module class
         */
        public function __construct()
        {

            add_filter('taxopress_settings_post_type_ai_fields', [$this, 'filter_settings_post_type_ai_fields'], 10, 2);
        }


        /** Singleton instance */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Filter post type taxopress ai fields
         *
         * @return array
         */
        public function filter_settings_post_type_ai_fields($taxopress_ai_fields, $post_type)
        {

            $default_taxonomy_display_options = [
                'default' => esc_html__('Default', 'taxopress-pro'),
                'dropdown' => esc_html__('Dropdown', 'taxopress-pro'),
                'checkbox' => esc_html__('Checkbox', 'taxopress-pro'),
            ];
            
            // add taxonomy display option after taxopress_ai_{$post_type}_metabox_default_taxonomy
            $new_entry = array(
                'taxopress_ai_' . $post_type . '_metabox_display_option',
                '<div class="taxopress-ai-tab-content-sub taxopress-settings-subtab-title taxopress-ai-'. $post_type .'-content-sub enable_taxopress_ai_' . $post_type . '_metabox_field st-subhide-content">' . esc_html__('Metabox Taxonomy Display', 'taxopress-pro') . '</div>',
                'select',
                $default_taxonomy_display_options,
                '',
                'taxopress-ai-tab-content-sub taxopress-ai-'. $post_type .'-content-sub enable_taxopress_ai_' . $post_type . '_metabox_field st-subhide-content'
            );

            // Get the index of 'taxopress_ai_post_metabox_default_taxonomy' if it exists
            $field_to_find = 'taxopress_ai_' . $post_type . '_metabox_default_taxonomy';
            $keys = array_column($taxopress_ai_fields, 0);
            $insert_after_key = array_search($field_to_find, $keys);
        
            // Determine the insertion position adding fallback incase the setting doesn't exist
            $position = ($insert_after_key !== false) ? $insert_after_key + 1 : count($taxopress_ai_fields);
        
            // Insert new entry at the determined position
            $taxopress_ai_fields = array_merge(
                array_slice($taxopress_ai_fields, 0, $position, true),
                [$new_entry],
                array_slice($taxopress_ai_fields, $position, null, true)
            );

            return $taxopress_ai_fields;
        }
    }
}
