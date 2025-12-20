<?php
/**
 * Template part for displaying featured posts.
 *
 * @package Hermes
 */

?>

<article <?php post_class(); ?>>
	<header class="entry-header th-uppercase th-space-y-small">
		<?php
		the_title( sprintf( '<h2 class="entry-title th-my-0"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' );

		if ( 'post' === get_post_type() ) :
		?>
		<div class="entry-meta has-tiny-font-size th-font-headings th-opacity-75">
			<?php
			hermes_posted_on();

			hermes_entry_comments();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
