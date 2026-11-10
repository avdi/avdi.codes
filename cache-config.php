<?php
// A horrid kludge for the fact that Cloudways disables fpassthru()
if (!function_exists('fpassthru')) {
	function fpassthru($stream) {
		echo stream_get_contents($stream);
	}
}

// Content negotiation support for Surge.
// Copied from the ActivityPub plugin 9.4.0, includes/functions-request.php,
// Activitypub\accept_prefers_activitypub() (see also integration/surge-cache-config.php).
// A q-value-aware replacement for the 2023 snippet at
// https://dominikschilling.de/notes/http-accept-header-wordpress-cache-activitypub/ which put
// Mastodon's "..., text/html;q=0.1" into the HTML bucket. Copied rather than required so the
// config survives plugin removal; renamed and guarded because Surge loads this file more than once.
if ( ! function_exists( 'surge_accept_prefers_activitypub' ) ) {
function surge_accept_prefers_activitypub( $accept ) {
	$winner_quality = 0.0;
	$winner_is_ap   = false;

	foreach ( \explode( ',', (string) $accept ) as $part ) {
		$segments   = \explode( ';', $part );
		$media_type = \strtolower( \trim( (string) \array_shift( $segments ) ) );

		if ( '' === $media_type ) {
			continue;
		}

		// Read the quality (default 1.0) and profile parameters, in any order.
		$quality = 1.0;
		$profile = '';
		foreach ( $segments as $param ) {
			$param = \trim( $param );
			if ( 0 === \stripos( $param, 'q=' ) ) {
				// Only a valid number sets the quality; a malformed `q=` keeps the 1.0 default.
				$q_value = \trim( \substr( $param, 2 ) );
				if ( \is_numeric( $q_value ) ) {
					$quality = (float) $q_value;
				}
			} elseif ( 0 === \stripos( $param, 'profile=' ) ) {
				$profile = \strtolower( \trim( \substr( $param, 8 ), '"' ) );
			}
		}

		// A `q=0` means the client refuses this type; ignore it entirely.
		if ( $quality <= 0 ) {
			continue;
		}

		// Highest quality wins; on a tie the earlier type in the header keeps the lead.
		if ( $quality > $winner_quality ) {
			$winner_quality = $quality;

			// ActivityPub is `application/activity+json`, or `application/ld+json` with the AS2 profile
			// (matched without the scheme so both the http and https profile URIs are accepted).
			$winner_is_ap = 'application/activity+json' === $media_type
				|| ( 'application/ld+json' === $media_type && false !== \strpos( $profile, '://www.w3.org/ns/activitystreams' ) );
		}
	}

	return $winner_is_ap;
}
}

$representation = 'html';
if ( isset( $_SERVER['HTTP_ACCEPT'] ) && surge_accept_prefers_activitypub( $_SERVER['HTTP_ACCEPT'] ) ) {
	$representation = 'json';
}
$config['variants']['representation'] = $representation;
unset( $representation );

$config['ignore_cookies'][] = 'wpf_ref';

return $config;