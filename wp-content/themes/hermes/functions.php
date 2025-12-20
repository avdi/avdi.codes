<?php
/**
 * Hermes functions and definitions
 *
 * @package Hermes
 */

/**
 * This theme requires WordPress 5.8 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '5.8', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'hermes_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function hermes_setup() {

	/**
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Hermes, use a find and replace
	 * to change 'hermes' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'hermes', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/**
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// Set featured image size
	add_image_size( 'hermes-featured-image', 1440, 9999 );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'hermes' ),
		'social'  => esc_html__( 'Social Menu', 'hermes' ),
	) );

	// Enable support for custom logo.
	add_theme_support( 'custom-logo', array(
		'height'      => 200,
		'width'       => 600,
		'flex-height' => true,
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'hermes_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	/* Add support for responsive embedded content. */
	add_theme_support( 'responsive-embeds' );

	/**
	 * Add support for core custom logo.
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 500,
		'flex-width'  => true,
		'flex-height' => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	/**
	 * Enable support for Excerpt on Pages and Projects.
	 * See http://codex.wordpress.org/Excerpt
	 */
	add_post_type_support( 'page', 'excerpt' );

	/**
	 * Set up the theme.
	 */
	add_theme_support( 'themes-harbor-theme', array(
		'colors' => array(
			'primary-dark'    => '#040405',
			'primary-base'    => '#1c1c20',
			'primary-light'   => '#3b3b45',
			'secondary-dark'  => '#8d8d9d',
			'secondary-base'  => '#c4c4cc',
			'secondary-light' => '#f1f1f3',
			'accent-dark'     => '#998200',
			'accent-base'     => '#ffd800',
			'accent-light'    => '#ffe866',
		),
		'fonts'  => array(
			'body'     => 'PT Serif',
			'headings' => 'PT Sans',
		),
	) );

    /*
	 * Enable support for Automatic Updates.
	 */
    add_theme_support( 'themes-harbor-edd-license', array(
        'theme-slug'             => 'hermes',
        'download-id'            => '205',
		'documentation_readme'   => 'https://help.themesharbor.com/article/201-hermes-read-me-first',
		'documentation_overview' => 'https://help.themesharbor.com/collection/122-hermes',
    ) );

    /**
     * Load theme updater functions.
     */
    require( get_template_directory() . '/inc/updater/theme-updater.php' );

	/* Add support Gutenberg editor styles. */
	add_theme_support( 'editor-styles' );

	/* Disable Gutengerg custom font sizes */
	add_theme_support( 'disable-custom-font-sizes' );

	/* Add Gutengerg Block wide and fullscreen alignments (works only if the sidebar is inactive). */
	add_theme_support( 'align-wide' );

	/**
	 * Temporary opt-out from the Template Mode (WP 5.8).
	 */
	if ( current_theme_supports( 'block-templates' ) ) {
		remove_theme_support( 'block-templates' );
	}

	// Remove core block patterns.
	remove_theme_support( 'core-block-patterns' );
}
endif; // hermes_setup
add_action( 'after_setup_theme', 'hermes_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function hermes_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'hermes_content_width', 800 );
}
add_action( 'after_setup_theme', 'hermes_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function hermes_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'hermes' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Appears in the sidebar section of the site.', 'hermes' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title has-small-font-size th-uppercase th-tracking-wider"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer', 'hermes' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Appears in the footer section of the site.', 'hermes' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s footer-widget">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title has-secondary-light-color has-normal-font-size th-uppercase th-tracking-wider">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'hermes_widgets_init' );

if ( ! function_exists( 'hermes_enqueue_block_styles' ) ) :
	/**
	 * Enqueue individual block stylesheets.
	 */
	function hermes_enqueue_block_styles() {
		// Get all available block namespaces.
		$block_namespaces = glob( dirname( __FILE__ ) . '/assets/styles/blocks/*/' );
		$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$block_namespaces = array_map(
			function( $type_path ) {
				return basename( $type_path );
			},
			$block_namespaces
		);

		foreach ( $block_namespaces as $block_namespace ) {

			// Get all available block styles of the given block namespace.
			$block_styles = glob( dirname( __FILE__ ) . '/assets/styles/blocks/' . $block_namespace . '/*.css' );

			$block_styles = array_reduce(
				$block_styles,
				function( $carry, $styles_path ) {
					$basename = basename( $styles_path, '.css' );
					// Check if $basename has .min in its name.
					if ( strpos( $basename, '.min' ) === false ) {
						$carry[] = $basename;
					}
					return $carry;
				},
				array()
			);

			foreach ( $block_styles as $block_style ) {
				// Enqueue individual block stylesheets.
				wp_enqueue_block_style(
					$block_namespace . '/' . $block_style,
					array(
						'handle' => 'hermes-' . $block_namespace . '-' . $block_style . '-styles',
						'src'    => get_theme_file_uri( 'assets/styles/blocks/' . $block_namespace . '/' . $block_style . "{$suffix}.css" ),
						'path'   => get_theme_file_path( 'assets/styles/blocks/' . $block_namespace . '/' . $block_style . '.css' ),
					)
				);
			}
		}
	}
	endif;
add_action( 'after_setup_theme', 'hermes_enqueue_block_styles' );

/**
 * Enqueue scripts and styles.
 */
function hermes_scripts() {
	// Cache directory uri.
	$template_directory_uri = get_template_directory_uri();
	$suffix                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$is_singular            = is_singular();
	$is_grid_template       = is_page_template( 'templates/grid-page.php' );

	// Register global styles.
	wp_register_style(
		'hermes-templates-core-global',
		$template_directory_uri . "/assets/styles/templates/core/global{$suffix}.css",
		array(),
		'3.6.0'
	);

	// Register comments styles.
	wp_register_style(
		'hermes-templates-core-comments',
		$template_directory_uri . "/assets/styles/templates/core/comments{$suffix}.css",
		array(),
		'3.6.0'
	);

	// Register hfeed styles.
	wp_register_style(
		'hermes-templates-core-hfeed',
		$template_directory_uri . "/assets/styles/templates/core/hfeed{$suffix}.css",
		array(),
		'3.6.0'
	);

	wp_enqueue_style( 'hermes-style', $template_directory_uri . "/style{$suffix}.css", array(), '3.5.0' );
	wp_style_add_data( 'hermes-style', 'rtl', 'replace' );

	if ( is_child_theme() ) {
		wp_enqueue_style( 'hermes-child-style', get_stylesheet_uri(), array( 'hermes-style' ) );
	}

	// Enqueue global styles.
	wp_enqueue_style( 'hermes-templates-core-global' );

	// Load styles needed for singular views.
	if ( $is_singular ) {
		// Load JS functionliaty needed for single views.
		wp_enqueue_script( 'hermes-singular-script', $template_directory_uri . "/assets/js/public/pages/singular{$suffix}.js", array(), '3.0.0', true );

		// Load singular styles.
		wp_enqueue_style( 'hermes-singular-style', $template_directory_uri . "/assets/css/pages/singular/singular{$suffix}.css", array( 'hermes-style' ), '3.4.3' );
		wp_style_add_data( 'hermes-singular-style', 'rtl', 'replace' );
		wp_style_add_data( 'hermes-singular-style', 'suffix', $suffix );

		// Load reply script only when it is needed.
		if ( comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Load comments styles only when it is needed.
		if ( get_comments_number() || comments_open() ) {
			wp_enqueue_style( 'hermes-templates-core-comments' );
		}
	}

	if ( $is_grid_template || ( ! $is_singular && ! $is_grid_template ) ) {
		// Load hfeed and 404 styles.
		wp_enqueue_style( 'hermes-templates-core-hfeed' );
	}

	wp_enqueue_script( 'hermes-script', $template_directory_uri . "/assets/js/public/hermes{$suffix}.js", array(), '3.4.0', true );

	wp_localize_script( 'hermes-script',
		'screenReaderText',
		array(
			'expand'   => esc_html__( 'Expand child menu', 'hermes' ),
			'collapse' => esc_html__( 'Collapse child menu', 'hermes' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'hermes_scripts' );

/**
 * Enqueue block editor styles.
 */
function hermes_block_editor_assets() {
	// Enqueue the editor styles.
	wp_enqueue_style( 'hermes-google-fonts', hermes_google_fonts(), array(), null );
	wp_enqueue_style( 'hermes-block-editor-styles', get_theme_file_uri( '/assets/css/editor/base/block-editor-style.css' ), array( 'hermes-google-fonts' ), '3.4.3', 'all' );

	// Add custom fonts functionality to the Block editor.
	wp_add_inline_style( 'hermes-block-editor-styles', hermes_get_font_family_css() );
	// Add custom colors functionality to the Block editor.
	wp_add_inline_style( 'hermes-block-editor-styles', hermes_get_colors_css() );

	// Overwrite Core block styles with empty styles.
	wp_deregister_style( 'wp-block-library-theme' );
	wp_register_style( 'wp-block-library-theme', '' );
}
add_action( 'enqueue_block_editor_assets', 'hermes_block_editor_assets' );

/**
 * SVG Icons class.
 */
require get_template_directory() . '/inc/classes/class-themes-harbor-svg-icons.php';

/**
 * Load icons functionality.
 */
require get_template_directory() . '/inc/icon-functions.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/plugins/woocommerce.php';
}

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/plugins/jetpack.php';
}

/**
 * Load AMP compatibility file.
 */
if ( defined( 'AMP__VERSION' ) && version_compare( AMP__VERSION, '2.0', '>=' ) ) {
	require get_template_directory() . '/inc/plugins/amp-wp.php';
}

/**
 * Co-Authors Plus Compatibility File.
 */
if ( class_exists( 'CoAuthors_Plus' ) ) {
	require get_template_directory() . '/inc/plugins/coauthors-plus.php';
}

/**
 * Load MC4WP compatibility file.
 */
if ( defined( 'MC4WP_VERSION' ) ) {
	require get_template_directory() . '/inc/plugins/mc4wp.php';
}

/**
 * Load Yoast SEO compatibility file.
 */
if ( defined( 'WPSEO_VERSION' ) ) {
	require get_template_directory() . '/inc/plugins/yoast-seo.php';
}

/**
 *  Load typography functionality.
 */
require get_template_directory() . '/inc/typography/typography.php';

/**
 * Custom colors.
 */
require get_template_directory() . '/inc/custom-colors.php';

/**
 * Load block styles specially designed for the theme.
 */
require get_template_directory() . '/inc/register-global-styles.php';
