<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Hermes
 */

?>

<section class="no-results not-found th-w-full th-bg-white">
	<header class="page-header th-text-white th-bg-primary-base th-py-base th-px-small">
		<h1 class="page-title has-huge-font-size has-accent-base-color th-uppercase th-my-0"><?php esc_html_e( 'Nothing Found', 'avdeez' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content alignwide th-mx-auto th-content th-px-base th-py-medium th-space-y-base">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'avdeez' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'avdeez' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'avdeez' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
