<?php
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by/archive/no_loop', function( $default, $user, $args )
{
    $fn    = 'render';
    $class = 'ElementorPro\Modules\Posts\Skins\Skin_Base';
    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) and
         isset( $args['dbt'][$key]['class'] ) and ( $args['dbt'][$key]['class'] == $class ) )
    {
        return false;
    }
    return $default;
}, 10, 3 );
/*
add_filter( '_authorship/filter/get_user_by/archive/no_loop', function( $default, $user, $args )
{
    $fn    = 'render_avatar';
    $class = 'ElementorPro\Modules\Posts\Skins\Skin_Cards';
    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) and
         isset( $args['dbt'][$key]['class'] ) and ( $args['dbt'][$key]['class'] == $class ) )
    {
        return false;
    }
    return $default;
}, 10, 3 );
*/