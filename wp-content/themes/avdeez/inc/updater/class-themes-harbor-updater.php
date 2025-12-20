<?php
/**
 * This file provides the Themes Harbor Updater functionality.
 *
 * @package Themes_Harbor
 * @link    https://themesharbor.com/
 */

/**
 * Theme updater functions.
 *
 * @package Themes Harbor
 * @since   1.0.0
 */
class Themes_Harbor_Updater {

	/**
	 * The remote API URL.
	 *
	 * @var string
	 */
	private $remote_api_url;

	/**
	 * The request data.
	 *
	 * @var array
	 */
	protected $request_data;

	/**
	 * The response key.
	 *
	 * @var string
	 */
	protected $response_key;

	/**
	 * The theme slug.
	 *
	 * @var string
	 */
	protected $theme_slug;

	/**
	 * The license key.
	 *
	 * @var string
	 */
	protected $license;

	/**
	 * The license status.
	 *
	 * @var string
	 */
	protected $license_status;

	/**
	 * The version number.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * The author name.
	 *
	 * @var string
	 */
	protected $author;

	/**
	 * The response data.
	 *
	 * @var mixed
	 */
	protected $response = null;

	/**
	 * The strings data.
	 *
	 * @var array|null
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
	 * Initiate the Theme updater
	 *
	 * @param array $args    Array of arguments from the theme requesting an update check.
	 * @param array $strings Strings for the update process.
	 */
	public function __construct( $args = array(), $strings = array() ) {

		$defaults = array(
			'remote_api_url' => '',
			'request_data'   => array(),
			'theme_slug'     => get_template(), // use get_stylesheet() for child theme updates.
			'item_name'      => '',
			'license_status' => '',
			'license'        => '',
			'version'        => '',
			'author'         => '',
			'beta'           => false,
			'item_id'        => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->license        = $args['license'];
		$this->license_status = $args['license_status'];
		$this->item_name      = $args['item_name'];
		$this->version        = $args['version'];
		$this->theme_slug     = sanitize_key( $args['theme_slug'] );
		$this->author         = $args['author'];
		$this->beta           = $args['beta'];
		$this->remote_api_url = $args['remote_api_url'];
		$this->response_key   = $this->theme_slug . '-' . $this->beta . '-update-response';
		$this->strings        = $strings;
		$this->item_id        = $args['item_id'];

		add_filter( 'site_transient_update_themes', array( $this, 'theme_update_transient' ) );
		add_filter( 'delete_site_transient_update_themes', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-update-core.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'load_themes_screen' ) );
	}

	/**
	 * Show the update notification when neecessary
	 *
	 * @return void
	 */
	public function load_themes_screen() {
		add_thickbox();
		add_action( 'admin_notices', array( $this, 'update_nag' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'update_scripts' ) );
	}

	/**
	 * Display the update notifications
	 *
	 * @return void
	 */
	public function update_nag() {

		$strings      = $this->strings;
		$theme        = wp_get_theme( $this->theme_slug );
		$api_response = get_transient( $this->response_key );

		if ( false === $api_response ) {
			return;
		}

		$update_url     = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . rawurlencode( $this->theme_slug ), 'upgrade-theme_' . $this->theme_slug );
		$update_onclick = ' onclick="if ( confirm(\'' . esc_js( $strings['notice']['update_notice'] ) . '\') ) {return true;}return false;"';

