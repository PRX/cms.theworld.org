<?php
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by', function( $data, $args )
{
    list( $filter, $user ) = $data;

    if ( is_author() or is_guest_author() )
    {
        $fn    = 'get_author_name';
        $class = 'FLPageDataPost';
        $file  = '/bb-theme-builder/classes/class-fl-page-data.php';
        if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) and
             isset( $args['dbt'][$key]['class'] ) and ( $args['dbt'][$key]['class'] == $class ) /*and
             isset( $args['dbt'][$key]['file'] ) and substr_compare( $args['dbt'][$key]['file'], $file, strlen( $args['dbt'][$key]['file'] )-strlen( $file ), strlen( $file ) ) === 0 */ )
        {
            $filter = true;
        }
    }
    return array( $filter, $user );
}, 10, 2 );
add_filter( '_authorship/get_user_by/guest/archive/loop', function( $byline, $args )
{
    if ( is_author() or is_guest_author() )
    {
        $fn   = 'get_the_author_meta';
        $file = 'bb-plugin/modules/post-grid/includes/post-feed.php';

        if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) and
            isset( $args['dbt'][$key]['file'] ) and substr_compare( $args['dbt'][$key]['file'], $file, strlen( $args['dbt'][$key]['file'] )-strlen( $file ), strlen( $file ) ) === 0
        ){
            $byline = true;
        }
    }

    return $byline;
}, 10, 2 );