<?php
/**
 * Template Name: Blank content area page
 *
 * @package Hermes
 */

get_header();

	while ( have_posts() ) : the_post();

		the_content();

		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'avdeez' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>'
		) );

		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile;

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'avdeez' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<div class="entry-footer alignfull th-mb-0 th-py-small th-px-base has-small-font-size has-background th-links-inherit block-editor-rich-text__editable"><div class="alignwide edit-link wp-block-button is-style-outline has-tiny-font-size th-mx-auto">',
		'</div></div>',
		null,
		'post-edit-link wp-block-button__link'
	);

get_footer();
