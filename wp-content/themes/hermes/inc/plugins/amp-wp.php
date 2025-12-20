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
function hermes_amp_setup() {
    // Add theme support for AMP.
    add_theme_support( 'amp' );
}
add_action( 'after_setup_theme', 'hermes_amp_setup' );

/**
 * Enqueue/dequeue scripts and styles needed for AMP support.
 */
function hermes_amp_scripts() {
    if ( ! hermes_is_amp() ) {
        return;
    }

    if ( wp_script_is( 'hermes-singular-script' ) ) {
        wp_dequeue_script( 'hermes-singular-script' );
    }

    if ( wp_script_is( 'hermes-script' ) ) {
        wp_dequeue_script( 'hermes-script' );
    }
}
add_action( 'wp_enqueue_scripts', 'hermes_amp_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function hermes_amp_body_classes( $classes ) {
    if ( hermes_is_amp() ) {
        $classes[] = 'is-amp-page';
	}

    return $classes;
}
add_filter( 'body_class', 'hermes_amp_body_classes' );

/**
 * Registers the theme-specific AMP sanitizer by filtering the available AMP sanitizers.
 *
 * @since 1.0.0
 *
 * @param array $sanitizers AMP content sanitizers.
 * @return array Filtered sanitizers.
*/
function hermes_amp_content_sanitizer( $sanitizer_classes ) {
    if ( ! class_exists( 'Hermes_AMP_Sanitizer' ) ) {
        require_once( get_theme_file_path( '/inc/classes/class-hermes-amp-sanitizer.php' ) );
    }

	$sanitizer_classes[ 'Hermes_AMP_Sanitizer' ] = array();

	return $sanitizer_classes;
}
add_filter( 'amp_content_sanitizers', 'hermes_amp_content_sanitizer', 10, 1 );

/**
 * Checks if AMP page is rendered.
 */
function hermes_is_amp() {
    return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}
