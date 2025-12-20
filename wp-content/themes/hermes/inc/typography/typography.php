<?php
/**
 * Typography related functions.
 *
 * @package hermes
 *
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments
 */

/**
 * Return default fonts
 */
function hermes_get_default_fonts() {
	$fonts = array(
		'System Stack',
		'Arial, Helvetica, sans-serif',
		'Century Gothic, CenturyGothic, AppleGothic, sans-serif',
		'Comic Sans MS, cursive, sans-serif',
		'Helvetica Neue, Helvetica, sans-serif',
		'Impact, Charcoal, sans-serif',
		'Lucida Sans Unicode, Lucida Grande, sans-serif',
		'Segoe UI, Helvetica Neue, Helvetica, sans-serif',
		'Tahoma, Geneva, sans-serif',
		'Trebuchet MS, Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif',
		'Georgia, Times New Roman, Times, serif',
		'Palatino Linotype, Book Antiqua, Palatino, serif',
		'Times New Roman, Times, serif',
		'Lucida Console, Monaco, monospace',
		'Courier New, Courier, monospace',
	);

	return apply_filters( 'hermes_typography_default_fonts', $fonts );
}

/**
 * Return the theme fonts.
 */
function hermes_get_theme_fonts() {
	$the_theme = get_theme_support( 'themes-harbor-theme' );

	$font_family = array(
		'body'     => get_theme_mod( 'hermes_typography_body_font_family', $the_theme[0]['fonts']['body'] ),
		'headings' => get_theme_mod( 'hermes_typography_heading_font_family', $the_theme[0]['fonts']['headings'] ),
	);

	return $font_family;
}

/**
 * Register Google Fonts.
 */
function hermes_google_fonts() {
	$fonts_url     = '';
	$fonts         = array();
	$subsets       = 'latin,latin-ext,cyrillic,cyrillic-ext';
	$default_fonts = hermes_get_default_fonts();
	$theme_fonts   = hermes_get_theme_fonts();

	// Google font for body.
	if ( ! in_array( $theme_fonts['body'], $default_fonts, true ) ) {
		$fonts[] = $theme_fonts['body'] . ':300,300i,400,400i,700,700i';
	}

	// Google font for headings.
	if ( ! in_array( $theme_fonts['headings'], $default_fonts, true ) ) {
		$fonts[] = $theme_fonts['headings'] . ':300,300i,400,400i,700,700i';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg(
			array(
				'family'  => rawurlencode( implode( '|', $fonts ) ),
				'subset'  => rawurlencode( $subsets ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css'
		);
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Returns CSS for font families used on the site.
 *
 * @param bool $include_defaults Include default font styles when outputing CSS: True or False.
 * @return string
 */
function hermes_get_font_family_css( $include_defaults = false ) {
	$font_family_css      = '';
	$theme_fonts          = hermes_get_theme_fonts();
	$body_font_family     = $theme_fonts['body'];
	$headings_font_family = $theme_fonts['headings'];
	$the_theme            = get_theme_support( 'themes-harbor-theme' );
	$is_admin             = is_admin();

	if ( 'System Stack' === $body_font_family ) {
		$body_font_family = hermes_get_system_font_stack();
	}

	if ( 'System Stack' === $headings_font_family ) {
		$headings_font_family = hermes_get_system_font_stack();
	}

	if ( $the_theme[0]['fonts']['body'] !== $body_font_family || $include_defaults ) {
		if ( $is_admin ) {
			$font_family_css .= 'body .editor-styles-wrapper { --wp--preset--font-family--body-font: ' . $body_font_family . ' }';
		} else {
			$font_family_css .= 'body { --wp--preset--font-family--body-font: ' . $body_font_family . ' }';
		}
	}

	if ( $the_theme[0]['fonts']['headings'] !== $headings_font_family || $include_defaults ) {
		if ( $is_admin ) {
			$font_family_css .= 'body .editor-styles-wrapper { --wp--preset--font-family--headings-font: ' . $headings_font_family . ' }';
		} else {
			$font_family_css .= 'body { --wp--preset--font-family--headings-font: ' . $headings_font_family . ' }';
		}
	}

	return $font_family_css;
}

/**
 * Enqueue Google Fonts or default fonts.
 */
function hermes_enqueue_theme_fonts() {
	$google_fonts  = hermes_google_fonts();
	$theme_version = wp_get_theme()->get( 'Version' );

	if ( '' !== $google_fonts ) {
		wp_enqueue_style( 'hermes-google-fonts', $google_fonts, array(), $theme_version );
	}

	$font_family_css = hermes_get_font_family_css();

	if ( '' !== $font_family_css ) {
		wp_add_inline_style( 'hermes-style', $font_family_css );
	}
}
add_action( 'wp_enqueue_scripts', 'hermes_enqueue_theme_fonts' );

/**
 * Return System Font Stack.
 */
function hermes_get_system_font_stack() {
	return '-apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"';
}

/**
 * Load typography options.
 */
require get_template_directory() . '/inc/typography/customizer.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
