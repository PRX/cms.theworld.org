<?php
defined( 'ABSPATH' ) or exit;

add_filter( 'wpml_translatable_user_meta_fields', 'authorship_add_user_meta_fields_to_wpml' );
add_filter( 'authorship/filter_author_link', 'authorship_dont_filter_author_link_for_wpml', 10, 2 );
function authorship_add_user_meta_fields_to_wpml( $user_meta_fields )
{
    $user_meta_fields[] = 'user_url'; // from users table
    $user_meta_fields[] = 'molongui_author_phone';
    $user_meta_fields[] = 'molongui_author_job';
    $user_meta_fields[] = 'molongui_author_company';
    $user_meta_fields[] = 'molongui_author_company_link';
    $user_meta_fields[] = 'molongui_author_short_bio';
$user_meta_fields[] = 'molongui_author_long_bio';

    return $user_meta_fields;
}
function authorship_dont_filter_author_link_for_wpml( $default, $args )
{
    $fn = 'add_author_url_to_ls_lang';

    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) )
    {
        return true;
    }
    return $default;
}