<?php
$dashboard_url = fed_get_dashboard_url();
$login_page    = fed_get_login_url();
$login_page    = $login_page === false ? wp_login_url() : $login_page;
$current_link  = get_permalink();
if ( $dashboard_url === $current_link && ! is_user_logged_in() ) {
	wp_safe_redirect( $login_page );
}

get_header(); ?>

<div id="primary fed_dashboard" class="container-fluid">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</main>
</div>
<?php get_footer(); ?>
