<?php
/**
 * Back compat functionality
 *
 * Prevents the theme from running on WordPress versions prior to 5.8,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 5.8.
 *
 * @since 3.0.0
 */

/**
 * Display upgrade notice on theme switch.
 *
 * @return void
 */
function avdeez_switch_theme() {
	add_action( 'admin_notices', 'avdeez_upgrade_notice' );
}
add_action( 'after_switch_theme', 'avdeez_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * the theme on WordPress versions prior to 5.8.
 *
 * @global string $wp_version WordPress version.
 * @return void
 */
function avdeez_upgrade_notice() {
	echo '<div class="error"><p>';
	printf(
		/* translators: %s: WordPress Version. */
		esc_html__( 'This theme requires WordPress 5.8 or newer. You are running version %s. Please upgrade. It is very crucial to keep your WordPress installation up to date for security reasons. If some of your plugins do not support the latest WordPress version then contact the author of the plugin.', 'avdeez' ),
		esc_html( $GLOBALS['wp_version'] )
	);
	echo '</p></div>';
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 5.8.
*
 * @global string $wp_version WordPress version.
 * @return void
 */
function avdeez_customize() {
	wp_die(
		sprintf(
			/* translators: %s: WordPress Version. */
			esc_html__( 'This theme requires WordPress 5.8 or newer. You are running version %s. Please upgrade. It is very crucial to keep your WordPress installation up to date for security reasons. If some of your plugins do not support the latest WordPress version then contact the author of the plugin.', 'avdeez' ),
			esc_html( $GLOBALS['wp_version'] )
		),
		'',
		array(
			'back_link' => true,
		)
	);
}
add_action( 'load-customize.php', 'avdeez_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 5.8.
 *
 * @global string $wp_version WordPress version.
 * @return void
 */
function avdeez_preview() {
	if ( isset( $_GET['preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_die(
			sprintf(
				/* translators: %s: WordPress Version. */
				esc_html__( 'This theme requires WordPress 5.8 or newer. You are running version %s. Please upgrade. It is very crucial to keep your WordPress installation up to date for security reasons. If some of your plugins do not support the latest WordPress version then contact the author of the plugin.', 'avdeez' ),
				esc_html( $GLOBALS['wp_version'] )
			)
		);
	}
}
add_action( 'template_redirect', 'avdeez_preview' );
