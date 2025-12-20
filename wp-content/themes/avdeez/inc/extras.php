<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Hermes
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function avdeez_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'avdeez_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function avdeez_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of grid layout for archives and grid templates.
	if ( is_page_template( 'templates/grid-page.php' ) || ! is_singular() ) {
		$classes[] = avdeez_get_hfeed_layout_class();
	}

	// Adds a class of a position of the toggle sidebar.
	if ( 'right' === get_theme_mod( 'avdeez_toggle_sidebar_position', 'left' ) ) {
		$classes[] = 'toggle-sidebar--right';
	} else {
		$classes[] = 'toggle-sidebar--left';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	} else {
		// Add a class if a sidebar is inactive.
		if ( ! avdeez_is_active_sidebar() ) {
			$classes[] = 'inactive-sidebar';
		} else {
			$classes[] = 'has-sidebar';

			// Add a class if a sidebar is located on the left side of the site.
			if ( avdeez_is_left_sidebar() ) {
				$classes[] = 'left-sidebar';
			}
		}
	}

	return $classes;
}
add_filter( 'body_class', 'avdeez_body_classes' );

/**
 * Adds custom classes to the array of post classes.
 *
 * @param array $classes Classes for the entry element.
 * @return array
 */
function avdeez_post_classes( $classes ) {
	if ( has_post_thumbnail() && post_password_required() ) {
		$classes[] = 'has-post-thumbnail';
	}

	return $classes;
}
add_filter( 'post_class', 'avdeez_post_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function avdeez_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'avdeez_pingback_header' );

/**
 * Check if the sidebar is active on the current page.
 */
function avdeez_is_active_sidebar() {
	$is_active_sidebar = is_active_sidebar( 'sidebar-1' );

	/* Check if the current page is set to the Grid Page template or the Full Width template. */
	if ( is_page_template( 'templates/grid-page.php' ) ||
		 is_page_template( 'templates/full-width-page.php' ) ) {
		// By default, these templates do not support a sidebar.
		$is_active_sidebar = false;
	}

	return apply_filters( 'avdeez_is_active_sidebar', $is_active_sidebar );
}

/**
 * Check if user wants to move the sidebar to the left.
 */
function avdeez_is_left_sidebar() {
	if ( 'left' === get_theme_mod( 'avdeez_sidebar_position', 'right' ) ) {
		return true;
	}

	return false;
}

/**
 * Get a grid layout class.
 */
function avdeez_get_hfeed_layout_class() {
	return esc_attr( get_theme_mod( 'avdeez_hfeed_grid_layout', 'grid-layout-default' ) );
}

/**
 * Display page-links for paginated posts before jetpack share buttons and related posts.
 */
function avdeez_custom_link_pages( $content ) {
	if ( is_singular() ) {
		$content .= wp_link_pages( array(
			'before'      => '<div class="page-links has-tiny-font-size th-font-headings th-uppercase th-font-bold"><span class="page-links-title th-tracking-widest">' . esc_html__( 'Pages:', 'avdeez' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'avdeez' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text">, </span>',
			'echo'        => 0,
		) );
	}

	return $content;
}
add_filter( 'the_content', 'avdeez_custom_link_pages', 1 );

/**
 * Prints the feautred image URL.
 *
 * @since avdeez 1.0
 */
function avdeez_featured_image_url() {
	$thumbnail_url = avdeez_get_featured_image_url();

	if ( $thumbnail_url ) {
		echo $thumbnail_url; // WPCS: XSS OK.
	}
}

/**
 * Returns the feautred image URL.
 */
function avdeez_get_featured_image_url() {
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'avdeez-featured-image' );

	if ( ! is_array( $thumbnail ) ) {
		return false;
	}

	return esc_url( $thumbnail[0] );
}

/**
 * Add a span around the title prefix so that the prefix can be hidden with CSS if needed.
 *
 * @param string $title Archive title.
 * @return string Archive title with inserted span around prefix.
 */
function avdeez_filter_archive_title( $title ) {
	// Split the title into parts so we can wrap them with span tag.
	$title_parts = explode( ': ', $title, 2 );

	// Glue title's parts back together.
	if ( ! empty( $title_parts[1] ) ) {
		// Add a span around the title.
		$title = '<span>' . $title_parts[0] . ': </span>' . $title_parts[1];
		// Sanitize our title.
		$title = wp_kses( $title, array( 'span' => array(), ) );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'avdeez_filter_archive_title' );

/**
 * Add an icon to the menu items that has a sub-menu.
 *
 * @param stdClass $args An array of arguments.
 * @param object   $item Menu item.
 * @param int      $depth Depth of the current menu item.
 *
 * @return stdClass $args An object of wp_nav_menu() arguments.
 */
function avdeez_nav_menu_item_args( $args, $item, $depth ) {
	if ( 'primary' === $args->theme_location ) {
		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
			$args->after = '<span class="arrow-icon" aria-hidden="true">' . avdeez_get_icon_svg( 'chevron-down', 21 ) . '</span>';
		} else {
			$args->after = '';
		}
	}

	return $args;
}
add_filter( 'nav_menu_item_args', 'avdeez_nav_menu_item_args', 10, 3 );

/**
 * Adds inline styles from customizer to head of TinyMCE iframe.
 */
function avdeez_add_inline_classic_editor_styles( $mce_init ) {
	$include_default_fonts = true;
	$styles                = avdeez_get_font_family_css( $include_default_fonts );
	$styles               .= avdeez_get_colors_css();

	if ( ! isset( $mce_init['content_style'] ) ) {
		$mce_init['content_style'] = $styles . ' ';
	} else {
		$mce_init['content_style'] .= ' ' . $styles . ' ';
	}

	return $mce_init;
}
add_filter( 'tiny_mce_before_init', 'avdeez_add_inline_classic_editor_styles' );

/**
 * Checks if the current post has a content.
 *
 * @return bool
 */
function avdeez_has_content( $the_ID = null ) {
	if ( ! isset( $the_ID ) ) {
		$the_ID = get_the_ID();
	}

	return '' !== get_post_field( 'post_content', $the_ID ) ? true : false;
}
