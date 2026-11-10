<?php
/**
 * Plugin Name: ShipRise Good Morning boosts (packet FED)
 * Description: @avdi@avdi.codes boosts (ActivityPub Announce) avdi.org posts tagged "goodmorning", and nothing else. Mode option shiprise_gm_boost_mode: off | shadow | live. Site card: shibizops operations/sites/avdi-codes.md.
 */

namespace ShipRise\GoodMorningBoost;

const SOURCE_SITE        = 'https://avdi.org';
const SOURCE_HOST        = 'avdi.org';
const TAG_SLUG           = 'goodmorning';
const BOOSTING_USER_ID   = 1;
const MODE_OPTION        = 'shiprise_gm_boost_mode';
const STATE_OPTION       = 'shiprise_gm_boost_state';
const LOCK_OPTION        = 'shiprise_gm_boost_lock';
const CRON_HOOK          = 'shiprise_gm_boost_poll';
const CRON_INTERVAL      = 'shiprise_ten_minutes';
const LOOKBACK_SECONDS   = 172800;
const LOCK_STALE_SECONDS = 300;
const MAX_ATTEMPTS       = 5;
const LOG_LENGTH         = 30;
const PUBLIC_AUDIENCE    = 'https://www.w3.org/ns/activitystreams#Public';

\add_filter(
	'cron_schedules',
	function ( $schedules ) {
		$schedules[ CRON_INTERVAL ] = array(
			'interval' => 600,
			'display'  => 'Every ten minutes (ShipRise Good Morning boosts)',
		);
		return $schedules;
	}
);

\add_action( CRON_HOOK, __NAMESPACE__ . '\\poll' );

\add_action(
	'init',
	function () {
		if ( ! \wp_next_scheduled( CRON_HOOK ) ) {
			\wp_schedule_event( \time() + 60, CRON_INTERVAL, CRON_HOOK );
		}
	}
);

function mode() {
	$mode = \get_option( MODE_OPTION, 'off' );
	return \in_array( $mode, array( 'off', 'shadow', 'live' ), true ) ? $mode : 'off';
}

function state() {
	$state = \get_option( STATE_OPTION );
	if ( ! \is_array( $state ) ) {
		$state = array(
			'since_gmt' => \gmdate( 'Y-m-d H:i:s' ),
			'posts'     => array(),
			'log'       => array(),
		);
	}
	return $state;
}

function save_state( $state ) {
	$state['log'] = \array_slice( $state['log'], -LOG_LENGTH );
	\update_option( STATE_OPTION, $state, false );
}

function note( &$state, $message ) {
	$line           = \gmdate( 'c' ) . ' ' . $message;
	$state['log'][] = $line;
	\error_log( '[shiprise-gm-boost] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

function acquire_lock() {
	if ( \add_option( LOCK_OPTION, \time(), '', false ) ) {
		return true;
	}
	$held_since = (int) \get_option( LOCK_OPTION );
	if ( \time() - $held_since > LOCK_STALE_SECONDS ) {
		\update_option( LOCK_OPTION, \time(), false );
		return true;
	}
	return false;
}

function release_lock() {
	\delete_option( LOCK_OPTION );
}

function fetch_json( $url, $accept = 'application/json' ) {
	$response = \wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 15,
			'redirection' => 2,
			'headers'     => array( 'Accept' => $accept ),
		)
	);
	if ( \is_wp_error( $response ) ) {
		return $response;
	}
	$status = \wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status ) {
		return new \WP_Error( 'gm_http_status', "HTTP $status from $url" );
	}
	$body = \json_decode( \wp_remote_retrieve_body( $response ), true );
	return \is_array( $body ) ? $body : new \WP_Error( 'gm_bad_json', "Unparseable JSON from $url" );
}

function good_morning_tag_id() {
	$tags = fetch_json( SOURCE_SITE . '/wp-json/wp/v2/tags?slug=' . TAG_SLUG . '&_fields=id,slug' );
	if ( \is_wp_error( $tags ) ) {
		return $tags;
	}
	foreach ( $tags as $tag ) {
		if ( TAG_SLUG === ( $tag['slug'] ?? '' ) ) {
			return (int) $tag['id'];
		}
	}
	return new \WP_Error( 'gm_no_tag', 'Tag ' . TAG_SLUG . ' not found on ' . SOURCE_SITE );
}

