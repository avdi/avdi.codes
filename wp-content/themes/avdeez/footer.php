<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Hermes
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer wp-site-blocks th-font-headings has-primary-dark-background-color has-secondary-base-color">
		<div class="alignwide th-my-0">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="footer-widgets th-grid th-gap-base lg:th-grid-cols-4 th-my-medium">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div><!-- .footer-widgets -->
			<?php endif; ?>

			<div class="site-footer-bottom has-primary-base-background-color has-tiny-font-size th-links-inherit th-flex sm:th-flex-no-wrap th-flex-wrap th-py-small th-px-base th-uppercase">
				<div class="site-info sm:th-text-left th-text-center">
					<?php avdeez_footer_output(); ?>
				</div><!-- .site-info -->

				<div class="gotop-link sm:th-ml-auto sm:th-mr-0 sm:th-mt-0 th-mt-base th-mx-auto">
					<button id="gotop" class="gotop clean-button" type="button">
						<?php esc_html_e( 'Back to top', 'avdeez' ); ?>
					</button>
				</div><!-- .pull-right -->
			</div><!-- .site-footer-bottom -->
		</div><!-- .container -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
