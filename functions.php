<?php
/**
 * Eglatone functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Eglatone
 */

/**
 * Add an HTML class to MediaElement.js container elements to aid styling.
 *
 * Extends the core _wpmejsSettings object to add a new feature via the
 * MediaElement.js plugin API.
 */
function eglatone_mejs_add_container_class() {
	if ( ! wp_script_is( 'mediaelement', 'done' ) ) {
		return;
	}
	?>
	<script>
	(function() {
		var settings = window._wpmejsSettings || {};

		settings.features = settings.features || mejs.MepDefaults.features;

		settings.features.push( 'eglatone_class' );

		MediaElementPlayer.prototype.buildeglatone_class = function(player, controls, layers, media) {
			if ( ! player.isVideo ) {
				var container = player.container[0] || player.container;

				container.style.height = '';
				container.style.width = '';
				player.options.setDimensions = false;
			}

			if ( jQuery( '#' + player.id ).parents('#sticky-playlist-section').length ) {
				player.container.addClass( 'eglatone-mejs-container eglatone-mejs-sticky-playlist-container' );

				jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').addClass('displaynone');

				var volume_slider = controls[0].children[5];

				if ( jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').length > 0) {
					var playlist_button =
					jQuery('<div class="mejs-button mejs-playlist-button mejs-toggle-playlist">' +
						'<button type="button" aria-controls="mep_0" title="Toggle Playlist"></button>' +
					'</div>')

					// append it to the toolbar
					.appendTo( jQuery( '#' + player.id ) )

					// add a click toggle event
					.on( 'click',function() {
						jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').slideToggle();
						jQuery( this ).toggleClass('is-open')
					});

					var play_button = controls[0].children[0];

					// Add next button after volume slider
					var next_button =
					jQuery('<div class="mejs-button mejs-next-button mejs-next">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Next Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertAfter(play_button)

					// add a click toggle event
					.on( 'click',function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-next').trigger('click');
					});

					// Add prev button after volume slider
					var previous_button =
					jQuery('<div class="mejs-button mejs-previous-button mejs-previous">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Previous Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertBefore( play_button )

					// add a click toggle event
					.on( 'click',function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-prev').trigger('click');
					});
				}
			} else {
				player.container.addClass( 'eglatone-mejs-container' );
				if ( jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').length > 0) {
					var play_button = controls[0].children[0];

					// Add next button after volume slider
					var next_button =
					jQuery('<div class="mejs-button mejs-next-button mejs-next">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Next Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertAfter(play_button)

					// add a click toggle event
					.on( 'click',function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-next').trigger('click');
					});

					// Add prev button after volume slider
					var previous_button =
					jQuery('<div class="mejs-button mejs-previous-button mejs-previous">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Previous Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertBefore( play_button )

					// add a click toggle event
					.on( 'click',function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-prev').trigger('click');
					});
				}
			}
		}
	})();
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'eglatone_mejs_add_container_class', 11 );

