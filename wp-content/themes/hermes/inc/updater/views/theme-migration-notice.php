<?php
/**
 * Template for displaying migration notice in the Themes Info page.
 *
 * @package Themes Harbor
 */

$the_theme = get_theme_support( 'themes-harbor-theme' );

if ( ! isset( $the_theme[0]['migration'] ) ) {
	return;
}

$templates = $the_theme[0]['migration']['templates'];

if ( empty( $templates ) ) {
	return;
}

$theme_info = wp_get_theme( $this->theme_slug );
?>

	<div class="th-card" style="box-shadow: 0 0 0 0.25rem #ffba00;">
		<header class="th-card__header th-card--inner alternate">
			<h2 class="th-card__title" style="color: #895f00;">
				Migrating to a new era of WordPress
			</h2>
		</header><!-- .th-card__inner -->

		<div class="th-card--inner">
			<p class="th-content-text">
				Web technologies do not stay still, including a WordPress core. Unfortunately, businesses who fail to stay relevant with current technologies have the greatest potential to face extinction.
			</p>

			<p class="th-content-text">
				In order to provide you with the best WordPress experience in the <em><?php echo esc_html( $theme_info['Name'] ); ?></em> theme, and to keep your site professionally made, we need to follow these changes in a WordPress core.
			</p>

			<div class="grid-2-col">
			<?php

			foreach ( $templates as $template ) :
				$pages_with_template = $this->migration__get_pages_by_template( $template['slug'] );

				if ( empty( $pages_with_template ) ) :
					continue;
				endif;
				?>

				<div class="grid-col">
					<p class="th-content-text">
						Below you can find page(s) -- with active the <em><?php echo esc_html( $template['name'] ); ?></em> template -- that we recommend to update:
					</p>

					<ul>
					<?php
					foreach ( $pages_with_template as $page_with_template ) :
						?>
						<li>
							<a class="item-title" href="<?php echo esc_url( $page_with_template['url'] ); ?>"><?php echo esc_html( $page_with_template['title'] ); ?></a> &mdash; <span class="item-actions secondary-color">(<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $page_with_template['id'] ) ) . '&action=edit' ); ?>">Edit</a>)</span>
						</li>
						<?php
					endforeach;
					?>
					</ul>

					<p class="docs-action">
						<a class="button button-secondary" href="<?php echo esc_url( $template['help_url'] ); ?>" target="_blank">Read update guide</a>
					</p>
				</div><!-- .grid-col -->

				<?php
			endforeach;
			?>
			</div><!-- .grid-2-col -->
		</div><!-- .th-card--inner -->
	</div><!-- th-card -->

<?php
