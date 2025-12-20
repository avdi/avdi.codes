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
	'template'           => 'grid-page',
);

?>

<div id="primary-header" <?php avdeez_primary_header_classes( $page_header_args ); ?>>
	<?php
	if ( $has_post_thumbnail ) :
		the_post_thumbnail(
			'avdeez-featured-image',
			array(
				'class'           => 'wp-block-cover__image-background',
				'data-object-fit' => 'cover',
			)
		);
	endif;
	?>

	<div class="wp-block-cover__inner-container alignwide th-text-current md:th-flex th-items-end">
		<?php printf( '<h1 class="entry-title th-w-full md:th-w-8/12 th-uppercase alignwide has-grand-font-size th-tracking-normal th-my-0">%s</h1>', get_the_title( $current_id ) ); ?>
		<div class="entry-summary th-content th-links-inherit md:th-w-4/12 th-w-full md:th-mt-0 th-mt-base">
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
				'<p class="edit-link th-font-headings has-small-font-size th-uppercase md:th-text-right">',
				'</p>'
			);

			if ( has_excerpt( $current_id ) ) :
				echo wpautop( get_the_excerpt( $current_id ) );
			endif;
			?>
		</div><!-- .entry-summary -->
	</div><!-- .wp-block-cover__inner-container -->
</div><!-- .entry-header -->

<?php
do_action( 'avdeez_after_page_header' );

if ( avdeez_has_content( $current_id ) ) :
	?>
	<div class="content-container has-primary-dark-background-color has-secondary-light-color th-heading-text-inherit alignfull th-my-0 th-py-medium th-px-base">
		<div class="entry-content">
			<?php echo apply_filters( 'the_content', get_the_content( '', '', $current_id ) ); ?>
		</div><!-- .entry-content -->
	</div><!-- .entry-content -->
	<?php
endif;
