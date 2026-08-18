<?php
/**
 * Playlist Options
 *
 * @package Eglatone
 */

/**
 * Add sticky_playlist options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_sticky_playlist( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_sticky_playlist', array(
			'title' => esc_html__( 'Sticky Playlist', 'eglatone' ),
			'panel' => 'eglatone_theme_options',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_sticky_playlist_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'choices'           => eglatone_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'eglatone' ),
			'section'           => 'eglatone_sticky_playlist',
			'type'              => 'select',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_sticky_playlist',
			'default'           => '0',
			'sanitize_callback' => 'eglatone_sanitize_post',
			'active_callback'   => 'eglatone_is_sticky_playlist_active',
			'label'             => esc_html__( 'Page', 'eglatone' ),
			'section'           => 'eglatone_sticky_playlist',
			'type'              => 'dropdown-pages',
		)
	);
}
add_action( 'customize_register', 'eglatone_sticky_playlist', 12 );

/** Active Callback Functions **/
if ( ! function_exists( 'eglatone_is_sticky_playlist_active' ) ) :
	/**
	* Return true if sticky_playlist is active
	*
	* @since Eglatone 0.1
	*/
	function eglatone_is_sticky_playlist_active( $control ) {
		$enable = $control->manager->get_setting( 'eglatone_sticky_playlist_visibility' )->value();

		return eglatone_check_section( $enable );
	}
endif;