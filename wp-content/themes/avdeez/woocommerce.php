<?php
/**
 * The template for displaying WooCommerce content.
 *
 * @package Hermes
 */

get_header();

get_template_part( 'template-parts/global/page-header-woocommerce' );
?>

	<main id="main" class="site-main alignwide th-my-medium th-space-y-base th-divide-y th-divide-solid th-divide-secondary-base">

        <?php woocommerce_content(); ?>

	</main><!-- #main -->

<?php
get_footer();
