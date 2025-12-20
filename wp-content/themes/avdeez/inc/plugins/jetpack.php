<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Hermes
 */

function avdeez_jetpack_setup() {
	/**
	 * Add theme support for Infinite Scroll.
	 * See: http://jetpack.me/support/infinite-scroll/
	 */
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'footer_widgets' => 'footer-1',
		'footer'         => 'page',
		'render'         => 'avdeez_infinite_scroll_render',
		'wrapper'        => false,
	) );

	/**
	 * Add support for Content Options.
	 */
	add_theme_support( 'jetpack-content-options', array(
	    'post-details'      => array(
	        'stylesheet' => 'avdeez-style',
	        'date'       => '.posted-on',
	        'categories' => '.cat-links',
	        'tags'       => '.tags-links',
	        'author'     => '.byline',
	        'comment'    => '.comments-link',
		),
		'author-bio'         => true,
    	'author-bio-default' => true,
	) );

	// Add support for the Featured Content.
	add_theme_support( 'featured-content', array(
		'featured_content_filter' => 'avdeez_featured_posts',
		'max_posts'               => 5,
		'post_types'              => array( 'post', 'page' ),
	) );
}
add_action( 'after_setup_theme', 'avdeez_jetpack_setup' );

/**
 * Load assets needed for the jetpack modules.
 */
function avdeez_jetpack_assets() {
	// Cache directory uri.
	$suffix                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$template_directory_uri = get_template_directory_uri();

	// Load Jetpack styles for singular views.
	if ( is_singular() ) {
		wp_enqueue_style( 'avdeez-jetpack-singular-style', $template_directory_uri . "/assets/css/plugins/jetpack/jetpack-singular{$suffix}.css", array(), '3.1.2' );
		wp_style_add_data( 'avdeez-jetpack-singular-style', 'rtl', 'replace' );
		wp_style_add_data( 'avdeez-jetpack-singular-style', 'suffix', $suffix );
	} else {
		wp_enqueue_style( 'avdeez-jetpack-hfeed-style', $template_directory_uri . "/assets/css/plugins/jetpack/jetpack-hfeed{$suffix}.css", array(), '3.1.2' );
		wp_style_add_data( 'avdeez-jetpack-hfeed-style', 'rtl', 'replace' );
		wp_style_add_data( 'avdeez-jetpack-hfeed-style', 'suffix', $suffix );
	}

	// Load Jetpack styles.
	wp_enqueue_style( 'avdeez-jetpack-style', $template_directory_uri . "/assets/css/plugins/jetpack/jetpack{$suffix}.css", array(), '3.3.0' );
	wp_style_add_data( 'avdeez-jetpack-style', 'rtl', 'replace' );
	wp_style_add_data( 'avdeez-jetpack-style', 'suffix', $suffix );
}
add_action( 'wp_enqueue_scripts', 'avdeez_jetpack_assets' );


/**
 * Jetpack specific scripts and stylesheets for the editor.
 */
function avdeez_jetpack_block_editor_assets() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style( 'avdeez-jetpack-block-editor-styles', get_theme_file_uri( '/assets/css/editor/plugins/jetpack-block-editor-style.css' ), array( 'avdeez-block-editor-styles' ), '3.3.0', 'all' );
	wp_style_add_data( 'avdeez-jetpack-block-editor-styles', 'rtl', 'replace' );
	wp_style_add_data( 'avdeez-jetpack-block-editor-styles', 'suffix', $suffix );
}
add_action( 'enqueue_block_editor_assets', 'avdeez_jetpack_block_editor_assets' );

/**
 * Do not load the Infinite Scroll stylesheets and scripts needed
 * for the content area in archive views.
 */
add_filter( 'jetpack_infinite_scroll_load_scripts_and_styles', '__return_false' );

/**
 * Custom render fucntion for the Jetpack Infinite Scroll.
 */
function avdeez_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/hfeed/content', get_post_format() );
	}
}

/**
 * Getter function for Featured Content feature.
 */
function avdeez_get_featured_posts() {
	return apply_filters( 'avdeez_featured_posts', false );
}

/**
 * A helper conditional function that checks if there are featured posts.
 */
function avdeez_has_featured_posts() {
	return ! is_paged() && (bool) avdeez_get_featured_posts();
}

