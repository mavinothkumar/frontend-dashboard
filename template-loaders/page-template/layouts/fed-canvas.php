<?php
/**
 * FED Canvas / Blank (No Header, No Footer)
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full bg-slate-50">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'fed-canvas-body h-full bg-slate-50 antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8' ); ?>>
	<?php wp_body_open(); ?>
	<div id="fed-canvas-wrapper" class="fed-page-template fed-template-canvas w-full max-w-2xl mx-auto px-4">
		<main id="main" class="site-main w-full" role="main">
			<?php
			while ( have_posts() ) : the_post();
				the_content();
			endwhile;
			?>
		</main>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
