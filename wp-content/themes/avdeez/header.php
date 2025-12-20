<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Hermes
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/global/site-toggle-sidebar' ); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'avdeez' ); ?></a>

	<?php get_template_part( 'template-parts/global/site-search-form' ); ?>

	<header id="masthead" class="site-header has-primary-dark-background-color has-secondary-light-color th-font-headings th-links-inherit th-flex lg:th-flex-nowrap th-flex-wrap th-items-center th-p-small">
		<div class="site-branding lg:th-text-left th-text-center lg:th-mr-sm lg:th-pr-sm lg:th-w-auto th-w-full lg:th-border-r th-border-primary-light">
			<?php
			if ( has_custom_logo() ) :
				the_custom_logo();
			endif;

			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title has-medium-font-size has-secondary-light-color th-my-0 th-uppercase th-tracking-wide th-leading-snug"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
				<?php

			else :

				?>
				<p class="site-title has-medium-font-size th-my-0 th-uppercase th-tracking-wide th-leading-snug"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;

			$description = get_bloginfo( 'description', 'display' );
			if ( $description || is_customize_preview() ) :
				?>
				<p class="site-description th-mb-0 th-text-sm th-hidden"><?php echo esc_html( $description ); ?></p>
				<?php
			endif;
			?>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation has-small-font-size lg:th-block th-hidden th-uppercase" aria-label="<?php esc_attr_e( 'Primary menu', 'avdeez' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => 'ul',
			) );
			?>
		</nav><!-- #site-navigation -->

		<div class="header-controls lg:th-mt-0 lg:th-pt-0 th-flex th-items-center th-mt-small th-pt-small lg:th-ml-auto lg:th-w-auto th-w-full">
			<?php if ( has_nav_menu( 'social' ) ) : ?>
			<nav class="header-social th-leading-none lg:th-border-r th-border-primary-light" aria-label="<?php esc_attr_e( 'Social links menu', 'avdeez' ); ?>">
				<?php get_template_part( 'menu-social' ); ?>
			</nav><!-- .header-social -->
			<?php endif; ?>

			<button id="menu-button" class="header-menu-button th-flex th-items-center clean-button th-uppercase th-text-xs lg:th-hidden" type="button" aria-expanded="false">
				<?php avdeez_icon_svg( 'bars', 18 ); ?>
				<span class="th-leading-none th-font-bold th-ml-2xs">
					<?php esc_html_e( 'Menu', 'avdeez' ); ?>
				</span>
			</button><!-- .header-menu-button -->

			<?php
			if ( function_exists( 'avdeez_woocommerce_cart_link' ) ) :
				avdeez_woocommerce_cart_link();
			endif;
			?>

			<button class="header-search-button clean-button th-leading-none" type="button" aria-expanded="false">
				<?php avdeez_icon_svg( 'search', 18 ); ?>
				<span class="screen-reader-text">
					<?php esc_html_e( 'Open a search form in a modal window', 'avdeez' ); ?>
				</span>
			</button><!-- .header-search-button  -->
		</div><!-- .header-controls -->

		<?php if ( get_theme_mod( 'avdeez_is_sticky_header', '' ) ) : ?>
		<div id="sticky-header" class="sticky-header has-primary-dark-background-color th-items-center lg:th-flex th-hidden th-w-full th-p-small" aria-hidden="true"></div><!-- .sticky-header -->
		<?php endif; ?>
	</header><!-- #masthead -->

	<div id="content" class="site-content wp-site-blocks">
