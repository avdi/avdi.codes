<?php
/**
 * WooCommerce Compatibility File
 * See: https://docs.woocommerce.com/
 *
 * @package avdeez
 */

/**
 * WooCommerce setup function.
 */
function avdeez_woocommerce_setup() {
	// Default settings for the product grid.
	$product_grid = array(
		'default_columns' => 4,
		'min_columns'     => 2,
		'max_columns'     => 4,
		'default_rows'    => 3,
		'min_rows'        => 1,
	);

 	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 450,
		'single_image_width'    => 700,
		'product_grid'          => $product_grid,
	) );

    add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'avdeez_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function avdeez_woocommerce_scripts() {
    $suffix                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $template_directory_uri = get_template_directory_uri();

	// Register WooCommerce account stylesheet.
	wp_register_style(
		'avdeez-templates-woocommerce-account',
		get_theme_file_uri( "/assets/styles/templates/woocommerce/account{$suffix}.css" ),
		array(),
		'3.6.0'
	);

	// Register WooCommerce cart stylesheet.
	wp_register_style(
		'avdeez-templates-woocommerce-cart',
		get_theme_file_uri( "/assets/styles/templates/woocommerce/cart{$suffix}.css" ),
		array(),
		'3.6.0'
	);

	// Register WooCommerce checkout stylesheet.
	wp_register_style(
		'avdeez-templates-woocommerce-checkout',
		get_theme_file_uri( "/assets/styles/templates/woocommerce/checkout{$suffix}.css" ),
		array(),
		'3.6.0'
	);

	// Register WooCommerce hfeed stylesheet.
	wp_register_style(
		'avdeez-templates-woocommerce-hfeed',
		get_theme_file_uri( "/assets/styles/templates/woocommerce/hfeed{$suffix}.css" ),
		array(),
		'3.6.0'
	);

	// Register WooCommerce product stylesheet.
	wp_register_style(
		'avdeez-templates-woocommerce-product',
		get_theme_file_uri( "/assets/styles/templates/woocommerce/product{$suffix}.css" ),
		array(),
		'3.6.0'
	);

	// Display styles for singular product views.
	if ( is_product() ) {
		wp_enqueue_style( 'avdeez-templates-woocommerce-product' );

	} else if ( is_cart() ) {
		wp_enqueue_style( 'avdeez-templates-woocommerce-cart' );

	} else if ( is_checkout() ) {
		wp_enqueue_style( 'avdeez-templates-woocommerce-checkout' );

	} else if ( is_account_page() ) {
		wp_enqueue_style( 'avdeez-templates-woocommerce-account' );
	}

	// Display styles for archive product views.
	if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
		wp_enqueue_style( 'avdeez-templates-woocommerce-hfeed' );
	}

	wp_enqueue_style( 'avdeez-woocommerce-style', $template_directory_uri . "/assets/css/plugins/woocommerce/woocommerce{$suffix}.css", array(), '3.4.3' );
	wp_style_add_data( 'avdeez-woocommerce-style', 'rtl', 'replace' );
	wp_style_add_data( 'avdeez-woocommerce-style', 'suffix', $suffix );

    $font_path = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
		font-family: "star";
		src: url("' . $font_path . 'star.eot");
		src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
		url("' . $font_path . 'star.woff") format("woff"),
		url("' . $font_path . 'star.ttf") format("truetype"),
		url("' . $font_path . 'star.svg#star") format("svg");
		font-weight: normal;
		font-style: normal;
	}';

	$inline_font .= '@font-face {
		font-family: "WooCommerce";
		src: url("' . $font_path . 'WooCommerce.eot");
		src: url("' . $font_path . 'WooCommerce.eot?#iefix") format("embedded-opentype"),
		url("' . $font_path . 'WooCommerce.woff") format("woff"),
		url("' . $font_path . 'WooCommerce.ttf") format("truetype"),
		url("' . $font_path . 'WooCommerce.svg#WooCommerce") format("svg");
		font-weight: normal;
		font-style: normal;
	}';

	wp_add_inline_style( 'avdeez-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'avdeez_woocommerce_scripts' );

/**
 * WooCommerce specific scripts and stylesheets for the editor.
 */
function avdeez_woocommerce_block_editor_assets() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style( 'avdeez-woocommerce-block-editor-styles', get_theme_file_uri( '/assets/css/editor/plugins/woocommerce-block-editor-style.css' ), array( 'avdeez-block-editor-styles' ), '3.4.3', 'all' );
	wp_style_add_data( 'avdeez-woocommerce-block-editor-styles', 'rtl', 'replace' );
	wp_style_add_data( 'avdeez-woocommerce-block-editor-styles', 'suffix', $suffix );
}
add_action( 'enqueue_block_editor_assets', 'avdeez_woocommerce_block_editor_assets' );

/**
 * WooCommerce Post Class filter.
 */
