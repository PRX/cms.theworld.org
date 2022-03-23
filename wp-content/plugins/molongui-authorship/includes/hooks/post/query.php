<?php
defined( 'ABSPATH' ) or exit;
if ( !authorship_byline_takeover() ) return;
function authorship_get_posts_clauses( $clauses, &$query )
{
    add_filter( '_authorship/posts_clauses_request', function() use ( $clauses ){ return $clauses; } );

    return $clauses;
}
function authorship_author_posts( $posts, &$query )
{
    if ( $query->is_author and !$query->is_admin )//if ( $query->is_author )
    {
        global $wpdb;

        $clauses = apply_filters( '_authorship/posts_clauses_request', array() );
        $q = &$this->query_vars;
        $q = $this->fill_query_vars( $q );
        if ( isset( $q['no_found_rows'] ) ) {
            $q['no_found_rows'] = (bool) $q['no_found_rows'];
        } else {
            $q['no_found_rows'] = false;
        }
        if ( empty( $q['posts_per_page'] ) ) {
            $q['posts_per_page'] = get_option( 'posts_per_page' );
        }
        if ( isset( $q['showposts'] ) && $q['showposts'] ) {
            $q['showposts']      = (int) $q['showposts'];
            $q['posts_per_page'] = $q['showposts'];
        }
        if ( ( isset( $q['posts_per_archive_page'] ) && 0 != $q['posts_per_archive_page'] ) && ( $this->is_archive || $this->is_search ) ) {
            $q['posts_per_page'] = $q['posts_per_archive_page'];
        }
        $q['posts_per_page'] = (int) $q['posts_per_page'];
        if ( $q['posts_per_page'] < -1 ) {
            $q['posts_per_page'] = abs( $q['posts_per_page'] );
        } elseif ( 0 == $q['posts_per_page'] ) {
            $q['posts_per_page'] = 1;
        }
        if ( ! isset( $q['update_post_term_cache'] ) ) {
            $q['update_post_term_cache'] = true;
        }
        if ( ! isset( $q['update_post_meta_cache'] ) ) {
            $q['update_post_meta_cache'] = true;
        }

        $where    = isset( $clauses['where'] ) ? $clauses['where'] : '';
        $groupby  = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';
        $join     = isset( $clauses['join'] ) ? $clauses['join'] : '';
        $orderby  = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
        $distinct = isset( $clauses['distinct'] ) ? $clauses['distinct'] : '';
        $fields   = isset( $clauses['fields'] ) ? $clauses['fields'] : '';
        $limits   = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

        if ( ! empty( $groupby ) ) {
            $groupby = 'GROUP BY ' . $groupby;
        }
        if ( ! empty( $orderby ) ) {
            $orderby = 'ORDER BY ' . $orderby;
        }

        $found_rows = '';
        if ( ! $q['no_found_rows'] && ! empty( $limits ) ) {
            $found_rows = 'SQL_CALC_FOUND_ROWS';
        }

        $old_request = "SELECT $found_rows $distinct $fields FROM {$wpdb->posts} $join WHERE 1=1 $where $groupby $orderby $limits";
        $nometa_join  = $join . " LEFT JOIN ".$wpdb->postmeta." ON ( ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND ".$wpdb->postmeta.".meta_key = '_molongui_author' )";
        $nometa_where = $where . " LEFT JOIN ".$wpdb->postmeta." ON ( ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND ".$wpdb->postmeta.".meta_key = '_molongui_author' )";
        $nometa_ids   = $wpdb->get_col( "SELECT $found_rows $distinct {$wpdb->posts}.ID FROM {$wpdb->posts} $nometa_join WHERE 1=1 $nometa_where $groupby $orderby $limits" );
        $meta_join  = $join . "";
        $meta_where = $where . "";
        $meta_ids   = $wpdb->get_col( "SELECT $found_rows $distinct {$wpdb->posts}.ID FROM {$wpdb->posts} $join WHERE 1=1 $where $groupby $orderby $limits" );
        $ids = array_unique( array_merge( $nometa_ids, $meta_ids ) );
        if ( $ids )
        {
            $posts = $ids;
            $query->set_found_posts( $q, $limits );
            _prime_post_caches( $ids, $q['update_post_term_cache'], $q['update_post_meta_cache'] );
        }
        else
        {
            $posts = array();
        }
    }

    return $posts;
}
function authorship_filter_user_posts( $wp_query )
{
    if ( isset( $wp_query->is_guest_author )
         or ( molongui_is_request( 'admin' ) and !$wp_query->is_author )
         or ( !$wp_query->is_main_query() and apply_filters_ref_array( 'molongui_edit_main_query_only', array( true, &$wp_query ) ) )
    ) return;
    if ( $wp_query->is_author )
    {
        $meta_query = $wp_query->get( 'meta_query' );
        if ( !is_array( $meta_query ) and empty( $meta_query ) ) $meta_query = array();
        if ( molongui_is_request( 'admin' ) and $wp_query->is_author )
        {
            $author_id = $wp_query->query_vars['author'];
        }
        else
        {
            $author = get_users( array( 'nicename' => $wp_query->query_vars['author_name'] ) );
            if ( !$author ) return;

            $author_id = $author[0]->ID;
        }
        $meta_query[] = array
        (
            array
            (
                'key'     => '_molongui_author',
                'value'   => 'user-'.$author_id,
                'compare' => '==',
            ),
        );
        $wp_query->set( 'meta_query', $meta_query );
        add_filter( '_authorship/posts_where', '__return_true' );
    }
}
add_action( 'pre_get_posts', 'authorship_filter_user_posts', 999 );
function authorship_remove_author_from_where_clause( $where, $wp_query )
{
    if ( apply_filters( '_authorship/posts_where', false ) )
    {
        if ( !empty( $wp_query->query_vars['author'] ) )
        {
            global $wpdb;
$alias = 'molongui_meta';
            $where = str_replace( ' AND '.$wpdb->posts.'.post_author IN ('.$wp_query->query_vars['author'].')', '', $where );
            $where = str_replace( ' AND ('.$wpdb->posts.'.post_author = '.$wp_query->query_vars['author'].')' , '', $where );
        }
    }
    return $where;
}
add_filter( 'posts_where', 'authorship_remove_author_from_where_clause', 10, 2 );
function authorship_add_user_posts_join( $join, $query )
{
    if ( is_admin() or !is_author() or is_guest_author() or !$query->is_main_query() ) return $join;
    $alias = 'molongui_meta';
    global $wpdb;
    $join .= ' LEFT JOIN '.$wpdb->postmeta.' AS '.$alias.'_1 ON '.$wpdb->posts.'.ID = '.$alias.'_1.post_id ';
    $join .= ' LEFT JOIN '.$wpdb->postmeta.' AS '.$alias.'_2 ON '.$wpdb->posts.'.ID = '.$alias.'_2.post_id ';

    return $join;
}
function authorship_modify_alias( $alias, $clause, $parent_query, $meta_query )
{
    if ( is_admin() or !is_author() or is_guest_author() or !is_main_query() or apply_filters( 'authorship/skip/query/alias', false ) ) return $alias;

    if ( '_molongui_author' === $clause['key'] and 'NOT EXISTS' === $clause['compare'] )
    {
        return 'molongui_meta_1';
    }
    elseif ( '_molongui_author' === $clause['key'] and 'NOT EXISTS' !== $clause['compare'] )
    {
        return 'molongui_meta_2';
    }

    return $alias;
}
//add_filter( 'meta_query_find_compatible_table_alias', 'authorship_modify_alias', 10, 4 );