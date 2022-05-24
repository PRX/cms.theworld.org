<?php
/*
Plugin Name: TW Custom Tags
Description: Creates the custom taxonomies for metadata
*/

// Register Cities Taxonomy
function tw_city_taxonomy()
{
    $labels = [
        'name' => _x('Cities', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x('City', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name' => __('Cities', 'text_domain'),
        'all_items' => __('All Cities', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New City Name', 'text_domain'),
        'add_new_item' => __('Add New City', 'text_domain'),
        'edit_item' => __('Edit City', 'text_domain'),
        'update_item' => __('Update City', 'text_domain'),
        'view_item' => __('View City', 'text_domain'),
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
    register_taxonomy('city', ['post'], $args);
}
add_action('init', 'tw_city_taxonomy', 0);

// Register Continent Taxonomy
function tw_continent_taxonomy()
{
    $labels = [
        'name' => _x('Continent', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x(
            'Continent',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Continents', 'text_domain'),
        'all_items' => __('All Continents', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Continent Name', 'text_domain'),
        'add_new_item' => __('Add New Continent', 'text_domain'),
        'edit_item' => __('Edit Continent', 'text_domain'),
        'update_item' => __('Update Continent', 'text_domain'),
        'view_item' => __('View Continent', 'text_domain'),
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
    register_taxonomy('continent', ['post'], $args);
}
add_action('init', 'tw_continent_taxonomy', 0);

// Register Country Taxonomy
function tw_country_taxonomy()
{
    $labels = [
        'name' => _x('Country', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x(
            'Country',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Countries', 'text_domain'),
        'all_items' => __('All Countries', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Country Name', 'text_domain'),
        'add_new_item' => __('Add New Country', 'text_domain'),
        'edit_item' => __('Edit Country', 'text_domain'),
        'update_item' => __('Update Country', 'text_domain'),
        'view_item' => __('View Country', 'text_domain'),
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
    register_taxonomy('country', ['post'], $args);
}
add_action('init', 'tw_country_taxonomy', 0);

// Register Person Taxonomy
function tw_person_taxonomy()
{
    $labels = [
        'name' => _x('Person', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x(
            'Person',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Persons', 'text_domain'),
        'all_items' => __('All Persons', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Person Name', 'text_domain'),
        'add_new_item' => __('Add New Person', 'text_domain'),
        'edit_item' => __('Edit Person', 'text_domain'),
        'update_item' => __('Update Person', 'text_domain'),
        'view_item' => __('View Person', 'text_domain'),
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
    register_taxonomy('person', ['post'], $args);
}
add_action('init', 'tw_person_taxonomy', 0);

// Register Province or State Taxonomy
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

// Register Region Taxonomy
function tw_region_taxonomy()
{
    $labels = [
        'name' => _x('Region', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x(
            'Region',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Regions', 'text_domain'),
        'all_items' => __('All Regions', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Region Name', 'text_domain'),
        'add_new_item' => __('Add New Region', 'text_domain'),
        'edit_item' => __('Edit Region', 'text_domain'),
        'update_item' => __('Update Region', 'text_domain'),
        'view_item' => __('View Region', 'text_domain'),
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
    register_taxonomy('region', ['post'], $args);
}
add_action('init', 'tw_region_taxonomy', 0);

// Register Social Tag Taxonomy
function tw_social_tag_taxonomy()
{
    $labels = [
        'name' => _x('Social Tag', 'Taxonomy General Name', 'text_domain'),
        'singular_name' => _x(
            'Social Tag',
            'Taxonomy Singular Name',
            'text_domain'
        ),
        'menu_name' => __('Social Tags', 'text_domain'),
        'all_items' => __('All Social Tags', 'text_domain'),
        'parent_item' => __('Parent Item', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'new_item_name' => __('New Social Tag Name', 'text_domain'),
        'add_new_item' => __('Add New Social Tag', 'text_domain'),
        'edit_item' => __('Edit Social Tag', 'text_domain'),
        'update_item' => __('Update Social Tag', 'text_domain'),
        'view_item' => __('View Social Tag', 'text_domain'),
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
    register_taxonomy('social_tag', ['post'], $args);
}
add_action('init', 'tw_social_tag_taxonomy', 0);