function avdeez_woocommerce_post_class( $classes, $product ) {
    // is_product() - Returns true on a single product page
    // NOT single product page, so return
    if ( ! is_product() ) {
        return $classes;
    }

    // Add new classes for singular products.
	if ( get_the_ID() === get_queried_object_id() ) {
		$classes[] = 'singular-product lg:th-flex lg:th-flex-wrap th-space-y-base th-divide-y th-divide-solid th-divide-secondary-base';
	}


    return $classes;
}
add_filter( 'woocommerce_post_class', 'avdeez_woocommerce_post_class', 10, 2 );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

if ( ! function_exists( 'avdeez_sorting_wrapper' ) ) {
	/**
	 * Sorting wrapper.
	 */
	function avdeez_sorting_wrapper() {
		echo '<div class="shop-sorting th-flex th-flex-wrap th-space-y-base">';
	}
}
add_action( 'woocommerce_before_shop_loop', 'avdeez_sorting_wrapper', 19 );

if ( ! function_exists( 'avdeez_sorting_wrapper_close' ) ) {
	/**
	 * Sorting wrapper close.
	 */
	function avdeez_sorting_wrapper_close() {
		echo '</div><!-- .shop-sorting -->';
	}
}
add_action( 'woocommerce_before_shop_loop', 'avdeez_sorting_wrapper_close', 31 );

/**
 * Adds a wrapper for a demo store banner.
 */
function avdeez_woocommerce_demo_store( $output, $notice ) {
	$output = str_replace( 'style="display:none;">', 'style="display:none;"><span>', $output );
	$output = str_replace( '<a href="#" class="woocommerce-store-notice__dismiss-link">', '</span><a href="#" class="woocommerce-store-notice__dismiss-link">', $output );

	return $output;
}
add_filter( 'woocommerce_demo_store', 'avdeez_woocommerce_demo_store', 2, 10 );

/**
 * Wrap the reset link within a container.
 */
function avdeez_woocommerce_reset_variations_link() {
	printf(
		'<div class="reset_variations"><button class="reset-button th-inline-flex th-items-center" type="button">%1$s %2$s</button></div>',
		avdeez_get_icon_svg( 'close', 18 ),
		esc_html__( 'Clear', 'avdeez' )
	);
}
add_action( 'woocommerce_reset_variations_link', 'avdeez_woocommerce_reset_variations_link', 15 );

/**
 * Display the edit link in single product views.
 */
function avdeez_woocommerce_single_product_summary() {
	edit_post_link(
		esc_html__( 'Edit', 'avdeez' ),
		'<footer class="entry-footer"><div class="edit-link wp-block-button is-style-outline has-small-font-size">',
		'</div></footer>',
		null,
		'wp-block-button__link'
	);
}
add_action( 'woocommerce_single_product_summary', 'avdeez_woocommerce_single_product_summary', 60 );

/**
 * Remove support for Jetpack infinite scroll for WooCommerce products
 * due to the issue in the Jetpack core.
 *
 * See: https://github.com/Automattic/jetpack/issues/8385
 *
 * @param  WP_Query $query The current archive object.
 */
function avdeez_wc_disable_infinite_scroll( $query ) {
	// Define the context.
	// Not on dashboard pages when inside the main query only on cpt archives.
	if ( ! is_admin() && $query->is_main_query() && ( is_post_type_archive( 'product' ) || is_tax( 'product_cat' ) ) ) {
		// Remove infinite scroll inside this context.
		remove_theme_support( 'infinite-scroll' );
	}
}
add_action( 'pre_get_posts', 'avdeez_wc_disable_infinite_scroll' );

function avdeez_woocommerce_yoast_breadcrumb_classes( $classes ) {
	$is_shop_archive = is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag();

	if ( $is_shop_archive ) {
		$classes[] = 'alignfull has-secondary-light-color has-primary-dark-background-color th-my-0';
	}

	return $classes;
}
add_filter( 'avdeez_yoast_breadcrumb_classes', 'avdeez_woocommerce_yoast_breadcrumb_classes' );

if ( ! function_exists( 'avdeez_woocommerce_cart_link' ) ) :
	/**
	 * The cart callback function for the mobile cart icon.
	 */
	function avdeez_woocommerce_cart_link() {
		$woo                   = WC();
		$is_woo_cart_available = $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;

		/* Validates whether the Woo Cart instance is available in the request. */
		if ( ! $is_woo_cart_available ) {
			return;
		}
		?>
		<a class="cart-contents th-border-r th-border-primary-light" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'avdeez' ); ?>">
			<?php avdeez_icon_svg( 'shopping-bag', 18 ); ?>
			<span class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
		</a>
		<?php
	}
endif;

if ( ! function_exists( 'avdeez_woocommerce_cart_link_fragment' ) ) :
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function avdeez_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		avdeez_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
endif;
add_filter( 'woocommerce_add_to_cart_fragments', 'avdeez_woocommerce_cart_link_fragment' );

/**
 * Hide Woo title on the main shop page.
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );

/**
 * Remove output of the archive description in archive views.
 * The theme displays it in the different section.
 */
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
