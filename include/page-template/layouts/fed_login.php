<?php
get_header(); ?>

<div id="primary fed_login" class="container-fluid">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</main>
</div>
<?php get_footer(); ?>
