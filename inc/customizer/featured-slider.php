<?php
/**
 * Featured Slider Options
 *
 * @package Eglatone
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_slider_options( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_featured_slider', array(
			'panel' => 'eglatone_theme_options',
			'title' => esc_html__( 'Featured Slider', 'eglatone' ),
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_slider_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'choices'           => eglatone_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'eglatone' ),
			'section'           => 'eglatone_featured_slider',
			'type'              => 'select',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_slider_number',
			'default'           => '4',
			'sanitize_callback' => 'eglatone_sanitize_number_range',

			'active_callback'   => 'eglatone_is_slider_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'eglatone' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
				'max'   => 20,
				'step'  => 1,
			),
			'label'             => esc_html__( 'No of Slides', 'eglatone' ),
			'section'           => 'eglatone_featured_slider',
			'type'              => 'number',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_slider_content_show',
			'default'           => 'hide-content',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'active_callback'   => 'eglatone_is_slider_active',
			'choices'           => eglatone_content_show(),
			'label'             => esc_html__( 'Display Content', 'eglatone' ),
			'section'           => 'eglatone_featured_slider',
			'type'              => 'select',
		)
	);

	$slider_number = get_theme_mod( 'eglatone_slider_number', 4 );

	for ( $i = 1; $i <= $slider_number ; $i++ ) {
		eglatone_register_option( $wp_customize, array(
				'name'              => 'eglatone_slider_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Eglatone_Note_Control',
				'active_callback'   => 'eglatone_is_slider_active',
				'label'             => esc_html__( 'Slide #', 'eglatone' ) . $i,
				'section'           => 'eglatone_featured_slider',
				'type'              => 'description',
			)
		);

		// Page Sliders
		eglatone_register_option( $wp_customize, array(
				'name'              => 'eglatone_slider_page_' . $i,
				'sanitize_callback' => 'eglatone_sanitize_post',
				'active_callback'   => 'eglatone_is_slider_active',
				'label'             => esc_html__( 'Page', 'eglatone' ) . ' # ' . $i,
				'section'           => 'eglatone_featured_slider',
				'type'              => 'dropdown-pages',
			)
		);

		eglatone_register_option( $wp_customize, array(
				'name'              => 'eglatone_slider_title_image_' . $i,
				'sanitize_callback' => 'eglatone_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'eglatone_is_slider_active',
				'label'             => esc_html__( 'Title Image', 'eglatone' ),
				'section'           => 'eglatone_featured_slider',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'eglatone_slider_options' );

/** Active Callback Functions */

if ( ! function_exists( 'eglatone_is_slider_active' ) ) :
	/**
	* Return true if slider is active
	*
	* @since Eglatone 0.1
	*/
	function eglatone_is_slider_active( $control ) {
		$enable = $control->manager->get_setting( 'eglatone_slider_option' )->value();

		//return true only if previwed page on customizer matches the type option selected
		return eglatone_check_section( $enable );
	}
endif;