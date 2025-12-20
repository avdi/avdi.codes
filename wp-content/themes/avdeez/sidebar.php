<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Hermes
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area sidebar-section has-small-font-size th-space-y-base">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
