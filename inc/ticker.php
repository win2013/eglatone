<?php
/**
 * TECHED.TV ticker
 *
 * A slim scrolling strip of the latest podcast episodes, rendered between the
 * Featured Content section and the blog grid on the front page.
 *
 * Items come from a feed URL (default: this site's own /feed/podcast), cached
 * in a transient. Because the default feed is local, permalinks resolve back to
 * real posts, so we can pull proper featured images rather than feed artwork.
 *
 * @package Eglatone
 */

/**
 * Is the ticker switched on?
 *
 * @return bool
 */
function eglatone_ticker_enabled() {
	return (bool) get_theme_mod( 'eglatone_ticker_enable', true );
}

/**
 * Feed the ticker reads from.
 *
 * @return string
 */
function eglatone_ticker_feed_url() {
	$url = get_theme_mod( 'eglatone_ticker_feed', '' );

	if ( '' === trim( (string) $url ) ) {
		$url = home_url( '/feed/podcast' );
	}

	return esc_url_raw( $url );
}

/**
 * How many items to show.
 *
 * @return int
 */
function eglatone_ticker_count() {
	$count = (int) get_theme_mod( 'eglatone_ticker_count', 10 );

	if ( $count < 2 ) {
		$count = 2;
	}

	if ( $count > 30 ) {
		$count = 30;
	}

	return $count;
}

/**
 * Seconds for one full pass of the strip. Larger = slower.
 *
 * @return int
 */
function eglatone_ticker_speed() {
	$speed = (int) get_theme_mod( 'eglatone_ticker_speed', 45 );

	if ( $speed < 10 ) {
		$speed = 10;
	}

	if ( $speed > 240 ) {
		$speed = 240;
	}

	return $speed;
}

/**
 * Fetch and normalise the ticker items.
 *
 * Cached for 6 hours. The cache key includes the feed URL and item count, so
 * changing either in the Customizer busts the cache automatically.
 *
 * @return array List of array( title, link, date, thumb ).
 */
function eglatone_ticker_items() {
	$url   = eglatone_ticker_feed_url();
	$count = eglatone_ticker_count();

	if ( ! $url ) {
		return array();
	}

	$key    = 'eglatone_ticker_' . md5( $url . '|' . $count );
	$cached = get_transient( $key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	if ( ! function_exists( 'fetch_feed' ) ) {
		include_once ABSPATH . WPINC . '/feed.php';
	}

	$feed = fetch_feed( $url );

	if ( is_wp_error( $feed ) ) {
		// Cache the failure briefly so a broken feed does not stall every request.
		set_transient( $key, array(), 15 * MINUTE_IN_SECONDS );

		return array();
	}

	$max   = $feed->get_item_quantity( $count );
	$items = $feed->get_items( 0, $max );
	$out   = array();

	foreach ( $items as $item ) {
		$link  = $item->get_permalink();
		$title = $item->get_title();

		if ( ! $link || ! $title ) {
			continue;
		}

		$thumb = '';

		/*
		 * The default feed is this site's own, so the permalink usually maps to
		 * a local post - that gives us a real, correctly sized featured image.
		 */
		$post_id = url_to_postid( $link );

		if ( $post_id ) {
			$thumb = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
		}

		// Fall back to whatever artwork the feed itself carries.
		if ( ! $thumb ) {
			$enclosure = $item->get_enclosure();

			if ( $enclosure ) {
				$candidate = $enclosure->get_thumbnail();

				if ( ! $candidate ) {
					$candidate = $enclosure->get_link();

					// Only use the enclosure link if it is actually an image.
					if ( $candidate && ! preg_match( '/\.(jpe?g|png|gif|webp)(\?|$)/i', $candidate ) ) {
						$candidate = '';
					}
				}

				if ( $candidate ) {
					$thumb = $candidate;
				}
			}
		}

		$timestamp = $item->get_date( 'U' );

		$out[] = array(
			'title' => wp_strip_all_tags( $title ),
			'link'  => esc_url_raw( $link ),
			'date'  => $timestamp ? date_i18n( get_option( 'date_format' ), (int) $timestamp ) : '',
			'thumb' => $thumb ? esc_url_raw( $thumb ) : '',
		);
	}

	set_transient( $key, $out, 6 * HOUR_IN_SECONDS );

	return $out;
}

/**
 * Customizer controls for the ticker.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_ticker_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_ticker', array(
		'title'       => esc_html__( 'News Ticker', 'eglatone' ),
		'panel'       => 'eglatone_theme_options',
		'priority'    => 6,
		'description' => esc_html__( 'A scrolling strip of the latest episodes, shown between the Featured Content section and the blog grid on the front page.', 'eglatone' ),
	) );

	$wp_customize->add_setting( 'eglatone_ticker_enable', array(
		'default'           => true,
		'sanitize_callback' => 'eglatone_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'eglatone_ticker_enable', array(
		'label'   => esc_html__( 'Show the ticker', 'eglatone' ),
		'section' => 'eglatone_ticker',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'eglatone_ticker_heading', array(
		'default'           => esc_html__( 'Latest from TECHED.TV', 'eglatone' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_ticker_heading', array(
		'label'       => esc_html__( 'Label', 'eglatone' ),
		'description' => esc_html__( 'Short text shown at the start of the strip. Leave empty for no label.', 'eglatone' ),
		'section'     => 'eglatone_ticker',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_ticker_feed', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'eglatone_ticker_feed', array(
		'label'       => esc_html__( 'Feed URL', 'eglatone' ),
		/* translators: %s: default feed URL. */
		'description' => sprintf( esc_html__( 'Leave empty to use %s. Any RSS or Atom feed works - e.g. your Medium or YouTube feed.', 'eglatone' ), esc_html( home_url( '/feed/podcast' ) ) ),
		'section'     => 'eglatone_ticker',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'eglatone_ticker_count', array(
		'default'           => 10,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'eglatone_ticker_count', array(
		'label'       => esc_html__( 'Number of items', 'eglatone' ),
		'section'     => 'eglatone_ticker',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 2,
			'max'  => 30,
			'step' => 1,
		),
	) );

	$wp_customize->add_setting( 'eglatone_ticker_speed', array(
		'default'           => 45,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'eglatone_ticker_speed', array(
		'label'       => esc_html__( 'Seconds per full pass', 'eglatone' ),
		'description' => esc_html__( 'Higher is slower. 45 is a comfortable reading pace.', 'eglatone' ),
		'section'     => 'eglatone_ticker',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 240,
			'step' => 5,
		),
	) );
}
add_action( 'customize_register', 'eglatone_ticker_customize_register', 12 );

/**
 * Drop the cached feed whenever the Customizer is saved, so edits show up at once.
 */
function eglatone_ticker_flush_cache() {
	$key = 'eglatone_ticker_' . md5( eglatone_ticker_feed_url() . '|' . eglatone_ticker_count() );

	delete_transient( $key );
}
add_action( 'customize_save_after', 'eglatone_ticker_flush_cache' );