if ( ! function_exists( 'eglatone_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function eglatone_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Eglatone, use a find and replace
		 * to change 'eglatone' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'eglatone', get_parent_theme_file_path( '/languages' ) );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * This theme styles the visual editor to resemble the theme style,
		 * specifically font, colors, and column width.
		 *
		 * Google fonts url addition
		 *
		 * Font Awesome addition
		 */
		add_editor_style( array(
			'assets/css/editor-style.css',
			eglatone_fonts_url(),
			trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/css/font-awesome/css/all.min.css' )
		);

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Used in Portfolio
		set_post_thumbnail_size( 666, 666, true ); // Ratio 1:1

		// Used in Archive: Excerpt image
		add_image_size( 'eglatone-archive', 1920, 9999, true ); // Flexible Height

		// Used in featured slider
		add_image_size( 'eglatone-slider', 1920, 1080, true );

		// Used in testimonials and Playlist
		add_image_size( 'eglatone-testimonial', 720, 720, true ); // Ratio 1:1

		// Used in team
		add_image_size( 'eglatone-team', 1160, 670, true ); // Ratio 16:10

		// Used in service
		add_image_size( 'eglatone-service', 140, 140, true ); // Ratio 1:1

		// Used in logo slider and Stats Section
		add_image_size( 'eglatone-logo-slider', 140, 80, true ); // Ratio 4:3

		// Used in Stats Section
		add_image_size( 'eglatone-stats', 80, 80, true ); // Ratio 1:1

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1'              => esc_html__( 'Primary', 'eglatone' ),
			'social-menu'         => esc_html__( 'Header Social Menu', 'eglatone' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		/**
		 * Add support for essential widget image.
		 *
		 */
		add_theme_support( 'ew-newsletter-image' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( 'Small', 'eglatone' ),
					'shortName' => esc_html__( 'S', 'eglatone' ),
					'size'      => 13,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( 'Normal', 'eglatone' ),
					'shortName' => esc_html__( 'M', 'eglatone' ),
					'size'      => 18,
					'slug'      => 'normal',
				),
				array(
					'name'      => esc_html__( 'Large', 'eglatone' ),
					'shortName' => esc_html__( 'L', 'eglatone' ),
					'size'      => 42,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( 'Huge', 'eglatone' ),
					'shortName' => esc_html__( 'XL', 'eglatone' ),
					'size'      => 56,
					'slug'      => 'huge',
				),
			)
		);

		// Add support for custom color scheme.
		add_theme_support( 'editor-color-palette', array(
			array(
				'name'  => esc_html__( 'White', 'eglatone' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
			array(
				'name'  => esc_html__( 'Black', 'eglatone' ),
				'slug'  => 'black',
				'color' => '#000000',
			),
			array(
				'name'  => esc_html__( 'Eighty Black', 'eglatone' ),
				'slug'  => 'eighty-black',
				'color' => '#151515',
			),
			array(
				'name'  => esc_html__( 'Sixty Five Black', 'eglatone' ),
				'slug'  => 'sixty-five-black',
				'color' => '#151515',
			),
			array(
				'name'  => esc_html__( 'Gray', 'eglatone' ),
				'slug'  => 'gray',
				'color' => '#444444',
			),
			array(
				'name'  => esc_html__( 'Medium Gray', 'eglatone' ),
				'slug'  => 'medium-gray',
				'color' => '#7b7b7b',
			),
			array(
				'name'  => esc_html__( 'Light Gray', 'eglatone' ),
				'slug'  => 'light-gray',
				'color' => '#f8f8f8',
			),
			array(
				'name'  => esc_html__( 'Dark Yellow', 'eglatone' ),
				'slug'  => 'dark-yellow',
				'color' => '#ffa751',
			),
			array(
				'name'  => esc_html__( 'Yellow', 'eglatone' ),
				'slug'  => 'yellow',
				'color' => '#f9a926',
			),
		) );

		/**
		 * Adds support for Catch Breadcrumb.
		 */
		add_theme_support( 'catch-breadcrumb', array(
			'content_selector' => '.site-content .wrapper .content-area',
			'breadcrumb_dynamic' => 'before',
		) );
	}
endif;
add_action( 'after_setup_theme', 'eglatone_setup' );

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 *
 */
function eglatone_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-2' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar-4' ) ) {
		$count++;
	}

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class ) {
		echo 'class="widget-area footer-widget-area ' . esc_attr( $class ) . '"';
	}
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function eglatone_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'eglatone_content_width', 920 );
}
add_action( 'after_setup_theme', 'eglatone_content_width', 0 );

if ( ! function_exists( 'eglatone_template_redirect' ) ) :
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet for different value other than the default one
	 *
	 * @global int $content_width
	 */
	function eglatone_template_redirect() {
		$layout = eglatone_get_theme_layout();

		if ( 'no-sidebar-full-width' === $layout ) {
			$GLOBALS['content_width'] = 1510;
		}
	}
