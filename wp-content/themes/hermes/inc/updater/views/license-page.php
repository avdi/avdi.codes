<?php
/**
 * Template for displaying the license page in the admin panel.
 *
 * @package Themes Harbor
 */

$changelogs_link           = 'https://go.themesharbor.com/view-changelogs-via-dashbaord';
$subscribe_link            = 'https://go.themesharbor.com/stay-connected-via-dashboard';
$contacts_link             = 'https://go.themesharbor.com/get-support-via-dashboard';
$support_policy_link       = 'https://themesharbor.com/support-policy/';
$locating_license_key_link = 'https://go.themesharbor.com/locate-license-via-dashboard';
$theme_support             = get_theme_support( 'themes-harbor-edd-license' );
$strings                   = $this->strings;
$license                   = $this->get_license_key();
$license_status            = $this->get_license_status();
$theme                     = wp_get_theme( $this->theme_slug );

// Checks license status to display under license key.
if ( ! $license ) {
	$message = $strings['label']['enter_key'];
} else {
	/**
	 * To remove trasient, use `delete_transient( $this->theme_slug . '_license_message' );`
	 */

	if ( ! get_transient( $this->theme_slug . '_license_message', false ) ) {
		set_transient( $this->theme_slug . '_license_message', $this->check_license(), ( 60 * 60 * 24 ) );
	}

	$message = get_transient( $this->theme_slug . '_license_message' );
}

?>

<div class="wrap"></div>

