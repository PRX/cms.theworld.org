<?php
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by', function( $data, $args )
{
    $fn    = 'get_author_canonical_url';
    $class = 'The_SEO_Framework\Generate_Url';
    list( $filter, $user ) = $data;
    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) and
         isset( $args['dbt'][$key]['class'] ) and ( $args['dbt'][$key]['class'] == $class ) )
    {
        $filter = false;
    }
    return array( $filter, $user );
}, 10, 2 );

add_filter( 'authorship/filter_author_link', function( $default, $args )
{
    $i     = 4;
    $fn    = 'get_author_canonical_url';
    $class = 'The_SEO_Framework\Generate_Url';
    if ( isset( $args['dbt'][$i]['function'] ) and $args['dbt'][$i]['function'] == $fn and isset( $args['dbt'][$i]['class'] ) and $args['dbt'][$i]['class'] == $class ) return true;
    return $default;
}, 10, 2 );
