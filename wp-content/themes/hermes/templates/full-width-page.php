<?php
/**
 * Template Name: Full Width Page
 *
 * @package Hermes
 */

get_header();

get_template_part( 'template-parts/singular/page-header' );
?>

	<main id="main" class="site-main alignwide th-pb-base th-space-y-base th-divide-y th-divide-solid th-divide-secondary-base">

		<?php
		while ( have_posts() ) : the_post();

		get_template_part( 'template-parts/singular/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

	</main><!-- #main -->

<?php
get_footer();
