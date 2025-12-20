<?php
/**
 * The template for displaying all singular pages.
 *
 * @package Hermes
 */

get_header();

get_template_part( 'template-parts/singular/page-header' );
?>

<div class="main-aside alignwide th-grid th-gap-medium th-pb-base">
	<main id="main" class="site-main th-w-full th-space-y-base th-divide-y th-divide-solid th-divide-secondary-base">

	<?php
	while ( have_posts() ) : the_post();

		// Display page content.
		get_template_part( 'template-parts/singular/content', get_post_type() );

		// Display post navigation.
		if ( is_single() ) :
			the_post_navigation( array(
				'next_text' => '<span class="meta-title has-tiny-font-size has-secondary-dark-color th-font-headings th-tracking-widest th-uppercase th-block th-mb-small">' . esc_html__( 'Next Post', 'avdeez' ) . '</span><span class="post-title th-font-bold">%title</span>',
				'prev_text' => '<span class="meta-title has-tiny-font-size has-secondary-dark-color th-font-headings th-tracking-widest th-uppercase th-block th-mb-small">' . esc_html__( 'Previous Post', 'avdeez' ) . '</span><span class="post-title th-font-bold">%title</span>',
			) );
		endif;

		// Display page comments.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile;
	?>

	</main><!-- #main -->

	<?php get_sidebar(); ?>
</div><!-- .container -->

<?php
get_footer();
