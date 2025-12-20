<?php
/**
 * Template for displaying the theme update notice in the license page.
 *
 * @package Themes Harbor
 */

?>

<div class="th-card th-card--alert">
	<header class="th-card__header th-card--inner alternate">
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="svg-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />
		</svg>
		<h2 class="th-card__title">Important notice</h2>
	</header>

	<div class="th-card--inner">
		<p class="th-lead">
			<strong>Your site is running an outdated version of the theme.</strong>
		</p>

		<p>
			Updating your WordPress site is essential to <strong>maintaining uptime</strong>, <strong>enabling new features</strong>, <strong>preventing security vulnerabilities</strong>, and <strong>fixing performance issues</strong>.
		</p>

		<?php
		$license_key    = $this->get_license_key();
		$license_status = $this->get_license_status();

		if ( ! $license_key || 'valid' !== $license_status ) :
			?>
			<p>The easiest way to update the theme:</p>
			<ol>
				<li>
					Enter your valid license key in the form below, and then activate it in order to enable one-click updates for the theme within a WordPress dashboard.
				</li>
				<li>
					Navigate to the <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>"><strong>Themes</strong></a> page to update the theme.
				</li>
				<li>
					<strong>Note:</strong> If the update links are missing, make sure your WordPress installation is up to date.
				</li>
			</ol>

			<hr>

			<p class="secondary-color">
				If you would like to learn more about the ways to update a WordPress theme, please
				<a href="https://go.themesharbor.com/update-theme-info-via-dashboard" target="_blank">
					view update guide
				</a>
				.
			</p>
			<?php
		else :
			if ( 'valid' === $license_status ) :
				?>
				<p class="th-lead">Navigate to the <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">Themes</a> page to update the theme.</p>
				<p class="th-lead"><strong>Note:</strong> If the update links are missing, make sure your WordPress installation is up to date.</p>
				<?php
			endif;
		endif;
		?>
	</div><!-- .th-card--inner -->
</div><!-- .th-card -->
