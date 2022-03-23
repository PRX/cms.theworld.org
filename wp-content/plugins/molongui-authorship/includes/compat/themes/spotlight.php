<?php
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by/archive/no_loop', function( $default, $user, $args )
{
    $fn = 'csco_get_post_meta';
    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) ) return false;
    return $default;
}, 10, 3 );