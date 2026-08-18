<?php
/**
 * The template for displaying the TECHED.TV ticker
 *
 * @package Eglatone
 */

// Front page only.
if ( ! is_front_page() ) {
	return;
}

/*
 * If the standalone TECHED.TV Ticker plugin is active, defer to it entirely:
 * render its output in this slot so the strip still lands between Featured
 * Content and the blog grid, but the plugin's settings drive it. Set the
 * plugin's placement to "Manual" so it does not also print itself elsewhere.
 */
if ( function_exists( 'techedtv_ticker_render' ) ) {
	techedtv_ticker_render( array( 'force' => true ) );

	return;
}

if ( ! function_exists( 'eglatone_ticker_enabled' ) || ! eglatone_ticker_enabled() ) {
	return;
}

$eglatone_ticker_items = eglatone_ticker_items();

if ( empty( $eglatone_ticker_items ) ) {
	return;
}

$eglatone_ticker_heading = get_theme_mod( 'eglatone_ticker_heading', esc_html__( 'Latest from TECHED.TV', 'eglatone' ) );
$eglatone_ticker_speed   = eglatone_ticker_speed();
?>

<div id="eglatone-ticker-section" class="eglatone-ticker section" data-speed="<?php echo esc_attr( $eglatone_ticker_speed ); ?>">
	<?php if ( $eglatone_ticker_heading ) : ?>
		<div class="ticker-label">
			<span class="ticker-pulse" aria-hidden="true"></span>
			<span class="ticker-label-text"><?php echo esc_html( $eglatone_ticker_heading ); ?></span>
		</div><!-- .ticker-label -->
	<?php endif; ?>

	<div class="ticker-viewport">
		<ul class="ticker-track">
			<?php foreach ( $eglatone_ticker_items as $eglatone_ticker_item ) : ?>
				<li class="ticker-item">
					<a class="ticker-link" href="<?php echo esc_url( $eglatone_ticker_item['link'] ); ?>">
						<?php if ( $eglatone_ticker_item['thumb'] ) : ?>
							<span class="ticker-thumb" style="background-image: url( <?php echo esc_url( $eglatone_ticker_item['thumb'] ); ?> );"></span>
						<?php endif; ?>

						<span class="ticker-text">
							<span class="ticker-title"><?php echo esc_html( $eglatone_ticker_item['title'] ); ?></span>

							<?php if ( $eglatone_ticker_item['date'] ) : ?>
								<span class="ticker-date"><?php echo esc_html( $eglatone_ticker_item['date'] ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul><!-- .ticker-track -->
	</div><!-- .ticker-viewport -->
</div><!-- #eglatone-ticker-section -->
