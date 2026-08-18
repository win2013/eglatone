<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Eglatone
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Eglatone 0.1
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function eglatone_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Always add a front-page class to the front page.
	if ( is_front_page() && ! is_home() ) {
		$classes[] = 'page-template-front-page';
	}

	$classes[] = 'fluid-layout';

	$classes[] = 'navigation-default';

	// Adds a class with respect to layout selected.
	$layout  = eglatone_get_theme_layout();
	$sidebar = eglatone_get_sidebar_id();

	$layout_class = "no-sidebar content-width-layout";

	if ( 'no-sidebar-full-width' === $layout ) {
		$layout_class = 'no-sidebar full-width-layout';
	} elseif ( 'right-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$layout_class = 'two-columns-layout content-left';
		}
	}

	$classes[] = $layout_class;

	$classes[] = 'excerpt';

	$classes[] = 'header-media-fluid';

	$enable_sticky_playlist = get_theme_mod( 'eglatone_sticky_playlist_visibility', 'disabled' );

	if ( eglatone_check_section( $enable_sticky_playlist ) ) {
		$classes[] = 'sticky-playlist-enabled';
	}

	$enable_slider = eglatone_check_section( get_theme_mod( 'eglatone_slider_option', 'disabled' ) );

	$header_image = eglatone_featured_overall_image();

	if ( 'disable' !== $header_image ) {
		$classes[] = 'has-header-media';
	}

	if ( $enable_slider || 'disable' !== $header_image ) {
		$classes[] = 'absolute-header';
	}

	if ( ! eglatone_has_header_media_text() ) {
		$classes[] = 'header-media-text-disabled';
	}

	// Add a class if there is a custom header.
	if ( has_header_image() ) {
		$classes[] = 'has-header-image';
	}

	// Added color scheme to body class.
	$classes[] = 'color-scheme-' . esc_attr( get_theme_mod( 'color_scheme', 'default' ) );

	return $classes;
}
add_filter( 'body_class', 'eglatone_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function eglatone_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'eglatone_pingback_header' );

/**
 * Adds custom overlay for Header Media
 */
function eglatone_header_media_image_overlay_css() {
	$overlay = get_theme_mod( 'eglatone_header_media_image_opacity' );

	$css = '';

	$overlay_bg = $overlay / 100;

	if ( $overlay ) {
	$css = '.custom-header-overlay {
		background-color: rgba(0, 0, 0, ' . esc_attr( $overlay_bg ) . ' );
    } '; // Dividing by 100 as the option is shown as % for user
}

	wp_add_inline_style( 'eglatone-style', $css );
}
add_action( 'wp_enqueue_scripts', 'eglatone_header_media_image_overlay_css', 11 );

/**
 * Adds portfolio background CSS
 */
function eglatone_portfolio_bg_css() {
	$enable   = get_theme_mod( 'eglatone_portfolio_option', 'disabled' );

	if ( ! eglatone_check_section( $enable ) ) {
		// Bail if contact section is disabled.
		return;
	}
	$css = '';
	
	$background = get_theme_mod( 'eglatone_portfolio_bg_image' );
	
	if ( $background ) {
		$css = '#portfolio-content-section { background-image: url("' . esc_url( $background ) . '"); }';
	}

	wp_add_inline_style( 'eglatone-style', $css );
}
add_action( 'wp_enqueue_scripts', 'eglatone_portfolio_bg_css', 11 );

/**
 * Adds service background CSS
 */
function eglatone_service_bg_css() {
	$enable   = get_theme_mod( 'eglatone_service_option', 'disabled' );

	if ( ! eglatone_check_section( $enable ) ) {
		// Bail if contact section is disabled.
		return;
	}

	$css = '';
	
	$background = get_theme_mod( 'eglatone_service_bg_image' );
	
	if ( $background ) {
		$css = '#service-section { background-image: url("' . esc_url( $background ) . '"); }';
	}

	wp_add_inline_style( 'eglatone-style', $css );
}
add_action( 'wp_enqueue_scripts', 'eglatone_service_bg_css', 11 );

/**
 * Adds footer background CSS
 */
