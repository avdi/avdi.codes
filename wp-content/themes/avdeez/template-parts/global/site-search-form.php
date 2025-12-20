<?php
/**
 * The part displays a search form designed to be shown in the site header.
 *
 * @package Hermes
 */

?>

<div id="search-container" class="header-search-container th-font-headings sm:th-items-center sm:th-justify-center th-flex-col th-px-lg sm:th-py-lg th-pt-3xl th-w-full th-h-full" role="dialog" aria-hidden="true">
	<form method="get" class="header-searchform has-secondary-base-color th-flex th-w-full th-max-w-prose" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
		<label for="header-search-field" class="th-w-full">
			<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'avdeez' ); ?></span>
			<input type="text" id="header-search-field" class="field has-large-font-size th-w-full" placeholder="<?php esc_attr_e( 'Search &#x2026;', 'avdeez' ); ?>" name="s" value="" />
		</label>

		<button type="submit" class="submit clean-button">
			<?php avdeez_icon_svg( 'search', 24 ); ?>
			<span class="screen-reader-text">
				<?php esc_html_e( 'Search', 'avdeez' ); ?>
			</span>
		</button>

		<button id="close-search-container" class="header-search-close-button clean-button th-flex th-items-center th-uppercase th-tracking-wider" type="button">
			<?php avdeez_icon_svg( 'close' ); ?>
			<?php esc_html_e( 'Close', 'avdeez' ); ?>
		</button><!-- .header-search-close-button -->
	</form><!-- .header-searchform -->
</div><!-- #search-container -->