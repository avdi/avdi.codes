<?php
/**
 * The part displays page header in WooCommerce views.
 *
 * @package Hermes
 */

if ( ! is_product() ) :
    ?>
    <header class="page-header alignfull has-secondary-light-color has-primary-dark-background-color th-space-y-small th-my-0 th-py-base th-px-small">
        <h1 class="page-title has-medium-font-size has-accent-base-color th-uppercase th-my-0">
			<?php woocommerce_page_title(); ?>
		</h1>

        <?php
        if ( is_archive() && ! is_shop() ) :
            the_archive_description( '<div class="taxonomy-description has-secondary-light-color th-max-w-prose th-content">', '</div>' );
        elseif ( is_shop() ) :
            $page_id = wc_get_page_id( 'shop' );

            if ( avdeez_has_content( $page_id ) ) {
                printf(
                    '<div class="taxonomy-description has-secondary-light-color th-content">%s</div>',
                    do_shortcode( wp_kses_post( get_post_field( 'post_content', $page_id ) ) )
                );
            }
        endif;
        ?>
    </header>
    <?php
endif;

do_action( 'avdeez_after_page_header' );
