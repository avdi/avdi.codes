<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Hermes
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area th-space-y-base">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title has-normal-font-size th-uppercase th-mt-0">
			<?php
				$comments_number = get_comments_number();

				/* translators: %s is a placeholder that will be replaced by a variable passed as an argument. */
				printf( esc_html( _n( '%s comment', '%s comments', $comments_number, 'hermes' ) ), esc_html( number_format_i18n( $comments_number ) ) );
			?>
		</h2><!-- .comments-title -->

		<ol class="comment-list th-mb-0">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 96,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

	endif;

	if ( ! comments_open() ) :
		printf( '<p class="no-comments th-mb-0">%s</p>', esc_html__( 'Comments are closed.', 'hermes' ) );
	endif;

	comment_form( array(
		'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title has-normal-font-size th-mt-0 th-mb-small th-uppercase">',
		'title_reply_after'  => '</h3>',
		'submit_field'	     => '<p class="form-submit th-mb-0">%1$s %2$s</p>'
	) );
	?>

</div><!-- #comments -->
