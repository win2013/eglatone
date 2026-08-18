<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Eglatone
 */

/*
 * Header extras: category chips, author byline, reading or listening time.
 * Everything below is self-contained in this template so the feature needs
 * only this one PHP file uploaded.
 */
$eglatone_cats = ( 'post' === get_post_type() ) ? get_the_category() : array();

// Reading time, ~200 words per minute.
$eglatone_words   = str_word_count( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ) );
$eglatone_minutes = max( 1, (int) ceil( $eglatone_words / 200 ) );

// Episode duration, when the post carries an audio enclosure.
$eglatone_enclosure = get_post_meta( get_the_ID(), 'enclosure', true );
$eglatone_has_audio = ! empty( $eglatone_enclosure );
$eglatone_duration  = '';

if ( $eglatone_has_audio ) {
	$eglatone_parts = explode( "\n", trim( (string) $eglatone_enclosure ) );

	if ( ! empty( $eglatone_parts[3] ) ) {
		$eglatone_extra = maybe_unserialize( $eglatone_parts[3] );

		if ( is_array( $eglatone_extra ) && ! empty( $eglatone_extra['duration'] ) ) {
			$eglatone_duration = $eglatone_extra['duration'];
		}
	}
}

// Capture the featured image so we know whether to draw the band at all.
ob_start();
eglatone_single_image();
$eglatone_image = trim( ob_get_clean() );

$eglatone_has_image = ( false !== strpos( $eglatone_image, '<figure' ) );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		$header_image = eglatone_featured_overall_image();

		if ( 'disable' === $header_image ) : ?>

		<header class="entry-header">
			<?php if ( $eglatone_cats ) : ?>
				<div class="entry-chips">
					<?php foreach ( $eglatone_cats as $eglatone_cat ) : ?>
						<a class="entry-chip" href="<?php echo esc_url( get_category_link( $eglatone_cat->term_id ) ); ?>"><?php echo esc_html( $eglatone_cat->name ); ?></a>
					<?php endforeach; ?>
				</div><!-- .entry-chips -->
			<?php endif; ?>

			<?php the_title( '<h1 class="section-title">', '</h1>' ); ?>

			<?php
			if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta entry-byline">
				<span class="byline-avatar"><?php echo get_avatar( get_the_author_meta( 'ID' ), 36 ); ?></span>
				<span class="byline-author"><?php echo esc_html( get_the_author() ); ?></span>
				<span class="byline-sep" aria-hidden="true">&middot;</span>
				<?php eglatone_posted_on(); ?>
				<span class="byline-sep" aria-hidden="true">&middot;</span>
				<span class="byline-length">
					<?php
					if ( $eglatone_duration ) {
						/* translators: %s: episode duration, e.g. 42:15 */
						printf( esc_html__( '%s listen', 'eglatone' ), esc_html( $eglatone_duration ) );
					} elseif ( $eglatone_has_audio ) {
						esc_html_e( 'Listen', 'eglatone' );
					} else {
						/* translators: %d: estimated reading time in minutes. */
						printf( esc_html( _n( '%d min read', '%d min read', $eglatone_minutes, 'eglatone' ) ), (int) $eglatone_minutes );
					}
					?>
				</span>
			</div><!-- .entry-meta -->
			<?php
			endif; ?>
		</header><!-- .entry-header -->

	<?php endif; ?>

	<?php if ( $eglatone_has_image || $eglatone_has_audio ) : ?>
		<div class="eglatone-single-media<?php echo $eglatone_has_audio ? ' has-audio' : ''; ?>">
			<div class="eglatone-single-frame">
				<?php
				// Already rendered above, just place it.
				echo $eglatone_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme markup from eglatone_single_image().
				?>
				<div class="eglatone-single-player" aria-live="polite">
					<?php /* The audio player and its Play / Download / Embed links are moved in here by homepage-layout.js. */ ?>
				</div><!-- .eglatone-single-player -->
			</div><!-- .eglatone-single-frame -->
		</div><!-- .eglatone-single-media -->
	<?php else : ?>
		<?php
		echo $eglatone_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	<?php endif; ?>

	<div class="entry-content">
		<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'eglatone' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'eglatone' ),
				'after'  => '</span></div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<div class="entry-meta">
			<?php eglatone_entry_footer(); ?>
		</div><!-- .entry-meta -->

		<?php eglatone_author_bio(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
