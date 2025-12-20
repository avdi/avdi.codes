<?php
/**
 * This file provides the Themes Harbor Updater Admin functionality.
 *
 * @package Themes_Harbor
 * @link    https://themesharbor.com/
 */

/**
 * Theme updater admin page and functions.
 *
 * @package Themes Harbor
 * @since   1.0.0
 */
class Themes_Harbor_Updater_Admin {

	/**
	 * The remote API URL.
	 *
	 * @var string|null
	 */
	protected $remote_api_url = null;

	/**
	 * The theme slug.
	 *
	 * @var string|null
	 */
	protected $theme_slug = null;

	/**
	 * The version number.
	 *
	 * @var string|null
	 */
	protected $version = null;

	/**
	 * The author name.
	 *
	 * @var string|null
	 */
	protected $author = null;

	/**
	 * The download ID.
	 *
	 * @var string|null
	 */
	protected $download_id = null;

	/**
	 * The renew URL.
	 *
	 * @var string|null
	 */
	protected $renew_url = null;

	/**
	 * The strings.
	 *
	 * @var string|null
	 */
	protected $strings = null;

	/**
	 * The item name.
	 *
	 * @var string
	 */
	protected $item_name = '';

	/**
	 * Beta flag.
	 *
	 * @var bool
	 */
	protected $beta = false;

	/**
	 * The item ID.
	 *
	 * @var string
	 */
	protected $item_id = '';

	/**
	 * The response key.
	 *
	 * @var string
	 */
	protected $response_key = '';

