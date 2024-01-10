<?php

if (!class_exists('TaxoPress_Linked_Terms')) {
    /**
     * class TaxoPress_Linked_Terms
     */
    class TaxoPress_Linked_Terms
    {

        // class instance
        static $instance;

        const LINKED_TERM_FIELD = '_taxopress_linked_terms';

        /**
         * Construct the TaxoPress_Linked_Terms class
         */
        public function __construct()
        {
            add_action('admin_init', function () {
                foreach (array_keys(get_taxonomies()) as $taxonomy) {
                    if (!in_array($taxonomy, $this->excluded_linked_terms_taxonomy())) {
                        add_action($taxonomy . '_add_form_fields', [$this, 'add_term_fields']);
                        add_action($taxonomy . '_edit_form_fields', [$this, 'edit_term_fields'], 10, 2);
                        add_action('created_' . $taxonomy, [$this, 'save_term_fields']);
                        add_action('edited_' . $taxonomy, [$this, 'save_term_fields']);
                    }
                }
            }, 19);

            // Add linked term to post
            add_action('save_post', array($this, 'add_linked_term_to_post'), 100, 2);
        }

        public function excluded_linked_terms_taxonomy()
        {

            $excluded_taxonomy = [];
            $excluded_taxonomy[] = 'author';

            $excluded_taxonomy = apply_filters('taxopress_linked_terms_excluded_taxonomy', $excluded_taxonomy);

            return $excluded_taxonomy;
        }


        /** Singleton instance */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function taxopress_load_taxonomy_linked_terms_assets()
        {
            wp_enqueue_style(
                'taxopress-linked-terms-css',
                plugins_url('', __FILE__) . '/assets/css/taxonomy-linked-terms.css',
                [],
                STAGS_VERSION,
                'all'
            );
            wp_enqueue_script(
                'taxopress-linked-terms-js',
                plugins_url('', __FILE__) . '/assets/js/taxonomy-linked-terms.js',
                ['jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete'],
                STAGS_VERSION
            );

            wp_localize_script(
                'taxopress-linked-terms-js',
                'linkedTermsRequestAction',
                array(
                    'taxonomy' => !empty($_GET['taxonomy']) ? sanitize_key($_GET['taxonomy']) : 'post_tag',
                    'term_id'  => !empty($_GET['tag_ID']) ? sanitize_key($_GET['tag_ID']) : 0,
                )
            );
        }

        public function add_term_fields($taxonomy)
        {
            wp_nonce_field('taxopress_linked_terms', 'taxopress_linked_terms_nonce');
?>
            <div class="form-field">
                <label for="text"><?php esc_html_e('Linked Terms', 'taxopress-pro'); ?></label>
                <input type="text" class="taxopress-linked-terms-input term-linked-terms linked-term-autocomplete-input" name="taxopress_linked_terms[]" placeholder="<?php esc_attr_e('Start typing to choose existing terms.', 'taxopress-pro'); ?>" />
                <p><?php esc_html_e('When the main term is added to a post, these terms will be added also.', 'taxopress-pro'); ?></p>
                <ul class="taxopress-term-linked-terms wrapper"></ul>
            </div>
        <?php
            $this->taxopress_load_taxonomy_linked_terms_assets();
        }

        public function edit_term_fields($term, $taxonomy)
        {
            wp_nonce_field('taxopress_linked_terms', 'taxopress_linked_terms_nonce');

            // get meta data value
            $linked_terms = taxopress_get_linked_terms($term->term_id); 
        ?><tr class="form-field">
                <th>
                    <label for="text"><?php esc_html_e('Linked Terms', 'taxopress-pro'); ?></label>
                </th>
                <td>
                    <input type="text" class="taxopress-linked-terms-input term-linked-terms linked-term-autocomplete-input" name="taxopress_linked_terms[]" placeholder="<?php esc_attr_e('Start typing to choose existing terms.', 'taxopress-pro'); ?>" />
                    <p><?php esc_html_e('When the main term is added to a post, these terms will be added also.', 'taxopress-pro'); ?></p>
                    <ul class="taxopress-term-linked-terms wrapper">
                        <?php if (!empty($linked_terms)) : ?>
                            <?php foreach ($linked_terms as $term_linked_term) : ?>
                                <li>
                                    <span class="display-text"><?php echo esc_html($term_linked_term); ?></span>
                                    <span class="remove-linked_term">
                                        <span class="dashicons dashicons-no-alt"></span>
                                    </span>
                                    <input type="hidden" class="term-linked-terms" name="taxopress_linked_terms[]" value="<?php echo esc_attr($term_linked_term); ?>">
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
<?php
            $this->taxopress_load_taxonomy_linked_terms_assets();
        }

        public function save_term_fields($term_id)
        {

            if (!isset($_POST['taxopress_linked_terms_nonce']) || !wp_verify_nonce(sanitize_key($_POST['taxopress_linked_terms_nonce']), 'taxopress_linked_terms')) {
                return;
            }

            $taxopress_linked_terms = array_map('sanitize_text_field', $_POST['taxopress_linked_terms']);
            $taxopress_linked_terms = array_filter($taxopress_linked_terms);

            update_term_meta(
                $term_id,
                self::LINKED_TERM_FIELD,
                $taxopress_linked_terms
            );
        }

        /**
         * Add post linked terms
         *
         * @param integer $post_id
         * @param object $post
         * @return void
         */
        public function add_linked_term_to_post($post_id, $post) {
            // Check if the post is being updated
            if (wp_is_post_revision($post_id)) {
                return;
            }
    
            // Get all taxonomies associated with the post
            $taxonomies = get_object_taxonomies($post);
    
            foreach ($taxonomies as $taxonomy) {
                // Get all terms for the current taxonomy
                $terms = get_the_terms($post_id, $taxonomy);
                
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        // Add linked term
                        $linked_terms = taxopress_get_linked_terms($term->term_id);
                        if (!empty($linked_terms)) {
                            wp_set_object_terms($post_id, $linked_terms, $taxonomy, true);
                        }
                    }
                }
            }
        }
    }
}
