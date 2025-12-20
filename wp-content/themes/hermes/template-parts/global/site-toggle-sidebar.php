<?php
/**
 * The part displays a toggle sidebar which is shown in smaller screens.
 *
 * @package Hermes
 */

?>

<div id="toggle-sidebar" class="toggle-sidebar has-primary-dark-background-color" aria-hidden="true">
	<div class="inner-panel has-small-font-size">
		<button id="close-toggle-sidebar" class="toggle-sidebar-close-button has-tiny-font-size clean-button" type="button">
			<?php
			hermes_icon_svg( 'close', 16 );

			esc_html_e( 'Close', 'hermes' );
			?>
		</button><!-- .close-button -->

		<nav id="mobile-navigation" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile menu', 'hermes' ); ?>"></nav><!-- #mobile-navigation -->
	</div><!-- .inner-panel -->
</div><!-- #toggle-sidebar -->