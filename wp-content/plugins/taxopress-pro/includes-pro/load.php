<?php
//include licence
require_once (TAXOPRESS_ABSPATH . '/includes-pro/classes/licence.php');
//include pro modules
require_once (TAXOPRESS_ABSPATH . '/includes-pro/modules/taxonomy-synonyms/taxonomy-synonyms.php');
require_once (TAXOPRESS_ABSPATH . '/includes-pro/modules/linked-terms/linked-terms.php');
require_once (TAXOPRESS_ABSPATH . '/includes-pro/modules/autolinks/autolinks.php');
require_once (TAXOPRESS_ABSPATH . '/includes-pro/modules/autoterms/autoterms.php');
require_once (TAXOPRESS_ABSPATH . '/includes-pro/modules/taxopress-ai/taxopress-ai.php');

if (!class_exists('TaxoPress_Pro_Init')) {
    /**
     * class TaxoPress_Pro_Init
     */
    class TaxoPress_Pro_Init
    {
        // class instance
        public static $instance;

        /**
         * Construct the TaxoPress_Pro_Init class
         */
        public function __construct()
        {
            add_action( 'plugins_loaded', [$this, 'taxopress_load_module_classes'] );
            add_action( 'plugins_loaded', [$this, 'taxopress_load_admin_licence_menu'] );
            add_filter( 'taxopress_admin_pages', [$this, 'taxopress_pro_admin_pages'] );
            add_filter( 'taxopress_dashboard_features', [$this, 'taxopress_pro_dashboard_features'] );
            add_action( 'taxopress_admin_class_before_assets_register', [$this, 'taxopress_load_admin_pro_assets'] );
            add_action( 'taxopress_admin_class_after_styles_enqueue', [$this, 'taxopress_load_admin_pro_styles'] );
            add_filter( 'taxopress_post_tags_create_limit', [$this, 'taxopress_action_is_false'] );
            add_filter( 'taxopress_related_posts_create_limit', [$this, 'taxopress_action_is_false'] );
            add_filter( 'taxopress_tag_clouds_create_limit', [$this, 'taxopress_action_is_false'] );
            add_filter( 'taxopress_autolinks_create_limit', [$this, 'taxopress_action_is_false'] );
            add_filter( 'taxopress_autoterms_create_limit', [$this, 'taxopress_action_is_false'] );
            add_action( 'admin_init', [$this, 'taxopress_pro_only_upgrade_function'] );
        }

        /** Singleton instance */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function taxopress_load_module_classes(){
            if (taxopress_is_synonyms_enabled()) {
                TaxoPress_Taxonomy_Synonyms::get_instance();
            }
            if (taxopress_is_linked_terms_enabled()) {
                TaxoPress_Linked_Terms::get_instance();
            }
            if (1 === (int) SimpleTags_Plugin::get_option_value('active_auto_links')) {
                TaxoPress_Pro_Auto_Links::get_instance();
            }
            if (1 === (int) SimpleTags_Plugin::get_option_value('active_auto_terms')) {
                TaxoPress_Pro_Auto_Terms::get_instance();
            }
            TaxoPress_Pro_AI_Module::get_instance();
        }

        public function taxopress_load_admin_licence_menu(){
            TaxoPress_License::get_instance();
        }

        public function taxopress_pro_admin_pages($taxopress_pages){

            $taxopress_pages[] = 'st_licence';
            $taxopress_pages[] = 'st_linked_terms';

            return $taxopress_pages;
        }

        public function taxopress_pro_dashboard_features($features){

            $features['st_features_synonyms'] = [
                'label'        => esc_html__('Synonyms', 'taxopress-pro'),
                'description'  => esc_html__('This feature allows you to associate additional words with each term. For example, "website" can have synonyms such as "websites", "web site", and "web pages".', 'taxopress-pro'),
                'option_key'   => 'active_features_synonyms',
            ];

            $linked_terms_feature = [
                'label'        => esc_html__('Linked Terms', 'taxopress-pro'),
                'description'  => esc_html__('This feature allows you to connect terms. When the main term or any of these terms are added to the post, all the other terms will be added also.', 'taxopress-pro'),
                'option_key'   => 'active_features_linked_terms',
            ];

            // add linked term after terms in dashboard
            $index = array_search('st_terms', array_keys($features));
            if ($index !== false) {
                $features = array_slice($features, 0, $index + 1, true) + 
                    array('st_linked_terms' => $linked_terms_feature) + 
                    array_slice($features, $index + 1, count($features) - 1, true);
            } else {
                $features['st_linked_terms'] = $linked_terms_feature;
            }

            return $features;
        }

        public function taxopress_load_admin_pro_assets(){
            wp_register_style( 'st-admin-pro', STAGS_URL . '/includes-pro/assets/css/pro.css', array(), STAGS_VERSION, 'all' );
            wp_register_script( 'st-admin-pro', STAGS_URL . '/includes-pro/assets/js/pro.js', array( 'jquery' ), STAGS_VERSION );
        }

        public function taxopress_load_admin_pro_styles(){
            wp_enqueue_style( 'st-admin-pro' );
            wp_enqueue_script( 'st-admin-pro' );
        }

        public function taxopress_action_is_false($limit){
            return false;
        }

        public function taxopress_pro_only_upgrade_function()
        {

            if (!get_option('taxopress_pro_3_5_2_upgraded')) {
                //this upgrade is neccessary due to free version uninstall removing role for author
                if ( function_exists( 'get_role' ) ) {
                    $role = get_role( 'administrator' );
                    if ( null !== $role && ! $role->has_cap( 'simple_tags' ) ) {
                        $role->add_cap( 'simple_tags' );
                    }

                    if ( null !== $role && ! $role->has_cap( 'admin_simple_tags' ) ) {
                        $role->add_cap( 'admin_simple_tags' );
                    }

                    $role = get_role( 'editor' );
                    if ( null !== $role && ! $role->has_cap( 'simple_tags' ) ) {
                        $role->add_cap( 'simple_tags' );
                    }
                }
              update_option('taxopress_pro_3_5_2_upgraded', true);
           }

        }

    }
}

// Initialize the module
TaxoPress_Pro_Init::get_instance();
