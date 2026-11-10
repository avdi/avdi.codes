<?php
/**
 * Plugin Name: ShipRise Thrive to FluentCRM bridge
 * Description: Thrive forms on this site subscribe people into the FluentCRM on shiprise.com through its incoming webhook.
 *
 * Thrive's own "fluentcrm" connection needs FluentCRM installed on the same site.
 * This site has none, so the connection class is swapped for a subclass that posts
 * the signup to shiprise.com. Form settings (list ids, double opt-in) are untouched:
 * the list ids on avdi.codes are the same ids as on shiprise.com, except the
 * ones in SHIPRISE_THRIVE_BRIDGE_LIST_MAP.
 *
 * Inert until option shiprise_thrive_fcrm_webhook holds the webhook URL.
 * Webhook status must be "pending" (double opt-in); forms asking for single
 * opt-in are refused, never silently downgraded or upgraded.
 *
 * Lists that do not exist on shiprise.com are refused and logged, so Thrive keeps
 * the signup in its API error log instead of FluentCRM creating a contact on no list.
 *
 * Remove: delete this file. Thrive then reports "FluentCRM plugin is not installed"
 * as before.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SHIPRISE_THRIVE_BRIDGE_OPTION = 'shiprise_thrive_fcrm_webhook';

// Lists that do not exist on shiprise.com. Currently none.
const SHIPRISE_THRIVE_BRIDGE_LISTS_ABSENT_ON_SHIPRISE = array();

// DevSeeds (avdi.codes list 11, shortcode 16631) became the Code and Grace
// newsletter, shiprise.com list 2 (Avdi, 2026-11-10).
const SHIPRISE_THRIVE_BRIDGE_DEVSEEDS_LIST = 11;
const SHIPRISE_THRIVE_BRIDGE_CODE_AND_GRACE_LIST = 2;
const SHIPRISE_THRIVE_BRIDGE_LIST_MAP = array(
	SHIPRISE_THRIVE_BRIDGE_DEVSEEDS_LIST => SHIPRISE_THRIVE_BRIDGE_CODE_AND_GRACE_LIST,
);

function shiprise_thrive_bridge_log( $message ) {
	error_log( '[shiprise-thrive-bridge] ' . $message );
}

// The shiprise.com list id for a Thrive list id; unmapped lists share ids.
function shiprise_thrive_bridge_target_list( $thrive_list_id ) {
	$thrive_list_id = (int) $thrive_list_id;
	return SHIPRISE_THRIVE_BRIDGE_LIST_MAP[ $thrive_list_id ] ?? $thrive_list_id;
}

function shiprise_thrive_bridge_webhook_url() {
	$url = get_option( SHIPRISE_THRIVE_BRIDGE_OPTION );
	return is_string( $url ) && 0 === strpos( $url, 'https://' ) ? $url : '';
}

function shiprise_thrive_bridge_post( $list_id, $arguments ) {
	if ( empty( $arguments['email'] ) || ! is_email( $arguments['email'] ) ) {
		return 'Email is not valid';
	}
	if ( 'd' !== ( $arguments['fluentcrm_optin'] ?? '' ) ) {
		return 'The bridge only supports double opt-in';
	}
	$list_id = shiprise_thrive_bridge_target_list( $list_id );
	if ( in_array( $list_id, SHIPRISE_THRIVE_BRIDGE_LISTS_ABSENT_ON_SHIPRISE, true ) ) {
		shiprise_thrive_bridge_log( 'list ' . (int) $list_id . ' does not exist on shiprise.com; signup not sent' );
		return 'This mailing list is not available';
	}

	$body = array(
		'email' => $arguments['email'],
		'lists' => array( (int) $list_id ),
	);
	if ( ! empty( $arguments['name'] ) ) {
		$body['full_name'] = $arguments['name'];
	}

	$response = wp_remote_post(
		shiprise_thrive_bridge_webhook_url(),
		array(
			'timeout' => 15,
			'body'    => $body,
		)
	);
	if ( is_wp_error( $response ) ) {
		shiprise_thrive_bridge_log( 'list ' . (int) $list_id . ' transport error: ' . $response->get_error_message() );
		return 'Could not reach the mailing list';
	}

	$reply = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( 200 !== wp_remote_retrieve_response_code( $response ) || empty( $reply['success'] ) ) {
		shiprise_thrive_bridge_log( 'list ' . (int) $list_id . ' refused: HTTP ' . wp_remote_retrieve_response_code( $response ) . ' ' . substr( (string) wp_remote_retrieve_body( $response ), 0, 160 ) );
		return 'The mailing list refused the signup';
	}

	return true;
}

add_filter(
	'tve_filter_available_connection',
	function ( $connections ) {
		if ( ! shiprise_thrive_bridge_webhook_url() || function_exists( 'FluentCrmApi' ) ) {
			return $connections;
		}

		if ( ! class_exists( 'Shiprise_Thrive_FluentCRM_Bridge', false ) ) {
			class Shiprise_Thrive_FluentCRM_Bridge extends Thrive_Dash_List_Connection_FluentCRM {
				public function pluginInstalled() {
					return true;
				}

				public function add_subscriber( $list_identifier, $arguments ) {
					return shiprise_thrive_bridge_post( $list_identifier, $arguments );
				}
			}
		}

		$connections['fluentcrm'] = 'Shiprise_Thrive_FluentCRM_Bridge';
		return $connections;
	}
);
