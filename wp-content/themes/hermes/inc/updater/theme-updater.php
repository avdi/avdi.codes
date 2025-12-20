<?php
/**
 * Theme Updater functionality.
 *
 * @package Themes_Harbor
 */

$support = get_theme_support( 'themes-harbor-edd-license' );

if ( ! empty( $support[0] ) && isset( $support[0]['theme-slug'] ) && isset( $support[0]['download-id'] ) ) {
	$theme_slug  = $support[0]['theme-slug'];
	$download_id = $support[0]['download-id'];
} else {
	return;
}

// Includes the files needed for the theme updater.
if ( ! class_exists( 'Themes_Harbor_Updater_Admin' ) ) {
	include dirname( __FILE__ ) . '/class-themes-harbor-updater-admin.php';
}

// Prepare the configuration array.
$config = array(
	'remote_api_url' => wp_get_theme( $theme_slug )->get( 'AuthorURI' ), // Site where EDD is hosted.
	'item_name'      => wp_get_theme( $theme_slug )->get( 'Name' ), // Name of theme.
	'theme_slug'     => $theme_slug, // Theme slug.
	'version'        => wp_get_theme( $theme_slug )->get( 'Version' ), // The current version of this theme.
	'author'         => wp_get_theme( $theme_slug )->get( 'Author' ), // The author of this theme.
	'download_id'    => intval( $download_id ), // Optional, used for generating a license renewal link.
	'renew_url'      => '', // Optional, allows for a custom license renewal link.
	'beta'           => false, // Optional, set to true to opt into beta versions.
	'item_id'        => intval( $download_id ),
);

$strings = array(
	'status' => array(
		'error' => esc_html__( 'Error', 'hermes' ),
	),
	'notice' => array(
		'is_latest_verion'        => esc_html__( 'You have the latest version', 'hermes' ),
		'is_outdated_verion'      => esc_html__( 'You have an outdated version', 'hermes' ),
		/* translators: 1: Theme name, 2: Theme version, 3: URL, 4: URL, 5: URL, 6: Optional attributes. */
		'update_available'        => __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4$s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'hermes' ),
		/* translators: 1: Theme name, 2: Theme version, 3: URL, 4: URL, 5: URL. */
		'new_version_available'   => __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4$s">Check out what\'s new</a>. Note, the theme must be <a href="%5$s">activated with a valid theme license key</a> in order to receive updates.', 'hermes' ),
		/* translators: 1: Date */
		'expires_%s'              => esc_html__( 'It expires on %s.', 'hermes' ),
		/* translators: 1: Number of sites activated, 2: Total sites allowed */
		'%1$s_%2$_sites'          => esc_html__( 'You have %1$s / %2$s sites activated.', 'hermes' ),
		'license_keys_dont_match' => esc_html__( 'License keys do not match.', 'hermes' ),
		'site_is_inactive'        => esc_html__( 'Your license is not active for this URL.', 'hermes' ),
		'license_is_disabled'     => esc_html__( 'Your license key is disabled.', 'hermes' ),
		'license_is_inactive'     => esc_html__( 'Your license key is inactive.', 'hermes' ),
		'license_is_active'       => esc_html__( 'Your license key is active.', 'hermes' ),
		'license_is_revoked'      => esc_html__( 'Your license key has been disabled.', 'hermes' ),
		'license_is_invalid'      => esc_html__( 'Invalid license.', 'hermes' ),
		'status_unknown'          => esc_html__( 'License status is unknown.', 'hermes' ),
		'license_status_unknown'  => esc_html__( 'License status is unknown.', 'hermes' ),
		'update_notice'           => esc_html__( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'hermes' ),
		/* translators: 1: Date */
		'license_key_expired_%s'  => esc_html__( 'Your license key expired on %s.', 'hermes' ),
		'license_key_expired'     => esc_html__( 'Your license key has expired.', 'hermes' ),
		'license_expires_never'   => esc_html__( 'Lifetime License.', 'hermes' ),
		'feed_error'              => esc_html__( 'This documentation file feed seems to be temporarily down. You can always view it on Themes Harbor in the meantime.', 'hermes' ),
		'latest_updates_error'    => esc_html__( 'There seems to be a temporary problem retrieving the latest updates for this theme.', 'hermes' ),
		'unlimited'               => esc_html__( 'unlimited', 'hermes' ),
		'error_occurred'          => esc_html__( 'An error occurred, please try again.', 'hermes' ),
		'no_activations_left'     => esc_html__( 'Your license key has reached its activation limit.', 'hermes' ),
		/* translators: 1: Theme name */
		'item_name_mismatch_%s'   => esc_html__( 'This appears to be an invalid license key for %s.', 'hermes' ),
	),
	'action' => array(
		'locate_license'           => esc_html__( 'Locate a license key', 'hermes' ),
		'activate_license'         => esc_html__( 'Activate license key', 'hermes' ),
		'save_license'             => esc_html__( 'Save license key', 'hermes' ),
		'deactivate_license'       => esc_html__( 'Deactivate license key', 'hermes' ),
		'clear_license'            => esc_html__( 'Clear license key', 'hermes' ),
		'dismiss_notice'           => esc_html__( 'Dismiss this notice', 'hermes' ),
		'renew_license'            => esc_html__( 'Renew?', 'hermes' ),
		'start_here_documentation' => esc_html__( 'Start here', 'hermes' ),
		'open_documentation'       => esc_html__( 'Open documentation', 'hermes' ),
		'open_changelogs'          => esc_html__( 'Open changelogs', 'hermes' ),
		'ask_question'             => esc_html__( 'Ask a question', 'hermes' ),
		'read_support_policy'      => esc_html__( 'Read support policy', 'hermes' ),
		'subscribe_to_newsletter'  => esc_html__( 'Subscribe to our newsletter', 'hermes' ),
	),
	'label'  => array(
		'license_key_activation' => esc_html__( 'License key form', 'hermes' ),
		'theme_info'             => esc_html__( 'Theme Info', 'hermes' ),
		/* translators: 1: Version number */
		'theme_version'          => esc_html__( 'Version: %s', 'hermes' ),
		'documentation'          => esc_html__( 'Documentation', 'hermes' ),
		'changelog'              => esc_html__( 'Changelog', 'hermes' ),
		'enter_key'              => esc_html__( 'Enter your theme license key and save it.', 'hermes' ),
		'license_key'            => esc_html__( 'License key', 'hermes' ),
		'here_to_help'           => esc_html__( 'Support', 'hermes' ),
		'newsletter'             => esc_html__( 'Newsletter', 'hermes' ),
	),
	'text'   => array(
		'documentation' => esc_html__( 'Our efforts go into making installation as easy as possible for you. Click the button below to open a step by step guide on how to set up your current theme.', 'hermes' ),
		'changelog'     => esc_html__( 'Our themes are compatible with the latest WordPress version and we perform tests and updates on a regular basis to keep them safe and stable.', 'hermes' ),
		'help'          => esc_html__( 'Still cannot find your answer in documentation? Our support team is always available to answer any questions you may have.', 'hermes' ),
		'newsletter'    => esc_html__( 'Subscribe to our newsletter to be the first to know about the latest updates, educational guides and exclusive offers at Themes Harbor.', 'hermes' ),
	),
);

// Loads the updater classes.
$updater = new Themes_Harbor_Updater_Admin( $config, $strings );
