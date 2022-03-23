<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !authorship_byline_takeover() ) return;
function authorship_get_user_by( $user, $field, $value )
{
    global $pagenow;
    if ( is_admin() and ( !defined( 'DOING_AJAX' ) or !DOING_AJAX )
         or $pagenow == 'wp-login.php'
         or $field == 'login'
    )  return $user;
    global $in_comment_loop;
    if ( $in_comment_loop ) return $user;
    $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    if ( empty( $dbt ) ) return $user;
    global $wp_query;
    global $post;
    $old_user = $user;
    $filter = true;
    $args = array( 'field' => $field, 'value' => $value, 'post' => $post, 'query' => $wp_query, 'dbt' => $dbt );

    /*!
     * PRIVATE FILTER.
     *
     * For internal use only. Not intended to be used by plugin or theme developers.
     * Future compatibility NOT guaranteed.
     *
     * Please do not rely on this filter for your custom code to work. As a private filter it is meant to be
     * used only by Molongui. It may be edited, renamed or removed from future releases without prior notice
     * or deprecation phase.
     *
     * If you choose to ignore this notice and use this filter, please note that you do so at on your own risk
     * and knowing that it could cause code failure.
     */
    list( $filter, $user ) = apply_filters( '_authorship/filter/get_user_by', array( true, $user ), $args );
    if ( !$filter ) return is_null( $user ) ? $old_user : $user;

    /*!
     * PRIVATE FILTER.
     *
     * For internal use only. Not intended to be used by plugin or theme developers.
     * Future compatibility NOT guaranteed.
     *
     * Please do not rely on this filter for your custom code to work. As a private filter it is meant to be
     * used only by Molongui. It may be edited, renamed or removed from future releases without prior notice
     * or deprecation phase.
     *
     * If you choose to ignore this notice and use this filter, please note that you do so at on your own risk
     * and knowing that it could cause code failure.
     */
    if ( apply_filters( '_authorship/cache_delete/get_user_by', true, $user, $args ) )
    {
        wp_cache_delete( $user->ID, 'users' );
        wp_cache_delete( $user->user_login, 'userlogins' );
        wp_cache_delete( $user->user_email, 'useremail' );
        wp_cache_delete( $user->user_nicename, 'userslugs' );
    }
    if ( is_object( $wp_query ) and is_guest_author() )
    {
        $user = authorship_get_guest_by( $field, $value );
        return $user;
    }
    elseif ( is_object( $wp_query ) and $wp_query->is_author /*and in_the_loop()*/ )
    {
        if ( !in_the_loop() )
        {
            if ( !empty( $wp_query->query_vars['author'] ) ) $user = new WP_User( $wp_query->query_vars['author'] );

            /*!
             * PRIVATE FILTER.
             *
             * For internal use only. Not intended to be used by plugin or theme developers.
             * Future compatibility NOT guaranteed.
             *
             * Please do not rely on this filter for your custom code to work. As a private filter it is meant to be
             * used only by Molongui. It may be edited, renamed or removed from future releases without prior notice
             * or deprecation phase.
             *
             * If you choose to ignore this notice and use this filter, please note that you do so at on your own risk
             * and knowing that it could cause code failure.
             */
            if ( apply_filters( '_authorship/filter/get_user_by/archive/no_loop', true, $user, $args ) ) return $user;
        }
        global $post;
        if ( empty( $post ) or !$post->ID ) return $user;
        $post_id = apply_filters( 'authorship/override/get_user_by/archive/post_id', $post->ID, $post, $user, $dbt );
        $main_author = get_main_author( $post_id );
        $author = new Author( $main_author->id, $main_author->type );
        $user->ID            = $main_author->id; // We need to restore user ID that might have been altered above.
        $user->display_name  = get_byline( $post_id );
        $user->user_nicename = $author->get_slug();
        $user->nickname      = $user->display_name;
        return $user;
    }
    elseif ( ( ( is_object( $wp_query ) and $wp_query->is_home ) )
        or ( is_object( $wp_query ) and $wp_query->is_main_query() and get_option( 'page_for_posts' ) == $wp_query->get_queried_object_id() )
        or ( is_object( $wp_query ) and $wp_query->is_singular and molongui_is_post_type_enabled( MOLONGUI_AUTHORSHIP_PREFIX ) )
        or ( is_object( $wp_query ) and $wp_query->is_search )
        or ( is_object( $wp_query ) and $wp_query->is_category )
        or ( is_object( $wp_query ) and $wp_query->is_tag )
    ){
        global $post;
        if ( empty( $post ) or empty( $post->ID ) ) return $user;
        $post_id = apply_filters( 'molongui_authorship_override_get_user_by_post_id', $post->ID, $post, $user );
        $main_author = get_main_author( $post_id );
        $author_class = new Author( $main_author->id, $main_author->type );
        if ( is_multiauthor_post( $post_id ) )
        {
            $user->display_name     = get_byline( $post_id );
            $user->user_url         = authorship_author_link( $user->user_url, $post_id );
            $user->description      = '';
            $user->user_description = $user->description;
            $user->user_nicename    = $author_class->get_slug();
            $user->nickname         = $user->display_name;
            if ( isset( $dbt[1]['function'] ) and $dbt[1]['function'] != 'get_avatar_data' )
            {
                $user->user_email = $author_class->get_mail();
            }
            return $user;
        }
        elseif ( is_guest_post( $post_id ) )
        {
            $user->guest_id         = $author_class->get_id();
            $user->display_name     = $author_class->get_name();//get_byline( $post_id );
            $user->user_url         = $author_class->get_meta( 'web' );
            $user->description      = $author_class->get_bio();
            $user->user_description = $user->description;
            $user->user_nicename    = $author_class->get_slug();
            $user->nickname         = $user->display_name;
            if ( isset( $dbt[1]['function'] ) and $dbt[1]['function'] != 'get_avatar_data' )
            {
                $user->user_email = $author_class->get_mail();
            }
            return $user;
        }
    }
    return $user;
}
add_filter( '_authorship/get_user_by', 'authorship_get_user_by', 10, 3 );
function authorship_no_userdata( $false, $field, $value )
{
    global $wp_query;
    if ( isset( $wp_query ) and !empty( $wp_query->is_guest_author ) and !empty( $wp_query->guest_author_id ) )
    {
        $user = authorship_get_guest_by( $field, $value );
        $user->ID = $wp_query->guest_author_id;

        return $user;
    }
    else return $false;
}
add_filter( '_authorship/no_userdata', 'authorship_no_userdata', 10, 3 );
function authorship_get_guest_by( $field, $value )
{
    global $wp_query;
    global $post;
    $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 8 );
    $args = array( 'field' => $field, 'value' => $value, 'post' => $post, 'query' => $wp_query, 'dbt' => $dbt );
    $guest_id = $wp_query->guest_author_id;
    $author = new Author( $guest_id, 'guest' );
    $byline_name = false;
    if ( in_the_loop() and !empty( $post->ID ) ) $byline_name = true;

    /*!
     * PRIVATE FILTER.
     *
     * For internal use only. Not intended to be used by plugin or theme developers.
     * Future compatibility NOT guaranteed.
     *
     * Please do not rely on this filter for your custom code to work. As a private filter it is meant to be
     * used only by Molongui. It may be edited, renamed or removed from future releases without prior notice
     * or deprecation phase.
     *
     * If you choose to ignore this notice and use this filter, please note that you do so at on your own risk
     * and knowing that it could cause code failure.
     */
    $byline_name = apply_filters( '_authorship/get_user_by/guest/archive/loop', $byline_name, $args );

    if ( $byline_name )
    {
        global $post;
        $post_id = $post->ID;
        $main_author = get_main_author( $post_id );
        $author_m = new Author( $main_author->id, $main_author->type );
    }
    $user = new WP_User();
    $user->guest_id         = $guest_id;
    $user->display_name     = $byline_name ? get_byline( $post_id ) : $author->get_name();
    $user->user_url         = $author->get_meta( 'web' );
    $user->description      = $author->get_bio();
    $user->user_description = $user->description;
    $user->user_nicename    = $byline_name ? $author_m->get_slug() : $author->get_slug();
    $user->nickname         = $user->display_name;
    $user->user_email       = $author->get_mail();
    $user->first_name       = $author->get_meta( 'first_name' );
    $user->last_name        = $author->get_meta( 'last_name'  );
    $user->user_registered  = get_the_date( '', $guest_id );
    return $user;
}