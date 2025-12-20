<?php
/**
 * Hermes Theme Customizer
 *
 * @package Hermes
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function avdeez_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/* THEME OPTIONS */
	$wp_customize->add_panel( 'avdeez_theme_panel', array(
		'priority' => 9999,
		'title'    => esc_html__( 'Theme Options', 'avdeez' ),
	) );

	/* General options */
	$wp_customize->add_section( 'avdeez_general_options', array(
		'title'    => esc_html__( 'General Options', 'avdeez' ),
		'priority' => 1,
		'panel'    => 'avdeez_theme_panel'
	) );

	/* General options: Sidebar Position */
	$wp_customize->add_setting( 'avdeez_sidebar_position', array(
		'default'            => 'right',
		'sanitize_callback' => 'avdeez_sanitize_sidebar_position',
	) );

	$wp_customize->add_control( 'avdeez_sidebar_position', array(
		'label'       => esc_html__( 'Sidebar Position', 'avdeez' ),
		'type'        => 'select',
		'section'     => 'avdeez_general_options',
		'choices'     => array(
			'right' => esc_html__( 'Right', 'avdeez' ),
			'left'  => esc_html__( 'Left', 'avdeez' ),
		),
		'priority'    => 10,
		'description' => esc_html__( 'By default the Hermes theme will show the sidebar on the right side of your site. Use this option to change the position of your sidebar.', 'avdeez' ),
	));

	/* Archive view grid layout. */
	$wp_customize->add_setting( 'avdeez_hfeed_grid_layout', array(
		'default'           => 'grid-layout-default',
		'sanitize_callback' => 'avdeez_sanitize_hfeed_grid_layout',
	) );

	$wp_customize->add_control( 'avdeez_hfeed_grid_layout', array(
		'label'       => esc_html__( 'Grid Layout', 'avdeez' ),
		'type'        => 'select',
		'section'     => 'avdeez_general_options',
		'choices'     => array(
			'grid-layout-default' => esc_html__( 'Default', 'avdeez' ),
			'grid-layout-1-col'   => esc_html__( 'One column', 'avdeez' ),
			'grid-layout-2-col'   => esc_html__( 'Two columns', 'avdeez' ),
			'grid-layout-3-col'   => esc_html__( 'Three columns', 'avdeez' ),
		),
		'priority'    => 10,
		'description' => esc_html__( 'Use this option to change a layout of the grid shown in archive views and the Grid Page templates.', 'avdeez' ),
	) );

	/* Header Options */
	$wp_customize->add_section( 'avdeez_header_options', array(
		'title'    => esc_html__( 'Header Options', 'avdeez' ),
		'priority' => 2,
		'panel'    => 'avdeez_theme_panel',
	) );

	/* Header Options: Sticky Header. */
	$wp_customize->add_setting( 'avdeez_is_sticky_header', array(
		'default'           => '',
		'sanitize_callback' => 'avdeez_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'avdeez_is_sticky_header', array(
		'label'       => esc_html__( 'Enable sticky header mode', 'avdeez' ),
		'section'     => 'avdeez_header_options',
		'priority'    => 1,
		'type'        => 'checkbox',
		'description' => esc_html__( 'The header links will remain in view, even as a user scrolls down the page.', 'avdeez' ),
	) );

	/* Header Options: Position of the mobile menu. */
	$wp_customize->add_setting( 'avdeez_toggle_sidebar_position', array(
		'default'           => 'left',
		'sanitize_callback' => 'avdeez_sanitize_toggle_sidebar_position',
	) );

	$wp_customize->add_control( 'avdeez_toggle_sidebar_position', array(
		'label'       => esc_html__( 'Mobile Menu Position', 'avdeez' ),
		'type'        => 'radio',
		'section'     => 'avdeez_header_options',
		'choices' => array(
			'left'  => esc_html__( 'Left', 'avdeez' ),
			'right' => esc_html__( 'Right', 'avdeez' ),
		),
		'priority'    => 2,
		'description' => esc_html__( 'By default the Hermes theme will show the mobile menu on the left side of your site. Use this option to change the position of your mobile menu.', 'avdeez' ),
	) );

	/* Footer Text */
	$wp_customize->add_setting( 'avdeez_footer_custom_text', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
	) );

	$wp_customize->add_control( 'avdeez_footer_custom_text', array(
		'type'        => 'textarea',
		'label'       => esc_html__( 'Footer Credits', 'avdeez' ),
		'section'     => 'avdeez_general_options',
		'description' => esc_html__( 'You can add your own credit text to WordPress footer by using this option.', 'avdeez' ),
	) );
}
add_action( 'customize_register', 'avdeez_customize_register' );

/**
 * Sanitize sidebar position.
 */
function avdeez_sanitize_sidebar_position( $input ) {
	if ( in_array( $input, array( 'left', 'right' ) ) ) {
		return $input;
	}

	return 'right';
}

/**
 * Sanitize position of the toggle sidebar.
 */
function avdeez_sanitize_toggle_sidebar_position( $input ) {
	if ( in_array( $input, array( 'left', 'right' ) ) ) {
		return $input;
	}

	return 'left';
}

/**
 * Sanitize grid layout.
 */
function avdeez_sanitize_hfeed_grid_layout( $input ) {
	if ( in_array( $input, array( 'grid-layout-1-col', 'grid-layout-2-col', 'grid-layout-3-col' ) ) ) {
		return $input;
	}

	return 'grid-layout-default';
}

/**
 * Sanitize the checkbox value.
 */
function avdeez_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return true;
	}

	return false;
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function avdeez_customize_preview_js() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'avdeez_customizer', get_template_directory_uri() . "/assets/js/admin/customizer{$suffix}.js", array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'avdeez_customize_preview_js' );
