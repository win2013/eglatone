<?php
/**
 * Hero Content Options
 *
 * @package Eglatone
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_hero_content_options( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_hero_content_options', array(
			'title' => esc_html__( 'Hero Content', 'eglatone' ),
			'panel' => 'eglatone_theme_options',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_hero_content_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'choices'           => eglatone_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'eglatone' ),
			'section'           => 'eglatone_hero_content_options',
			'type'              => 'select',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_hero_content',
			'default'           => '0',
			'sanitize_callback' => 'eglatone_sanitize_post',
			'active_callback'   => 'eglatone_is_hero_content_active',
			'label'             => esc_html__( 'Page', 'eglatone' ),
			'section'           => 'eglatone_hero_content_options',
			'type'              => 'dropdown-pages',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_hero_content_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'active_callback'   => 'eglatone_is_hero_content_active',
			'choices'           => eglatone_content_show(),
			'label'             => esc_html__( 'Display Content', 'eglatone' ),
			'section'           => 'eglatone_hero_content_options',
			'type'              => 'select',
		)
	);
}
add_action( 'customize_register', 'eglatone_hero_content_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'eglatone_is_hero_content_active' ) ) :
	/**
	* Return true if hero content is active
	*
	* @since Eglatone 0.1
	*/
	function eglatone_is_hero_content_active( $control ) {
		$enable = $control->manager->get_setting( 'eglatone_hero_content_visibility' )->value();

		return eglatone_check_section( $enable );
	}
endif;