function eglatone_footer_bg_css() {
	$background = get_theme_mod( 'eglatone_footer_bg_image' );

	$css = '';
	
	if ( $background ) {
		$css = '#colophon { background-image: url("' . esc_url( $background ) . '"); }';
	}

	wp_add_inline_style( 'eglatone-style', $css );
}
add_action( 'wp_enqueue_scripts', 'eglatone_footer_bg_css', 11 );

/**
 * Remove first post from blog as it is already show via recent post template
 */
function eglatone_alter_home( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$cats = get_theme_mod( 'eglatone_front_page_category' );

		if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
			$query->query_vars['category__in'] = $cats;
		}

		if ( ! is_admin() ) {
			// Load the blog grid in batches, topped up by the "... more" button.
			$query->set( 'posts_per_page', eglatone_blog_batch_size() );
		}
	}
}
add_action( 'pre_get_posts', 'eglatone_alter_home' );

/**
 * Number of columns used by the blog ("News") grid.
 *
 * Set in Customizer > Theme Options > Homepage Layout. Also filterable via
 * add_filter( 'eglatone_blog_grid_columns', function() { return 3; } );
 *
 * @return int
 */
function eglatone_blog_grid_columns() {
	$columns = (int) get_theme_mod( 'eglatone_blog_columns', 4 );

	$columns = (int) apply_filters( 'eglatone_blog_grid_columns', $columns );

	// Only 2, 3 or 4 columns are supported by the stylesheet.
	return in_array( $columns, array( 2, 3, 4 ), true ) ? $columns : 4;
}

/**
 * Number of cards per row in a homepage section carousel.
 *
 * @param string $section Either 'service' or 'featured'.
 * @return int
 */
function eglatone_section_columns( $section = 'featured' ) {
	$mod = ( 'service' === $section ) ? 'eglatone_service_columns' : 'eglatone_featured_columns';

	$columns = (int) get_theme_mod( $mod, 3 );

	$columns = (int) apply_filters( 'eglatone_section_columns', $columns, $section );

	if ( $columns < 1 ) {
		$columns = 1;
	}

	if ( $columns > 4 ) {
		$columns = 4;
	}

	return $columns;
}

/**
 * How many posts are shown initially and added by each "... more" click.
 *
 * @return int
 */
function eglatone_blog_batch_size() {
	$number = (int) get_theme_mod( 'eglatone_blog_batch', 16 );

	$number = (int) apply_filters( 'eglatone_blog_batch_size', $number );

	if ( $number < 1 ) {
		$number = 16;
	}

	if ( $number > 100 ) {
		$number = 100;
	}

	return $number;
}

/**
 * Whether the current view should use the multi column blog grid with the
 * "... more" loader instead of the default stacked list + pagination.
 *
 * @return bool
 */
function eglatone_use_blog_grid() {
	return (bool) apply_filters( 'eglatone_use_blog_grid', is_home() );
}

/**
 * Build the WP_Query arguments used by the blog grid loader so that the
 * AJAX request matches the main query.
 *
 * @param int $paged Page of results to fetch.
 * @return array
 */
function eglatone_blog_grid_query_args( $paged ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => eglatone_blog_batch_size(),
		'paged'               => absint( $paged ),
		'ignore_sticky_posts' => 1, // Sticky posts are already pinned to page 1.
	);

	$cats = get_theme_mod( 'eglatone_front_page_category' );

	if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
		$args['category__in'] = $cats;
	}

	return apply_filters( 'eglatone_blog_grid_query_args', $args, $paged );
}

if ( ! function_exists( 'eglatone_load_more_button' ) ) :
	/**
	 * Output the "... more" button below the blog grid.
	 */
	function eglatone_load_more_button() {
		global $wp_query;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged = max( 1, (int) get_query_var( 'paged' ) );
		?>
		<div class="eglatone-load-more view-all-button">
			<button type="button"
				class="eglatone-load-more-button"
				data-page="<?php echo esc_attr( $paged ); ?>"
				data-max="<?php echo esc_attr( $wp_query->max_num_pages ); ?>"
				data-target="#infinite-post-wrap">
				<span class="load-more-text"><?php esc_html_e( '... more', 'eglatone' ); ?></span>
				<span class="load-more-busy"><?php esc_html_e( 'Loading&hellip;', 'eglatone' ); ?></span>
			</button>
			<p class="eglatone-load-more-finished" aria-live="polite"></p>
		</div><!-- .eglatone-load-more -->
		<?php
	}