/**
 * Published avdi.org posts carrying the goodmorning tag, published strictly after $after_gmt.
 */
function recent_good_morning_posts( $after_gmt ) {
	$tag_id = good_morning_tag_id();
	if ( \is_wp_error( $tag_id ) ) {
		return $tag_id;
	}
	$query = \http_build_query(
		array(
			'tags'     => $tag_id,
			'after'    => \gmdate( 'Y-m-d\TH:i:s', \strtotime( $after_gmt . ' UTC' ) - DAY_IN_SECONDS ),
			'per_page' => 20,
			'orderby'  => 'date',
			'order'    => 'asc',
			'_fields'  => 'id,date_gmt,link,tags,status',
			// avdi.org's Varnish caches REST lists and purges only post URLs on save; a new key per poll slot reads fresh.
			'_gm_slot' => (int) \floor( \time() / 600 ),
		)
	);
	$posts = fetch_json( SOURCE_SITE . '/wp-json/wp/v2/posts?' . $query );
	if ( \is_wp_error( $posts ) ) {
		return $posts;
	}
	return \array_values(
		\array_filter(
			$posts,
			function ( $post ) use ( $tag_id, $after_gmt ) {
				return 'publish' === ( $post['status'] ?? '' )
					&& \in_array( $tag_id, $post['tags'] ?? array(), true )
					&& \strtotime( $post['date_gmt'] . ' UTC' ) > \strtotime( $after_gmt . ' UTC' )
					&& SOURCE_HOST === \wp_parse_url( $post['link'] ?? '', PHP_URL_HOST );
			}
		)
	);
}

/**
 * The post's public ActivityPub object as avdi.org serves it, or a WP_Error.
 */
function public_activity_object( $permalink ) {
	$object = fetch_json( $permalink, 'application/activity+json' );
	if ( \is_wp_error( $object ) ) {
		return $object;
	}
	$id       = $object['id'] ?? '';
	$audience = \array_merge( (array) ( $object['to'] ?? array() ), (array) ( $object['cc'] ?? array() ) );
	if ( SOURCE_HOST !== \wp_parse_url( $id, PHP_URL_HOST ) ) {
		return new \WP_Error( 'gm_foreign_object', "Object id $id is not on " . SOURCE_HOST );
	}
	if ( ! \in_array( PUBLIC_AUDIENCE, $audience, true ) ) {
		return new \WP_Error( 'gm_not_public', "Object $id is not addressed to Public" );
	}
	return $object;
}

function announce( $object_id ) {
	$existing = \Activitypub\Collection\Outbox::get_by_object_id( $object_id, 'Announce' );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$actor = \Activitypub\Collection\Actors::get_by_id( BOOSTING_USER_ID );
	if ( \is_wp_error( $actor ) ) {
		return $actor;
	}
	$activity = new \Activitypub\Activity\Activity();
	$activity->set_type( 'Announce' );
	$activity->set_actor( $actor->get_id() );
	$activity->set_object( $object_id );
	$activity->set_to( array( PUBLIC_AUDIENCE ) );
	$activity->set_cc( array( $actor->get_followers() ) );
	$activity->set_published( \gmdate( 'Y-m-d\TH:i:s\Z' ) );

	$outbox_id = \Activitypub\add_to_outbox( $activity, null, BOOSTING_USER_ID, ACTIVITYPUB_CONTENT_VISIBILITY_PUBLIC );
	return $outbox_id ? (int) $outbox_id : new \WP_Error( 'gm_outbox_refused', "add_to_outbox refused $object_id" );
}

/**
 * Boost one source post (as returned by the REST API) and record the outcome in $state.
 */
function boost( array $post, array &$state ) {
	$key    = (string) $post['id'];
	$record = $state['posts'][ $key ] ?? array( 'attempts' => 0 );
	if ( 'boosted' === ( $record['status'] ?? '' ) ) {
		return;
	}
	++$record['attempts'];

	$object    = public_activity_object( $post['link'] );
	$outbox_id = \is_wp_error( $object ) ? $object : announce( $object['id'] );

	if ( \is_wp_error( $outbox_id ) ) {
		$record['status'] = 'failed';
		note( $state, "boost failed for {$post['link']} (attempt {$record['attempts']}): " . $outbox_id->get_error_message() );
	} else {
		$record['status']    = 'boosted';
		$record['object']    = $object['id'];
		$record['outbox_id'] = $outbox_id;
		$record['at']        = \gmdate( 'c' );
		note( $state, "boosted {$post['link']} ({$object['id']}) as outbox item $outbox_id" );
	}
	$state['posts'][ $key ] = $record;
}

