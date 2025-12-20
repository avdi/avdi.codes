<?php
/**
 * Template part for displaying single posts.
 *
 * @package Hermes
 */

$post_classes = array( 'th-space-y-base' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>
	<div class="entry-content th-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer th-space-y-base">
		<?php
		hermes_entry_footer();

		hermes_author_bio();
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