endif; // eglatone_load_more_button.

/**
 * AJAX handler for the blog grid "... more" button.
 */
function eglatone_load_more_posts_callback() {
	check_ajax_referer( 'eglatone_load_more', 'nonce' );

	$paged = isset( $_POST['page'] ) ? absint( $_POST['page'] ) + 1 : 2;

	$loop = new WP_Query( eglatone_blog_grid_query_args( $paged ) );

	if ( ! $loop->have_posts() ) {
		wp_send_json_success( array(
			'html'     => '',
			'page'     => $paged,
			'has_more' => false,
		) );
	}

	ob_start();

	while ( $loop->have_posts() ) {
		$loop->the_post();

		get_template_part( 'template-parts/content/content', get_post_format() );
	}

	wp_reset_postdata();

	wp_send_json_success( array(
		'html'     => ob_get_clean(),
		'page'     => $paged,
		'has_more' => ( $paged < $loop->max_num_pages ),
	) );
}
add_action( 'wp_ajax_eglatone_load_more', 'eglatone_load_more_posts_callback' );
add_action( 'wp_ajax_nopriv_eglatone_load_more', 'eglatone_load_more_posts_callback' );

if ( ! function_exists( 'eglatone_content_nav' ) ) :
	/**
	 * Display navigation/pagination when applicable
	 *
	 * @since Eglatone 0.1
	 */
	function eglatone_content_nav() {
		global $wp_query;

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$pagination_type = get_theme_mod( 'eglatone_pagination_type', 'default' );

		if ( ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) || class_exists( 'Catch_Infinite_Scroll' ) ) {
			// Support infinite scroll plugins.
			the_posts_navigation();
		} elseif ( 'numeric' === $pagination_type && function_exists( 'the_posts_pagination' ) ) {
			the_posts_pagination( array(
				'prev_text'          => '<span>' . esc_html__( 'Prev', 'eglatone' ) . '</span>',
				'next_text'          => '<span>' . esc_html__( 'Next', 'eglatone' ) . '</span>',
				'screen_reader_text' => '<span class="nav-subtitle screen-reader-text">' . esc_html__( 'Page', 'eglatone' ) . ' </span>',
			) );
		} else {
			the_posts_navigation();
		}
	}
endif; // eglatone_content_nav

/**
 * Check if a section is enabled or not based on the $value parameter
 * @param  string $value Value of the section that is to be checked
 * @return boolean return true if section is enabled otherwise false
 */
function eglatone_check_section( $value ) {
	return ( 'entire-site' == $value  || ( is_front_page() && 'homepage' === $value ) );
}

/**
 * Return the first image in a post. Works inside a loop.
 * @param [integer] $post_id [Post or page id]
 * @param [string/array] $size Image size. Either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).
 * @param [string/array] $attr Query string or array of attributes.
 * @return [string] image html
 *
 * @since Eglatone 0.1
 */
function eglatone_get_first_image( $postID, $size, $attr, $src = false ) {
	ob_start();

	ob_end_clean();

	$image 	= '';

	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field('post_content', $postID ) , $matches);

	if ( isset( $matches[1][0] ) ) {
		// Get first image.
		$first_img = $matches[1][0];

		if ( $src ) {
			//Return url of src is true
			return $first_img;
		}

		return '<img class="wp-post-image" src="'. esc_url( $first_img ) .'">';
	}

	return false;
}

function eglatone_get_theme_layout() {
	$layout = '';

	if ( is_page_template( 'templates/full-width-page.php' ) ) {
		$layout = 'no-sidebar-full-width';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'eglatone_default_layout', 'right-sidebar' );

		if ( is_home() || is_archive() ) {
			$layout = get_theme_mod( 'eglatone_homepage_archive_layout', 'no-sidebar-full-width' );
		}
	}

	return $layout;
}

