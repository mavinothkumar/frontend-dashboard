<?php
/**
 * FED Container (With Header & Footer)
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div id="fed-container-wrapper" class="fed-page-template fed-template-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
	<main id="main" class="site-main w-full" role="main">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</main>
</div>
<?php
get_footer();
