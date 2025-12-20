<?php
/**
 * Template for displaying search forms.
 *
 * @package Hermes
 */

// Create an unique ID.
$unique_id = esc_attr( uniqid( 'search-form-' ) );
?>

<form role="search" method="get" class="search-form th-flex th-mx-auto th-max-w-base" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo $unique_id; ?>" class="th-block th-w-full">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'hermes' ) ?></span>
		<input type="search" id="<?php echo $unique_id; ?>" class="search-field th-block th-w-full"
		placeholder="<?php echo esc_attr_x( 'Search this website&hellip;', 'placeholder', 'hermes' ) ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>" name="s"
		title="<?php echo esc_attr_x( 'Search for:', 'label', 'hermes' ) ?>" />
	</label>

	<button type="submit" class="search-submit th-ml-xs">
		<?php esc_html_e( 'Search', 'hermes' ); ?>
	</button>
</form><!-- .search-form -->
