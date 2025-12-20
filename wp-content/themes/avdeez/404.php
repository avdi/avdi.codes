<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Hermes
 */

get_header();
?>

	<main id="main" class="site-main alignfull th-my-0">
		<section class="error-404 not-found">
			<header class="page-header has-primary-dark-background-color th-py-base th-px-small">
				<h1 class="page-title has-huge-font-size has-accent-base-color th-uppercase th-my-0">
					<?php esc_html_e( 'Oops! That page can\'t be found.', 'avdeez' ); ?>
				</h1>
			</header><!-- .page-header -->

			<div class="page-content alignwide th-flex th-gap-medium sm:th-flex-row th-flex-col th-items-center sm:th-px-base th-px-base th-mx-auto th-my-medium">
				<p class="title-404 th-font-bold th-leading-none th-font-headings th-inline-block" aria-hidden="true">
					<?php esc_html_e( '404', 'avdeez' ); ?>
				</p>

				<div class="th-w-full th-space-y-base">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'avdeez' ); ?></p>

					<?php get_search_form(); ?>
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->
	</main><!-- #main -->

<?php
get_footer();
