<?php
/**
 * Customizer functionality
 *
 * @package Eglatone
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since Eglatone 0.1
 *
 * @see eglatone_header_style()
 */
function eglatone_custom_header_and_background() {
	$default_background_color = '#ffffff';
	$default_text_color       = '#03065d';

	/**
	 * Filter the arguments used when adding 'custom-background' support in Eglatone.
	 *
	 * @since Eglatone 0.1
	 *
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *
	 *     @type string $default-color Default color of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters( 'eglatone_custom_background_args', array(
		'default-color' => $default_background_color,
	) ) );

	/**
	 * Filter the arguments used when adding 'custom-header' support in Eglatone.
	 *
	 * @since Eglatone 0.1
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'eglatone_custom_header_args', array(
		'default-image'      	 => get_parent_theme_file_uri( '/assets/images/header-image.jpg' ),
		'default-text-color'     => $default_text_color,
		'width'                  => 1920,
		'height'                 => 1080,
		'flex-height'            => true,
		'flex-width'            => true,
		'wp-head-callback'       => 'eglatone_header_style',
		'video'                  => true,
	) ) );

	register_default_headers( array(
		'default-image' => array(
			'url'           => '%s/assets/images/header-image.jpg',
			'thumbnail_url' => '%s/assets/images/header-image-275x155.jpg',
			'description'   => esc_html__( 'Default Header Image', 'eglatone' ),
		),
		'second-image' => array(
			'url'           => '%s/assets/images/header-image-second.jpg',
			'thumbnail_url' => '%s/assets/images/header-image-second-275x155.jpg',
			'description'   => esc_html__( 'Alternate Header Image', 'eglatone' ),
		),
	) );
}
add_action( 'after_setup_theme', 'eglatone_custom_header_and_background' );

/**
 * Customize video play/pause button in the custom header.
 *
 * @param array $settings header video settings.
 */
function eglatone_video_controls( $settings ) {
	$settings['l10n']['play'] = '<span class="screen-reader-text">' . esc_html__( 'Play background video', 'eglatone' ) . '</span>';
	$settings['l10n']['pause'] = '<span class="screen-reader-text">' . esc_html__( 'Pause background video', 'eglatone' ) . '</span>';
	return $settings;
}
add_filter( 'header_video_settings', 'eglatone_video_controls' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Eglatone 1.0
 * @see eglatone_customize_register()
 *
 * @return void
 */
function eglatone_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 *
 * @since Eglatone 0.1
 */
function eglatone_customize_control_js() {
	$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$path = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/source/' : 'assets/js/';

	wp_enqueue_style( 'eglatone-custom-controls-css', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/css/customizer.css' );

}
add_action( 'customize_controls_enqueue_scripts', 'eglatone_customize_control_js' );
