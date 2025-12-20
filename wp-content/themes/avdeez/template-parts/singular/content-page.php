<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Hermes
 */

$post_classes = array( 'th-space-y-base' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>
	<div class="entry-content th-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<?php
	edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Edit <span class="screen-reader-text">%s</span>', 'avdeez' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		),
		'<footer class="entry-footer"><div class="edit-link has-small-font-size th-font-headings th-uppercase th-font-bold">',
		'</div></footer>'
	); ?>
</article><!-- #post-## -->
