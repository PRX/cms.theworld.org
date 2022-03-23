<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by', function( $data, $args )
{
    list( $filter, $user ) = $data;
    if ( empty( $args['post'] ) or !$args['post']->ID ) return array( $filter, $user );
    $fn      = 'extra_get_post_author_link';
    $post_id = $args['post']->ID;
    if ( $key = array_search( $fn, array_column( $args['dbt'], 'function' ) ) )
    {
        if ( is_multiauthor_post( $post_id ) )
        {
            $main_author = get_main_author( $post_id );
            $author_class = new Author( $main_author->id, $main_author->type );
            $user->display_name     = get_byline( $post_id );
            $user->user_url         = authorship_author_link( $user->user_url, $post_id );
            $user->description      = '';
            $user->user_description = $user->description;
            $user->user_nicename    = $author_class->get_slug();
            $user->nickname         = $user->display_name;
            $filter = true;
        }
        elseif ( is_guest_post( $post_id ) )
        {
            $main_author = get_main_author( $post_id );
            $author_class = new Author( $main_author->id, $main_author->type );
            $user->guest_id         = $author_class->get_id();
            $user->display_name     = $author_class->get_name();//get_byline( $post_id );
            $user->user_url         = $author_class->get_meta( 'web' );
            $user->description      = $author_class->get_bio();
            $user->user_description = $user->description;
            $user->user_nicename    = $author_class->get_slug();
            $user->nickname         = $user->display_name;
            $filter = true;
        }
    }
    return array( $filter, $user );
}, 10, 2 );
add_filter( 'authorship/box/render/bypass_check', function()
{
    $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 8 );
    if ( isset( $dbt[7]['function'] ) and $dbt[7]['function'] == "et_theme_builder_frontend_render_post_content" ) return true;
    return false;
});