function eglatone_get_sidebar_id() {
	$sidebar = '';

	$layout = eglatone_get_theme_layout();

	if ( 'no-sidebar-full-width' === $layout || 'no-sidebar' === $layout ) {
		return $sidebar;
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebar = 'sidebar-1'; // Primary Sidebar.
	}

	return $sidebar;
}

if ( ! function_exists( 'eglatone_truncate_phrase' ) ) :
	/**
	 * Return a phrase shortened in length to a maximum number of characters.
	 *
	 * Result will be truncated at the last white space in the original string. In this function the word separator is a
	 * single space. Other white space characters (like newlines and tabs) are ignored.
	 *
	 * If the first `$max_characters` of the string does not contain a space character, an empty string will be returned.
	 *
	 * @since Eglatone 0.1
	 *
	 * @param string $text            A string to be shortened.
	 * @param integer $max_characters The maximum number of characters to return.
	 *
	 * @return string Truncated string
	 */
	function eglatone_truncate_phrase( $text, $max_characters ) {

		$text = trim( $text );

		if ( mb_strlen( $text ) > $max_characters ) {
			//* Truncate $text to $max_characters + 1
			$text = mb_substr( $text, 0, $max_characters + 1 );

			//* Truncate to the last space in the truncated string
			$text = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );
		}

		return $text;
	}
endif; //eglatone_truncate_phrase

if ( ! function_exists( 'eglatone_get_the_content_limit' ) ) :
	/**
	 * Return content stripped down and limited content.
	 *
	 * Strips out tags and shortcodes, limits the output to `$max_char` characters, and appends an ellipsis and more link to the end.
	 *
	 * @since Eglatone 0.1
	 *
	 * @param integer $max_characters The maximum number of characters to return.
	 * @param string  $more_link_text Optional. Text of the more link. Default is "(more...)".
	 * @param bool    $stripteaser    Optional. Strip teaser content before the more text. Default is false.
	 *
	 * @return string Limited content.
	 */
	function eglatone_get_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

		$content = get_the_content( '', $stripteaser );

		// Strip tags and shortcodes so the content truncation count is done correctly.
		$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

		// Remove inline styles / .
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char
		$content = eglatone_truncate_phrase( $content, $max_characters );

		// More link?
		if ( $more_link_text ) {
			$link   = apply_filters( 'get_the_content_more_link', sprintf( '<a href="%s" class="more-link">%s</a>', esc_url( get_permalink() ), $more_link_text ), $more_link_text );
			$output = sprintf( '<p>%s %s</p>', $content, $link );
		} else {
			$output = sprintf( '<p>%s</p>', $content );
			$link = '';
		}

		return apply_filters( 'eglatone_get_the_content_limit', $output, $content, $link, $max_characters );

	}
endif; //eglatone_get_the_content_limit

if ( ! function_exists( 'eglatone_content_image' ) ) :
	/**
	 * Template for Featured Image in Archive Content
	 *
	 * To override this in a child theme
	 * simply fabulous-fluid your own eglatone_content_image(), and that function will be used instead.
	 *
	 * @since Eglatone 0.1
	 */
	function eglatone_content_image() {
		if ( has_post_thumbnail() && eglatone_jetpack_featured_image_display() && is_singular() ) {
			global $post, $wp_query;

			// Get Page ID outside Loop.
			$page_id = $wp_query->get_queried_object_id();

			if ( $post ) {
		 		if ( is_attachment() ) {
					$parent = $post->post_parent;

					$individual_featured_image = get_post_meta( $parent, 'eglatone-featured-image', true );
				} else {
					$individual_featured_image = get_post_meta( $page_id, 'eglatone-featured-image', true );
				}
			}

			if ( empty( $individual_featured_image ) ) {
				$individual_featured_image = 'default';
			}

			if ( 'disable' === $individual_featured_image ) {
				echo '<!-- Page/Post Single Image Disabled or No Image set in Post Thumbnail -->';
				return false;
			} else {
				$class = array();

				$image_size = 'post-thumbnail';

				if ( 'default' !== $individual_featured_image ) {
					$image_size = $individual_featured_image;
					$class[]    = 'from-metabox';
				} else {
					$layout = eglatone_get_theme_layout();

					if ( 'no-sidebar-full-width' === $layout ) {
						$image_size = 'eglatone-slider';
					}
				}

				$class[] = $individual_featured_image;
				?>
				<div class="post-thumbnail <?php echo esc_attr( implode( ' ', $class ) ); ?>">
					<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image_size ); ?>
					</a>
				</div>
		   	<?php
			}
		} // End if ().
	}
