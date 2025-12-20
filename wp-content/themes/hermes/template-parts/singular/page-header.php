<?php
/**
 * The part displays page header in single views.
 *
 * @package Hermes
 */

// Get a current id of the page/post outside of the main loop.
$current_id = get_queried_object_id();
// Get a current post type.
$current_post_type = get_post_type( $current_id );
// Check if current post has a featured image.
$has_post_thumbnail = has_post_thumbnail( $current_id );
// Set arguments needed for the page header.
$page_header_args = array(
	'has_post_thumbnail' => $has_post_thumbnail,
);

?>

<header id="primary-header" <?php hermes_primary_header_classes( $page_header_args ); ?>>
	<?php
	if ( $has_post_thumbnail ) :
		the_post_thumbnail(
			'hermes-featured-image',
			array(
				'class'           => 'wp-block-cover__image-background',
				'data-object-fit' => 'cover',
			)
		);
	endif;
	?>

	<div class="wp-block-cover__inner-container alignwide th-text-current th-space-y-base">
		<?php
		if ( 'post' === $current_post_type ) :
		?>
		<div class="entry-meta has-tiny-font-size th-font-headings th-tracking-widest th-links-inherit">
			<?php hermes_posted_on( $current_id ); ?>
		</div><!-- .entry-meta -->
		<?php
		endif;

		printf( '<h1 class="entry-title alignwide has-grand-font-size th-tracking-normal th-my-0">%s</h1>', get_the_title( $current_id ) );
		?>

		<button id="skip-to-entry" class="clean-button th-flex skip-to-entry th-hidden" type="button">
			<?php hermes_icon_svg( 'chevron-down', 18 ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Skip to entry content', 'hermes' ); ?></span>
		</button>
	</div><!-- .container -->
</header><!-- .entry-header -->

<?php
do_action( 'hermes_after_page_header' );
