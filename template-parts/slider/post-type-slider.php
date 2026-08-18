<?php
/**
 * The template used for displaying slider
 *
 * @package Eglatone
 */

$quantity      = get_theme_mod( 'eglatone_slider_number', 4 );
$no_of_post    = 0; // for number of posts
$post_list     = array(); // list of valid post/page ids
$eglatone_type = get_theme_mod( 'eglatone_slider_type', 'category' );
$show_content  = get_theme_mod( 'eglatone_slider_content_show', 'hide-content' );

$args = array(
	'post_type'           => 'any',
	'orderby'             => 'post__in',
	'ignore_sticky_posts' => 1, // ignore sticky posts
);

for ( $i = 1; $i <= $quantity; $i++ ) {
	$eglatone_post_id = '';

	$eglatone_post_id = get_theme_mod( 'eglatone_slider_page_' . $i );

	if ( $eglatone_post_id && '' !== $eglatone_post_id ) {
		$post_list = array_merge( $post_list, array( $eglatone_post_id ) );

		$no_of_post++;
	}
}

$args['post__in'] = $post_list;

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;

$loop = new WP_Query( $args );

while ( $loop->have_posts() ) :
	$loop->the_post();

	$thumbnail = 'eglatone-slider';	
	?>
	<article class="<?php echo esc_attr( 'post post-' . get_the_ID() . ' hentry slides' ); ?>">
		<div class="hentry-inner">
			<div class="slider-image-wrapper">
				<a class="cover-link" title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
					<?php 
					if  ( has_post_thumbnail() ) {
						the_post_thumbnail( 'eglatone-slider' );
					} else {
						echo '<img class="wp-post-image" src="' . trailingslashit( get_template_directory_uri() ) . 'assets/images/no-thumb-1920x1080.jpg"/>';
					}
					?>
				</a>
			</div><!-- .slider-image-wrapper -->

			<div class="slider-content-wrapper">
				<div class="entry-container">
					<header class="entry-header">
						<h2 class="entry-title">
							<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
								<?php 
								$eglatone_title_image = get_theme_mod( 'eglatone_slider_title_image_' . ( $loop->current_post  + 1 ) );

								if ( $eglatone_title_image ) : 
								?>
								<span class="title-image"><img src="<?php echo esc_url( $eglatone_title_image ); ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>"/></span>
								<?php endif; ?>

								<?php the_title( '<span class="title-text">','</span>' ); ?>
							</a>
						</h2>
					</header>

					<?php
					if ( 'excerpt' === $show_content ) {
						echo '<div class="entry-summary"><p>' . wp_kses_post( get_the_excerpt() ) . '</p></div><!-- .entry-summary -->';
					} elseif ( 'full-content' === $show_content ) {
						$content = apply_filters( 'the_content', get_the_content() );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
					} 
					?>
				</div><!-- .entry-container -->
			</div><!-- .slider-content-wrapper -->
		</div><!-- .hentry-inner -->
	</article><!-- .slides -->
<?php
endwhile;

wp_reset_postdata();
