<?php

if (!class_exists('TaxoPress_Pro_Post_Tags')) {
    /**
     * Class TaxoPress_Pro_Post_Tags
     */
    class TaxoPress_Pro_Post_Tags
    {
        // Singleton instance
        private static $instance;

        /**
         * Construct the TaxoPress_Pro_Post_Tags class
         */
        public function __construct()
        {
            add_action('admin_init', [$this, 'taxopress_pro_copy_posttags']);
            add_filter('taxopress_posttags_row_actions', [$this, 'taxopress_pro_copy_action'], 10, 2);
        }

        /** Singleton instance */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function taxopress_pro_copy_posttags()
        {
            if (isset($_GET['copied_posttags']) && (int) $_GET['copied_posttags'] === 1) {
                add_action('admin_notices', [$this, 'taxopress_posttags_copy_success_admin_notice']);
                add_filter('removable_query_args', [$this, 'taxopress_copied_posttags_filter_removable_query_args']);
            }

            if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'taxopress-copy-posttags') {
                $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
                if (wp_verify_nonce($nonce, 'posttags-action-request-nonce')) {
                    $this->taxopress_action_copy_posttags(sanitize_text_field($_REQUEST['taxopress_posttags']));
                }
                add_filter('removable_query_args', [$this, 'taxopress_copy_posttags_filter_removable_query_args']);
            }
        }

        public function taxopress_action_copy_posttags($posttags_id)
        {
            if (!taxopress_is_pro_version()) {
                wp_safe_redirect(admin_url('admin.php?page=st_post_tags&add=new_item'));
                exit;
            }

            $posttagss = taxopress_get_posttags_data();

            if (array_key_exists($posttags_id, $posttagss)) {
                $new_posttags = $posttagss[$posttags_id];
                $new_posttags['title'] .= '-copy';

                $new_id = (int) get_option('taxopress_posttags_ids_increament') + 1;
                $new_posttags['ID'] = $new_id;

                $posttagss[$new_id] = $new_posttags;

                update_option('taxopress_posttagss', $posttagss);
                update_option('taxopress_posttags_ids_increament', $new_id);
            }

            wp_safe_redirect(
                add_query_arg([
                    'page'             => 'st_post_tags',
                    'copied_posttags'  => 1,
                ], taxopress_admin_url('admin.php'))
            );
            exit();
        }

        public function taxopress_posttags_copy_success_admin_notice()
        {
            echo taxopress_admin_notices_helper(esc_html__('Shortcode entry successfully copied.', 'simple-tags'), true);
        }

        public function taxopress_copied_posttags_filter_removable_query_args(array $args)
        {
            return array_merge($args, ['copied_posttags']);
        }

        public function taxopress_copy_posttags_filter_removable_query_args(array $args)
        {
            return array_merge($args, ['action', 'taxopress_posttags', '_wpnonce']);
        }

        public function taxopress_pro_copy_action($actions, $item)
        {
            $actions['copy'] = sprintf(
                '<a href="%s" class="copy-posttags">%s</a>',
                add_query_arg([
                    'page'               => 'st_post_tags',
                    'action'             => 'taxopress-copy-posttags',
                    'taxopress_posttags' => esc_attr($item['ID']),
                    '_wpnonce'           => wp_create_nonce('posttags-action-request-nonce')
                ], admin_url('admin.php')),
                esc_html__('Copy', 'simple-tags')
            );

            if (isset($actions['delete'])) {
                $new_actions = [];
                foreach ($actions as $key => $action) {
                    if ($key === 'delete') {
                        $new_actions['copy'] = $actions['copy'];
                    }
                    $new_actions[$key] = $action;
                }
                return $new_actions;
            }

            return $actions;
        }
    }
}
