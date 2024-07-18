<?php
// Content negotiation support for Surge
// via: https://dominikschilling.de/notes/http-accept-header-wordpress-cache-activitypub/
$representation = 'html'; // Or 'generic'.
if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
	$accept = strtolower( $_SERVER['HTTP_ACCEPT'] );

	if ( str_contains( $accept, 'text/html' ) ) {
		$representation = 'html';
	} elseif (
		str_contains( $accept, 'application/json' ) ||
		str_contains( $accept, 'application/activity+json' ) ||
		str_contains( $accept, 'application/ld+json' )
	) {
		$representation = 'json';
	}
}
$config['variants']['representation'] = $representation;
unset( $accept, $representation );

return $config;