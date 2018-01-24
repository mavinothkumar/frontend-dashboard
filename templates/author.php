<?php
/**
 * Author Page
 *
 * @package frontend-dashboard
 */

get_header();
$user = get_user_by( 'slug', get_query_var( 'author_name' ) );
fed_show_user_profile_page( $user);

get_footer();