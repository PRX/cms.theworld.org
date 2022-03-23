<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'is_guest_post' ) )
{
    function is_guest_post( $post_id = null )
    {
        if ( empty( $post_id ) )
        {
            global $post;
            if ( empty( $post ) ) return false;
            $post_id = $post->ID;
        }
        $author = get_post_meta( $post_id, '_molongui_main_author', true );
        if ( !empty( $author ) ) if ( strncmp( $author, 'guest', strlen( 'guest' ) ) === 0 ) return true;
        return false;
    }
}
if ( !function_exists( 'has_guest_author' ) )
{
    function has_guest_author( $post_id = null )
    {
        if ( empty( $post_id ) )
        {
            global $post;
            if ( empty( $post ) ) return false;
            $post_id = $post->ID;
        }
        $authors = get_post_meta( $post_id, '_molongui_author', false );
        if ( empty( $authors ) ) return false;
        foreach ( $authors as $author )
        {
            $prefix = 'guest';
            if ( strncmp( $author, $prefix, strlen( $prefix ) ) === 0 ) return true;
        }
        return false;
    }
}
if ( !function_exists( 'is_multiauthor_post' ) )
{
    function is_multiauthor_post( $post_id = null )
    {
        if ( empty( $post_id ) )
        {
            global $post;
            if ( empty( $post ) ) return false;
            $post_id = $post->ID;
        }

        return ( count( get_post_meta( $post_id, '_molongui_author', false ) ) > 1 ? true : false );
    }
}
if ( !function_exists( 'is_multiauthor_link' ) )
{
    function is_multiauthor_link( $link )
    {
        $arg = '?molongui_byline=true';

        return ( strpos( $link, $arg ) !== false ? true : false );
    }
}
if ( !function_exists( 'get_main_author' ) )
{
    function get_main_author( $post_id )
    {
        $data = new stdClass();
        $ignore_guest = false;
        $main_author = get_post_meta( $post_id, '_molongui_main_author', true );
        if ( !empty( $main_author ) )
        {
            $split      = explode( '-', $main_author );
            $data->id   = $split[1];
            $data->type = $split[0];
            $data->ref  = $main_author;
            if ( $data->type == 'guest' and !authorship_is_feature_enabled( 'guest' ) )
            {
                $ignore_guest = true;
            }
        }
        if ( empty( $main_author ) or $ignore_guest )
        {
            if ( $post_author = get_post_field( 'post_author', $post_id ) )
            {
                $data->id   = $post_author;
                $data->type = 'user';
                $data->ref  = $data->type.'-'.$data->id;
            }
            else return false;
        }
        return $data;
    }
}
if ( !function_exists( 'get_post_authors' ) )
{
    function get_post_authors( $post_id = null, $key = '' )
    {
        if ( empty( $post_id ) or !is_integer( $post_id ) )
        {
            global $post;
            if ( empty( $post ) ) return false;
            $post_id = $post->ID;
        }
        $data = array();
        $main_author = get_main_author( $post_id );
        if ( empty( $main_author ) ) return false;
        if ( !authorship_is_feature_enabled( 'multi' ) ) return array( $main_author );
        $authors = get_post_meta( $post_id, '_molongui_author', false );
        if ( !empty( $authors) )
        {
            $guest_enabled = authorship_is_feature_enabled( 'guest' );

            foreach ( $authors as $author )
            {
                $split = explode( '-', $author );
                if ( $split[1] == $main_author->id ) continue;
                if ( $split[0] === 'guest' and !$guest_enabled ) continue;
                $data[] = (object) array( 'id' => (int)$split[1], 'type' => $split[0], 'ref' => $author );
            }
        }
        array_unshift( $data, $main_author );
        if ( !$key ) return $data;
        $values = array();
        foreach ( $data as $author ) $values[] = $author->$key;
        return $values;
    }
}
if ( !function_exists( 'get_byline' ) )
{
    function get_byline( $pid = null, $separator = '', $last_separator = '', $linked = false )
    {
        if ( is_null( $pid ) or !is_integer( $pid ) or !$pid )
        {
            global $post;
            if ( empty( $post ) ) return '';
            $pid = $post->ID;
        }
        if ( $authors = get_post_authors( $pid ) )
        {
            $options = authorship_get_options();
            switch ( $options['byline_multiauthor_display'] )
            {
                case 'main':

                    $byline = mount_byline( $authors, '1', false, '', '', $linked );

                break;
                case '1':

                    $byline = mount_byline( $authors, '1', true, '', $last_separator, $linked );

                break;
                case '2':

                    $byline = mount_byline( $authors, '2', true, $separator, $last_separator, $linked );

                break;
                case '3':

                    $byline = mount_byline( $authors, '3', true, $separator, $last_separator, $linked );

                break;
                case 'all':
                default:

                    $byline = mount_byline( $authors, count( $authors ), false, $separator, $last_separator, $linked );

                break;
            }
        }
        return $byline;
    }
}
if ( !function_exists( 'mount_byline' ) )
{
    function mount_byline( $authors, $qty, $count = true, $separator = '', $last_separator = '', $linked = false )
    {
        if ( !$authors ) return;
        $string = '';
        $total  = count( $authors );
        $i = 0;
        $options = authorship_get_options();
        $separator      = ( !empty( $separator ) ? $separator : ( !empty( $options['byline_multiauthor_separator'] ) ? $options['byline_multiauthor_separator'] : ',' ) );
        $last_separator = ( !empty( $last_separator ) ? $last_separator : ( !empty( $options['byline_multiauthor_last_separator'] ) ? $options['byline_multiauthor_last_separator'] : __( 'and', 'molongui-authorship' ) ) );
        if ( $qty < $total )
        {
            for ( $j = 0; $j < $qty; $j++ )
            {
                $divider = ( $i == 0 ? '' : ( $i == ( $total - 1 ) ? ' '.$last_separator.' ' : $separator.' ' ) );
                $author_class = new Author( $authors[$j]->id, $authors[$j]->type );
                if ( $linked ) $item = $author_class->get_link();
                else $item = $author_class->get_name();
                $string .= $divider . $item;
                if ( ++$i == $qty ) break;
            }
            if ( $count ) $string .= ' '.sprintf( __( '%s %d more', 'molongui-authorship' ), $last_separator, $total - $qty );
        }
        else
        {
            foreach ( $authors as $author )
            {
                $divider = ( $i == 0 ? '' : ( $i == ( $total - 1 ) ? ' '.$last_separator.' ' : $separator.' ' ) );
                $author_class = new Author( $author->id, $author->type );
                if ( $linked ) $item = $author_class->get_link();
                else $item = $author_class->get_name();
                $string .= $divider . $item;
                if ( ++$i == $qty ) break;
            }
        }

        return $string;
    }
}
if ( !function_exists( 'get_coauthored_posts' ) )
{
    function get_coauthored_posts( $authors, $get_all = false, $exclude = array(), $entry = 'post', $meta_query = array() )
    {
        $options = authorship_get_options();
        switch ( $entry )
        {
            case 'all':
                $entries = molongui_get_post_types( 'all', 'names', false );
            break;

            case 'selected':
                $entries = molongui_supported_post_types( MOLONGUI_AUTHORSHIP_PREFIX, 'all', false );
            break;

            case 'related':
                $entries = explode( ",", $options['related_post_types'] );
            break;

            default:
                $entries = $entry;
            break;
        }
        if ( count( $authors ) > 1 )
        {
            $mq['authors']['relation'] = 'AND';
            foreach( $authors as $author )
            {
                $mq['authors'][] = array( 'key' => '_molongui_author', 'value' => $author->ref, 'compare' => '=' );
            }
        }
        else
        {
            $mq['authors'] = array( 'key' => '_molongui_author', 'value' => $authors, 'compare' => '=' );
        }
        if ( !empty( $meta_query ) )
        {
            $mq['authors']['relation'] = 'AND';
            $mq['authors'] = array
            (
                'key'   => $meta_query['key'],
                'value' => $meta_query['value'],
            );
        }
        $args = array
        (
            'post_type'      => $entries,
            'orderby'        => !empty( $options['related_orderby'] ) ? $options['related_orderby'] : 'date',
            'order'          => !empty( $options['related_order'] )   ? $options['related_order']   : 'desc',
            'posts_per_page' => $get_all ? '-1' : $options['related_items'],
            'post__not_in'   => $exclude,
            'meta_query'     => $mq,
            'site_id'        => get_current_blog_id(),
            'language'       => molongui_get_language(),
        );
        $data = molongui_query( $args, 'posts' );
        if ( !empty( $data->posts ) ) foreach ( $data->posts as $post ) $posts[] = $post;
        return ( !empty( $posts ) ? $posts : array() );
    }
}
if ( !function_exists( 'authorship_guess_post_type' ) )
{
    function authorship_guess_post_type()
    {
        global $post, $typenow, $pagenow, $current_screen;

        $post_id   = isset( $_REQUEST['post'] ) ? (int)$_REQUEST['post'] : false;
        $post_type = null;

        if ( $post and $post->post_type )
        {
            $post_type = authorship_get_post_type( $post );
        }
        elseif ( $typenow )
        {
            $post_type = $typenow;
        }
        elseif ( $current_screen and !empty( $current_screen->post_type ) )
        {
            $post_type = $current_screen->post_type;
        }
        elseif ( isset( $_REQUEST['post_type'] ) and !empty( $_REQUEST['post_type'] ) and is_string( $_REQUEST['post_type'] ) )
        {
            $post_type = sanitize_key( $_REQUEST['post_type']);
        }
        elseif ( 'post.php' == $pagenow and !empty( $post_id ) )
        {
            $post_type = authorship_get_post_type( $post_id );
        }
        elseif ( 'edit.php' == $pagenow and empty( $_REQUEST['post_type'] ) )
        {
            $post_type = 'post';
        }
        elseif ( isAuthor() )
        {
            $post_type = 'post';
        }

        return $post_type;
    }
}
if ( !function_exists( 'authorship_get_post_type' ) )
{
    function authorship_get_post_type( $postOrPostId )
    {
        $post = null;

        if ( is_numeric( $postOrPostId ) )
        {
            $postOrPostId = (int)$postOrPostId;

            if ( !empty( $postOrPostId ) )
            {
                $post = get_post( $postOrPostId );
            }
        }
        else
        {
            $post = $postOrPostId;
        }

        if ( !$post instanceof WP_Post )
        {
            return false;
        }

        return $post->post_type;
    }
}
if ( !function_exists( 'isAuthor' ) )
{
    function isAuthor()
    {
        global $wp_query;

        if ( !isset( $wp_query ) ) return;

        return is_author();
    }
}
function authorship_post_status( $post_type = '' )
{
    $post_status = array( 'draft', 'publish', 'future', 'pending', 'private' );

    if ( ( is_array( $post_type ) and in_array( 'attachment', $post_type ) ) or 'attachment' === $post_type )
    {
        $post_status[] = 'inherit';
    }

    return apply_filters( 'authorship/post_status', $post_status );
}