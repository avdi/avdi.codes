<?php
/**
 * Yoast SEO Compatibility File
 * See: https://yoast.com/
 *
 * @package Hermes
 */

 /**
 * Output Yoast breadcrumb navigation if availible.
 */
function avdeez_yoast_breadcrumb() {
	if ( ! function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}

    // Do not display breadcrumb on the front page.
    if ( is_front_page() ) {
        return;
    }

    $classes = array(
        'entry-breadcrumbs yoast',
        'has-tiny-font-size th-uppercase',
        'has-primary-light-color',
        'th-font-headings',
    );

    if ( is_singular() ) {
        if ( ! is_page_template( 'templates/grid-page.php' ) ) {
            $classes[] = 'th-links-inherit th-mt-lg th-mb-0';

            if ( avdeez_is_active_sidebar() || is_page_template( 'templates/full-width-page.php' ) ) {
                $classes[] = 'alignwide';
            }
        } else {
            $classes[] = 'alignfull has-primary-dark-background-color has-secondary-light-color th-border-b th-border-primary-light th-my-0 th-p-base th-w-full';
        }
    } else {
        $classes[] = 'has-secondary-light-color th-py-base sm:th-px-base th-px-lg th-w-full';
    }

    $classes = apply_filters( 'avdeez_yoast_breadcrumb_classes', $classes );

    yoast_breadcrumb( '<div class="' . join( ' ', $classes ) . '">', '</div>' );
}
add_action( 'avdeez_after_page_header', 'avdeez_yoast_breadcrumb' );