<div class="th-container">
	<div class="th-card th-card--main-header">
		<div class="th-card--main-header__header th-card--inner">
			<div class="th-thumb">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/screenshot.png' ); ?>">
			</div><!-- .th-thumb -->

			<div class="th-theme-info">
				<h2 class="th-theme-title"><?php echo esc_html( $theme['Name'] ); ?></h2>
				<p class="secondary-color">WordPress theme for professionals</p>
			</div><!-- .th-theme-info -->

			<div class="th-theme-v">
				<p class="secondary-color">
					<?php printf( esc_html( $strings['label']['theme_version'] ), '<strong>' . esc_html( $theme['Version'] ) . '</strong>' ); ?>
				</p>
				<?php $this->show_theme_version_notice(); ?>
			</div><!-- .th-theme-v -->
		</div><!-- .th-card--inner -->

		<footer class="th-card__footer th-card--inner alternate secondary-color">
			<p>This theme is created and actively maintained by <?php printf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://go.themesharbor.com/view-themesharbor-via-dashboard', 'Themes Harbor' ); ?>.</p>
		</footer><!-- .th-card__footer -->
	</div><!-- .th-card -->

	<?php
	do_action( 'thti_after_header' );

	$license_classes = array(
		'th-card',
		'th-card--license',
	);

	if ( ! $this->is_license_active() ) {
		$license_classes[] = 'license-inactive';
	}
	?>

	<div class="<?php echo esc_attr( join( ' ', $license_classes ) ); ?>">
		<header class="th-card__header th-card--inner has-help-link alternate">
			<h2 class="th-card__title">
				<?php echo esc_html( $strings['label']['license_key_activation'] ); ?>
			</h2>
			<a class="th-card__link" href="<?php echo esc_url( $locating_license_key_link ); ?>" target="_blank">
				<?php echo esc_html( $strings['action']['locate_license'] ); ?>
			</a>
		</header><!-- .th-card__inner -->

		<form class="th-license__form th-card--inner" method="post" action="options.php">
			<?php settings_fields( esc_attr( $this->theme_slug . '-license' ) ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->theme_slug ); ?>_license_key"><?php echo esc_html( $message ); ?></label>
			</p>

			<div class="th-license__form--inner">
				<input id="<?php echo esc_attr( $this->theme_slug ); ?>_license_key" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" placeholder="<?php echo esc_attr( $strings['label']['license_key'] ); ?>"/>

				<?php if ( $license ) : ?>

					<?php if ( 'valid' === $license_status ) : ?>
						<input type="submit" class="button button-primary" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_deactivate" value="<?php echo esc_attr( $strings['action']['deactivate_license'] ); ?>"/>
					<?php else : ?>
						<input type="submit" class="button button-primary" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_activate" value="<?php echo esc_attr( $strings['action']['activate_license'] ); ?>"/>
						<input type="submit" class="button button-secondary" name="<?php echo esc_attr( $this->theme_slug ); ?>_license_clear" value="<?php echo esc_attr( $strings['action']['clear_license'] ); ?>"/>
					<?php endif; ?>

				<?php else : ?>
					<input type="submit" class="button button-primary" name="submit" value="<?php echo esc_attr( $strings['action']['save_license'] ); ?>"/>
				<?php endif; ?>
			</div>

			<details>
				<summary><strong>Why is it important to activate your license key?</strong></summary>
				<ul>
					<li><p>By activating the theme with a license, you will gain access to <strong>automatic theme updates straight from the WordPress dashboard</strong>.</p></li>
					<li><p>It means that you will be automatically <strong>notified when there is an update available</strong>, instead of having to manually check the Themes Harbor website for a new version.</p></li>
					<li><p><strong>Updates will be made with a single click</strong>, just like with themes and plugins from the official WordPress repository.</p></li>
					<li><p>It is absolutely crucial to keep your website up to date on a regular basis because of the following main reasons: <strong>security</strong>, <strong>performance</strong>, <strong>bug fixes</strong>, <strong>new functionality</strong>, <strong>better compatibility</strong>.</p></li>
					<li><p>And remember, <strong>updating a website is easier than fixing a hacked website</strong>.</p></li>
				</ul>
			</details>

			<?php wp_nonce_field( esc_attr( $this->theme_slug . '_nonce' ), esc_attr( $this->theme_slug . '_nonce' ) ); ?>
		</form><!-- .th-license__form -->

		<?php if ( 'expired' === $license_status ) : ?>
		<footer class="th-card__footer th-card--inner alternate secondary-color">
			<p>You can <a href="<?php echo esc_url( $this->get_renewal_link() ); ?>" target="_blank">renew your license key here</a> <em>(the link will open a new tab at Themes Harbor where you can complete a renewal process)</em>. Once the license key is renewed, return back to this page, and click <strong>Activate license key</strong>.</p>
		</footer><!-- .th-card__footer -->
		<?php endif; ?>
	</div><!-- .th-card--license -->

	<div class="grid--2col">
		<div class="th-card">
			<header class="th-card__header th-card--inner alternate">
				<h2 class="th-card__title">
					<?php echo esc_html( $strings['label']['documentation'] ); ?>
				</h2>
			</header>
			<div class="th-card--inner">
				<p><?php echo wp_kses_post( $strings['text']['documentation'] ); ?></p>
				<p>
					<a href="<?php echo esc_url( $theme_support[0]['documentation_readme'] ); ?>" target="_blank" class="button button-primary"><?php echo esc_html( $strings['action']['start_here_documentation'] ); ?></a>
					<a href="<?php echo esc_url( $theme_support[0]['documentation_overview'] ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html( $strings['action']['open_documentation'] ); ?></a>
				</p>
			</div>
		</div><!-- .th-card -->

		<div class="th-card">
			<header class="th-card__header th-card--inner alternate">
				<h2 class="th-card__title">
					<?php echo esc_html( $strings['label']['here_to_help'] ); ?>
				</h2>
			</header>
			<div class="th-card--inner">
				<p class="th-mar-t-0 th-mar-b-20"><?php echo wp_kses_post( $strings['text']['help'] ); ?></p>
				<p class="th-mar-t-b-0">
					<a href="<?php echo esc_url( $contacts_link ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html( $strings['action']['ask_question'] ); ?></a>
					<a href="<?php echo esc_url( $support_policy_link ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html( $strings['action']['read_support_policy'] ); ?></a>
				</p>
			</div>
		</div>
	</div><!-- .grid--2col -->

	<div class="grid--2col">
		<div class="th-card">
			<header class="th-card__header th-card--inner alternate">
				<h2 class="th-card__title">
					<?php echo esc_html( $strings['label']['changelog'] ); ?>
				</h2>
			</header>
			<div class="th-card--inner">
				<p class="th-mar-t-0 th-mar-b-20"><?php echo wp_kses_post( $strings['text']['changelog'] ); ?></p>
				<p class="th-mar-t-b-0">
					<a href="<?php echo esc_url( $changelogs_link ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html( $strings['action']['open_changelogs'] ); ?></a>
				</p>
			</div>
		</div><!-- .th-card -->

		<div class="th-card">
			<header class="th-card__header th-card--inner alternate">
				<h2 class="th-card__title">
					<?php echo esc_html( $strings['label']['newsletter'] ); ?>
				</h2>
			</header>
			<div class="th-card--inner">
				<p><?php echo wp_kses_post( $strings['text']['newsletter'] ); ?></p>
				<p>
					<a href="<?php echo esc_url( $subscribe_link ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html( $strings['action']['subscribe_to_newsletter'] ); ?></a>
				</p>
			</div>
		</div><!-- .th-card -->
	</div><!-- .grid--2col -->
</div><!-- .th-container -->
