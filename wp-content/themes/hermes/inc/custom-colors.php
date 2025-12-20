<?php
/**
 * Custom color functionality. This functionality is needed for Classic themes.
 *
 * @package hermes
 *
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments
 */

/**
 *	Register theme color palette.
 */
function hermes_register_color_palette() {
	$the_theme     = get_theme_support( 'themes-harbor-theme' );
	$custom_colors = get_theme_mod( 'hermes_colors' );

	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => esc_html__( 'Primary (dark)', 'hermes' ),
			'slug'  => 'primary-dark',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'primary', 'dark' ) ),
		),
		array(
			'name'  => esc_html__( 'Primary (base)', 'hermes' ),
			'slug'  => 'primary-base',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'primary', 'base' ) ),
		),
		array(
			'name'  => esc_html__( 'Primary (light)', 'hermes' ),
			'slug'  => 'primary-light',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'primary', 'light' ) ),
		),
		array(
			'name'  => esc_html__( 'Secondary (dark)', 'hermes' ),
			'slug'  => 'secondary-dark',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'secondary', 'dark' ) ),
		),
		array(
			'name'  => esc_html__( 'Secondary (base)', 'hermes' ),
			'slug'  => 'secondary-base',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'secondary', 'base' ) ),
		),
		array(
			'name'  => esc_html__( 'Secondary (light)', 'hermes' ),
			'slug'  => 'secondary-light',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'secondary', 'light' ) ),
		),
		array(
			'name'  => esc_html__( 'Accent (dark)', 'hermes' ),
			'slug'  => 'accent-dark',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'accent', 'dark' ) ),
		),
		array(
			'name'  => esc_html__( 'Accent (base)', 'hermes' ),
			'slug'  => 'accent-base',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'accent', 'base' ) ),
		),
		array(
			'name'  => esc_html__( 'Accent (light)', 'hermes' ),
			'slug'  => 'accent-light',
			'color'	=> esc_attr( hermes_get_theme_color( $the_theme[0]['colors'], $custom_colors, 'accent', 'light' ) ),
		),
	) );
}
add_action( 'after_setup_theme', 'hermes_register_color_palette' );

/**
 * Return a theme color.
 *
 * @param array $default_colors Default theme colors.
 * @param array $custom_colors Custom theme colors.
 * @param string $type Type of color (primary, secondary, accent).
 * @param string $level Level of color (dark, base, light).
 * @return string
 */
function hermes_get_theme_color( $default_colors, $custom_colors, $type, $level ) {
	$default_slug = $type . '-' . $level;
	$custom_slug  = $type . '_base';

	// Check if the user has set a custom primary color via the Customizer.
	$is_custom_color_set = isset( $custom_colors[$custom_slug] ) && $default_colors[$default_slug] !== $custom_colors[$custom_slug];

	if ( $is_custom_color_set ) {
		return hermes_get_theme_custom_color( $custom_colors, $type, $level );
	}

	return hermes_get_theme_default_color( $default_colors, $type, $level );
}

/**
 * Return a default color.
 *
 * @param array $colors Default theme colors.
 * @param string $type Type of color (primary, secondary, accent).
 * @param string $level Level of color (dark, base, light).
 * @return string
 */
function hermes_get_theme_default_color( $colors, $type, $level ) {
	$slug = $type . '-' . $level;

	return $colors[$slug];
}

/**
 * Return a custom color.
 *
 * @param array $colors Custom theme colors.
 * @param string $type Type of color (primary, secondary, accent).
 * @param string $level Level of color (dark, base, light).
 * @return string
 */
function hermes_get_theme_custom_color( $colors, $type, $level ) {
	$slug = $type . '_base';

	// Set luminance percentage based on type.
	switch ( $type ) {
		case "secondary":
			$percentage = 'light' === $level ? 0.78 : -0.55;
			break;
		case "accent":
			$percentage = 'light' === $level ? 0.95 : -0.55;
			break;
		default:
			$percentage = 'light' === $level ? 0.1 : -0.55;
	}

	// Set custom color.
	switch ( $level ) {
		case "base":
			return $colors[$slug];
		default:
			return hermes_color_luminance( $colors[$slug], $percentage );
	}
}

/**
 * Add custom color options.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function hermes_register_color_options( $wp_customize ) {
    $the_theme = get_theme_support( 'themes-harbor-theme' );

	/* Custom color options: Accent Color */
	$wp_customize->add_setting( 'hermes_colors[accent_base]', array(
		'default'           => $the_theme[0]['colors']['accent-base'],
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hermes_colors_accent_base', array(
		'label'	   => esc_html__( 'Accent Color', 'hermes' ),
		'section'  => 'colors',
		'settings' => 'hermes_colors[accent_base]',
		'priority' => 100,
	) ) );

	/* Custom color options: Primary Color */
	$wp_customize->add_setting( 'hermes_colors[primary_base]', array(
		'default'           => $the_theme[0]['colors']['primary-base'],
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hermes_colors_primary_base', array(
		'label'	   => esc_html__( 'Primary Color', 'hermes' ),
		'section'  => 'colors',
		'settings' => 'hermes_colors[primary_base]',
		'priority' => 101,
	) ) );

	/* Custom color options: Secondary Color */
	$wp_customize->add_setting( 'hermes_colors[secondary_base]', array(
		'default'           => $the_theme[0]['colors']['secondary-base'],
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hermes_colors_secondary_base', array(
		'label'	   => esc_html__( 'Secondary Color', 'hermes' ),
		'section'  => 'colors',
		'settings' => 'hermes_colors[secondary_base]',
		'priority' => 102,
	) ) );
}
add_action( 'customize_register', 'hermes_register_color_options' );