endif;
add_action( 'template_redirect', 'eglatone_template_redirect' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function eglatone_widgets_init() {
	$args = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar( array(
		'name'        => esc_html__( 'Sidebar', 'eglatone' ),
		'id'          => 'sidebar-1',
		'description' => esc_html__( 'Add widgets here.', 'eglatone' ),
		) + $args
	);

	register_sidebar( array(
		'name'        => esc_html__( 'Footer 1', 'eglatone' ),
		'id'          => 'sidebar-2',
		'description' => esc_html__( 'Add widgets here to appear in your footer.', 'eglatone' ),
		) + $args
	);

	register_sidebar( array(
		'name'        => esc_html__( 'Footer 2', 'eglatone' ),
		'id'          => 'sidebar-3',
		'description' => esc_html__( 'Add widgets here to appear in your footer.', 'eglatone' ),
		) + $args
	);

	register_sidebar( array(
		'name'        => esc_html__( 'Footer 3', 'eglatone' ),
		'id'          => 'sidebar-4',
		'description' => esc_html__( 'Add widgets here to appear in your footer.', 'eglatone' ),
		) + $args
	);

	register_sidebar( array(
		'name'        => esc_html__( 'Instagram', 'eglatone' ),
		'id'          => 'sidebar-instagram',
		'description' => esc_html__( 'AAppears above footer. This sidebar is only for Widget from Instagram Gallery', 'eglatone' ),
		) + $args
	);

}
add_action( 'widgets_init', 'eglatone_widgets_init' );

if ( ! function_exists( 'eglatone_fonts_url' ) ) :
	/**
	 * Register Google fonts for Eglatone
	 *
	 * Create your own eglatone_fonts_url() function to override in a child theme.
	 *
	 * @since Eglatone 0.1
	 *
	 * @return string Google fonts URL for the theme.
	 */
	function eglatone_fonts_url() {
		/* Translators: If there are characters in your language that are not
		* supported by Montserrat, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$montserrat = _x( 'on', 'Montserrat: on or off', 'eglatone' );

		if ( 'off' === $montserrat ) {
			return;
		}

		// Load google font locally.
		require_once get_theme_file_path( 'inc/wptt-webfont-loader.php' );

		return wptt_get_webfont_url( 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,400italic,700italic' );
	}
endif;

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Eglatone 0.1
 */
function eglatone_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'eglatone_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 */
function eglatone_scripts() {
	$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$path = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/source/' : 'assets/js/';

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'eglatone-fonts', eglatone_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'eglatone-style', get_stylesheet_uri(), null, date( 'Ymd-Gis', filemtime( get_template_directory() . '/style.css' ) ) );

	// Theme block stylesheet.
	wp_enqueue_style( 'eglatone-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'eglatone-style' ), '1.0' );

	// Font Awesome
	wp_enqueue_style('font-awesome', trailingslashit(esc_url(get_template_directory_uri())) . 'assets/css/font-awesome/css/all.min.css', array(), '6.7.2', 'all');

	wp_enqueue_script( 'eglatone-skip-link-focus-fix', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'skip-link-focus-fix' . $min . '.js', array(), '201800703', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'jquery-match-height', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'jquery.matchHeight' . $min . '.js', array( 'jquery' ), '201800703', true );

	$deps[] = 'jquery';

	$enable_featured_content = get_theme_mod( 'eglatone_featured_content_option', 'disabled' );
	$enable_contact          = get_theme_mod( 'eglatone_contact_section_option', 'disabled' );

	if ( eglatone_check_section( $enable_featured_content ) || eglatone_check_section( $enable_contact ) ) {
		$deps[] = 'jquery-match-height';
	}

	//Slider Scripts
	$enable_slider      = eglatone_check_section( get_theme_mod( 'eglatone_slider_option', 'disabled' ) );
	$enable_testimonial_slider      = eglatone_check_section( get_theme_mod( 'eglatone_testimonial_option', 'disabled' ) );

	// Service and Featured Content sections are rendered as 2-up carousels.
	$enable_service = eglatone_check_section( get_theme_mod( 'eglatone_service_option', 'disabled' ) );

	if ( $enable_slider || $enable_testimonial_slider || $enable_service || eglatone_check_section( $enable_featured_content ) ) {
		// Enqueue owl carousel css. Must load CSS before JS.
		wp_enqueue_style( 'owl-carousel-core', get_theme_file_uri( 'assets/css/owl-carousel/owl.carousel.min.css' ), null, '2.3.4' );
		wp_enqueue_style( 'owl-carousel-default', get_theme_file_uri( 'assets/css/owl-carousel/owl.theme.default.min.css' ), null, '2.3.4' );
		// Enqueue script
		wp_enqueue_script( 'owl-carousel', get_theme_file_uri( $path . 'owl.carousel' . $min . '.js'), array( 'jquery' ), '2.3.4', true );

		$deps[] = 'owl-carousel';

	}

	wp_enqueue_script( 'eglatone-script', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'functions' . $min . '.js', $deps, '201800703', true );

	wp_localize_script( 'eglatone-script', 'eglatoneOptions', array(
		'screenReaderText' => array(
			'expand'   => esc_html__( 'expand child menu', 'eglatone' ),
			'collapse' => esc_html__( 'collapse child menu', 'eglatone' ),
		),
		'rtl' => is_rtl(),
	) );

	// Homepage layout overrides: 2-up section carousels + blog grid loader.
	$eglatone_layout_css = get_template_directory() . '/assets/css/homepage-layout.css';
	$eglatone_layout_js  = get_template_directory() . '/assets/js/homepage-layout.js';

	if ( file_exists( $eglatone_layout_css ) ) {
		wp_enqueue_style(
			'eglatone-homepage-layout',
			get_theme_file_uri( 'assets/css/homepage-layout.css' ),
			array( 'eglatone-style' ),
			date( 'Ymd-Gis', filemtime( $eglatone_layout_css ) )
		);
	}

	if ( file_exists( $eglatone_layout_js ) ) {
		wp_enqueue_script(
			'eglatone-homepage-layout',
			get_theme_file_uri( 'assets/js/homepage-layout.js' ),
			array_unique( array_merge( $deps, array( 'jquery' ) ) ),
			date( 'Ymd-Gis', filemtime( $eglatone_layout_js ) ),
			true
		);

		wp_localize_script( 'eglatone-homepage-layout', 'eglatoneLayout', array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'eglatone_load_more' ),
			'rtl'         => is_rtl(),
			'loadingText' => esc_html__( 'Loading&hellip;', 'eglatone' ),
			'moreText'    => esc_html__( '... more', 'eglatone' ),
			'doneText'    => esc_html__( 'You have reached the end.', 'eglatone' ),
			'errorText'   => esc_html__( 'Something went wrong. Please try again.', 'eglatone' ),
		) );
	}

	// Remove Media CSS, we have ouw own CSS for this.
	wp_deregister_style('wp-mediaelement');
}
add_action( 'wp_enqueue_scripts', 'eglatone_scripts' );

