<?php
/**
 * Customizer functionality for typography.
 *
 * @package hermes
 *
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments
 */

/**
 * Options for the theme typography.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function hermes_typography_customize_register( $wp_customize ) {
	// Load the Customizer control for the font-family options.
	require get_template_directory() . '/inc/typography/class-themes-harbor-customizer-font-family-control.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

	// Register font-family control.
	$wp_customize->register_control_type( 'Themes_Harbor_Customizer_Font_Family_Control' );
	// Get theme defaults.
	$the_theme = get_theme_support( 'themes-harbor-theme' );

	/* Typography */
	$wp_customize->add_section(
		'hermes_typography_options',
		array(
			'title'       => esc_html__( 'Typography Options', 'hermes' ),
			'priority'    => 1,
			'panel'       => 'hermes_theme_panel',
			'description' => esc_html__( 'Select System Stack option if your site uses a custom font to avoid loading unused fonts.', 'hermes' ),
		)
	);

	/* Typography: Body font family */
	$wp_customize->add_setting(
		'hermes_typography_body_font_family',
		array(
			'default'           => $the_theme[0]['fonts']['body'],
			'sanitize_callback' => 'esc_attr',
		)
	);

	$wp_customize->add_control(
		new Themes_Harbor_Customizer_Font_Family_Control(
			$wp_customize,
			'hermes_typography_body_fontfamily',
			array(
				'type'        => 'font-family',
				'label'       => esc_html__( 'Body Font Family', 'hermes' ),
				'section'     => 'hermes_typography_options',
				'settings'    => 'hermes_typography_body_font_family',
				'description' => esc_html__( 'Select the body font family which will be used for text in the body of your website.', 'hermes' ),
			)
		)
	);

	/* Typography: Headings font family */
	$wp_customize->add_setting(
		'hermes_typography_heading_font_family',
		array(
			'default'           => $the_theme[0]['fonts']['headings'],
			'sanitize_callback' => 'esc_attr',
		)
	);

	$wp_customize->add_control(
		new Themes_Harbor_Customizer_Font_Family_Control(
			$wp_customize,
			'hermes_typography_headings_fontfamily',
			array(
				'type'        => 'font-family',
				'label'       => esc_html__( 'Headings Font Family', 'hermes' ),
				'section'     => 'hermes_typography_options',
				'settings'    => 'hermes_typography_heading_font_family',
				'description' => esc_html__( 'Select the font family which will be used for the headings on your website.', 'hermes' ),
			)
		)
	);
}
add_action( 'customize_register', 'hermes_typography_customize_register' );

/**
 * Scripts, JS and CSS, needed for typography controls.
 */
function hermes_typography_customize_scripts() {
	$controls_style = '
    .th-control-header {
        margin-bottom: 5px;
    }

    .th-description {
        margin-top: 5px;
    }

    .th-control-header .th-reset {
        float: right;
        padding-left: 7px;
        padding-right: 7px;
        height: 26px;
        width: 30px;
    }

    .th-control-header .customize-control-title {
        float: left;
        width: calc(100% - 45px);
        margin-bottom: 0;
    }
    ';

	wp_add_inline_style( 'customize-controls', $controls_style );

	$controls_script = "
    wp.customize.controlConstructor['font-family'] = wp.customize.Control.extend({
        ready: function () {
            var control = this;
            var selectContainer = control.container.find( 'select' );
            var defaulValue = selectContainer.data( 'default' );

            this.container.on( 'click', 'button.th-reset', function(e) {
                e.preventDefault();
                selectContainer.val( defaulValue ).change();
            });

            this.container.on( 'change', 'select', function () {
                control.setting.set( selectContainer.val() );
            });
        }
    });
    ";

	wp_add_inline_script( 'customize-controls', $controls_script );
}
add_action( 'customize_controls_enqueue_scripts', 'hermes_typography_customize_scripts' );
