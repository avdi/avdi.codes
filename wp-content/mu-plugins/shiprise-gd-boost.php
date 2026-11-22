<?php
/**
 * Plugin Name: ShipRise Graceful.Dev boosts (packet FEDI-BOOST-GD-2)
 * Description: @avdi@avdi.codes boosts (ActivityPub Announce) what @gracefuldev@mastodon.social publishes: its own posts, and replies to itself, never boosts or replies to others. Mode option shiprise_gd_boost_mode: off | shadow | live. Sibling of shiprise-gm-boost.php. Site card: shibizops operations/sites/avdi-codes.md.
 */

namespace ShipRise\GracefulDevBoost;

const SOURCE_HOST        = 'mastodon.social';
const SOURCE_ACCOUNT_ID  = '109972478778031888';
const SOURCE_HANDLE      = 'gracefuldev';
const BOOSTING_USER_ID   = 1;
const MODE_OPTION        = 'shiprise_gd_boost_mode';
const STATE_OPTION       = 'shiprise_gd_boost_state';
const LOCK_OPTION        = 'shiprise_gd_boost_lock';
const CRON_HOOK          = 'shiprise_gd_boost_poll';
const CRON_INTERVAL      = 'shiprise_gd_ten_minutes';
const LOOKBACK_SECONDS   = 172800;
const LOCK_STALE_SECONDS = 300;
const MAX_ATTEMPTS       = 5;
const LOG_LENGTH         = 30;
const PAGE_SIZE          = 40;
const PUBLIC_AUDIENCE    = 'https://www.w3.org/ns/activitystreams#Public';

\add_filter(
	'cron_schedules',
	function ( $schedules ) {
		$schedules[ CRON_INTERVAL ] = array(
			'interval' => 600,
			'display'  => 'Every ten minutes (ShipRise Graceful.Dev boosts)',
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
	\error_log( '[shiprise-gd-boost] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
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

function fetch_json( $url ) {
	$response = \wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 15,
			'redirection' => 2,
			'headers'     => array( 'Accept' => 'application/json' ),
		)
	);
	if ( \is_wp_error( $response ) ) {
		return $response;
	}
	$status = \wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status ) {
		return new \WP_Error( 'gd_http_status', "HTTP $status from $url" );
	}
	$body = \json_decode( \wp_remote_retrieve_body( $response ), true );
	return \is_array( $body ) ? $body : new \WP_Error( 'gd_bad_json', "Unparseable JSON from $url" );
}

/**
 * True for the account's own original posts and its replies to itself; false for boosts, replies to
 * anyone else, non-public posts, and anything whose uri is not one of the account's statuses.
 */
function boostable( array $status ) {
	$uri_path = (string) \wp_parse_url( $status['uri'] ?? '', PHP_URL_PATH );
	return empty( $status['reblog'] )
		&& 'public' === ( $status['visibility'] ?? '' )
		&& ( empty( $status['in_reply_to_id'] ) || SOURCE_ACCOUNT_ID === (string) ( $status['in_reply_to_account_id'] ?? '' ) )
		&& SOURCE_ACCOUNT_ID === (string) ( $status['account']['id'] ?? '' )
		&& SOURCE_HOST === \wp_parse_url( $status['uri'] ?? '', PHP_URL_HOST )
		&& 0 === \strpos( $uri_path, '/users/' . SOURCE_HANDLE . '/statuses/' );
}

/**
 * Boostable statuses created strictly after $after_gmt, oldest first.
 */
function recent_statuses( $after_gmt ) {
	$url      = 'https://' . SOURCE_HOST . '/api/v1/accounts/' . SOURCE_ACCOUNT_ID . '/statuses?limit=' . PAGE_SIZE;
	$statuses = fetch_json( $url );
	if ( \is_wp_error( $statuses ) ) {
		return $statuses;
	}
	$after = \strtotime( $after_gmt . ' UTC' );
	$fresh = \array_filter(
		$statuses,
		function ( $status ) use ( $after ) {
			return \is_array( $status )
				&& boostable( $status )
				&& \strtotime( $status['created_at'] ?? '' ) > $after;
		}
	);
	\usort(
		$fresh,
		function ( $a, $b ) {
			return \strtotime( $a['created_at'] ) <=> \strtotime( $b['created_at'] );
		}
	);
	return $fresh;
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
	return $outbox_id ? (int) $outbox_id : new \WP_Error( 'gd_outbox_refused', "add_to_outbox refused $object_id" );
}

/**
 * Boost one status (as returned by the Mastodon API) and record the outcome in $state.
 */
function boost( array $status, array &$state ) {
	$key    = (string) $status['id'];
	$record = $state['posts'][ $key ] ?? array( 'attempts' => 0 );
	if ( 'boosted' === ( $record['status'] ?? '' ) ) {
		return;
	}
	++$record['attempts'];

	$outbox_id = announce( $status['uri'] );

	if ( \is_wp_error( $outbox_id ) ) {
		$record['status'] = 'failed';
		note( $state, "boost failed for {$status['uri']} (attempt {$record['attempts']}): " . $outbox_id->get_error_message() );
	} else {
		$record['status']    = 'boosted';
		$record['object']    = $status['uri'];
		$record['outbox_id'] = $outbox_id;
		$record['at']        = \gmdate( 'c' );
		note( $state, "boosted {$status['uri']} as outbox item $outbox_id" );
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
	$statuses  = recent_statuses( $after_gmt );

	if ( \is_wp_error( $statuses ) ) {
		note( $state, 'poll failed: ' . $statuses->get_error_message() );
	} else {
		foreach ( $statuses as $status ) {
			$record = $state['posts'][ (string) $status['id'] ] ?? array();
			if ( 'boosted' === ( $record['status'] ?? '' ) || ( $record['attempts'] ?? 0 ) >= MAX_ATTEMPTS ) {
				continue;
			}
			if ( 'shadow' === $mode ) {
				if ( empty( $record ) ) {
					$state['posts'][ (string) $status['id'] ] = array( 'status' => 'shadowed', 'attempts' => 0 );
					note( $state, "shadow: would boost {$status['uri']}" );
				}
				continue;
			}
			boost( $status, $state );
		}
	}
	save_state( $state );
	release_lock();
}

if ( \defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Graceful.Dev boosts: status, mode, run.
	 */
	\WP_CLI::add_command(
		'gd-boost',
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
			 * List what the next poll would act on, with no state change.
			 */
			public function candidates() {
				$state    = state();
				$after    = \max( $state['since_gmt'], \gmdate( 'Y-m-d H:i:s', \time() - LOOKBACK_SECONDS ) );
				$statuses = recent_statuses( $after );
				if ( \is_wp_error( $statuses ) ) {
					\WP_CLI::error( $statuses->get_error_message() );
				}
				foreach ( $statuses as $status ) {
					\WP_CLI::line( $status['created_at'] . ' ' . $status['uri'] );
				}
				\WP_CLI::line( \count( $statuses ) . ' candidate(s) after ' . $after . ' UTC' );
			}
		}
	);
}