/**
 * Enqueue editor styles for Gutenberg
 */
function eglatone_block_editor_styles() {
	// Block styles.
	wp_enqueue_style( 'eglatone-block-editor-style', get_theme_file_uri( 'assets/css/editor-blocks.css' ) );

	// Add custom fonts.
	wp_enqueue_style( 'eglatone-fonts', eglatone_fonts_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'eglatone_block_editor_styles' );

if ( ! function_exists( 'eglatone_excerpt_length' ) ) :
	/**
	 * Sets the post excerpt length to n words.
	 *
	 * function tied to the excerpt_length filter hook.
	 * @uses filter excerpt_length
	 *
	 * @since Eglatone 0.1
	 */
	function eglatone_excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}

		// Getting data from Customizer Options
		$length	= get_theme_mod( 'eglatone_excerpt_length', 20 );

		return absint( $length );
	}
endif; //eglatone_excerpt_length
add_filter( 'excerpt_length', 'eglatone_excerpt_length', 999 );

if ( ! function_exists( 'eglatone_excerpt_more' ) ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a option from customizer
	 *
	 * @return string option from customizer prepended with an ellipsis.
	 */
	function eglatone_excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}

		$more_tag_text = get_theme_mod( 'eglatone_excerpt_more_text',  esc_html__( 'Continue reading', 'eglatone' ) );

		$link = sprintf( '<p><a href="%1$s" class="more-link">%2$s</a></p>',
			esc_url( get_permalink() ),
			/* translators: %s: Name of current post */
			wp_kses_data( $more_tag_text ). '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>'
			);

		return $link;
	}
endif;
add_filter( 'excerpt_more', 'eglatone_excerpt_more' );

if ( ! function_exists( 'eglatone_custom_excerpt' ) ) :
	/**
	 * Adds Continue reading link to more tag excerpts.
	 *
	 * function tied to the get_the_excerpt filter hook.
	 *
	 * @since Eglatone 0.1
	 */
	function eglatone_custom_excerpt( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$more_tag_text = get_theme_mod( 'eglatone_excerpt_more_text', esc_html__( 'Continue reading', 'eglatone' ) );

			$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
				esc_url( get_permalink() ),
				/* translators: %s: Name of current post */
				wp_kses_data( $more_tag_text ). '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>'
			);

			$link = ' &hellip; ' . $link;

			$output .= $link;
		}

		return $output;
	}
endif; //eglatone_custom_excerpt
add_filter( 'get_the_excerpt', 'eglatone_custom_excerpt' );

if ( ! function_exists( 'eglatone_more_link' ) ) :
	/**
	 * Replacing Continue reading link to the_content more.
	 *
	 * function tied to the the_content_more_link filter hook.
	 *
	 * @since Eglatone 0.1
	 */
	function eglatone_more_link( $more_link, $more_link_text ) {
		$more_tag_text = get_theme_mod( 'eglatone_excerpt_more_text', esc_html__( 'Continue reading', 'eglatone' ) );

		return ' &hellip; ' . str_replace( $more_link_text, wp_kses_data( $more_tag_text ), $more_link );
	}
