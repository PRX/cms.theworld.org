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
            $description_text .= '<p class="taxopress-field-description description">';
            $description_text .= sprintf(esc_html__('You need an API key to use OpenAI. %1s %2sClick here to add an API Key.%3s', 'taxopress-pro'), '<br>', '<a target="_blank" href="'. esc_url($taxopress_ai_settings) .'">', '</a>');
            $description_text .= '</p>';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_open_ai',
                'class'      => 'autoterm_use_open_ai  autoterm-terms-to-use-field',
                'labeltext'  => esc_html__('OpenAI', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
            $selected           = (isset($current) && isset($current['autoterm_use_ibm_watson'])) ? taxopress_disp_boolean($current['autoterm_use_ibm_watson']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_ibm_watson'] : '';

            $description_text = esc_html__('This will automatically add new terms from the IBM Watson service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_ibm_watson',
                'class'      => 'autoterm_use_ibm_watson  autoterm-terms-to-use-field',
                'labeltext'  => esc_html__('IBM Watson', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
            $selected           = (isset($current) && isset($current['autoterm_use_dandelion'])) ? taxopress_disp_boolean($current['autoterm_use_dandelion']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_dandelion'] : '';

            $description_text = esc_html__('This will automatically add new terms from the Dandelion service. Please test carefully before use.', 'taxopress-pro');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_dandelion',
                'class'      => 'autoterm_use_dandelion  autoterm-terms-to-use-field',
                'labeltext'  => esc_html__('Dandelion', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
            $selected           = (isset($current) && isset($current['autoterm_use_opencalais'])) ? taxopress_disp_boolean($current['autoterm_use_opencalais']) : '';
            $select['selected'] = !empty($selected) ? $current['autoterm_use_opencalais'] : '';

            $description_text = esc_html__('This will automatically add new terms from the LSEG / Refinitiv service. Please test carefully before use.', 'taxopress-pro');
            $description_text .= '<p class="taxopress-field-description description">';
            $description_text .= sprintf(esc_html__('%1sYou need an API key to use LSEG / Refinitiv.%2s', 'taxopress-pro'), '<a target="_blank" href="'. esc_url($taxopress_ai_settings) .'">', '</a>');
            $description_text .= '</p>';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $ui->get_select_checkbox_input([
                'namearray'  => 'taxopress_autoterm',
                'name'       => 'autoterm_use_opencalais',
                'class'      => 'autoterm_use_opencalais  autoterm-terms-to-use-field',
                'labeltext'  => esc_html__('LSEG / Refinitiv', 'taxopress-pro'),
                'aftertext'  => $description_text,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'selections' => $select,
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
                    'Example <code>/\b({term})\b/i</code> will match whole word and <code>{term}</code> will be replaced with the term name before the regex action.',
                    'taxopress-pro'
                ),
                'required'  => false,
            ]);
        }
    }
}