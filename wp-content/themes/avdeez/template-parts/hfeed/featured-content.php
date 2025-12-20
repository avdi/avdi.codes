<?php
/**
 * The template for displaying Jetpack Featured Content section.
 *
 * @package Hermes
 */

$featured_posts = avdeez_get_featured_posts();

if ( empty( $featured_posts ) ) {
	return;
}

$primary_posts              = avdeez_jetpack_featured_content_primary( $featured_posts );
$secondary_posts            = avdeez_jetpack_featured_content_secondary( $featured_posts );
$has_primary_featured_image = false;
?>

<div id="featured-content" class="featured-content alignfull wp-block-cover th-flex th-flex-wrap th-h-full th-my-0">
	<?php
	if ( $primary_posts ) :

		foreach ( (array) $primary_posts as $order => $post ) :
			setup_postdata( $post );

			if ( has_post_thumbnail() ) :
				$has_primary_featured_image = true;

				the_post_thumbnail(
					'avdeez-featured-image',
					array(
						'class'           => 'wp-block-cover__image-background',
						'data-object-fit' => 'cover',
					)
				);
				?>
				<a class="more-link th-absolute th-inset-0" href="<?php echo  esc_url( get_permalink() ); ?>" aria-hidden="true"></a>
				<?php
			endif;
			?>
			<div <?php avdeez_jetpack_featured_content_column_class( 'primary', $has_primary_featured_image  ); ?>>
				<?php
				avdeez_jetpack_featured_content_tag_banner();

				get_template_part( 'template-parts/hfeed/content', 'featured' );
				?>
			</div><!-- .primary-content -->
			<?php
		endforeach;
		wp_reset_postdata();

	endif;

	if ( $secondary_posts ) :
	?>
	<div <?php avdeez_jetpack_featured_content_column_class( 'secondary', $has_primary_featured_image  ); ?>>
		<?php
		foreach ( (array) $secondary_posts as $order => $post ) :
			setup_postdata( $post );
			get_template_part( 'template-parts/hfeed/content', 'featured' );
		endforeach;
		wp_reset_postdata();
		?>
	</div><!-- .secondary-content -->
	<?php
	endif;

	if ( $has_primary_featured_image ) :
		echo '<div class="hentry-overlay th-absolute th-inset-0" aria-hidden="true"></div>';
	endif;
	?>
</div><!-- .featured-content -->
