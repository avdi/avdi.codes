<?php
/**
 * The default template for displaying a social menu content.
 *
 * @package Hermes
 */

wp_nav_menu( array(
	'theme_location' => 'social',
	'container'      => 'ul',
	'menu_class'     => 'social-list menu lg:th-flex lg:th-flex-wrap th-gap-md th-items-center th-justify-end th-hidden th-list-none th-m-0',
	'depth'          => 1,
	'link_before'    => '<span class="screen-reader-text social-meta">',
	'link_after'     => '</span>',
) );
