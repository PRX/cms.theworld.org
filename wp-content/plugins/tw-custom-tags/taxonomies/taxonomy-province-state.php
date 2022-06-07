<?php
/**
 * Register Province or State Taxonomy
 *
 * @return void
 */
function tw_province_state_taxonomy()
{
    $labels = [
        'name' => _x(
            'Province or State',
            'Taxonomy General Name',
            'text_domain'
        ),
        'singular_name' => _x(
            'Province or State',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Provinces or States', 'text_domain'),
        'all_items' => __('All Provinces or States', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Province or State Name', 'text_domain'),
        'add_new_item' => __('Add New Province or State', 'text_domain'),
        'edit_item' => __('Edit Province or State', 'text_domain'),
        'update_item' => __('Update Province or State', 'text_domain'),
        'view_item' => __('View Province or State', 'text_domain'),
        'separate_items_with_commas' => __(
            'Separate Cities with commas',
            'text_domain'
        ),
        'add_or_remove_items' => __('Add or remove Cities', 'text_domain'),
        'choose_from_most_used' => __(
            'Choose from the most used',
            'text_domain'
        ),
        'popular_items' => __('Popular Items', 'text_domain'),
        'search_items' => __('Search Cities', 'text_domain'),
        'not_found' => __('Not Found', 'text_domain'),
        'no_terms' => __('No items', 'text_domain'),
        'items_list' => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
    ];
    $args = [
        'labels' => $labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => false,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'show_in_rest' => true,
    ];
    register_taxonomy('province_state', ['post'], $args);
}
add_action('init', 'tw_province_state_taxonomy', 0);
