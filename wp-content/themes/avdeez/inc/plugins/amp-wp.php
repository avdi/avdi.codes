<?php
/**
 * AMP for WP Compatibility File.
 * See: https://amp-wp.org/
 *
 * @package Hermes
 */

/**
 * Set up AMP functionality needed for WP.
 */
function avdeez_amp_setup() {
    // Add theme support for AMP.
    add_theme_support( 'amp' );
}
add_action( 'after_setup_theme', 'avdeez_amp_setup' );

/**
 * Enqueue/dequeue scripts and styles needed for AMP support.
 */
function avdeez_amp_scripts() {
    if ( ! avdeez_is_amp() ) {
        return;
    }

    if ( wp_script_is( 'avdeez-singular-script' ) ) {
        wp_dequeue_script( 'avdeez-singular-script' );
    }

    if ( wp_script_is( 'avdeez-script' ) ) {
        wp_dequeue_script( 'avdeez-script' );
    }
}
add_action( 'wp_enqueue_scripts', 'avdeez_amp_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function avdeez_amp_body_classes( $classes ) {
    if ( avdeez_is_amp() ) {
        $classes[] = 'is-amp-page';
	}

    return $classes;
}
add_filter( 'body_class', 'avdeez_amp_body_classes' );

/**
 * Registers the theme-specific AMP sanitizer by filtering the available AMP sanitizers.
 *
 * @since 1.0.0
 *
 * @param array $sanitizers AMP content sanitizers.
 * @return array Filtered sanitizers.
*/
function avdeez_amp_content_sanitizer( $sanitizer_classes ) {
    if ( ! class_exists( 'Hermes_AMP_Sanitizer' ) ) {
        require_once( get_theme_file_path( '/inc/classes/class-avdeez-amp-sanitizer.php' ) );
    }

	$sanitizer_classes[ 'Hermes_AMP_Sanitizer' ] = array();

	return $sanitizer_classes;
}
add_filter( 'amp_content_sanitizers', 'avdeez_amp_content_sanitizer', 10, 1 );

/**
 * Checks if AMP page is rendered.
 */
function avdeez_is_amp() {
    return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}
