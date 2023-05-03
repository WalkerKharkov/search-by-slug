<?php

/*
    Plugin Name: Search by Slug
    Description: This plugin allows you to search for a post or page by its slug
    Author: ElDiablo
    Version: 1.1.0
*/

const SBS_SEARCH_COMMAND = 'slug:';

if ( ! function_exists( 'sbs_search_by_slug_query_adjustment' ) ) {
    function sbs_search_by_slug_query_adjustment( $query ) {
        // works in wp_admin area only
        if ( ! is_admin() ) return;

        $search_string = $query->get( 's' );

        if ( empty( $search_string ) ) return;

        // checks if search by slug activated
        if ( strpos( trim( $search_string ), SBS_SEARCH_COMMAND ) !== 0 ) return;

        $slug = esc_sql( trim( str_replace( SBS_SEARCH_COMMAND, '', $search_string ) ) );
        $query->set( 's', '' );
        $query->set( 'sbs_slug_search', $slug );
    }

    add_action( 'pre_get_posts', 'sbs_search_by_slug_query_adjustment' );
}

if ( ! function_exists( 'sbs_search_by_slug_request_adjustment' ) ) {
    function sbs_search_by_slug_request_adjustment( $request, WP_Query $query ) {
        if ( isset( $query->query_vars['sbs_slug_search'] ) && ! empty( $query->query_vars['sbs_slug_search'] ) ) {
            global $wpdb;
            $search_string = 'AND ((' . $wpdb->posts . '.post_name LIKE "%' . $query->query_vars['sbs_slug_search'] . '%") AND ';
            $request = preg_replace( '/AND \(/', $search_string, $request, 1);
        }

        return $request;
    }

    add_filter( 'posts_request', 'sbs_search_by_slug_request_adjustment', 10, 2 );
}

