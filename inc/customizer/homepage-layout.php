<?php
/**
 * Homepage Layout options
 *
 * Column counts for the Services and Featured Content carousels and for the
 * blog ("News") grid, plus how many posts each "... more" click loads.
 *
 * @package Eglatone
 */

/**
 * Allowed column choices, shared by the controls and the sanitizers.
 *
 * @return array
 */
function eglatone_layout_column_choices() {
	return array(
		'1' => esc_html__( '1 column', 'eglatone' ),
		'2' => esc_html__( '2 columns', 'eglatone' ),
		'3' => esc_html__( '3 columns', 'eglatone' ),
		'4' => esc_html__( '4 columns', 'eglatone' ),
	);
}

/**
 * Sanitize a column count.
 *
 * @param mixed $input Raw value.
 * @return int
 */
function eglatone_sanitize_columns( $input ) {
	$input = (int) $input;

	return ( $input >= 1 && $input <= 4 ) ? $input : 3;
}

/**
 * Sanitize the "posts per batch" value.
 *
 * @param mixed $input Raw value.
 * @return int
 */
function eglatone_sanitize_batch_size( $input ) {
	$input = absint( $input );

	if ( $input < 1 ) {
		$input = 16;
	}

	if ( $input > 100 ) {
		$input = 100;
	}

	return $input;
}

/**
 * Register the Homepage Layout customizer section.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_homepage_layout_options( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_homepage_layout', array(
		'title'       => esc_html__( 'Homepage Layout', 'eglatone' ),
		'panel'       => 'eglatone_theme_options',
		'priority'    => 5,
		'description' => esc_html__( 'Set how many columns the homepage sections and the blog grid use.', 'eglatone' ),
	) );

	// Services section columns.
	$wp_customize->add_setting( 'eglatone_service_columns', array(
		'default'           => 3,
		'sanitize_callback' => 'eglatone_sanitize_columns',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'eglatone_service_columns', array(
		'label'       => esc_html__( 'Services section: cards per row', 'eglatone' ),
		'description' => esc_html__( 'Arrows appear only when you have more services than fit in one row.', 'eglatone' ),
		'section'     => 'eglatone_homepage_layout',
		'type'        => 'select',
		'choices'     => eglatone_layout_column_choices(),
	) );

	// Featured Content section columns.
	$wp_customize->add_setting( 'eglatone_featured_columns', array(
		'default'           => 3,
		'sanitize_callback' => 'eglatone_sanitize_columns',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'eglatone_featured_columns', array(
		'label'       => esc_html__( 'Featured Content section: cards per row', 'eglatone' ),
		'description' => esc_html__( 'Arrows appear only when you have more items than fit in one row.', 'eglatone' ),
		'section'     => 'eglatone_homepage_layout',
		'type'        => 'select',
		'choices'     => eglatone_layout_column_choices(),
	) );

	// Blog grid columns.
	$blog_choices = eglatone_layout_column_choices();
	unset( $blog_choices['1'] );

	$wp_customize->add_setting( 'eglatone_blog_columns', array(
		'default'           => 4,
		'sanitize_callback' => 'eglatone_sanitize_columns',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'eglatone_blog_columns', array(
		'label'       => esc_html__( 'Blog grid: columns', 'eglatone' ),
		'description' => esc_html__( 'Columns on desktop. The grid steps down automatically on tablets and phones.', 'eglatone' ),
		'section'     => 'eglatone_homepage_layout',
		'type'        => 'select',
		'choices'     => $blog_choices,
	) );

	// Posts per "... more" batch.
	$wp_customize->add_setting( 'eglatone_blog_batch', array(
		'default'           => 16,
		'sanitize_callback' => 'eglatone_sanitize_batch_size',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'eglatone_blog_batch', array(
		'label'       => esc_html__( 'Blog grid: posts per load', 'eglatone' ),
		'description' => esc_html__( 'How many posts show initially, and how many each "... more" click adds. 1-100.', 'eglatone' ),
		'section'     => 'eglatone_homepage_layout',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 1,
			'max'  => 100,
			'step' => 1,
		),
	) );
}
add_action( 'customize_register', 'eglatone_homepage_layout_options', 12 );
