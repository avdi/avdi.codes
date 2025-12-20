<?php
/**
 * Template Name: Parent page + child page(s)
 *
 * @package Hermes
 */

get_header();

$current_page_id = get_the_ID();
$has_child_pages = count( get_posts( array( 'post_parent' => $current_page_id, 'post_type' => 'page' ) ) );
$child_pages     = false;

if ( $has_child_pages ) {
    $child_pages = new WP_Query( array(
        'post_type'      => 'page',
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'order'          => 'ASC',
        'post_parent'    => $current_page_id,
        'posts_per_page' => 99,
        'no_found_rows'  => true,
    ) );
}

get_template_part( 'template-parts/singular/page-header', 'grid' );

if ( $child_pages && $child_pages->have_posts() && ! post_password_required() ) :
    ?>

    <main id="main" class="site-main alignfull has-primary-dark-background-color hentry-grid th-flex th-flex-wrap th-my-0">
        <?php
        while ( $child_pages->have_posts() ) : $child_pages->the_post();

            get_template_part( 'template-parts/hfeed/content', get_post_format() );

        endwhile;

        wp_reset_postdata();
        ?>
	</main><!-- #main -->

    <?php
endif;

get_footer();