endif; //eglatone_more_link
add_filter( 'the_content_more_link', 'eglatone_more_link', 10, 2 );

/**
 * Implement the Custom Header feature
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * Customizer additions
 */
require get_parent_theme_file_path( '/inc/customizer/customizer.php' );

/**
 * Color Scheme additions
 */
require get_parent_theme_file_path( '/inc/header-background-color.php' );

/**
 * Custom modules added to this theme.
 *
 * Loaded defensively: a file that has not been uploaded yet is skipped and
 * reported in the admin instead of taking the whole site down with a fatal
 * "failed to open stream" on require.
 */
function eglatone_load_custom_modules() {
	$modules = array(
		'/inc/schema-jsonld.php'             => 'Schema (JSON-LD) editor',
		'/inc/ticker.php'                    => 'TECHED.TV ticker',
		'/inc/hero.php'                      => 'Achievements hero section',
		'/inc/customizer/homepage-layout.php' => 'Homepage Layout customizer options',
	);

	$missing = array();

	foreach ( $modules as $relative => $label ) {
		$path = get_parent_theme_file_path( $relative );

		if ( file_exists( $path ) ) {
			require_once $path;
		} else {
			$missing[ $relative ] = $label;
		}
	}

	if ( $missing ) {
		add_action( 'admin_notices', function () use ( $missing ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			echo '<div class="notice notice-warning"><p><strong>' .
				esc_html__( 'Eglatone: some theme files are missing.', 'eglatone' ) .
				'</strong> ' .
				esc_html__( 'These features are switched off until the files are uploaded:', 'eglatone' ) .
				'</p><ul style="list-style:disc;margin-left:22px;">';

			foreach ( $missing as $relative => $label ) {
				echo '<li><code>' . esc_html( ltrim( $relative, '/' ) ) . '</code> &mdash; ' . esc_html( $label ) . '</li>';
			}

			echo '</ul></div>';
		} );
	}
}
eglatone_load_custom_modules();

/**
 * Load Jetpack compatibility file
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_parent_theme_file_path( '/inc/jetpack.php' );
}

/**
 * Load Social Widgets
 */
require get_parent_theme_file_path( '/inc/widget-social-icons.php' );

/**
 * Load TGMPA
 */
require get_parent_theme_file_path( '/inc/class-tgm-plugin-activation.php' );

/**
 * Load Theme About Page
 */
require get_parent_theme_file_path( '/inc/about.php' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function eglatone_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		// Catch Web Tools.
		array(
			'name' => 'Catch Web Tools', // Plugin Name, translation not required.
			'slug' => 'catch-web-tools',
		),
		// Catch Gallery.
		array(
			'name' => 'Catch Gallery', // Plugin Name, translation not required.
			'slug' => 'catch-gallery',
		),
	);

	if ( ! class_exists( 'Catch_Infinite_Scroll_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Catch Infinite Scroll', // Plugin Name, translation not required.
			'slug' => 'catch-infinite-scroll',
		);
	}

	if ( ! class_exists( 'Essential_Content_Types_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Essential Content Types', // Plugin Name, translation not required.
			'slug' => 'essential-content-types',
		);
	}

	if ( ! class_exists( 'Essential_Widgets_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Essential Widgets', // Plugin Name, translation not required.
			'slug' => 'essential-widgets',
		);
	}

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'eglatone',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'eglatone_register_required_plugins' );

/**
 * Checks if there are options already present from free version and adds it to the Pro theme options
 *
 * @since Eglatone 0.1
 * @hook after_theme_switch
 */
function eglatone_setup_options( $old_theme_name ) {
	if ( $old_theme_name ) {
		$old_theme_slug = sanitize_title( $old_theme_name );
		$free_version_slug = array(
			'eglatone',
		);

		$pro_version_slug  = 'eglatone';

		$free_options = get_option( 'theme_mods_' . $old_theme_slug );

		// Perform action only if theme_mods_eglatone free version exists.
		if ( in_array( $old_theme_slug, $free_version_slug ) && $free_options && '1' !== get_theme_mod( 'free_pro_migration' ) ) {
			$new_options = wp_parse_args( get_theme_mods(), $free_options );

			if ( update_option( 'theme_mods_' . $pro_version_slug, $free_options ) ) {
				// Set Migration Parameter to true so that this script does not run multiple times.
				set_theme_mod( 'free_pro_migration', '1' );
			}
		}
	}
}
add_action( 'after_switch_theme', 'eglatone_setup_options' );
