<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship/get_avatar_data/skip', function( $default, $avatar, $dbt )
{
    $i    = 4;
    $fn   = 'get_avatar';
    $file = '/sfwd-lms/themes/ld30/templates/focus/masthead.php';
    if ( isset( $dbt[$i]['function'] ) and $dbt[$i]['function'] == $fn and
         isset( $dbt[$i]['file'] ) and substr_compare( $dbt[$i]['file'], $file, strlen( $dbt[$i]['file'] )-strlen( $file ), strlen( $file ) ) === 0
    ) return true;
    return $default;
}, 10, 3 );