/**
 * Return first primary item for the Featured Content section.
 *
 * @param array $items Post ojects for Featured Content.
 */
function avdeez_jetpack_featured_content_primary( $items ) {
   if ( ! $items ) {
	   return;
   }

   return array_slice( $items, 0, 1 );
}

/**
 * Return last four secondary items for the Featured Content section.
 *
 * @param array $items Post ojects for Featured Content.
 */
function avdeez_jetpack_featured_content_secondary( $items ) {
   if ( ! $items ) {
	   return;
   }

   if ( count( $items ) > 1 ) {
	   return array_slice( $items, 1, 4 );
   } else {
	   return;
   }
}

/**
 * Prints classes for the Featured Content columns.
 *
 * @param string $section Name of the section.
 * @param bool $has_featured_image True or false, if the primary post has a featured image.
 */
function avdeez_jetpack_featured_content_column_class( $section = '', $has_featured_image = false ) {
	$classes = array( 'th-w-full' );

	switch ( $section ) {
		case 'primary':
			$classes[] = 'primary-content md:th-w-8/12 th-self-end md:th-mb-0 th-mb-base';
			break;

		case 'secondary':
			$classes[] = 'secondary-content md:th-w-4/12 th-space-y-small md:th-py-base';
			break;
	}

	if ( $has_featured_image ) {
		$classes[] = 'has-post-thumbnail has-secondary-light-color th-h-full th-links-inherit';
	}

	printf( 'class="%s"', join( ' ', $classes ) ); // WPCS: XSS OK.
}

/**
 * Prints a small featured banner above the title of the primary post
 * to show that this section displays featured items.
 */
function avdeez_jetpack_featured_content_tag_banner() {
	$featured_content = (array) get_option( 'featured-content' );

	if ( ! empty( $featured_content ) ) {
		if ( isset( $featured_content['tag-name'] ) && $featured_content['tag-name'] ) {
			$term = get_term_by( 'name', $featured_content['tag-name'], 'post_tag' );
			$term_link = get_term_link( $term );

			if ( ! is_wp_error( $term_link ) ) {
				printf(
					'<div class="featured-label has-tiny-font-size th-font-headings has-accent-base-color th-links-inherit th-uppercase th-mb-sm th-flex th-items-center th-font-bold">%1$s<a class="th-no-underline" href="%2$s">%3$s</a></div>',
					avdeez_get_icon_svg( 'bookmark', 16 ),
					$term_link,
					$featured_content['tag-name']
				);
			}
		}
	}
}

/**
 * Exclude specific images from lazy loading.
 */
function avdeez_jetpack_exclude_class_from_lazy_load( $classes ) {
	$classes[] = 'custom-logo';

    return $classes;
}
add_filter( 'jetpack_lazy_images_blocked_classes', 'avdeez_jetpack_exclude_class_from_lazy_load', 999, 1 );

/**
 * Display the Featured Content section if needed.
 */
function avdeez_jetpack_display_featured_content() {
	if ( avdeez_has_featured_posts() && is_home() ) {
		get_template_part( 'template-parts/hfeed/featured-content' );
	}
}
add_action( 'avdeez_before_site_main', 'avdeez_jetpack_display_featured_content' );

/**
 * Display an icon in the Infinite Scroll button.
 */
function avdeez_jetpack_infinite_scroll_js_settings( $settings ) {
	$settings['text'] = avdeez_get_icon_svg( 'refresh', 14 ) . $settings['text'];

	return $settings;
}
add_filter( 'infinite_scroll_js_settings', 'avdeez_jetpack_infinite_scroll_js_settings' );

/**
 * Change visibility of the author bio section based on the Content Options settings.
 */
function avdeez_jetpack_has_author_bio( $is_visible ) {
	$jetpack_content_options = get_theme_support( 'jetpack-content-options' );
	$author_bio_default      = ( isset( $jetpack_content_options[0]['author-bio-default'] ) && false === $jetpack_content_options[0]['author-bio-default'] ) ? '' : 1;
	$is_visible              = get_option( 'jetpack_content_author_bio', $author_bio_default );

	return $is_visible;
}
add_filter( 'avdeez_has_author_bio', 'avdeez_jetpack_has_author_bio' );
