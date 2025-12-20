<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Hermes
 */

if ( ! function_exists( 'avdeez_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function avdeez_posted_on( $the_ID = null ) {
		if ( ! isset( $the_ID ) ) {
			$the_ID = get_the_ID();
		} else {
			$the_ID = absint( $the_ID );
		}

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U', $the_ID ) !== get_the_modified_time( 'U', $the_ID ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C, $the_ID ) ),
			esc_html( get_the_date( '', $the_ID ) ),
			esc_attr( get_the_modified_date( DATE_W3C, $the_ID ) ),
			esc_html( get_the_modified_date( '', $the_ID ) )
		);

		// Post date.
		$posted_on = sprintf( '<span class="prefix">%1$s </span>%2$s',
			esc_html_x( 'Posted on', 'post date', 'avdeez' ),
			$time_string
		);

		// Author of the post.
		$the_author_id = get_post_field( 'post_author', $the_ID );
		$the_author    = '<a href="' . esc_url( get_author_posts_url( $the_author_id ) ) . '" rel="author">' . esc_html( get_the_author_meta( 'display_name', $the_author_id ) ) . '</a>';
		$byline        = sprintf(
			'<span class="prefix">%1$s </span>%2$s',
			esc_html_x( 'by', 'post author', 'avdeez' ),
			apply_filters( 'avdeez_entry_author', $the_author )
		);

		echo '<span class="posted-on th-inline-block">' . $posted_on . '</span><span class="byline th-inline-block"> ' . $byline . '</span>'; // WPCS: XSS OK.
	}
endif;

if ( ! function_exists( 'avdeez_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function avdeez_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'avdeez' ) );
			if ( $categories_list && avdeez_categorized_blog() ) {
				printf( '<div class="cat-links has-tiny-font-size th-font-headings th-uppercase th-links-inherit th-tracking-wide"><span class="links-title has-secondary-dark-color th-tracking-widest">%1$s</span>%2$s</div>', esc_html__( 'This entry was posted in:', 'avdeez' ), $categories_list );
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'avdeez' ) );
			if ( $tags_list && ! is_wp_error( $tags_list ) ) {
				printf( '<div class="tags-links has-tiny-font-size th-font-headings th-uppercase th-links-inherit th-tracking-wide"><span class="links-title has-secondary-dark-color th-tracking-widest">%1$s</span>%2$s</div>', esc_html__( 'Tagged with:', 'avdeez' ), $tags_list );
			}
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'avdeez' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<div class="edit-link has-small-font-size th-font-headings th-uppercase th-font-bold">',
			'</div>'
		);
	}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function avdeez_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'avdeez_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'avdeez_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so avdeez_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so avdeez_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in avdeez_categorized_blog.
 */
function avdeez_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'avdeez_categories' );
}
add_action( 'edit_category', 'avdeez_category_transient_flusher' );
add_action( 'save_post',     'avdeez_category_transient_flusher' );

