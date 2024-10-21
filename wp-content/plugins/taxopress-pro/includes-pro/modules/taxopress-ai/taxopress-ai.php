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

            //add_action('taxopress_ai_fields', [$this, 'filter_taxopress_ai_fields']);
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
         * Get open ai models
         *
         * @return array
         */
        public static function get_open_ai_models()
        {
            $models = [
                'gpt-3.5-turbo'     => esc_html__('gpt-3.5-turbo', 'taxopress-pro'),
                'gpt-4o-mini'       => esc_html__('gpt-4o-mini', 'taxopress-pro'),
                'gpt-4o'            => esc_html__('gpt-4o', 'taxopress-pro'),
                'chatgpt-4o-latest' => esc_html__('chatgpt-4o-latest', 'taxopress-pro')
            ];

            return $models;
        }


        /**
         * Filter TaxoPress AI fields
         *
         * @param array $current
         *
         * @return array
         */
        public function filter_taxopress_ai_fields($fields){

            //add OpenAI fields
            $fields['open_ai_api_key'] = [
                'label' => esc_html__('API Key', 'taxopress-pro'),
                'description'  => esc_html__('Enter your OpenAI API Key.', 'taxopress-pro'),
                'type' => 'text',
                'tab' => 'open_ai',
            ];
            $fields['open_ai_model'] = [
                'label' => esc_html__('OpenAI Models', 'taxopress-pro'),
                'description'  => esc_html__('Some models availability depends on your subscription and access.', 'taxopress-pro'),
                'type' => 'select',
                'default_value' => 'gpt-3.5-turbo',
                'classes' => 'taxopress-ai-select2',
                'options' => self::get_open_ai_models(),
                'tab' => 'open_ai',
            ];
            $fields['open_ai_show_post_count'] = [
                'label' => esc_html__('Show Term Post Count', 'taxopress-pro'),
                'description' => esc_html__('This will show number of posts attached to the term.', 'taxopress-pro'),
                'type' => 'checkbox',
                'default_value' => 0,
                'tab' => 'open_ai',
            ];
            $fields['open_ai_cache_result'] = [
                'label' => esc_html__('Cache Results', 'taxopress-pro'),
                'description' => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'type' => 'checkbox',
                'default_value' => 1,
                'tab' => 'open_ai',
            ];
            $fields['open_ai_tag_prompt'] = [
                'label' => esc_html__('OpenAI Prompt (Beta)', 'taxopress-pro'),
                'description' => sprintf(esc_html__('%1s Click here for prompt documentation. %2s', 'taxopress-pro'), '<a target="_blank" href="https://taxopress.com/docs/openai-prompts/">', '</a>'),
                'type' => 'textarea',
                'default_value' => "Extract tags from the following content: '{content}'. Tags:",
                'tab' => 'open_ai',
            ];

            //add ibm watson fields
            $fields['ibm_watson_api_url'] = [
                'label' => esc_html__('API URL', 'taxopress-pro'),
                'description'  => esc_html__('Enter your IBM Watson API URL.', 'taxopress-pro'),
                'type' => 'url',
                'tab' => 'ibm_watson',
            ];

            $fields['ibm_watson_api_key'] = [
                'label' => esc_html__('API Key', 'taxopress-pro'),
                'description'  => esc_html__('Enter your IBM Watson API Key.', 'taxopress-pro'),
                'type' => 'text',
                'tab' => 'ibm_watson',
            ];
            $fields['ibm_watson_show_post_count'] = [
            'label' => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'description' => esc_html__('This will show number of posts attached to the term.', 'taxopress-pro'),
            'type' => 'checkbox',
            'default_value' => 0,
            'tab' => 'ibm_watson',
            ];
            $fields['ibm_watson_cache_result'] = [
                'label' => esc_html__('Cache Results', 'taxopress-pro'),
                'description' => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'type' => 'checkbox',
                'default_value' => 1,
                'tab' => 'ibm_watson',
            ];
            //add dandelion fields
            $fields['dandelion_api_token'] = [
                'label' => esc_html__('API Token', 'taxopress-pro'),
                'description'  => esc_html__('Enter your Dandelion API Key.', 'taxopress-pro'),
                'type' => 'text',
                'tab' => 'dandelion',
            ];

            $fields['dandelion_api_confidence_value'] = [
                'label' => esc_html__('API Confidence Value', 'taxopress-pro'),
                'description' => esc_html__('Choose a value between 0 and 1. A high value such as 0.8 will provide a few, accurate suggestions. A low value such as 0.2 will produce more suggestions, but they may be less accurate.', 'taxopress-pro'),
                'other_attr' => 'step=".1" min="0" max="1"',
                'type' => 'number',
                'default_value' => '0.6',
                'tab' => 'dandelion',
            ];
            $fields['dandelion_show_post_count'] = [
            'label' => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'description' => esc_html__('This will show number of posts attached to the term.', 'taxopress-pro'),
            'type' => 'checkbox',
            'default_value' => 0,
            'tab' => 'dandelion',
            ];
            $fields['dandelion_cache_result'] = [
                'label' => esc_html__('Cache Results', 'taxopress-pro'),
                'description' => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'type' => 'checkbox',
                'default_value' => 1,
                'tab' => 'dandelion',
            ];

            //add open calais fields
            $fields['open_calais_api_key'] = [
                'label' => esc_html__('API Key', 'taxopress-pro'),
                'description'  => esc_html__('Enter your LSEG / Refinitiv API Key.', 'taxopress-pro'),
                'type' => 'text',
                'tab' => 'open_calais',
            ];
            $fields['open_calais_show_post_count'] = [
            'label' => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'description' => esc_html__('This will show number of posts attached to the term.', 'taxopress-pro'),
            'type' => 'checkbox',
            'default_value' => 0,
            'tab' => 'open_calais',
            ];
            $fields['open_calais_cache_result'] = [
                'label' => esc_html__('Cache Results', 'taxopress-pro'),
                'description' => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'type' => 'checkbox',
                'default_value' => 1,
                'tab' => 'open_calais',
            ];

            return $fields;
        }
    }
}