function poll() {
	$mode = mode();
	if ( 'off' === $mode || ! \function_exists( 'Activitypub\\add_to_outbox' ) ) {
		return;
	}
	if ( ! acquire_lock() ) {
		return;
	}
	$state     = state();
	$after_gmt = \max( $state['since_gmt'], \gmdate( 'Y-m-d H:i:s', \time() - LOOKBACK_SECONDS ) );
	$posts     = recent_good_morning_posts( $after_gmt );

	if ( \is_wp_error( $posts ) ) {
		note( $state, 'poll failed: ' . $posts->get_error_message() );
	} else {
		foreach ( $posts as $post ) {
			$record = $state['posts'][ (string) $post['id'] ] ?? array();
			if ( 'boosted' === ( $record['status'] ?? '' ) || ( $record['attempts'] ?? 0 ) >= MAX_ATTEMPTS ) {
				continue;
			}
			if ( 'shadow' === $mode ) {
				if ( empty( $record ) ) {
					$state['posts'][ (string) $post['id'] ] = array( 'status' => 'shadowed', 'attempts' => 0 );
					note( $state, "shadow: would boost {$post['link']}" );
				}
				continue;
			}
			boost( $post, $state );
		}
	}
	save_state( $state );
	release_lock();
}

if ( \defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Good Morning boosts: status, mode, run, canary.
	 */
	\WP_CLI::add_command(
		'gm-boost',
		new class() {
			/**
			 * Show the mode, watermark, recorded posts, and recent log.
			 */
			public function status() {
				\WP_CLI::line( 'mode: ' . mode() );
				\WP_CLI::line( \wp_json_encode( state(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
			}

			/**
			 * Set the mode. Leaving off moves the watermark to now, so nothing published earlier is boosted;
			 * shadow to live keeps it, so live boosts what shadow saw.
			 *
			 * <mode>
			 * : off, shadow, or live.
			 */
			public function mode( $args ) {
				$new_mode = $args[0];
				if ( ! \in_array( $new_mode, array( 'off', 'shadow', 'live' ), true ) ) {
					\WP_CLI::error( 'Mode is off, shadow, or live.' );
				}
				$state = state();
				if ( 'off' === mode() && 'off' !== $new_mode ) {
					$state['since_gmt'] = \gmdate( 'Y-m-d H:i:s' );
				}
				note( $state, 'mode ' . mode() . " -> $new_mode" );
				save_state( $state );
				\update_option( MODE_OPTION, $new_mode, true );
				\WP_CLI::success( "mode $new_mode, watermark {$state['since_gmt']} UTC" );
			}

			/**
			 * Run one poll now, in the current mode.
			 */
			public function run() {
				poll();
				$this->status();
			}

			/**
			 * Boost one named avdi.org post, ignoring the watermark. It must carry the goodmorning tag.
			 *
			 * <post-id>
			 * : The avdi.org post ID.
			 */
			public function canary( $args ) {
				$tag_id = good_morning_tag_id();
				$post   = \is_wp_error( $tag_id ) ? $tag_id : fetch_json( SOURCE_SITE . '/wp-json/wp/v2/posts/' . \absint( $args[0] ) . '?_fields=id,date_gmt,link,tags,status' );
				if ( \is_wp_error( $post ) ) {
					\WP_CLI::error( $post->get_error_message() );
				}
				if ( 'publish' !== ( $post['status'] ?? '' ) || ! \in_array( $tag_id, $post['tags'] ?? array(), true ) ) {
					\WP_CLI::error( "Post {$args[0]} is not a published goodmorning post; refusing." );
				}
				$state = state();
				boost( $post, $state );
				save_state( $state );
				\WP_CLI::line( \wp_json_encode( $state['posts'][ (string) $post['id'] ], JSON_UNESCAPED_SLASHES ) );
			}
		}
	);
}
