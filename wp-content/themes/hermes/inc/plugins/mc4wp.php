<?php
/**
 * MC4WP Compatibility File
 *
 * @link https://wordpress.org/plugins/mailchimp-for-wp/
 *
 * @package hermes
 *
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments
 */

/**
 * Load assets needed for the jetpack modules.
 */
function hermes_mc4wp_enqueue_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Register singular styles.
	wp_register_style(
		'hermes-templates-mc4wp-global',
		get_theme_file_uri( "/assets/styles/templates/mc4wp/global{$suffix}.css" ),
		array(),
		'1.0.0'
	);

	// Load mc4wp styles.
	wp_enqueue_style( 'hermes-templates-mc4wp-global' );
}
add_action( 'wp_enqueue_scripts', 'hermes_mc4wp_enqueue_scripts' );