if ( ! function_exists( 'avdeez_entry_comments' ) ) :
	/**
	 * Prints HTML with meta information for the comments.
	 */
	function avdeez_entry_comments() {
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			$screen_reader_text = '<span class="screen-reader-text"> ' . sprintf(
				/* translators: 1: comment count number, 2: title. */
				esc_html( _nx( 'comment on &ldquo;%s&rdquo;', 'comments on &ldquo;%s&rdquo;', get_comments_number(), 'comments link', 'avdeez' ) ),
				get_the_title()
			) . '</span>';

			echo '<span class="comments-link th-inline-flex th-items-center th-text-2xs th-leading-none">' . avdeez_get_icon_svg( 'message-circle' );
			comments_popup_link(
				'0' . $screen_reader_text,
				'1' . $screen_reader_text,
				'%' . $screen_reader_text
			);
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 * Trigger wp_body_open action immediately after the opening body tag.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;

/**
 * Prints class attribute for the primary header section.
 *
 * @param $args Extra arguments to retrieve the page header classes.
 */
function avdeez_primary_header_classes( $args = null ) {
	$defaults = array(
		'has_post_thumbnail' => false,
		'template'			 => '',
	);

	if ( empty( $args ) ) {
        $args = array();
	}

	$args = wp_parse_args( $args, $defaults );

	// Default classes.
	$classes = array(
		'alignfull fullscreen-header',
		'wp-block-cover',
		'th-py-medium th-mb-0',
	);

	if ( '' === $args['template'] ) {
		$classes[] = 'entry-header th-uppercase th-tracking-wide th-text-center th-justify-center th-flex-col';
	}

	// Add classes needed for a grid template.
	if ( 'grid-page' === $args['template'] ) {
		$classes[] = 'page-header-grid th-space-y-base md:th-flex-row th-flex-col md:th-items-end th-p-base th-justify-center';
	}

	// Add class if current post has a featured image.
	if ( $args['has_post_thumbnail'] ) {
		$classes[] = 'has-background-dim has-primary-dark-background-color has-white-color has-background';

		if ( '' === $args['template'] ) {
			$classes[] = 'th-min-h-screen';
		}

	} else {
		$classes[] = 'has-secondary-light-background-color';
	}

	printf( 'class="%s"', join( ' ', $classes ) );
}

if ( ! function_exists( 'avdeez_the_custom_logo' ) ) :
	/**
	 * It will be removed in the future versions.
	 */
	function avdeez_the_custom_logo() {
		return;
	}
endif;

if ( ! function_exists( 'avdeez_entry_author_bio' ) ) :
	/**
	 * It will be removed in the future versions.
	 */
	function avdeez_entry_author_bio() {
		return;
	}
endif;

if ( ! function_exists( 'avdeez_get_author_bio_links' ) ) :
	/**
	 * Outputs the author links in the biography section.
	 *
	 * @param int $author_id The author id (optional).
	 */
	function avdeez_get_author_bio_links( $author_id = null ) {
		// Author's ID.
		if ( ! isset( $author_id ) ) {
			$author_id = get_the_author_meta( 'ID' );
		}

		$links = array(
			'home'       => get_the_author_meta( 'user_url', $author_id ),
			'twitter'    => get_the_author_meta( 'twitter', $author_id ),
			'facebook'   => get_the_author_meta( 'facebook', $author_id ),
			'instagram'  => get_the_author_meta( 'instagram', $author_id ),
			'linkedin'   => get_the_author_meta( 'linkedin', $author_id ),
			'pinterest'  => get_the_author_meta( 'pinterest', $author_id ),
			'myspace'    => get_the_author_meta( 'myspace', $author_id ),
			'soundcloud' => get_the_author_meta( 'soundcloud', $author_id ),
			'tumblr'     => get_the_author_meta( 'tumblr', $author_id ),
			'youtube'    => get_the_author_meta( 'youtube', $author_id ),
			'wikipedia'  => get_the_author_meta( 'wikipedia', $author_id ),
		);

		$links = array_filter( $links );

		if ( empty( $links ) ) {
			return;
		}

		$author_links = '<ul class="author-links th-flex th-flex-wrap th-gap-small th-links-inherit th-list-none th-leading-none th-mb-0 th-ml-0 th-p-0">';

		foreach ( $links as $key => $bio_link ) :
			switch ( $key ) :
				case 'twitter':
					$screen_reader_text = esc_html_x( 'Twitter profile', 'author links', 'avdeez' );
					$bio_link           = 'https://twitter.com/' . $bio_link;
					break;
				case 'facebook':
					$screen_reader_text = esc_html_x( 'Facebook profile', 'author links', 'avdeez' );
					break;
				case 'instagram':
					$screen_reader_text = esc_html_x( 'Instagram profile', 'author links', 'avdeez' );
					break;
				case 'linkedin':
					$screen_reader_text = esc_html_x( 'LinkedIn profile', 'author links', 'avdeez' );
					break;
				case 'pinterest':
					$screen_reader_text = esc_html_x( 'Pinterest profile', 'author links', 'avdeez' );
					break;
				case 'myspace':
					$screen_reader_text = esc_html_x( 'MySpace profile', 'author links', 'avdeez' );
					break;
				case 'soundcloud':
					$screen_reader_text = esc_html_x( 'SoundCloud profile', 'author links', 'avdeez' );
					break;
				case 'tumblr':
					$screen_reader_text = esc_html_x( 'Tumblr profile', 'author links', 'avdeez' );
					break;
				case 'youtube':
					$screen_reader_text = esc_html_x( 'YouTube profile', 'author links', 'avdeez' );
					break;
				case 'wikipedia':
					$screen_reader_text = esc_html_x( 'Wikipedia page', 'author links', 'avdeez' );
					break;
				case 'home':
					$screen_reader_text = esc_html_x( 'Personal website', 'author links', 'avdeez' );
					break;
			endswitch;

			$author_links .= sprintf(
				'<li class="th-inline-block has-primary-light-color th-mr-sm"><a href="%1$s" target="_blank" rel="nofollow">%2$s<span class="screen-reader-text">%3$s</span></a></li>',
				esc_url( $bio_link ),
				avdeez_get_social_icon_svg( $key, 16 ),
				esc_html( $screen_reader_text )
			);

		endforeach;

		$author_links .= '</ul><!-- .author-links -->';

		return $author_links;
	}
endif;

if ( ! function_exists( 'avdeez_has_author_bio' ) ) :
	/**
	 * Check if the user wants to display the author bio section.
	 */
	function avdeez_has_author_bio() {
		return apply_filters( 'avdeez_has_author_bio', true );
	}
endif;

if ( ! function_exists( 'avdeez_author_bio' ) ) :
	/**
	 * Outputs the author biography.
	 */
	function avdeez_author_bio() {
		if ( ! avdeez_has_author_bio() ) {
			return;
		}

		echo apply_filters( 'avdeez_author_bio', avdeez_get_author_bio() );
	}
endif;

if ( ! function_exists( 'avdeez_get_author_bio' ) ) :
	/**
	 * Returns the author biography.
	 *
	 * @param int $author_id The author id (optional).
	 */
	function avdeez_get_author_bio( $author_id = null ) {
		// Author's ID.
		if ( ! isset( $author_id ) ) {
			$author_id = get_the_author_meta( 'ID' );
		}
		// Display name of the current author.
		$the_author = get_the_author_meta( 'display_name', $author_id );
		// Biography of the current author.
		$the_author_bio = get_the_author_meta( 'description', $author_id );
		// Profile picture of the current author.
		$the_author_picture = get_avatar( get_the_author_meta( 'user_email', $author_id ), apply_filters( 'avdeez_author_bio_avatar_size', 192 ) );
		// Link to all posts of the current author.
		$the_author_link = get_author_posts_url( $author_id );
		// Title prefix which is displayed before the author name.
		$heading_prefix = sprintf(
			'<span class="th-block has-tiny-font-size has-secondary-dark-color th-tracking-widest">%s</span>',
			esc_html__( 'About the author', 'avdeez' )
		);
		// Display name of the current author along with a link to the author's archive.
		$the_author_name = sprintf(
			'<a class="th-inline-block has-normal-font-size" href="%1$s" rel="author">%2$s</a>',
			esc_url( $the_author_link ),
			esc_html( $the_author )
		);

		if ( $the_author_bio ) {
			$the_author_bio = '<p class="author-bio-content th-mb-0">' . $the_author_bio . '</p>';
		}

		// Link's label.
		$more_link_label = sprintf( esc_html__( 'View all posts by %s', 'avdeez' ), $the_author );
		// Author's arhive link.
		$more_link = sprintf(
			'<p class="author-link-container has-secondary-dark-color has-tiny-font-size th-font-headings th-uppercase th-links-inherit"><a class="author-link" href="%1$s" rel="author">%2$s</a></p>',
			esc_url( $the_author_link ),
			$more_link_label
		);

		// Add the more link to the author bio.
		$the_author_bio .= $more_link;
		// Add the social links to the author bio.
		$the_author_bio .= avdeez_get_author_bio_links( $author_id );

		return sprintf(
			'
			<div class="entry-author sm:th-flex">
				<div class="author-avatar th-rounded">%1$s</div>
				<div class="author-bio th-self-center sm:th-ml-lg th-space-y-small">
					<p class="author-name th-flex th-flex-col th-font-headings th-my-0 th-links-inherit th-uppercase">%2$s %3$s</p>
					%4$s
				</div>
			</div>
			',
			$the_author_picture,
			$heading_prefix,
			$the_author_name,
			$the_author_bio
		);
	}
endif;

if ( ! function_exists( 'avdeez_footer_output' ) ):
	/**
	 * Custom Text for the Footer Section
	 */
	function avdeez_footer_output() {
		$custom_text = get_theme_mod( 'avdeez_footer_custom_text', '' );

		if ( '' !== $custom_text ) :
			echo $custom_text;
		else :
		?>

			<a href="<?php echo esc_url( 'http://wordpress.org/' ); ?>"><?php printf( __( 'Proudly powered by %s', 'avdeez' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'avdeez' ), 'Hermes', '<a href="https://themesharbor.com/" rel="nofollow">Themes Harbor</a>' ); ?>

		<?php
		endif;
	}
endif;
