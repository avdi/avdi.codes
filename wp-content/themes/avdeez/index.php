<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Hermes
 */

get_header();

	do_action( 'avdeez_before_site_main' );
	?>

	<main id="main" class="site-main alignfull hentry-grid has-primary-dark-background-color th-flex th-flex-wrap th-my-0">

	<?php
	if ( have_posts() ) :

		get_template_part( 'template-parts/global/page-header' );

		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/hfeed/content', get_post_format() );

		endwhile;

		the_posts_navigation();

	else :

		get_template_part( 'template-parts/global/content', 'none' );

	endif;
	?>

	</main><!-- #main -->

<?php
get_footer();
