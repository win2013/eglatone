<?php
/**
 * The template for displaying testimonial items
 *
 * @package Eglatone
 */
?>

<?php
$number = get_theme_mod( 'eglatone_testimonial_number', 3 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$args = array(
	'ignore_sticky_posts' => 1 // ignore sticky posts
);

$post_list  = array();// list of valid post/page ids

$args['post_type'] = 'jetpack-testimonial';

for ( $i = 1; $i <= $number; $i++ ) {
	$eglatone_post_id = '';

	$eglatone_post_id =  get_theme_mod( 'eglatone_testimonial_cpt_' . $i );

	if ( $eglatone_post_id && '' !== $eglatone_post_id ) {
		// Polylang Support.
		if ( class_exists( 'Polylang' ) ) {
			$eglatone_post_id = pll_get_post( $eglatone_post_id, pll_current_language() );
		}

		$post_list = array_merge( $post_list, array( $eglatone_post_id ) );

	}
}

$args['post__in'] = $post_list;
$args['orderby'] = 'post__in';

$args['posts_per_page'] = $number;
$loop = new WP_Query( $args );

if ( $loop -> have_posts() ) :
	while ( $loop -> have_posts() ) :
		$loop -> the_post();

		get_template_part( 'template-parts/testimonial/content', 'testimonial' );
	endwhile;
	wp_reset_postdata();
endif;
