<?php
/**
 * Add Testimonial Settings in Customizer
 *
 * @package Eglatone
*/

/**
 * Add testimonial options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_testimonial_options( $wp_customize ) {
	// Add note to Jetpack Testimonial Section
	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_jetpack_testimonial_cpt_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Eglatone_Note_Control',
			'label'             => sprintf( esc_html__( 'For Testimonial Options for Eglatone Theme, go %1$shere%2$s', 'eglatone' ),
				'<a href="javascript:wp.customize.section( \'eglatone_testimonials\' ).focus();">',
				 '</a>'
			),
		   'section'            => 'jetpack_testimonials',
			'type'              => 'description',
			'priority'          => 1,
		)
	);

	$wp_customize->add_section( 'eglatone_testimonials', array(
			'panel'    => 'eglatone_theme_options',
			'title'    => esc_html__( 'Testimonials', 'eglatone' ),
		)
	);

	    $action = 'install-plugin';
    $slug   = 'essential-content-types';

    $install_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => $action,
                'plugin' => $slug
            ),
            admin_url( 'update.php' )
        ),
        $action . '_' . $slug
    );

    eglatone_register_option( $wp_customize, array(
            'name'              => 'eglatone_testimonial_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Eglatone_Note_Control',
            'active_callback'   => 'eglatone_is_ect_testimonial_inactive',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For Testimonial, install %1$sEssential Content Types%2$s Plugin with testimonial Type Enabled', 'eglatone' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'

            ),
           'section'            => 'eglatone_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_testimonial_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'active_callback'   => 'eglatone_is_ect_testimonial_active',
			'choices'           => eglatone_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'eglatone' ),
			'section'           => 'eglatone_testimonials',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_testimonial_bg_image',
			'sanitize_callback' => 'eglatone_sanitize_image',
			'active_callback'   => 'eglatone_is_testimonial_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Background Image', 'eglatone' ),
			'section'           => 'eglatone_testimonials',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_testimonial_cpt_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Eglatone_Note_Control',
			'active_callback'   => 'eglatone_is_testimonial_active',
			/* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'eglatone' ),
				'<a href="javascript:wp.customize.section( \'jetpack_testimonials\' ).focus();">',
				'</a>'
			),
			'section'           => 'eglatone_testimonials',
			'type'              => 'description',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_testimonial_number',
			'default'           => '3',
			'sanitize_callback' => 'eglatone_sanitize_number_range',
			'active_callback'   => 'eglatone_is_testimonial_active',
			'label'             => esc_html__( 'Number of items', 'eglatone' ),
			'section'           => 'eglatone_testimonials',
			'type'              => 'number',
			'input_attrs'       => array(
				'style'             => 'width: 100px;',
				'min'               => 0,
			),
		)
	);

	$number = get_theme_mod( 'eglatone_testimonial_number', 3 );

	for ( $i = 1; $i <= $number ; $i++ ) {
		//for CPT
		eglatone_register_option( $wp_customize, array(
				'name'              => 'eglatone_testimonial_cpt_' . $i,
				'sanitize_callback' => 'eglatone_sanitize_post',
				'active_callback'   => 'eglatone_is_testimonial_active',
				'label'             => esc_html__( 'Testimonial', 'eglatone' ) . ' ' . $i ,
				'section'           => 'eglatone_testimonials',
				'type'              => 'select',
				'choices'           => eglatone_generate_post_array( 'jetpack-testimonial' ),
			)
		);
	} // End for().
}
add_action( 'customize_register', 'eglatone_testimonial_options' );

/**
 * Active Callback Functions
 */
if ( ! function_exists( 'eglatone_is_testimonial_active' ) ) :
	/**
	* Return true if testimonial is active
	*
	* @since Eglatone 0.1
	*/
	function eglatone_is_testimonial_active( $control ) {
		$enable = $control->manager->get_setting( 'eglatone_testimonial_option' )->value();

		//return true only if previwed page on customizer matches the type of content option selected
		return eglatone_check_section( $enable );
	}
endif;

if ( ! function_exists( 'eglatone_is_ect_testimonial_inactive' ) ) :
    /**
    *
    * @since Chique 1.0
    */
    function eglatone_is_ect_testimonial_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Jetpack_testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_testimonial' ) );
    }
endif;

if ( ! function_exists( 'eglatone_is_ect_testimonial_active' ) ) :
    /**
    *
    * @since Chique 1.0
    */
    function eglatone_is_ect_testimonial_active( $control ) {
        return ( class_exists( 'Essential_Content_Jetpack_testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_testimonial' ) );
    }
endif;
