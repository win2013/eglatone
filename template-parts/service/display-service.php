<?php
/**
 * The template for displaying service content
 *
 * @package Eglatone
 */
?>

<?php
$enable_content = get_theme_mod( 'eglatone_service_option', 'disabled' );

if ( ! eglatone_check_section( $enable_content ) ) {
	// Bail if service content is disabled.
	return;
}

$eglatone_title    = get_option( 'ect_service_title', esc_html__( 'Services', 'eglatone' ) );
$sub_title = get_option( 'ect_service_content' );

$classes[] = 'service-section';
$classes[] = 'section';

if ( ! $eglatone_title && ! $sub_title ) {
	$classes[] = 'no-section-heading';
}

$background = get_theme_mod( 'eglatone_service_bg_image' );

if ( $background ) {
	$classes[] = 'has-background-image';
}
?>

<div id="service-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( '' !== $eglatone_title || $sub_title ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $eglatone_title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $eglatone_title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<div class="section-description-wrapper section-subtitle">
						<?php
						$sub_title = apply_filters( 'the_content', $sub_title );
						echo wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<?php

		$wrapper_classes[] = 'section-content-wrapper service-content-wrapper';

		/**
		 * Homepage layout: render services as a compact 2-up carousel
		 * instead of the original static four column grid.
		 */
		$wrapper_classes[] = 'eglatone-2up-carousel owl-carousel';
		?>

		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-columns="<?php echo esc_attr( eglatone_section_columns( 'service' ) ); ?>">
			<?php
			get_template_part( 'template-parts/service/content-service' );
			?>
		</div><!-- .service-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #service-section -->
