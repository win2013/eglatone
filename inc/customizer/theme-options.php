<?php
/**
 * Theme Options
 *
 * @package Eglatone
 */

/**
 * Add theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_theme_options( $wp_customize ) {
	$wp_customize->add_panel( 'eglatone_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'eglatone' ),
		'priority' => 130,
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_latest_posts_title',
			'default'           => esc_html__( 'News', 'eglatone' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Latest Posts Title', 'eglatone' ),
			'section'           => 'eglatone_theme_options',
		)
	);

	// Layout Options
	$wp_customize->add_section( 'eglatone_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'eglatone' ),
		'panel' => 'eglatone_theme_options',
		)
	);

	/* Default Layout */
	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'eglatone' ),
			'section'           => 'eglatone_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'eglatone' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'eglatone' ),
			),
		)
	);

	/* Homepage/Archive Layout */
	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_homepage_archive_layout',
			'default'           => 'no-sidebar-full-width',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'label'             => esc_html__( 'Homepage/Archive Layout', 'eglatone' ),
			'section'           => 'eglatone_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'eglatone' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'eglatone' ),
			),
		)
	);

	/* Single Page/Post Image */
	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_single_layout',
			'default'           => 'disabled',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'label'             => esc_html__( 'Single Page/Post Image', 'eglatone' ),
			'section'           => 'eglatone_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'disabled'              => esc_html__( 'Disabled', 'eglatone' ),
				'post-thumbnail'        => esc_html__( 'Post Thumbnail', 'eglatone' ),
			),
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'eglatone_excerpt_options', array(
		'panel'     => 'eglatone_theme_options',
		'title'     => esc_html__( 'Excerpt Options', 'eglatone' ),
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_excerpt_length',
			'default'           => '20',
			'sanitize_callback' => 'absint',
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'eglatone' ),
			'section'  => 'eglatone_excerpt_options',
			'type'     => 'number',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_excerpt_more_text',
			'default'           => esc_html__( 'Continue reading...', 'eglatone' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Read More Text', 'eglatone' ),
			'section'           => 'eglatone_excerpt_options',
			'type'              => 'text',
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'eglatone_search_options', array(
		'panel'     => 'eglatone_theme_options',
		'title'     => esc_html__( 'Search Options', 'eglatone' ),
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_search_text',
			'default'           => esc_html__( 'Search', 'eglatone' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Search Text', 'eglatone' ),
			'section'           => 'eglatone_search_options',
			'type'              => 'text',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'eglatone_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'eglatone' ),
		'panel'       => 'eglatone_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'eglatone' ),
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_recent_posts_heading',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__( 'News', 'eglatone' ),
			'label'             => esc_html__( 'Recent Posts Heading', 'eglatone' ),
			'section'           => 'eglatone_homepage_options',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_static_page_heading',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'	=> 'eglatone_is_static_page_enabled',
			'default'           => esc_html__( 'Archives', 'eglatone' ),
			'label'             => esc_html__( 'Posts Page Header Text', 'eglatone' ),
			'section'           => 'eglatone_homepage_options',
		)
	);

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_front_page_category',
			'sanitize_callback' => 'eglatone_sanitize_category_list',
			'custom_control'    => 'Eglatone_Multi_Cat',
			'label'             => esc_html__( 'Categories', 'eglatone' ),
			'section'           => 'eglatone_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	// Pagination Options.
	$pagination_type = get_theme_mod( 'eglatone_pagination_type', 'default' );

	$nav_desc = '';

	/**
	* Check if navigation type is Jetpack Infinite Scroll and if it is enabled
	*/
	$nav_desc = sprintf(
		wp_kses(
			__( 'For infinite scrolling, use %1$sCatch Infinite Scroll Plugin%2$s with Infinite Scroll module Enabled.', 'eglatone' ),
			array(
				'a' => array(
					'href' => array(),
					'target' => array(),
				),
				'br'=> array()
			)
		),
		'<a target="_blank" href="https://wordpress.org/plugins/catch-infinite-scroll/">',
		'</a>'
	);

	$wp_customize->add_section( 'eglatone_pagination_options', array(
		'description'     => $nav_desc,
		'panel'           => 'eglatone_theme_options',
		'title'           => esc_html__( 'Pagination Options', 'eglatone' ),
		'active_callback' => 'eglatone_scroll_plugins_inactive'
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'eglatone_sanitize_select',
			'choices'           => eglatone_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'eglatone' ),
			'section'           => 'eglatone_pagination_options',
			'type'              => 'select',
		)
	);

	/* Scrollup Options */
	$wp_customize->add_section( 'eglatone_scrollup', array(
		'panel'    => 'eglatone_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'eglatone' ),
	) );

	$action = 'install-plugin';
	$slug   = 'to-top';

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

	// Add note to Scroll up Section
    eglatone_register_option( $wp_customize, array(
            'name'              => 'eglatone_to_top_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Eglatone_Note_Control',
            'active_callback'   => 'eglatone_is_to_top_inactive',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For Scroll Up, install %1$sTo Top%2$s Plugin', 'eglatone' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'

            ),
           'section'            => 'eglatone_scrollup',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    eglatone_register_option( $wp_customize, array(
            'name'              => 'eglatone_to_top_option_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Eglatone_Note_Control',
            'active_callback'   => 'eglatone_is_to_top_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For Scroll Up Options, go %1$shere%2$s', 'eglatone'  ),
                 '<a href="javascript:wp.customize.panel( \'to_top_panel\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'eglatone_scrollup',
            'type'              => 'description',
        )
    );

	//Footer Background Image.
	$wp_customize->add_section( 'eglatone_footer_background', array(
		'panel'     => 'eglatone_theme_options',
		'title'     => esc_html__( 'Footer Background Image', 'eglatone' ),
	) );

	eglatone_register_option( $wp_customize, array(
			'name'              => 'eglatone_footer_bg_image',
			'sanitize_callback' => 'eglatone_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Footer Background Image', 'eglatone' ),
			'section'           => 'eglatone_footer_background',
		)
	);
}
add_action( 'customize_register', 'eglatone_theme_options' );


/** Active Callback Functions */
if ( ! function_exists( 'eglatone_scroll_plugins_inactive' ) ) :
	/**
	* Return true if infinite scroll functionality exists
	*
	* @since Eglatone 0.1
	*/
	function eglatone_scroll_plugins_inactive( $control ) {
		if ( ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) || class_exists( 'Catch_Infinite_Scroll' ) ) {
			// Support infinite scroll plugins.
			return false;
		}

		return true;
	}
endif;

if ( ! function_exists( 'eglatone_is_static_page_enabled' ) ) :
	/**
	* Return true if A Static Page is enabled
	*
	* @since Eglatone 1.1.2
	*/
	function eglatone_is_static_page_enabled( $control ) {
		$enable = $control->manager->get_setting( 'show_on_front' )->value();
		if ( 'page' === $enable ) {
			return true;
		}
		return false;
	}
endif;

if ( ! function_exists( 'eglatone_is_to_top_inactive' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Simclick 0.1
    */
    function eglatone_is_to_top_inactive( $control ) {
        return ! ( class_exists( 'To_Top' ) );
    }
endif;

if ( ! function_exists( 'eglatone_is_to_top_active' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Simclick 0.1
    */
    function eglatone_is_to_top_active( $control ) {
        return ( class_exists( 'To_Top' ) );
    }
endif;
