<?php
/**
 * The part displays page header in all views except singular.
 *
 * @package Hermes
 */

// Checks if the current view is "search".
$is_search = is_search();
// Checks if the current view is "archive".
$is_archive = is_archive();

if ( is_home() && ! is_front_page() ) :
	?>
	<header class="screen-reader-text th-w-full">
		<h1 class="page-title"><?php single_post_title(); ?></h1>
	</header>
	<?php
elseif ( $is_search || $is_archive ) :
	?>
	<header class="page-header has-secondary-light-color has-primary-dark-background-color th-space-y-small th-py-base th-px-small th-w-full">
	<?php
	if ( $is_search ) :
		?>
		<h1 class="page-title has-medium-font-size has-accent-base-color th-uppercase th-my-0">
			<?php printf( esc_html__( 'Search Results for: %s', 'hermes' ), '<span>' . get_search_query() . '</span>' ); ?>
		</h1>
		<?php
	elseif ( $is_archive ) :
		the_archive_title( '<h1 class="page-title has-medium-font-size has-accent-base-color th-uppercase th-my-0">', '</h1>' );
		the_archive_description( '<div class="taxonomy-description th-content th-max-w-prose">', '</div>' );
	endif;
	?>
	</header><!-- .page-header -->
	<?php
endif;

do_action( 'hermes_after_page_header' );
