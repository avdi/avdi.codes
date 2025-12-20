<?php
/**
 * Typography Control.
 *
 * @package hermes
 *
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine
 * @phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) ) :
	/**
	 * A special Customizer control for fonts.
	 */
	class Themes_Harbor_Customizer_Font_Family_Control extends WP_Customize_Control {
		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $type = 'font-family';

		/**
		 * Add custom parameters to pass to the JS via JSON.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function to_json() {
			parent::to_json();

			$this->json['value']             = $this->sanitize_font( $this->value() );
			$this->json['googleFonts']       = $this->get_google_fonts();
			$this->json['defaultFonts']      = $this->get_default_fonts();
			$this->json['link']              = $this->get_link();
			$this->json['default']           = $this->setting->default;
			$this->json['resetLabel']        = esc_html__( 'Reset to default', 'hermes' );
			$this->json['defaultFontsTitle'] = esc_html__( 'System fonts', 'hermes' );
			$this->json['googleFontsTitle']  = esc_html__( 'Google fonts', 'hermes' );
		}

		/**
		 * Sanitize a default font.
		 *
		 * @since  1.0.0
		 * @param String $font A single name of the font.
		 * @return string
		 */
		private function sanitize_font( $font ) {
			$font = esc_attr( $font );

			if ( in_array( $font, $this->get_google_fonts(), true ) ) {
				return $font;
			}

			return '';
		}

		/**
		 * Get json of all google fonts.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		private function get_google_fonts_json() {
			$font_data = get_transient( 'hermes-google-fonts-json' );

			if ( ! empty( $font_data ) ) {
				return $font_data;
			}

			include get_template_directory() . '/inc/typography/google-fonts-json.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			if ( isset( $google_fonts_json ) ) {
				$font_data = json_decode( $google_fonts_json, true );
				set_transient( 'hermes-google-fonts-json', $font_data, WEEK_IN_SECONDS );
				return $font_data;
			}

			return false;
		}

		/**
		 * Get all availible default fonts.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_default_fonts() {
			return hermes_get_default_fonts();
		}

		/**
		 * Get all availible google fonts.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_google_fonts() {
			$font_data = $this->get_google_fonts_json();
			$fonts     = array();

			if ( ! empty( $font_data ) ) {
				foreach ( $font_data as $family => $variants ) {
					$fonts[ $family ] = $family;
				}
			}

			return $fonts;
		}

		/**
		 * Underscore JS template to handle the control's output.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function content_template() {
			?>

			<label>
				<div class="th-control-header wp-clearfix">
					<div class="customize-control-notifications-container"></div>

					<# if ( data.label ) { #>
					<span class="customize-control-title">{{ data.label }}</span>
					<# } #>
					<button type="button" class="button th-reset dashicons dashicons-image-rotate" title="{{ data.resetLabel }}">
						<span class="screen-reader-text">{{ data.resetLabel }}</span>
					</button>
				</div>

				<select {{{ data.link }}} name="_customize-{{ data.type }}-{{ data.settings.default }}" id="_customize-input-{{ data.settings.default }}" data-default="{{ data.default }}">
					<optgroup label="{{ data.defaultFontsTitle }}">
					<# for ( key in data.defaultFonts ) { #>
						<option value="{{ data.defaultFonts[ key ] }}" <# if ( key === data.value ) { #> selected="selected" <# } #>>{{ data.defaultFonts[ key ] }}</option>
					<# } #>
					</optgroup>

					<optgroup label="{{ data.googleFontsTitle }}">
					<# for ( key in data.googleFonts ) { #>
						<option value="{{ key }}" <# if ( key === data.value ) { #> selected="selected" <# } #>>{{ data.googleFonts[ key ] }}</option>
					<# } #>
					</optgroup>

				</select>

				<# if ( data.description ) { #>
				<span class="description th-description customize-control-description">{{{ data.description }}}</span>
				<# } #>
			</label>

			<?php
		}
	}

endif;