		if ( version_compare( $this->version, $api_response->new_version, '<' ) ) {

			// Holds ID of the div conatiner with a changelog information.
			$changelog_container_id = $this->theme_slug . '_changelog';

			if ( 'valid' === $this->license_status ) {
				$update_nag = sprintf(
					$strings['notice']['update_available'],
					$theme->get( 'Name' ),
					$api_response->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $changelog_container_id,
					$theme->get( 'Name' ),
					$update_url,
					$update_onclick
				);
			} else {
				$update_nag = sprintf(
					$strings['notice']['new_version_available'],
					$theme->get( 'Name' ),
					$api_response->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $changelog_container_id,
					$theme->get( 'Name' ),
					admin_url( 'themes.php?page=' . $this->theme_slug . '-license' )
				);
			}

			echo '<div id="themesharbor-nag" class="update-nag notice-warning notice inline">' . wp_kses_post( $update_nag ) . '</div>';

			$changelog_content   = $api_response->sections['changelog'];
			$changelog_full_link = $api_response->homepage . 'changelog';
			$accessible_link     = '<div class="full-changelog"><strong><a href="' . esc_url( $changelog_full_link ) . '" target="_blank">View a full changelog &#x2192;</a></strong></div>';
			$changelog_content   = str_replace( $changelog_full_link, $accessible_link, $changelog_content );

			printf( '<div id="%1$s" style="display:none;"><div class="inner">%2$s</div></div>', esc_html( $changelog_container_id ), wp_kses_post( $changelog_content ) );
		}
	}

	/**
	 * Load custom styles and scripts when a new update is availible.
	 *
	 * @return void
	 */
	public function update_scripts() {
		wp_enqueue_style( 'themesharbor-update-style', get_template_directory_uri() . '/inc/updater/assets/th-theme-update.css', false, '1.0.0' );

		wp_add_inline_script(
			'jquery',
			'jQuery(document).ready(function(){
				jQuery( "#themesharbor-nag" ).on( "click", "a.thickbox", function() {
			        jQuery( "body" ).addClass( "themesharbor-update" );
			    });
			});'
		);
	}

	/**
	 * Update the theme update transient with the response from the version check
	 *
	 * @param  array $value   The default update values.
	 * @return array|boolean  If an update is available, returns the update parameters, if no update is needed returns false, if
	 *                        the request fails returns false.
	 */
	public function theme_update_transient( $value ) {
		$update_data = $this->check_for_update();

		if ( $update_data ) {
			// Fix for warning messages on Dashboard / Updates page.
			if ( ! is_object( $value ) ) {
				$value = new stdClass();
			}

			// Make sure the theme property is set.
			$update_data['theme'] = $this->theme_slug;

			if ( version_compare( $this->version, $update_data['new_version'], '<' ) ) {
				$value->response[ $this->theme_slug ] = $update_data;
			} else {
				$value->no_update[ $this->theme_slug ] = $update_data;
			}
		}

		return $value;
	}

	/**
	 * Remove the update data for the theme
	 *
	 * @return void
	 */
	public function delete_theme_update_transient() {
		delete_transient( $this->response_key );
	}

	/**
	 * Makes a call to the API.
	 *
	 * @since 3.0.0
	 *
	 * @return array $response decoded JSON response.
	 */
	public function get_api_response() {
		$api_params = array(
			'edd_action'  => 'get_version',
			'license'     => $this->license,
			'name'        => $this->item_name,
			'slug'        => $this->theme_slug,
			'version'     => $this->version,
			'author'      => $this->author,
			'beta'        => $this->beta,
			'item_id'     => $this->item_id,
			'php_version' => phpversion(),
			'wp_version'  => get_bloginfo( 'version' ),
		);

		$response = wp_remote_post(
			$this->remote_api_url,
			array(
				'timeout' => 15,
				'body'    => $api_params,
			)
		);

		return $response;
	}

	/**
	 * Sets a call made to the API.
	 *
	 * @since 3.0.0
	 */
	public function set_api_response() {
		$this->response = $this->get_api_response();
	}

	/**
	 * Call the EDD SL API (using the URL in the construct) to get the latest version information
	 *
	 * @return array|boolean  If an update is available, returns the update parameters, if no update is needed returns false, if
	 *                        the request fails returns false.
	 */
	public function check_for_update() {
		$update_data = get_transient( $this->response_key );

		if ( false === $update_data ) {
			$failed = false;

			$this->set_api_response();

			// Make sure the response was successful.
			if ( is_wp_error( $this->response ) || 200 !== wp_remote_retrieve_response_code( $this->response ) ) {
				$failed = true;
				add_action( 'admin_notices', array( $this, 'show_error_notice' ) );
			}

			$update_data = json_decode( wp_remote_retrieve_body( $this->response ) );

			if ( ! is_object( $update_data ) ) {
				$failed = true;
			}

			// If the response failed, try again in 30 minutes.
			if ( $failed ) {
				$data              = new stdClass();
				$data->new_version = $this->version;

				set_transient( $this->response_key, $data, strtotime( '+30 minutes', current_time( 'timestamp' ) ) );

				return false;
			}

			// If the status is 'ok', return the update arguments.
			if ( ! $failed ) {
				$update_data->sections = maybe_unserialize( $update_data->sections );
				set_transient( $this->response_key, $update_data, strtotime( '+12 hours', current_time( 'timestamp' ) ) );
			}
		}

		if ( version_compare( $this->version, $update_data->new_version, '>=' ) ) {
			return false;
		}

		if ( 'valid' !== $this->license_status ) {
			return false;
		}

		return (array) $update_data;
	}

	/**
	 * Display an error banner if there are problems
	 * with connection to Themes Harbor.
	 *
	 * @since 3.0.0
	 */
	public function show_error_notice() {
		if ( ! isset( $this->response ) || ! is_object( $this->response ) ) {
			return;
		}

		if ( ! method_exists( $this->response, 'get_error_message' ) ) {
			return;
		}

		echo '<div class="notice notice-error"><p>' . wp_kses_post( $this->response->get_error_message() ) . '</p></div>';
	}
}