/**
 * Returns custom CSS for the theme colors.
 */
function hermes_get_colors_css() {
    $colors_css    = '';
    $custom_colors = get_theme_mod( 'hermes_colors' );
    $the_theme     = get_theme_support( 'themes-harbor-theme' );

    // Accent color.
    if ( isset( $custom_colors['accent_base'] ) && $the_theme[0]['colors']['accent-base'] !== $custom_colors['accent_base'] ) {
        $colors_css .= '--wp--preset--color--accent-base: ' . esc_attr( $custom_colors['accent_base'] ) . ';';
        $colors_css .= '--wp--preset--color--accent-dark: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'accent', 'dark' ) ) . ';';
        $colors_css .= '--wp--preset--color--accent-light: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'accent', 'light' ) ) . ';';
    }

    // Primary color.
    if ( isset( $custom_colors['primary_base'] ) && $the_theme[0]['colors']['primary-base'] !== $custom_colors['primary_base'] ) {
        $colors_css .= '--wp--preset--color--primary-base: ' . esc_attr( $custom_colors['primary_base'] ) . ';';
        $colors_css .= '--wp--preset--color--primary-dark: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'primary', 'dark' ) ) . ';';
        $colors_css .= '--wp--preset--color--primary-light: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'primary', 'light' ) ) . ';';
    }

    // Secondary color.
    if ( isset( $custom_colors['secondary_base'] ) && $the_theme[0]['colors']['secondary-base'] !== $custom_colors['secondary_base'] ) {
        $colors_css .= '--wp--preset--color--secondary-base: ' . esc_attr( $custom_colors['secondary_base'] ) . ';';
        $colors_css .= '--wp--preset--color--secondary-dark: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'secondary', 'dark' ) ) . ';';
        $colors_css .= '--wp--preset--color--secondary-light: ' . esc_attr( hermes_get_theme_custom_color( $custom_colors, 'secondary', 'light' ) ) . ';';
    }

    if ( '' !== $colors_css ) {
		if ( is_admin() ) {
			$colors_css = 'body .editor-styles-wrapper {' . $colors_css . '}';
		} else {
			$colors_css = 'body {' . $colors_css . '}';
		}
    }

    return $colors_css;
}

/**
 * Use custom colors if needed.
 */
function hermes_use_custom_colors() {
    $new_colors = hermes_get_colors_css();

    if ( ! $new_colors ) {
        return;
    }

    wp_add_inline_style( 'hermes-style', $new_colors );
}
add_action( 'wp_enqueue_scripts', 'hermes_use_custom_colors', 99 );

/**
 * Get readable color depending on the color of the background.
 *
 * @param string $hexcolor Hexcolor code.
 * @return string
 */
function hermes_get_readable_color( $hexcolor ) {

	$r            = hexdec( substr( $hexcolor, 0, 2 ) );
	$g            = hexdec( substr( $hexcolor, 2, 2 ) );
	$b            = hexdec( substr( $hexcolor, 4, 2 ) );
	$new_hexcolor = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

	return ( $new_hexcolor >= 128 ) ? '#000000' : '#ffffff';

}

/**
 * Lightens/darkens a given color (hex format), returning the altered color in hex format.
 *
 * @param string $hexcolor Hexcolor code.
 * @param int    $percent Percentage value of the action.
 * @return string
 */
function hermes_color_luminance( $hexcolor, $percent ) {
	// Validate hex string.
	$hexcolor = preg_replace( '/[^0-9a-f]/i', '', $hexcolor );

	if ( strlen( $hexcolor ) < 6 ) {
		$hexcolor = $hexcolor[0] + $hexcolor[0] + $hexcolor[1] + $hexcolor[1] + $hexcolor[2] + $hexcolor[2];
	}

	$hexcolor = array_map( 'hexdec', str_split( str_pad( str_replace( '#', '', $hexcolor ), 6, '0' ), 2 ) );

	foreach ( $hexcolor as $i => $color ) {
		$from           = $percent < 0 ? 0 : $color;
		$to             = $percent < 0 ? $color : 255;
		$pvalue         = ceil( ( $to - $from ) * $percent );
		$hexcolor[ $i ] = str_pad( dechex( $color + $pvalue ), 2, '0', STR_PAD_LEFT );
	}

	return '#' . implode( $hexcolor );
}

/**
 * Convert hexdec color string to rgb(a) string.
 *
 * @param string $color Hexcolor code.
 * @param int    $opacity Color opacity.
 * @return string
 */
function hermes_hex_to_rgba( $color, $opacity = false ) {
	$default = 'rgb(0,0,0)';

	// Return default if no color provided.
	if ( empty( $color ) ) {
		return $default;
	}

	// Sanitize $color if "#" is provided.
	if ( '#' === $color[0] ) {
		$color = substr( $color, 1 );
	}

	// Check if color has 6 or 3 characters and get values.
	if ( 6 === strlen( $color ) ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( 3 === strlen( $color ) ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	// Convert hexadec to rgb.
	$rgb = array_map( 'hexdec', $hex );

	// Check if opacity is set(rgba or rgb).
	if ( $opacity ) {
		if ( abs( $opacity ) > 1 ) {
			$opacity = 1.0;
		}

		$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode( ',', $rgb ) . ')';
	}

	// Return rgb(a) color string.
	return $output;
}
