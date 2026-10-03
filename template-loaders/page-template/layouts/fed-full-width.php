<?php
/**
 * FED Full Width (With Header & Footer)
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div id="fed-full-width-wrapper" class="fed-page-template fed-template-full-width w-full min-h-screen">
	<main id="main" class="site-main w-full" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</main>
</div>
<?php
get_footer();
