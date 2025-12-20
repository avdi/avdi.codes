<?php
/**
 * Block styles.
 */

if ( ! function_exists( 'register_block_style' ) ) {
	return;
}

/**
 * Register Block Styles.
 */
function avdeez_register_global_block_styles() {
    // Media&Text: Rounded Image.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/media-text',
		array(
			'name'  => 'th-rounded-img',
			'label' => esc_html__( 'Rounded image', 'avdeez' ),
		)
	);

    // Table: with borders.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/table',
		array(
			'name'  => 'borders',
			'label' => esc_html__( 'Borders', 'avdeez' ),
		)
	);

    // List: Inline.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/list',
		array(
			'name'  => 'th-inline-list',
			'label' => esc_html__( 'Inline list', 'avdeez' ),
		)
	);

	// Quote: Minimal.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/quote',
		array(
			'name'  => 'th-minimal',
			'label' => esc_html__( 'Minimal', 'avdeez' ),
		)
	);

	// Columns: Separated with borders.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/columns',
		array(
			'name'  => 'th-border-separate',
			'label' => esc_html__( 'Border separate', 'avdeez' ),
		)
	);

	// Query: Separated with borders.
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/post-template',
		array(
			'name'  => 'th-border-separate',
			'label' => esc_html__( 'Border separate', 'avdeez' ),
		)
	);
}
add_action( 'init', 'avdeez_register_global_block_styles' );
