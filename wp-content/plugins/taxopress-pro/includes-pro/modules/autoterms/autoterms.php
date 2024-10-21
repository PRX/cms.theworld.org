<?php

if (!class_exists('TaxoPress_Pro_Auto_Terms')) {
    /**
     * class TaxoPress_Pro_Auto_Terms
     */
    class TaxoPress_Pro_Auto_Terms
    {
        // class instance
        static $instance;

        /**
         * Construct the TaxoPress_Pro_Auto_Terms class
         */
        public function __construct()
        {
            add_action( 'taxopress_autoterms_after_autoterm_schedule', [$this, 'taxopress_pro_autoterm_schedule_field'] );
            add_action( 'taxopress_cron_autoterms_hourly', [$this, 'taxopress_cron_autoterms_hourly_execution'] );
            add_action( 'taxopress_cron_autoterms_daily', [$this, 'taxopress_cron_autoterms_daily_execution'] );
            add_action( 'taxopress_autoterms_after_autoterm_terms_to_use', [$this, 'taxopress_autoterms_after_autoterm_terms_to_use_field'] );
            add_action( 'taxopress_autoterms_after_autoterm_advanced', [$this, 'taxopress_pro_autoterm_advanced_field'] );
            // Schedule cron events
            add_action( 'init', [$this, 'schedule_taxopress_cron_events'] );
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
         * Schedule taxopress cron events
         *
         * @return void
         */
        public function schedule_taxopress_cron_events() {
            if ( ! wp_next_scheduled( 'taxopress_cron_autoterms_hourly' ) ) {
                wp_schedule_event( time(), 'hourly', 'taxopress_cron_autoterms_hourly' );
            }            
            
            if ( ! wp_next_scheduled( 'taxopress_cron_autoterms_daily' ) ) {
                wp_schedule_event( time(), 'daily', 'taxopress_cron_autoterms_daily' );
            }
        }

        public function taxopress_pro_autoterm_schedule_field($current)
        {

            $ui = new taxopress_admin_ui();

            $cron_options = [
                'disable' => __('None', 'taxopress-pro'),
                'hourly' => __('Hourly', 'taxopress-pro'),
                'daily'  => __('Daily', 'taxopress-pro'),
            ];

            ?>
            <tr valign="top">
                <th scope="row"><label><?php echo esc_html__('Schedule Auto Terms for your content', 'taxopress-pro'); ?></label></th>

                <td>
                    <?php
                    $cron_schedule               = (!empty($current['cron_schedule'])) ? $current['cron_schedule'] : 'disable';
                    foreach ($cron_options as $option => $label) {
                        $checked_status = ($option === $cron_schedule)  ? 'checked' : ''; 
                        ?>
                        <label> 
                            <input 
                                class="autoterm_cron" 
                                type="radio" 
                                id="autoterm_cron_<?php echo esc_attr($option); ?>" 
                                name="taxopress_autoterm[cron_schedule]" 
                                value="<?php echo esc_attr($option); ?>"
                                <?php echo esc_html($checked_status); ?>
                            /> <?php echo esc_html($label); ?>
                            </label> 
                            <br /><br />
                    <?php
                    }
                    ?>
                </td>
            </tr>
            <?php

            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_schedule_exclude'])) ? taxopress_disp_boolean($current['autoterm_schedule_exclude']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_schedule_exclude'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_schedule_exclude',
                'class'      => '',
                'labeltext'  => esc_html__('Exclude previously analyzed content', 'taxopress-pro'),
                'aftertext'  => esc_html__('This enables you to skip posts that have already been analyzed by the Schedule feature.', 'taxopress-pro'),
                'selections' => $select, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_number_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'schedule_terms_batches',
                'textvalue' => isset($current['schedule_terms_batches']) ? esc_attr($current['schedule_terms_batches']) : '20',
                'labeltext' => esc_html__(
                    'Limit per batches',
                    'taxopress-pro'
                ),
                'helptext'  => esc_html__('This enables your scheduled Auto Terms to run in batches. If you have a lot of content, set this to a lower number to avoid timeouts.', 'taxopress-pro'),
                'min'       => '1',
                'required'  => true,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_number_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'schedule_terms_sleep',
                'textvalue' => isset($current['schedule_terms_sleep']) ? esc_attr($current['schedule_terms_sleep']) : '10',
                'labeltext' => esc_html__('Batches wait time', 'taxopress-pro'),
                'helptext'  => esc_html__('This is the wait time (in seconds) between processing batches of Auto Terms. If you have a lot of existing content, set this to a higher number to avoid timeouts.', 'taxopress-pro'),
                'min'       => '0',
                'required'  => true,
            ]);

            $select             = [
                'options' => [
                    [
                        'attr' => '1',
                        'text' => esc_attr__('24 hours ago', 'taxopress-pro')
                    ],
                    [
                        'attr' => '7',
                        'text' => esc_attr__('7 days ago', 'taxopress-pro')
                    ],
                    [
                        'attr' => '14',
                        'text' => esc_attr__('2 weeks ago', 'taxopress-pro')
                    ],
                    [
                        'attr' => '30',
                        'text' => esc_attr__('1 month ago', 'taxopress-pro'),
                        'default' => 'true'
                    ],
                    [
                        'attr' => '180',
                        'text' => esc_attr__('6 months ago', 'taxopress-pro')
                    ],
                    [
                        'attr' => '365',
                        'text' => esc_attr__('1 year ago', 'taxopress-pro')
                    ],
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('No limit', 'taxopress-pro')
                    ],
                ],
            ];

            if (isset($current) && is_array($current)) {
                $select             = [
                    'options' => [
                        [
                            'attr' => '1',
                            'text' => esc_attr__('24 hours ago', 'taxopress-pro')
                        ],
                        [
                            'attr' => '7',
                            'text' => esc_attr__('7 days ago', 'taxopress-pro')
                        ],
                        [
                            'attr' => '14',
                            'text' => esc_attr__('2 weeks ago', 'taxopress-pro')
                        ],
                        [
                            'attr' => '30',
                            'text' => esc_attr__('1 month ago', 'taxopress-pro'),
                        ],
                        [
                            'attr' => '180',
                            'text' => esc_attr__('6 months ago', 'taxopress-pro')
                        ],
                        [
                            'attr' => '365',
                            'text' => esc_attr__('1 year ago', 'taxopress-pro')
                        ],
                        [
                            'attr'    => '0',
                            'text'    => esc_attr__('No limit', 'taxopress-pro'),
                            'default' => 'true'
                        ],
                    ],
                ];
            }

            $selected           = (isset($current) && isset($current['schedule_terms_limit_days'])) ? taxopress_disp_boolean($current['schedule_terms_limit_days']) : '';
            $select['selected'] = !empty($selected) ? $current['schedule_terms_limit_days'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_number_select([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'schedule_terms_limit_days',
                'labeltext'  => esc_html__(
                    'Limit Auto Terms, based on published date',
                    'taxopress-pro'
                ),
                'aftertext'  => esc_html__('This setting can limit your scheduled Auto Terms query to only recent content. We recommend using this feature to avoid timeouts on large sites.', 'taxopress-pro'),
                'selections' => $select, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
        }

        public function taxopress_cron_autoterms_hourly_execution()
        {

            global $wpdb;

            $autoterms = taxopress_get_autoterm_data();

            $flag = false;
            foreach ($autoterms as $autoterm_key => $autoterm_data) {
                $cron_schedule = isset($autoterm_data['cron_schedule']) ? $autoterm_data['cron_schedule'] : 'disable';
                $post_types = isset($autoterm_data['post_types']) ? (array)$autoterm_data['post_types'] : [];
                $post_status = isset($autoterm_data['post_status']) && is_array($autoterm_data['post_status']) ? $autoterm_data['post_status'] : ['publish'];
                $autoterm_schedule_exclude = isset($autoterm_data['autoterm_schedule_exclude']) ? (int)$autoterm_data['autoterm_schedule_exclude'] : 0;

                if ($cron_schedule !== 'hourly') {
                    continue;
                }

                if (empty($post_types)) {
                    continue;
                }

                $schedule_terms_limit_days     = (int) $autoterm_data['schedule_terms_limit_days'];
                $schedule_terms_limit_days_sql = '';
                if ($schedule_terms_limit_days > 0) {
                    $schedule_terms_limit_days_sql = 'AND post_date > "' . date('Y-m-d H:i:s', time() - $schedule_terms_limit_days * 86400) . '"';
                }


                $limit = (isset($autoterm_data['schedule_terms_batches']) && (int)$autoterm_data['schedule_terms_batches'] > 0) ? (int)$autoterm_data['schedule_terms_batches'] : 20;

                $sleep = (isset($autoterm_data['schedule_terms_sleep']) && (int)$autoterm_data['schedule_terms_sleep'] > 0) ? (int)$autoterm_data['schedule_terms_sleep'] : 0;

                if ($autoterm_schedule_exclude > 0) {
                    $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ( ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_taxopress_autotermed' ) WHERE post_type IN ('" . implode("', '", $post_types) . "') AND {$wpdb->postmeta}.post_id IS NULL AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
                } else {
                    $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type IN ('" . implode("', '", $post_types) . "') AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
                }

                if (!empty($objects)) {
                    $current_post = 0;
                    foreach ($objects as $object) {
                        $current_post++;
                        update_post_meta($object->ID, '_taxopress_autotermed', 1);
                        SimpleTags_Client_Autoterms::auto_terms_post($object, $autoterm_data['taxonomy'], $autoterm_data, true, 'hourly_cron_schedule', 'st_autoterms');
                        unset($object);
                        if ($sleep > 0 && $current_post % $limit == 0) {
                            sleep($sleep);
                        }
                    }
                }
            }
        }

        public function taxopress_cron_autoterms_daily_execution()
        {

            global $wpdb;

            $autoterms = taxopress_get_autoterm_data();

            $flag = false;
            foreach ($autoterms as $autoterm_key => $autoterm_data) {
                $cron_schedule = isset($autoterm_data['cron_schedule']) ? $autoterm_data['cron_schedule'] : 'disable';
                $post_types = isset($autoterm_data['post_types']) ? (array)$autoterm_data['post_types'] : [];
                $post_status = isset($autoterm_data['post_status']) && is_array($autoterm_data['post_status']) ? $autoterm_data['post_status'] : ['publish'];
                $autoterm_schedule_exclude = isset($autoterm_data['autoterm_schedule_exclude']) ? (int)$autoterm_data['autoterm_schedule_exclude'] : 0;


                if ($cron_schedule !== 'daily') {
                    continue;
                }

                if (empty($post_types)) {
                    continue;
                }

                $schedule_terms_limit_days     = (int) $autoterm_data['schedule_terms_limit_days'];
                $schedule_terms_limit_days_sql = '';
                if ($schedule_terms_limit_days > 0) {
                    $schedule_terms_limit_days_sql = 'AND post_date > "' . date('Y-m-d H:i:s', time() - $schedule_terms_limit_days * 86400) . '"';
                }

                $limit = (isset($autoterm_data['schedule_terms_batches']) && (int)$autoterm_data['schedule_terms_batches'] > 0) ? (int)$autoterm_data['schedule_terms_batches'] : 20;

                $sleep = (isset($autoterm_data['schedule_terms_sleep']) && (int)$autoterm_data['schedule_terms_sleep'] > 0) ? (int)$autoterm_data['schedule_terms_sleep'] : 0;

                if ($autoterm_schedule_exclude > 0) {
                    $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ( ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_taxopress_autotermed' ) WHERE post_type IN ('" . implode("', '", $post_types) . "') AND {$wpdb->postmeta}.post_id IS NULL AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} ORDER BY ID DESC LIMIT {$limit}");
                } else {
                    $objects = (array) $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type IN ('" . implode("', '", $post_types) . "') AND post_status IN ('" . implode("', '", $post_status) . "') {$schedule_terms_limit_days_sql} LIMIT {$limit}");
                }

                if (!empty($objects)) {
                    $current_post = 0;
                    foreach ($objects as $object) {
                        $current_post++;
                        update_post_meta($object->ID, '_taxopress_autotermed', 1);
                        SimpleTags_Client_Autoterms::auto_terms_post($object, $autoterm_data['taxonomy'], $autoterm_data, true, 'daily_cron_schedule', 'st_autoterms');
                        unset($object);
                        if ($sleep > 0 && $current_post % $limit == 0) {
                            sleep($sleep);
                        }
                    }
                }
            }
        }

        public function taxopress_autoterms_after_autoterm_terms_to_use_field($current)
        {
            $taxopress_ai_settings = admin_url('admin.php?page=st_taxopress_ai');
            $ui = new taxopress_admin_ui();
            
            ?>
            <tr class="autoterm-description-tr">
                <td colspan="2">
                    <p class="taxopress-field-description description autoterm-terms-use-openai-notice">
                        <?php printf(esc_html__('OpenAI is an external service that can scan your content and suggest relevant terms. %1sClick here for details%2s.', 'taxopress-pro'), '<a target="blank" href="https://taxopress.com/docs/register-openai/">', '</a>'); ?>
                    </p>
                </td>
            </tr>
            <?php
            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_use_open_ai'])) ? taxopress_disp_boolean($current['autoterm_use_open_ai']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_open_ai'] : '';

            $description_text = esc_html__('This will automatically add new terms from the OpenAI service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_open_ai',
                'class'      => 'autoterm_use_open_ai  autoterm-terms-to-use-field autoterm-terms-use-openai fields-control',
                'labeltext'  => esc_html__('Enable OpenAI', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
            ]);
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                    'namearray' => 'taxopress_autoterm',
                    'name'      => 'open_ai_api_key',
                    'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-openai',
                    'textvalue' => isset($current['open_ai_api_key']) ? esc_attr($current['open_ai_api_key']) : '',
                    'labeltext' => esc_html__('API Key', 'taxopress-pro'),
                    'helptext' => esc_html__('Enter your OpenAI API Key.', 'taxopress-pro'),
                    'required'  => false,
            ]);
            
            $options = [];
            $open_ai_models = [
                'gpt-3.5-turbo'     => esc_html__('gpt-3.5-turbo', 'taxopress-pro'),
                'gpt-4o-mini'       => esc_html__('gpt-4o-mini', 'taxopress-pro'),
                'gpt-4o'            => esc_html__('gpt-4o', 'taxopress-pro'),
                'chatgpt-4o-latest' => esc_html__('chatgpt-4o-latest', 'taxopress-pro')
            ];
            foreach ($open_ai_models as $model_name => $model_label) {
                if ($model_name == 'gpt-3.5-turbo') {
                    $options[] = [
                        'attr'    => $model_name,
                        'text'    => $model_label,
                        'default' => 'true',
                    ];
                } else {
                    $options[] = [
                        'attr' => $model_name,
                        'text' => $model_label,
                    ];
                }
            }

            $select             = [
                'options' => $options,
            ];
            $selected           = isset($current) && !empty($current['open_ai_model']) ? taxopress_disp_boolean($current['open_ai_model']) : '';
            $select['selected'] = !empty($selected) ? $current['open_ai_model'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input_main([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'open_ai_model',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-openai',
                'labeltext'  => esc_html__('OpenAI Models', 'taxopress-pro'),
                'aftertext'  => esc_html__('Some models availability depends on your subscription and access.', 'taxopress-pro'),
                'required'   => false,
                'selections' => $select,
            ]);

            $select             = [
            'options' => [
                [
                    'attr'    => '0',
                    'text'    => esc_attr__('False', 'taxopress-pro'),
                    'default' => 'true',
                ],
                [
                    'attr' => '1',
                    'text' => esc_attr__('True', 'taxopress-pro'),
                ],
            ],
        ];
        $selected           = ( isset($current) && isset($current['open_ai_show_post_count']) ) ? taxopress_disp_boolean($current['open_ai_show_post_count']) : '';
        $select['selected'] = !empty($selected) ? $current['open_ai_show_post_count'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'open_ai_show_post_count',
                'labeltext'  => esc_html__('Show Term Post Count', 'taxopress-pro'),
                'aftertext'  => esc_html__('This will show the number of posts attached to the terms.', 'taxopress-pro'),
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-openai',
                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);


            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = ( isset($current) && isset($current['open_ai_cache_result']) ) ? taxopress_disp_boolean($current['open_ai_cache_result']) : '';
            $select['selected'] = !empty($selected) ? $current['open_ai_cache_result'] : '';
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $ui->get_select_checkbox_input([
                    'namearray'  => 'taxopress_autoterm',
                    'name'       => 'open_ai_cache_result',
                    'labeltext'  => esc_html__('Cache Results', 'taxopress-pro'),
                    'aftertext'  => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                    'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-openai',
                    'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ]);

                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $ui->get_textarea_input([
                    'namearray' => 'taxopress_autoterm',
                    'name'      => 'open_ai_tag_prompt',
                    'rows'      => '4',
                    'cols'      => '40',
                    'class'     => 'autoterm-terms-to-use-field autoterm-terms-use-openai',
                    'textvalue' => isset($current['open_ai_tag_prompt']) && !empty($current['open_ai_tag_prompt']) ? esc_attr($current['open_ai_tag_prompt']) : "Extract tags from the following content: '{content}'. Tags:",
                    'labeltext' => esc_html__(
                        'OpenAI Prompt (Beta)',
                        'taxopress-pro'
                    ),
                    'helptext'  => sprintf(esc_html__('%1s Click here for prompt documentation. %2s', 'taxopress-pro'), '<a target="_blank" href="https://taxopress.com/docs/openai-prompts/">', '</a>'),
                    'required'  => false,
                ]);
            ?>
            <tr class="autoterm-description-tr">
                <td colspan="2">
                    <p class="taxopress-field-description description autoterm-terms-use-ibm-watson-notice">
                        <?php printf(esc_html__('IBM Watson is an external service that can scan your content and suggest relevant terms. %1sClick here for details%2s.', 'taxopress-pro'), '<a target="blank" href="https://taxopress.com/docs/register-ibm/">', '</a>'); ?>
                    </p>
                </td>
            </tr>
            <?php
            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_use_ibm_watson'])) ? taxopress_disp_boolean($current['autoterm_use_ibm_watson']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_ibm_watson'] : '';

            $description_text = esc_html__('This will automatically add new terms from the IBM Watson service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_ibm_watson',
                'class'      => 'autoterm_use_ibm_watson  autoterm-terms-to-use-field autoterm-terms-use-ibm-watson fields-control',
                'labeltext'  => esc_html__('Enable IBM Watson', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'ibm_watson_api_url',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-ibm-watson',
                'textvalue' => isset($current['ibm_watson_api_url']) ? esc_attr($current['ibm_watson_api_url']) : '',
                'labeltext' => esc_html__('API URL', 'taxopress-pro'),
                'helptext' => esc_html__('Enter your IBM Watson API URL.', 'taxopress-pro'),
                'required'  => false,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'ibm_watson_api_key',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-ibm-watson',
                'textvalue' => isset($current['ibm_watson_api_key']) ? esc_attr($current['ibm_watson_api_key']) : '',
                'labeltext' => esc_html__('API Key', 'taxopress-pro'),
                'helptext' => esc_html__('Enter your IBM Watson API Key.', 'taxopress-pro'),
                'required'  => false,
            ]);

            $select             = [
            'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'taxopress-pro'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'taxopress-pro'),
            ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['ibm_watson_show_post_count']) ) ? taxopress_disp_boolean($current['ibm_watson_show_post_count']) : '';
            $select['selected'] = !empty($selected) ? $current['ibm_watson_show_post_count'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
            'namearray'  => 'taxopress_autoterm',
            'name'       => 'ibm_watson_show_post_count',
            'labeltext'  => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'aftertext'  => esc_html__('This will show the number of posts attached to the terms.', 'taxopress-pro'),
            'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-ibm-watson',
            'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
            
            
            $select             = [
            'options' => [
                [
                    'attr'    => '0',
                    'text'    => esc_attr__('False', 'taxopress-pro'),
                    'default' => 'true',
                ],
                [
                    'attr' => '1',
                    'text' => esc_attr__('True', 'taxopress-pro'),
                ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['ibm_watson_cache_result']) ) ? taxopress_disp_boolean($current['ibm_watson_cache_result']) : '';
            $select['selected'] = !empty($selected) ? $current['ibm_watson_cache_result'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'ibm_watson_cache_result',
                'labeltext'  => esc_html__('Cache Results', 'taxopress-pro'),
                'aftertext'  => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-ibm-watson',
                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
            
            ?>
            <tr class="autoterm-description-tr">
                <td colspan="2">
                    <p class="taxopress-field-description description autoterm-terms-use-dandelion-notice">
                        <?php printf(esc_html__('Dandelion is an external service that can scan your content and suggest relevant terms. %1sClick here for details%2s.', 'taxopress-pro'), '<a target="blank" href="https://taxopress.com/docs/register-dandelion/">', '</a>'); ?>
                    </p>
                </td>
            </tr>
            <?php
            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_use_dandelion'])) ? taxopress_disp_boolean($current['autoterm_use_dandelion']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_dandelion'] : '';

            $description_text = esc_html__('This will automatically add new terms from the Dandelion service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_dandelion',
                'class'      => 'autoterm_use_dandelion  autoterm-terms-to-use-field autoterm-terms-use-dandelion fields-control',
                'labeltext'  => esc_html__('Enable Dandelion', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'dandelion_api_token',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-dandelion',
                'textvalue' => isset($current['dandelion_api_token']) ? esc_attr($current['dandelion_api_token']) : '',
                'labeltext' => esc_html__('API Token', 'taxopress-pro'),
                'helptext' => esc_html__('Enter your Dandelion API Key.', 'taxopress-pro'),
                'required'  => false,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_number_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'dandelion_api_confidence_value',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-dandelion',
                'textvalue' => isset($current['dandelion_api_confidence_value']) ? esc_attr($current['dandelion_api_confidence_value']) : '0.6',
                'labeltext' => esc_html__('API Confidence Value', 'taxopress-pro'),
                'helptext'  => esc_html__('Choose a value between 0 and 1. A high value such as 0.8 will provide a few, accurate suggestions. A low value such as 0.2 will produce more suggestions, but they may be less accurate.', 'taxopress-pro'),
                'other_attr' => 'step=".1" min="0" max="1"',
                'min' => '0',
                'required'  => false,
            ]);

            $select             = [
            'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'taxopress-pro'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'taxopress-pro'),
            ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['dandelion_show_post_count']) ) ? taxopress_disp_boolean($current['dandelion_show_post_count']) : '';
            $select['selected'] = !empty($selected) ? $current['dandelion_show_post_count'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
            'namearray'  => 'taxopress_autoterm',
            'name'       => 'dandelion_show_post_count',
            'labeltext'  => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'aftertext'  => esc_html__('This will show the number of posts attached to the terms.', 'taxopress-pro'),
            'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-dandelion',
            'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
            
            
            $select             = [
            'options' => [
                [
                    'attr'    => '0',
                    'text'    => esc_attr__('False', 'taxopress-pro'),
                    'default' => 'true',
                ],
                [
                    'attr' => '1',
                    'text' => esc_attr__('True', 'taxopress-pro'),
                ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['dandelion_cache_result']) ) ? taxopress_disp_boolean($current['dandelion_cache_result']) : '';
            $select['selected'] = !empty($selected) ? $current['dandelion_cache_result'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'dandelion_cache_result',
                'labeltext'  => esc_html__('Cache Results', 'taxopress-pro'),
                'aftertext'  => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-dandelion',
                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);

            ?>
            <tr class="autoterm-description-tr">
                <td colspan="2">
                    <p class="taxopress-field-description description autoterm-terms-use-lseg-refinitiv-notice">
                        <?php printf(esc_html__('LSEG / Refinitiv is an external service that can scan your content and suggest relevant terms. %1sClick here for details%2s.', 'taxopress-pro'), '<a target="blank" href="https://taxopress.com/docs/register-opencalais/">', '</a>'); ?>
                    </p>
                </td>
            </tr>
            <?php
            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_use_opencalais'])) ? taxopress_disp_boolean($current['autoterm_use_opencalais']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_opencalais'] : '';

            $description_text = esc_html__('This will automatically add new terms from the LSEG / Refinitiv service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_opencalais',
                'class'      => 'autoterm_use_opencalais  autoterm-terms-to-use-field autoterm-terms-use-lseg-refinitiv fields-control',
                'labeltext'  => esc_html__('Enable LSEG / Refinitiv', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'open_calais_api_key',
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-lseg-refinitiv',
                'textvalue' => isset($current['open_calais_api_key']) ? esc_attr($current['open_calais_api_key']) : '',
                'labeltext' => esc_html__('API Key', 'taxopress-pro'),
                'helptext' => esc_html__('Enter your LSEG / Refinitiv API Key.', 'taxopress-pro'),
                'required'  => false,
            ]);

            $select             = [
            'options' => [
            [
                'attr'    => '0',
                'text'    => esc_attr__('False', 'taxopress-pro'),
                'default' => 'true',
            ],
            [
                'attr' => '1',
                'text' => esc_attr__('True', 'taxopress-pro'),
            ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['open_calais_show_post_count']) ) ? taxopress_disp_boolean($current['open_calais_show_post_count']) : '';
            $select['selected'] = !empty($selected) ? $current['open_calais_show_post_count'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
            'namearray'  => 'taxopress_autoterm',
            'name'       => 'open_calais_show_post_count',
            'labeltext'  => esc_html__('Show Term Post Count', 'taxopress-pro'),
            'aftertext'  => esc_html__('This will show the number of posts attached to the terms.', 'taxopress-pro'),
            'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-lseg-refinitiv',
            'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
            
            
            $select             = [
            'options' => [
                [
                    'attr'    => '0',
                    'text'    => esc_attr__('False', 'taxopress-pro'),
                    'default' => 'true',
                ],
                [
                    'attr' => '1',
                    'text' => esc_attr__('True', 'taxopress-pro'),
                ],
            ],
            ];
            $selected           = ( isset($current) && isset($current['open_calais_cache_result']) ) ? taxopress_disp_boolean($current['open_calais_cache_result']) : '';
            $select['selected'] = !empty($selected) ? $current['open_calais_cache_result'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'open_calais_cache_result',
                'labeltext'  => esc_html__('Cache Results', 'taxopress-pro'),
                'aftertext'  => esc_html__('By caching the results locally, new API requests will not be made unless the post title or content changes. This saves API usage.', 'taxopress-pro'),
                'class'      => 'autoterm-terms-to-use-field autoterm-terms-use-lseg-refinitiv',
                'selections' => $select,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ]);
        }


        public function taxopress_pro_autoterm_advanced_field($current)
        {
            $ui = new taxopress_admin_ui();



            $select             = [
                'options' => [
                    [
                        'attr'    => '0',
                        'text'    => esc_attr__('False', 'taxopress-pro'),
                        'default' => 'true',
                    ],
                    [
                        'attr' => '1',
                        'text' => esc_attr__('True', 'taxopress-pro'),
                    ],
                ],
            ];
            $selected           = (isset($current) && isset($current['autoterm_use_regex'])) ? taxopress_disp_boolean($current['autoterm_use_regex']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_regex'] : '';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_regex',
                'class'      => 'autoterm_use_regex',
                'labeltext'  => esc_html__('Regular Expressions', 'taxopress-pro'),
                'aftertext'  => esc_html__('Use Regular Expressions to change how Auto Terms analyzes your posts.', 'taxopress-pro'),
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
            ]);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_text_input([
                'namearray' => 'taxopress_autoterm',
                'name'      => 'terms_regex_code',
                'class'     => 'terms_regex_code',
                'textvalue' => isset($current['terms_regex_code']) ? esc_attr(stripslashes($current['terms_regex_code'])) : '',
                'toplabel' => esc_html__('Regex code', 'taxopress-pro'),
                'labeltext'  => '',
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'helptext'  => __(
                    'Example <code>/\b({term})\b/i</code> will match whole word while <code>/({term})/i</code> will match at any location even if it\'s part of another word. <code>{term}</code> will be replaced with the term name before the regex action.',
                    'taxopress-pro'
                ),
                'required'  => false,
            ]);
        }
    }
}