endif; // eglatone_content_image.

if ( ! function_exists( 'eglatone_sections' ) ) :
	/**
	 * Display Sections on header and footer with respect to the section option set in eglatone_sections_sort
	 */
	function eglatone_sections( $selector = 'header' ) {
		get_template_part( 'template-parts/header/header-media' );

		// Press logos sit directly under the header image.
		if ( function_exists( 'eglatone_press_strip' ) ) {
			eglatone_press_strip();
		}

		get_template_part( 'template-parts/slider/display-slider' );
		get_template_part( 'template-parts/service/display-service' );
		get_template_part( 'template-parts/hero-content/content-hero' );
		get_template_part( 'template-parts/testimonial/display-testimonial' );
		get_template_part( 'template-parts/portfolio/display-portfolio' );
		// Achievements hero leads, then the featured cards support it.
		if ( function_exists( 'eglatone_hero_section' ) ) {
			eglatone_hero_section();
		}

		get_template_part( 'template-parts/featured-content/display-featured' );

		// Ticker last, as the divider into the blog grid.
		get_template_part( 'template-parts/ticker/display-ticker' );
	}
endif;

if ( ! function_exists( 'eglatone_post_thumbnail' ) ) :
	/**
	 * $image_size post thumbnail size
	 * $type html, html-with-bg, url
	 * $echo echo true/false
	 * $no_thumb display no-thumb image or not
	 */
	function eglatone_post_thumbnail( $image_size = 'post-thumbnail', $type = 'html', $echo = true, $no_thumb = false ) {
		$image = $image_url = '';
		
		if ( has_post_thumbnail() ) {
			$image_url = get_the_post_thumbnail_url( get_the_ID(), $image_size );
			$image     = get_the_post_thumbnail( get_the_ID(), $image_size );
		} else {
			if ( $no_thumb ) {
				global $_wp_additional_image_sizes;

				$image_url  = trailingslashit( get_template_directory_uri() ) . 'assets/images/no-thumb-' . $_wp_additional_image_sizes[ $image_size ]['width'] . 'x' . $_wp_additional_image_sizes[ $image_size ]['height'] . '.jpg';
				$image      = '<img src="' . esc_url( $image_url ) . '" alt="" />';
			}

			// Get the first image in page, returns false if there is no image.
			$first_image_url = eglatone_get_first_image( get_the_ID(), $image_size, '', true );

			// Set value of image as first image if there is an image present in the page.
			if ( $first_image_url ) {
				$image_url = $first_image_url;
				$image = '<img class="wp-post-image" src="'. esc_url( $image_url ) .'">';
			}
		}

		if ( ! $image_url ) {
			// Bail if there is no image url at this stage.
			return;
		}

		if ( 'url' === $type ) {
			return $image_url;
		}

		$output = '<div';

		if ( 'html-with-bg' === $type ) {
			$output .= ' class="post-thumbnail-background" style="background-image: url( ' . esc_url( $image_url ) . ' )"';
		} else {
			$output .= ' class="post-thumbnail"';
		}

		$output .= '>';

		$output .= '<a class="cover-link" href="' . esc_url( get_the_permalink() ) . '" title="' . the_title_attribute( 'echo=0' ) . '">';

		if ( 'html-with-bg' !== $type ) {
			$output .= $image;
		}

		$output .= '</a></div><!-- .post-thumbnail -->';

		if ( ! $echo ) {
			return $output;
		}

		echo $output;
	}
endif;
