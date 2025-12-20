<?php
/**
 * Co-Authors Plus Compatibility File.
 *
 * @link https://wordpress.org/plugins/co-authors-plus/
 *
 * @package Hermes
 */

/**
 * Display multiple authors (if needed) in arhive views.
 */
function hermes_coauthors_plus_entry_author( $author ) {
    return coauthors__echo('coauthors_posts_links_single', 'callback', array(
        'between' => ', ',
        'betweenLast' => ', ',
        'before' => '',
        'after' => ''
    ), null, false );
}
add_filter( 'hermes_entry_author', 'hermes_coauthors_plus_entry_author' );

/**
 * Display multiple authors (if needed) in the author bio section in single views.
 */
function hermes_coauthors_plus_author_bio( $author_bio ) {
    $coauthors = get_coauthors();

    if ( empty( $coauthors ) ) {
        return $author_bio;
    }

    $author_bio = '';

    foreach ( $coauthors as $coauthor ) {
        $author_bio .= hermes_get_author_bio( $coauthor->ID );
    }

    return $author_bio;
}
add_filter( 'hermes_author_bio', 'hermes_coauthors_plus_author_bio' );
