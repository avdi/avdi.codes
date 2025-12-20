<?php
/**
 * Template part for displaying posts.
 *
 * @package Hermes
 */

$post_classes      = array( 'wp-block-cover has-custom-content-position is-position-bottom-left', 'th-inner-border th-p-small', 'md:th-w-6/12', 'th-w-full' );
$current_post_type = get_post_type()
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>
	<?php
	if ( has_post_thumbnail() ) :
		the_post_thumbnail(
			'avdeez-featured-image',
			array(
				'class'           => 'wp-block-cover__image-background',
				'data-object-fit' => 'cover',
			)
		);
	endif;
	?>

	<div class="wp-block-cover__inner-container entry-header th-w-full th-max-w-prose th-stack--xs th-uppercase th-links-inherit th-text-current">
		<?php
		if ( 'post' === $current_post_type ) :
			printf( '<p class="entry-cats has-tiny-font-size th-font-headings th-mb-0">%s</p>', get_the_category_list( ' ' ) ); // WPCS: XSS OK.
		endif;

		the_title( sprintf( '<h2 class="entry-title th-text-md th-my-0"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' );
		?>

		<div class="entry-meta th-flex th-flex-wrap th-font-headings has-tiny-font-size th-opacity-75">
			<?php
			if ( 'post' === $current_post_type ) :
				avdeez_posted_on();
			endif;

			avdeez_entry_comments();
			?>
		</div><!-- .entry-meta -->
	</div><!-- .wp-block-cover__inner-container -->

	<a class="more-link th-w-full th-h-full" href="<?php echo  esc_url( get_permalink() ); ?>">
		<span class="screen-reader-text">
		<?php
		printf(
			/* translators: %s: Name of current post. */
			esc_html__( 'Continue reading %s', 'avdeez' ),
			'"' . get_the_title() . '"'
		);
		?>
		</span>
	</a>
</article><!-- #post-## -->