	/**
	 * Initialize the class.
	 *
	 * @param array $config  Configuration options.
	 * @param array $strings Localization strings.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $config = array(), $strings = array() ) {

		$config = wp_parse_args(
			$config,
			array(
				'remote_api_url' => '',
				'theme_slug'     => get_template(),
				'item_name'      => '',
				'license'        => '',
				'version'        => '',
				'author'         => '',
				'download_id'    => '',
				'renew_url'      => '',
				'beta'           => false,
				'item_id'        => '',
			)
		);

		// Set config arguments.
		$this->remote_api_url = $config['remote_api_url'];
		$this->item_name      = esc_html( $config['item_name'] );
		$this->theme_slug     = sanitize_key( $config['theme_slug'] );
		$this->version        = $config['version'];
		$this->author         = $config['author'];
		$this->download_id    = $config['download_id'];
		$this->renew_url      = $config['renew_url'];
		$this->beta           = $config['beta'];
		$this->item_id        = $config['item_id'];

		// Populate version fallback.
		if ( '' === $config['version'] ) {
			$theme         = wp_get_theme( $this->theme_slug );
			$this->version = $theme->get( 'Version' );
		}

		// Strings passed in from the updater config.
		$this->strings = $strings;
		// Information about the theme recieved from Themes Harbor server.
		$this->response_key = $this->theme_slug . '-' . $this->beta . '-update-response';

		add_action( 'init', array( $this, 'updater' ) );
		add_action( 'admin_init', array( $this, 'register_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ) );
		add_action( 'admin_init', array( $this, 'redirect_on_activation' ) );
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
		add_action( 'update_option_' . $this->theme_slug . '_license_key', array( $this, 'activate_license' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'show_license_notice' ), 7 );
		add_filter( 'http_request_args', array( $this, 'disable_wporg_request' ), 5, 2 );
		add_filter( 'site_transient_update_themes', array( $this, 'transient_update_themes' ) );
		add_filter( 'transient_update_themes', array( $this, 'transient_update_themes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_head', array( $this, 'dismiss_license_notice' ) );
		add_action( 'current_screen', array( $this, 'automatically_update_license_status' ) );
	}

	/**
	 * Creates the updater class.
	 *
	 * Since 1.0.0
	 */
	public function updater() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! class_exists( 'Themes_Harbor_Updater' ) ) {
			// Load our custom theme updater.
			include dirname( __FILE__ ) . '/class-themes-harbor-updater.php';
		}

		new Themes_Harbor_Updater(
			array(
				'remote_api_url' => $this->remote_api_url,
				'version'        => $this->version,
				'license'        => trim( get_option( $this->theme_slug . '_license_key' ) ),
				'item_name'      => $this->item_name,
				'item_id'        => $this->item_id,
				'author'         => $this->author,
				'beta'           => $this->beta,
				'license_status' => $this->get_license_status(),
				'theme_slug'     => $this->theme_slug,
			),
			$this->strings
		);
	}

	/**
	 * Get local license status.
	 *
	 * Note if inactive, value is empty
	 *
	 * @return string status active, expired or empty (inactive)
	 */
	public function get_license_status() {
		return get_option( $this->theme_slug . '_license_key_status', false );
	}

	/**
	 * Get local license key value.
	 *
	 * @return string license key value
	 */
	public function get_license_key() {
		return trim( get_option( $this->theme_slug . '_license_key' ) );
	}

	/**
	 * Clean local license key value.
	 */
	public function clear_license_key() {
		delete_option( $this->theme_slug . '_license_key' );
	}

	/**
	 * Check if the license is locally active.
	 *
	 * @return bool True if active
	 */
	public function is_license_active() {
		$active = false;

		if ( 'valid' === $this->get_license_status() ) {
			$active = true;
		}

		return $active;
	}

	/**
	 * Returns a menu label along with a notification drop when it is needed.
	 *
	 * @param string $label The initial label.
	 * @return string The label, possibly with notification drop.
	 *
	 * @since 3.0.0
	 */
	public function get_license_menu_label( $label ) {
		$counter = 0;

		// Display a notification drop if there is a problem with a license key.
		if ( ! $this->is_license_active() ) {
			++$counter;
		}

		// Display a notification drop if there a need for migration.
		if ( $this->migration__is_needed() ) {
			++$counter;
		}

		return $counter ? sprintf( '%1$s <span class="awaiting-mod">%2$d</span>', $label, $counter ) : $label;
	}

	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * Since 1.0.0
	 */
	public function license_menu() {
		$strings = $this->strings;

		add_theme_page(
			$strings['label']['theme_info'],
			$this->get_license_menu_label( $strings['label']['theme_info'] ),
			'manage_options',
			$this->theme_slug . '-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * Since 1.0.0
	 */
	public function register_option() {
		register_setting(
			$this->theme_slug . '-license',
			$this->theme_slug . '_license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitizes the license key.
	 *
	 * Since 1.0.0
	 *
	 * @param  string $new License key that was submitted.
	 * @return string $new Sanitized license key.
	 */
	public function sanitize_license( $new ) {

		$old = $this->get_license_key();

		if ( $old && $old !== $new ) {
			// New license has been entered, so must reactivate.
			delete_option( $this->theme_slug . '_license_key_status' );
			delete_transient( $this->theme_slug . '_license_message' );
		}

		$new = trim( $new );

		return $new;
	}

	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $api_params to be used for wp_remote_get.
	 * @return array|WP_Error $response decoded JSON response.
	 */
	public function get_api_response( $api_params ) {
		// Verify the remote server's SSL certificate.
		$verify_ssl = (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true );

		// Call the custom API.
		$response = wp_remote_post(
			$this->remote_api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		return $response;
	}

	/**
	 * Activates the license key.
	 *
	 * @since 1.0.0
	 */
	public function activate_license() {

		$strings = $this->strings;

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $this->get_license_key(),
			'item_name'  => rawurlencode( $this->item_name ),
			'item_id'    => $this->item_id,
			'url'        => home_url(),
		);

		$response = $this->get_api_response( $api_params );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = $strings['notice']['error_occurred'];
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				/**
				 * Use this code to clear transient:
				 * `delete_transient( $this->theme_slug . '_license_message' );`
				 */

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							$strings['notice']['license_key_expired_%s'],
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked':
						$message = $strings['notice']['license_is_revoked'];
						break;

					case 'missing':
						$message = $strings['notice']['license_is_invalid'];
						break;

					case 'invalid':
					case 'site_inactive':
						$message = $strings['notice']['site_inactive'];
						break;

					case 'item_name_mismatch':
						$message = sprintf( $strings['notice']['item_name_mismatch_%s'], $api_params['item_name'] );
						break;

					case 'no_activations_left':
						   $message = $strings['notice']['no_activations_left'];
						break;

					default:
						   $message = $strings['notice']['error_occurred'];
						break;
				}

				if ( ! empty( $message ) ) {
					$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
					$redirect = add_query_arg(
						array(
							'sl_theme_activation' => 'false',
							'message'             => rawurlencode( $message ),
						),
						$base_url
					);

					// Added: August 2021 - Display a proper error in the license message.
					set_transient( $this->theme_slug . '_license_message', $message, ( 60 * 60 * 24 ) );

					wp_redirect( $redirect );
					exit();
				}
			}
		}

		// $response->license will be either "active" or "inactive"
		if ( $license_data && isset( $license_data->license ) ) {
			update_option( $this->theme_slug . '_license_key_status', $license_data->license );
			delete_transient( $this->theme_slug . '_license_message' );
		}

		wp_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) );
		exit();

	}

	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_license() {

		$strings = $this->strings;

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $this->get_license_key(),
			'item_name'  => rawurlencode( $this->item_name ),
			'item_id'    => $this->item_id,
			'url'        => home_url(),
		);

		$response = $this->get_api_response( $api_params );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = $strings['notice']['error_occurred'];
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( $license_data && ( 'deactivated' === $license_data->license || 'failed' === $license_data->license ) ) {
				delete_option( $this->theme_slug . '_license_key_status' );
				delete_transient( $this->theme_slug . '_license_message' );
			}
		}

		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'themes.php?page=' . $this->theme_slug . '-license' );
			$redirect = add_query_arg(
				array(
					'sl_theme_activation' => 'false',
					'message'             => rawurlencode( $message ),
				),
				$base_url
			);

			wp_redirect( $redirect );
			exit();
		}

		wp_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) );
		exit();

	}

	/**
	 * Clear the license key.
	 *
	 * @since 3.0.0
	 */
	public function clear_license() {
		$this->clear_license_key();
		$this->clear_license_data();

		wp_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) );
		exit();
	}

	/**
	 * Clear license data, which includes the license_message, license_key_status.
	 *
	 * @since 3.0.0
	 */
	public function clear_license_data() {
		delete_option( $this->theme_slug . '_license_key_status' );
		delete_transient( $this->theme_slug . '_license_message' );
	}

	/**
	 * Constructs a renewal link
	 *
	 * @since 1.0.0
	 */
	public function get_renewal_link() {

		// If a renewal link was passed in the config, use that.
		if ( '' !== $this->renew_url ) {
			return $this->renew_url;
		}

		// If download_id was passed in the config, a renewal link can be constructed.
		$license_key = trim( get_option( $this->theme_slug . '_license_key', false ) );
		if ( '' !== $this->download_id && $license_key ) {
			$url  = esc_url( $this->remote_api_url );
			$url .= '/checkout/?edd_license_key=' . $license_key . '&download_id=' . $this->download_id;
			return $url;
		}

		// Otherwise return the remote_api_url.
		return $this->remote_api_url;

	}

	/**
	 * Checks if a license action was submitted.
	 *
	 * @since 1.0.0
	 */
	public function license_action() {
		if ( isset( $_POST[ $this->theme_slug . '_license_activate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->activate_license();
			}
		}

		if ( isset( $_POST[ $this->theme_slug . '_license_deactivate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->deactivate_license();
			}
		}

		if ( isset( $_POST[ $this->theme_slug . '_license_clear' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->clear_license();
			}
		}
	}

	/**
	 * Checks if license is valid and gets expire date.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message License status message.
	 */
	public function check_license() {

		$strings = $this->strings;

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $this->get_license_key(),
			'item_name'  => rawurlencode( $this->item_name ),
			'item_id'    => $this->item_id,
			'url'        => home_url(),
		);

		$response = $this->get_api_response( $api_params );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = $strings['notice']['license_status_unknown'];
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// If response doesn't include license data, return.
			if ( ! isset( $license_data->license ) ) {
				return $strings['notice']['license_status_unknown'];
			}

			// We need to update the license status at the same time the message is updated.
			if ( $license_data && isset( $license_data->license ) ) {
				update_option( $this->theme_slug . '_license_key_status', $license_data->license );
			}

			// Get the expire date.
			$expires = false;
			if ( isset( $license_data->expires ) && 'lifetime' !== $license_data->expires ) {
				$expires    = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) );
				$renew_link = '<a href="' . esc_url( $this->get_renewal_link() ) . '" target="_blank">' . $strings['action']['renew_license'] . '</a>';
			} elseif ( isset( $license_data->expires ) && 'lifetime' === $license_data->expires ) {
				$expires = 'lifetime';
			}

			// Stop and return message if invalid.
			if ( 'invalid' === $license_data->license ) {
				return $strings['notice']['license_keys_dont_match'];
			}

			// Get site counts.
			$site_count    = $license_data->site_count;
			$license_limit = $license_data->license_limit;

			// If unlimited.
			if ( 0 === $license_limit ) {
				$license_limit = $strings['notice']['unlimited'];
			}

			if ( 'valid' === $license_data->license ) {
				$message = $strings['notice']['license_is_active'] . ' ';
				if ( isset( $expires ) && 'lifetime' !== $expires ) {
					$message .= sprintf( $strings['notice']['expires_%s'], $expires ) . ' ';
				}
				if ( isset( $expires ) && 'lifetime' === $expires ) {
					$message .= $strings['notice']['license_expires_never'];
				}
				if ( $site_count && $license_limit ) {
					$message .= sprintf( $strings['notice']['%1$s_%2$_sites'], $site_count, $license_limit );
				}
			} elseif ( 'expired' === $license_data->license ) {
				if ( $expires ) {
					$message = sprintf( $strings['notice']['license_key_expired_%s'], $expires );
				} else {
					$message = $strings['notice']['license_key_expired'];
				}
				if ( $renew_link ) {
					$message .= ' ' . $renew_link;
				}
			} elseif ( 'inactive' === $license_data->license ) {
				$message = $strings['notice']['license_is_inactive'];
			} elseif ( 'disabled' === $license_data->license ) {
				$message = $strings['notice']['license_is_disabled'];
			} elseif ( 'site_inactive' === $license_data->license ) {
				// Site is inactive.
				$message = $strings['notice']['site_is_inactive'];
			} else {
				$message = $strings['notice']['license_status_unknown'];
			}
		}

		return $message;
	}

	/**
	 * Disable requests to wp.org repository for this theme.
	 *
	 * @param array  $r    An array of HTTP request arguments.
	 * @param string $url  The request URL.
	 * @return array An array of modified HTTP request arguments.
	 *
	 * @since 1.0.0
	 */
	public function disable_wporg_request( $r, $url ) {

		// If it's not a theme update request, bail.
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $r;
		}

		// Decode the JSON response.
		$themes = json_decode( $r['body']['themes'] );

		// Remove the active parent and child themes from the check.
		$parent = get_option( 'template' );
		$child  = get_option( 'stylesheet' );
		unset( $themes->themes->$parent );
		unset( $themes->themes->$child );

		// Encode the updated JSON response.
		$r['body']['themes'] = wp_json_encode( $themes );

		return $r;
	}

	/**
	 * Load Getting Started styles in the admin.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		global $pagenow;

		if ( 'themes.php' !== $pagenow ) {
			return;
		}

		// Getting Started styles.
		wp_enqueue_style( 'getting-started', get_template_directory_uri() . '/inc/updater/assets/th-theme-info.css', false, '3.0.0' );
	}

	/**
	 * Redirect to Getting Started page on theme activation.
	 *
	 * @since 1.0.0
	 */
	public function redirect_on_activation() {
		global $pagenow;

		if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ) { // phpcs:ignore
			wp_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) );
		}
	}

	/**
	 * Prevent the theme to get updates from WordPress.org.
	 *
	 * @param object $transient The transient object.
	 * @return object The modified transient object.
	 *
	 * @since 1.0.0
	 */
	public function transient_update_themes( $transient ) {

		if ( is_object( $transient ) && isset( $transient->response ) ) {

			$response = $transient->response;

			if ( isset( $response[ $this->theme_slug ]['url'] ) && 0 === strpos( $response[ $this->theme_slug ]['url'], 'https://wordpress.org/themes/' ) ) {
				unset( $response[ $this->theme_slug ] );
				$transient->response = $response;
			}
		}

		return $transient;
	}

	/**
	 * Update the license status.
	 */
	public function update_license_status() {
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $this->get_license_key(),
			'item_name'  => rawurlencode( $this->item_name ),
			'url'        => home_url(),
			'item_id'    => $this->item_id,
		);

		$response = $this->get_api_response( $api_params );

		// Make sure the response came back okay.
		if ( ! is_wp_error( $response ) || 200 === wp_remote_retrieve_response_code( $response ) ) {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( $license_data && isset( $license_data->license ) ) {
				update_option( $this->theme_slug . '_license_key_status', $license_data->license );
			}
		}
	}

	/**
	 * Automatically check for the license status change periodically on relevant pages.
	 */
	public function automatically_update_license_status() {
		// Admin only.
		if ( ! is_admin() ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! get_transient( $this->theme_slug . '_license_status_sync' ) && in_array( $screen->base, array( 'themes' ), true ) ) {
			$this->update_license_status();
			// Set transient to prevent check until next 72 hours.
			set_transient( $this->theme_slug . '_license_status_sync', true, 3 * DAY_IN_SECONDS );
		}
	}

	/**
	 * Register dismissal of license notices.
	 */
	public function dismiss_license_notice() {
		if ( isset( $_GET['theme-license-notice'] ) && check_admin_referer( 'theme-license-notice' ) ) {
			set_transient( $this->theme_slug . '_hide_license_notice', true, DAY_IN_SECONDS );
		}
	}

	/**
	 * Show inactive, expiring soon and expired license notices.
	 */
	public function show_license_notice() {
		// Don't show on the notice if it was dissmised.
		if ( get_transient( $this->theme_slug . '_hide_license_notice' ) ) {
			return;
		}

		// Show only on relevant pages as not to overwhelm the admin.
		// Don't show on Theme License page -- redundant.
		$screen = get_current_screen();
		if ( ! in_array( $screen->base, array( 'dashboard', 'themes' ), true ) ) {
			return;
		}

		// Show only if the license is inactive.
		if ( $this->is_license_active() ) {
			return;
		}

		// Default labels.
		$strings = $this->strings;

		if ( empty( $strings ) ) {
			return;
		}

		?>
		<div class="notice notice-error">
			<p>
				<strong><?php echo esc_html( $strings['notice']['license_is_inactive'] ); ?></strong>
				<span>To unlock updates, consider activating your license key.</span>
			</p>

			<p>
				<strong>
					<a href="<?php echo esc_url( admin_url( 'themes.php?page=' . $this->theme_slug . '-license' ) ); ?>"><?php echo esc_html( $strings['action']['activate_license'] ); ?></a>
				</strong>
				<span> | </span>
				<a href="https://go.themesharbor.com/locate-license-via-dashboard" target="_blank"><?php echo esc_html( $strings['action']['locate_license'] ); ?></a>
				<span> | </span>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'theme-license-notice', 'dismiss_license_notice' ), 'theme-license-notice' ) ); ?>"><?php echo esc_html( $strings['action']['dismiss_notice'] ); ?></a>
			</p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Check if the current version of the theme is latest or outdated,
	 * and display a short notice.
	 *
	 * @return string
	 */
	public function show_theme_version_notice() {
		$api_response = get_transient( $this->response_key );

		if ( ! $api_response ) {
			return;
		}

		$strings = $this->strings;

		if ( version_compare( $this->version, $api_response->new_version, '<' ) ) {
			printf( '<p class="th-mar-t-b-0 th-v-notice th-outdated-v">%s</p>', $strings['notice']['is_outdated_verion'] ); // WPCS: XSS OK.

			add_action( 'thti-after-header', array( $this, 'show_update_notice' ) );
		} else {
			printf( '<p class="th-mar-t-b-0 th-v-notice th-latest-v">%s</p>', $strings['notice']['is_latest_verion'] ); // WPCS: XSS OK.
		}

		// Display migration notice if needed.
		if ( $this->migration__is_needed() ) {
			add_action( 'thti-after-header', array( $this, 'migration__show_notice' ) );
		}
	}

	/**
	 * Check if the current version of the theme is latest or outdated.
	 *
	 * @since 3.0.0
	 */
	public function show_update_notice() {
		include dirname( __FILE__ ) . '/views/theme-update-notice.php';
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * @since 3.0.0
	 */
	public function license_page() {
		include dirname( __FILE__ ) . '/views/license-page.php';
	}

	/**
	 * Migration: find pages based on a template.
	 *
	 * This is needed for locating pages with set custom template
	 * that are planned to be removed in the next major version.
	 *
	 * @param string $template Name of template.
	 */
	public function migration__get_pages_by_template( $template ) {
		$pages = array();

		$query = new WP_Query(
			array(
				'numberposts' => 100,
				'post_type'   => 'page',
				'meta_query'  => array( // phpcs:ignore
					array(
						'key'     => '_wp_page_template',
						'value'   => esc_attr( $template ),
						'compare' => '=',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id      = get_the_ID();
				$pages[] = array(
					'id'    => $id,
					'title' => get_the_title( $id ),
					'url'   => get_permalink( $id ),
				);
			}
		}

		wp_reset_postdata();

		return $pages;
	}

	/**
	 * Migration: not everyone needs to make a migration.
	 * So, display a notice only for those users that need it.
	 */
	public function migration__is_needed() {
		$is_needed = false;
		$the_theme = get_theme_support( 'themes-harbor-theme' );

		if ( ! isset( $the_theme[0]['migration']['templates'] ) ) {
			return $is_needed;
		}

		if ( empty( $the_theme[0]['migration']['templates'] ) ) {
			return $is_needed;
		}

		foreach ( $the_theme[0]['migration']['templates'] as $template ) {
			if ( $template['has_template'] ) {

				if ( ! isset( $template['slug'] ) ) {
					continue;
				}

				$pages = $this->migration__get_pages_by_template( $template['slug'] );

				if ( $pages ) {
					$is_needed = true;
				}
			}
		}

		return $is_needed;
	}

	/**
	 * Migration: display notice on the Themes Info page.
	 */
	public function migration__show_notice() {
		include dirname( __FILE__ ) . '/views/theme-migration-notice.php';
	}
}

/**
 * This is a means of catching errors from the activation method above and displyaing it to the customer
 */
function edd_themesharbor_admin_notices() {
	if ( isset( $_GET['sl_theme_activation'] ) && ! empty( $_GET['message'] ) ) { // phpcs:ignore

		switch ( $_GET['sl_theme_activation'] ) { // phpcs:ignore

			case 'false':
				$message = urldecode( $_GET['message'] ); // phpcs:ignore
				printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );

				break;

			case 'true':
			default:
				break;
		}
	}
}
add_action( 'admin_notices', 'edd_themesharbor_admin_notices' );
