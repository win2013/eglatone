<?php
/**
 * Achievements hero section
 *
 * A stats band plus a short introduction with a CV link and a call to action,
 * rendered between the Featured Content section and the blog grid.
 *
 * Settings and markup live in this one file so the feature needs a single new
 * PHP file uploaded.
 *
 * @package Eglatone
 */

/**
 * Default copy, used as the Customizer defaults so the section works the
 * moment it is switched on.
 *
 * @return array
 */
function eglatone_hero_defaults() {
	return array(
		'eyebrow'   => __( 'Introduction', 'eglatone' ),
		'title'     => __( 'Serial entrepreneur, inventor, technology leader', 'eglatone' ),
		'text'      => __( 'Dr. Edwin Hernandez is a serial entrepreneur, inventor, and technology leader — the founder of EGLA Corp, a technology company that develops its own intellectual property and serves as a platform where other innovators build ventures at the EGLAVATOR, his technology incubator and accelerator in Boca Raton, FL. The EGLAVATOR assists and invests in early-stage technology companies, offering funding advice, go-to-market strategy, intellectual property guidance, and hands-on execution support.

As a technology leader, Dr. Hernandez has worked with engineering and business teams since his days at Motorola and Microsoft. Through his intellectual property portfolio, he and his team have reached licensing agreements covering Motorola, Google, Apple, T-Mobile, Verizon Wireless, DISH Wireless, Samsung, Nokia, Ericsson, Microsoft, Comcast, and other major technology companies. He is an expert in technology and venture funding with extensive experience in wireless communications, AI, cloud computing, IoT, electronic devices, and advanced software platforms — a hands-on coder who builds prototypes for many of the products developed at his incubator and is well versed in code reviews and hardware/software audits.

Dr. Hernandez earned a B.S. in Electronics Engineering from the Costa Rica Institute of Technology, and an M.S. in Electrical and Computer Engineering and a Ph.D. in Computer Engineering from the University of Florida. He is a named inventor on 17 issued U.S. patents and 5 European patents. He has provided technical expert witness services in 50+ intellectual property cases, including 15+ inter partes reviews (IPRs), and four testimonies at trial, Daubert hearings, and numerous depositions. He was a Fulbright Scholar and has twice served as a judge for the Mobile World Congress. He is a Microsoft and Motorola alumnus, a member of the Industry Advisory Board (IAB) at Florida Atlantic University, and producer of his own technology podcast, TECHEDTV.

Dr. Hernandez has testified as an expert witness in several high-value matters, including for Blue Radios at trial (a $24.8M award, per Nasdaq), for the Mojo Mobility legal team on wireless-charging patents (a $192M award, per Reuters), for the TOT Power Control team on power-control patents (a $110M award, per Reuters), and for Intellectual Ventures against LG in a multi-million-dollar licensing dispute.', 'eglatone' ),
		'cv_label'  => __( 'Download CV', 'eglatone' ),
		'cta_label' => __( 'Work with me', 'eglatone' ),
		'credentials' => __( 'Startup success: invested in PointsKash — $100M raised from Hawk Capital
Ex-Motorola
Ex-Microsoft
Fulbright Scholar
IEEE Member
FAU Industry Advisory Board
TECHEDTV Podcast host', 'eglatone' ),
		'form_title' => __( 'Work with me', 'eglatone' ),
		'form_intro' => __( 'Tell me a little about what you need and I will get back to you.', 'eglatone' ),
		'stats'     => array(
			1 => array( 'value' => '17+5',    'label' => __( 'US &amp; European patents issued', 'eglatone' ) ),
			2 => array( 'value' => '50+',     'label' => __( 'IP cases, incl. 15+ IPRs', 'eglatone' ) ),
			3 => array( 'value' => '$300M+',  'label' => __( 'in licenses &amp; settlements', 'eglatone' ) ),
			4 => array( 'value' => '80+',     'label' => __( 'startups incubated at EGLAVATOR', 'eglatone' ) ),
		),
	);
}

/**
 * Number of stat tiles the section supports.
 */
function eglatone_hero_stat_count() {
	return 4;
}

/**
 * Register the Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_hero_customize_register( $wp_customize ) {
	$defaults = eglatone_hero_defaults();

	$wp_customize->add_section( 'eglatone_hero_achievements', array(
		'title'       => esc_html__( 'Achievements Hero', 'eglatone' ),
		'panel'       => 'eglatone_theme_options',
		'priority'    => 7,
		'description' => esc_html__( 'Stats, introduction and buttons, shown between the Featured Content section and the blog grid.', 'eglatone' ),
	) );

	// Visibility, matching the pattern used by the theme's other sections.
	$wp_customize->add_setting( 'eglatone_hero_option', array(
		'default'           => 'homepage',
		'sanitize_callback' => 'eglatone_hero_sanitize_visibility',
	) );

	$wp_customize->add_control( 'eglatone_hero_option', array(
		'label'   => esc_html__( 'Show this section', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'select',
		'choices' => array(
			'disabled'    => esc_html__( 'Disabled', 'eglatone' ),
			'homepage'    => esc_html__( 'Homepage / Frontpage', 'eglatone' ),
			'entire-site' => esc_html__( 'Entire Site', 'eglatone' ),
		),
	) );

	// Stat tiles.
	for ( $i = 1; $i <= eglatone_hero_stat_count(); $i++ ) {
		$wp_customize->add_setting( 'eglatone_hero_stat_value_' . $i, array(
			'default'           => isset( $defaults['stats'][ $i ]['value'] ) ? $defaults['stats'][ $i ]['value'] : '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'eglatone_hero_stat_value_' . $i, array(
			/* translators: %d: stat number. */
			'label'   => sprintf( esc_html__( 'Stat %d — figure', 'eglatone' ), $i ),
			'section' => 'eglatone_hero_achievements',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( 'eglatone_hero_stat_label_' . $i, array(
			'default'           => isset( $defaults['stats'][ $i ]['label'] ) ? $defaults['stats'][ $i ]['label'] : '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'eglatone_hero_stat_label_' . $i, array(
			/* translators: %d: stat number. */
			'label'   => sprintf( esc_html__( 'Stat %d — caption', 'eglatone' ), $i ),
			'section' => 'eglatone_hero_achievements',
			'type'    => 'text',
		) );
	}

	// Introduction.
	$wp_customize->add_setting( 'eglatone_hero_eyebrow', array(
		'default'           => $defaults['eyebrow'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_eyebrow', array(
		'label'   => esc_html__( 'Small label above the heading', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_title', array(
		'default'           => $defaults['title'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_title', array(
		'label'   => esc_html__( 'Heading', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_text', array(
		'default'           => $defaults['text'],
		'sanitize_callback' => 'wp_kses_post',
	) );

	$wp_customize->add_control( 'eglatone_hero_text', array(
		'label'       => esc_html__( 'Introduction', 'eglatone' ),
		'description' => esc_html__( 'Separate paragraphs with a blank line. Everything after the first two paragraphs is hidden behind a "Read more" button.', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'textarea',
	) );

	// Buttons.
	$wp_customize->add_setting( 'eglatone_hero_cv_label', array(
		'default'           => $defaults['cv_label'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_cv_label', array(
		'label'   => esc_html__( 'CV button — text', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_cv_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'eglatone_hero_cv_url', array(
		'label'       => esc_html__( 'CV button — link', 'eglatone' ),
		'description' => esc_html__( 'Upload your CV to the Media Library and paste its URL here. The button is hidden while this is empty.', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'url',
	) );

	$wp_customize->add_setting( 'eglatone_hero_credentials', array(
		'default'           => $defaults['credentials'],
		'sanitize_callback' => 'wp_kses_post',
	) );

	$wp_customize->add_control( 'eglatone_hero_credentials', array(
		'label'       => esc_html__( 'Highlights', 'eglatone' ),
		'description' => esc_html__( 'One per line. Each line becomes a pill under the figures. Leave empty to hide the row.', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'eglatone_hero_cta_label', array(
		'default'           => $defaults['cta_label'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_cta_label', array(
		'label'   => esc_html__( 'Call to action — text', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_cta_type', array(
		'default'           => 'form',
		'sanitize_callback' => 'eglatone_hero_sanitize_cta_type',
	) );

	$wp_customize->add_control( 'eglatone_hero_cta_type', array(
		'label'       => esc_html__( 'Call to action — what it does', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'select',
		'choices'     => array(
			'form' => esc_html__( 'Open a contact form', 'eglatone' ),
			'link' => esc_html__( 'Go to a link', 'eglatone' ),
		),
	) );

	$wp_customize->add_setting( 'eglatone_hero_cta_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'eglatone_hero_cta_url', array(
		'label'       => esc_html__( 'Call to action — link', 'eglatone' ),
		'description' => esc_html__( 'Only used when the button is set to "Go to a link". Your contact page, booking link or mailto: address.', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'url',
	) );

	// Contact form.
	$wp_customize->add_setting( 'eglatone_hero_form_recipient', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_email',
	) );

	$wp_customize->add_control( 'eglatone_hero_form_recipient', array(
		'label'       => esc_html__( 'Form — send messages to', 'eglatone' ),
		/* translators: %s: site admin email address. */
		'description' => sprintf( esc_html__( 'Leave empty to use the site admin address (%s).', 'eglatone' ), esc_html( get_option( 'admin_email' ) ) ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'email',
	) );

	$wp_customize->add_setting( 'eglatone_hero_form_title', array(
		'default'           => $defaults['form_title'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_form_title', array(
		'label'   => esc_html__( 'Form — heading', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_form_intro', array(
		'default'           => $defaults['form_intro'],
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_form_intro', array(
		'label'   => esc_html__( 'Form — intro line', 'eglatone' ),
		'section' => 'eglatone_hero_achievements',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_hero_form_shortcode', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_hero_form_shortcode', array(
		'label'       => esc_html__( 'Form — use a shortcode instead', 'eglatone' ),
		'description' => esc_html__( 'Optional. Paste a Contact Form 7 or WPForms shortcode to use that form in the popup instead of the built-in one.', 'eglatone' ),
		'section'     => 'eglatone_hero_achievements',
		'type'        => 'text',
	) );
}

/**
 * Sanitize the call-to-action behaviour.
 *
 * @param string $input Raw value.
 * @return string
 */
function eglatone_hero_sanitize_cta_type( $input ) {
	return ( 'link' === $input ) ? 'link' : 'form';
}
add_action( 'customize_register', 'eglatone_hero_customize_register', 12 );

/**
 * Sanitize the visibility select.
 *
 * @param string $input Raw value.
 * @return string
 */
function eglatone_hero_sanitize_visibility( $input ) {
	$allowed = array( 'disabled', 'homepage', 'entire-site' );

	return in_array( $input, $allowed, true ) ? $input : 'homepage';
}

/**
 * Render the section.
 */
function eglatone_hero_section() {
	if ( ! eglatone_check_section( get_theme_mod( 'eglatone_hero_option', 'homepage' ) ) ) {
		return;
	}

	$defaults = eglatone_hero_defaults();

	// Collect the stat tiles that actually have a figure.
	$stats = array();

	for ( $i = 1; $i <= eglatone_hero_stat_count(); $i++ ) {
		$value = get_theme_mod( 'eglatone_hero_stat_value_' . $i, isset( $defaults['stats'][ $i ]['value'] ) ? $defaults['stats'][ $i ]['value'] : '' );
		$label = get_theme_mod( 'eglatone_hero_stat_label_' . $i, isset( $defaults['stats'][ $i ]['label'] ) ? $defaults['stats'][ $i ]['label'] : '' );

		if ( '' !== trim( (string) $value ) ) {
			$stats[] = array(
				'value' => $value,
				'label' => $label,
			);
		}
	}

	$eyebrow = get_theme_mod( 'eglatone_hero_eyebrow', $defaults['eyebrow'] );
	$title   = get_theme_mod( 'eglatone_hero_title', $defaults['title'] );
	$text    = get_theme_mod( 'eglatone_hero_text', $defaults['text'] );

	// Highlight pills: one per line.
	$credentials = get_theme_mod( 'eglatone_hero_credentials', $defaults['credentials'] );
	$credentials = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $credentials ) ), 'strlen' ) );

	$cta_type = get_theme_mod( 'eglatone_hero_cta_type', 'form' );

	$cv_label  = get_theme_mod( 'eglatone_hero_cv_label', $defaults['cv_label'] );
	$cv_url    = get_theme_mod( 'eglatone_hero_cv_url', '' );
	$cta_label = get_theme_mod( 'eglatone_hero_cta_label', $defaults['cta_label'] );
	$cta_url   = get_theme_mod( 'eglatone_hero_cta_url', '' );

	if ( ! $stats && ! trim( (string) $text ) && ! $title ) {
		// Nothing to show.
		return;
	}

	/*
	 * Split the introduction: the first two paragraphs stay visible, the rest
	 * goes behind "Read more" so the section does not swamp the page.
	 */
	$paragraphs = preg_split( '/\n\s*\n/', trim( (string) $text ) );
	$paragraphs = array_values( array_filter( array_map( 'trim', (array) $paragraphs ), 'strlen' ) );

	$lead = array_slice( $paragraphs, 0, 2 );
	$rest = array_slice( $paragraphs, 2 );
	?>
	<div id="eglatone-hero-section" class="eglatone-hero section">
		<div class="wrapper">

			<?php if ( $stats ) : ?>
				<ul class="hero-stats" role="list">
					<?php foreach ( $stats as $stat ) : ?>
						<li class="hero-stat">
							<span class="hero-stat-value"><?php echo wp_kses_post( $stat['value'] ); ?></span>
							<?php if ( $stat['label'] ) : ?>
								<span class="hero-stat-label"><?php echo wp_kses_post( $stat['label'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul><!-- .hero-stats -->
			<?php endif; ?>

			<?php if ( $credentials ) : ?>
				<ul class="hero-credentials" role="list">
					<?php foreach ( $credentials as $credential ) : ?>
						<li class="hero-credential"><?php echo wp_kses_post( $credential ); ?></li>
					<?php endforeach; ?>
				</ul><!-- .hero-credentials -->
			<?php endif; ?>

			<?php if ( $title || $lead ) : ?>
				<div class="hero-intro">
					<?php if ( $eyebrow ) : ?>
						<p class="hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="hero-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>

					<?php if ( $lead ) : ?>
						<div class="hero-text">
							<?php
							foreach ( $lead as $paragraph ) {
								echo wpautop( wp_kses_post( $paragraph ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div><!-- .hero-text -->
					<?php endif; ?>

					<?php if ( $rest ) : ?>
						<div class="hero-text hero-text-more" id="eglatone-hero-more">
							<?php
							foreach ( $rest as $paragraph ) {
								echo wpautop( wp_kses_post( $paragraph ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div><!-- .hero-text-more -->

						<button type="button"
							class="hero-more-toggle"
							aria-expanded="false"
							aria-controls="eglatone-hero-more"
							data-more="<?php esc_attr_e( 'Read more', 'eglatone' ); ?>"
							data-less="<?php esc_attr_e( 'Read less', 'eglatone' ); ?>">
							<?php esc_html_e( 'Read more', 'eglatone' ); ?>
						</button>
					<?php endif; ?>

					<?php
					$show_cta = ( 'form' === $cta_type ) ? (bool) $cta_label : (bool) $cta_url;
					?>
					<?php if ( $show_cta || $cv_url ) : ?>
						<div class="hero-actions">
							<?php if ( $show_cta ) : ?>
								<?php if ( 'form' === $cta_type ) : ?>
									<button type="button" class="hero-button hero-button-primary hero-open-form" aria-haspopup="dialog" aria-controls="eglatone-hero-dialog"><?php echo esc_html( $cta_label ); ?></button>
								<?php else : ?>
									<a class="hero-button hero-button-primary" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( $cv_url ) : ?>
								<a class="hero-button hero-button-ghost" href="<?php echo esc_url( $cv_url ); ?>"><?php echo esc_html( $cv_label ); ?></a>
							<?php endif; ?>
						</div><!-- .hero-actions -->
					<?php endif; ?>
				</div><!-- .hero-intro -->
			<?php endif; ?>

		</div><!-- .wrapper -->

		<?php
		if ( 'form' === $cta_type && $cta_label ) {
			eglatone_hero_contact_dialog();
		}
		?>
	</div><!-- #eglatone-hero-section -->
	<?php
}

/**
 * The contact popup markup.
 */
function eglatone_hero_contact_dialog() {
	$defaults  = eglatone_hero_defaults();
	$title     = get_theme_mod( 'eglatone_hero_form_title', $defaults['form_title'] );
	$intro     = get_theme_mod( 'eglatone_hero_form_intro', $defaults['form_intro'] );
	$shortcode = trim( (string) get_theme_mod( 'eglatone_hero_form_shortcode', '' ) );
	?>
	<div class="hero-dialog" id="eglatone-hero-dialog" role="dialog" aria-modal="true" aria-labelledby="eglatone-hero-dialog-title" hidden>
		<div class="hero-dialog-backdrop" data-hero-close></div>

		<div class="hero-dialog-panel" role="document">
			<button type="button" class="hero-dialog-close" data-hero-close aria-label="<?php esc_attr_e( 'Close', 'eglatone' ); ?>">&times;</button>

			<?php if ( $title ) : ?>
				<h3 class="hero-dialog-title" id="eglatone-hero-dialog-title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>

			<?php if ( $intro ) : ?>
				<p class="hero-dialog-intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>

			<?php if ( $shortcode ) : ?>
				<div class="hero-dialog-shortcode">
					<?php echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php else : ?>
				<form class="hero-form" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eglatone_hero_contact' ) ); ?>" novalidate>
					<p class="hero-field">
						<label for="eglatone-hero-name"><?php esc_html_e( 'Your name', 'eglatone' ); ?></label>
						<input type="text" id="eglatone-hero-name" name="name" required>
					</p>

					<p class="hero-field">
						<label for="eglatone-hero-email"><?php esc_html_e( 'Your email', 'eglatone' ); ?></label>
						<input type="email" id="eglatone-hero-email" name="email" required>
					</p>

					<p class="hero-field">
						<label for="eglatone-hero-subject"><?php esc_html_e( 'Subject', 'eglatone' ); ?></label>
						<input type="text" id="eglatone-hero-subject" name="subject">
					</p>

					<p class="hero-field">
						<label for="eglatone-hero-message"><?php esc_html_e( 'Message', 'eglatone' ); ?></label>
						<textarea id="eglatone-hero-message" name="message" rows="5" required></textarea>
					</p>

					<?php // Honeypot: real people never fill this in. ?>
					<p class="hero-field hero-field-hp" aria-hidden="true">
						<label for="eglatone-hero-website"><?php esc_html_e( 'Leave this field empty', 'eglatone' ); ?></label>
						<input type="text" id="eglatone-hero-website" name="website" tabindex="-1" autocomplete="off">
					</p>

					<input type="hidden" name="started" value="<?php echo esc_attr( time() ); ?>">

					<p class="hero-form-actions">
						<button type="submit" class="hero-button hero-button-primary hero-form-submit">
							<span class="hero-submit-text"><?php esc_html_e( 'Send message', 'eglatone' ); ?></span>
							<span class="hero-submit-busy"><?php esc_html_e( 'Sending&hellip;', 'eglatone' ); ?></span>
						</button>
					</p>

					<p class="hero-form-status" role="status" aria-live="polite"></p>
				</form>
			<?php endif; ?>
		</div><!-- .hero-dialog-panel -->
	</div><!-- .hero-dialog -->
	<?php
}

/**
 * AJAX handler for the hero contact form.
 */
function eglatone_hero_contact_submit() {
	check_ajax_referer( 'eglatone_hero_contact', 'nonce' );

	// Honeypot and a minimum fill time: both are silent bot filters.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Message could not be sent.', 'eglatone' ) ) );
	}

	$started = isset( $_POST['started'] ) ? absint( $_POST['started'] ) : 0;

	if ( $started && ( time() - $started ) < 3 ) {
		wp_send_json_error( array( 'message' => __( 'That was a little too quick — please try again.', 'eglatone' ) ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please add your name, a valid email address and a message.', 'eglatone' ) ) );
	}

	// Light rate limit: 5 messages per hour per IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'eglatone_hero_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );

	if ( $hits >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many messages from this address. Please try again later.', 'eglatone' ) ) );
	}

	$to = get_theme_mod( 'eglatone_hero_form_recipient', '' );

	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	if ( '' === $subject ) {
		$subject = __( 'Website enquiry', 'eglatone' );
	}

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	$body = sprintf(
		/* translators: 1: sender name, 2: sender email, 3: message body, 4: site name. */
		__( "Name: %1\$s\nEmail: %2\$s\n\n%3\$s\n\n--\nSent from the contact form on %4\$s", 'eglatone' ),
		$name,
		$email,
		$message,
		$site
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %s <%s>', $name, $email ),
	);

	$sent = wp_mail( $to, sprintf( '[%s] %s', $site, $subject ), $body, $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'The message could not be sent right now. Please email me directly.', 'eglatone' ) ) );
	}

	set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

	wp_send_json_success( array( 'message' => __( 'Thank you — your message is on its way.', 'eglatone' ) ) );
}
add_action( 'wp_ajax_eglatone_hero_contact', 'eglatone_hero_contact_submit' );
add_action( 'wp_ajax_nopriv_eglatone_hero_contact', 'eglatone_hero_contact_submit' );


/* ==========================================================================
 * Press mentions strip
 *
 * A scrolling row of publication logos ("Featured in ..."), rendered directly
 * under the header media. Lives in this file so it costs no extra upload.
 * ========================================================================== */

/**
 * Default press items, one per line: Name | image URL | article URL
 *
 * @return string
 */
function eglatone_press_defaults() {
	$u = 'https://edwinhernandez.com/wp-content/uploads/2026/02/';

	return
		'Yahoo Noticias | ' . $u . 'images.jpeg | https://es-us.noticias.yahoo.com/hondure%C3%B1o-creador-handoff-moderno-busca-194219603.html' . "\n" .
		'ABC | ' . $u . 'ABC-es-logo.png | https://www.abc.es/sociedad/edwin-hondureno-busca-impulsar-innovacion-pais-tras-20260207203935-vi.html' . "\n" .
		'Estrategia y Negocios | ' . $u . 'EyN-logo.png | https://www.revistaeyn.com/empresasymanagement/hondureno-creador-del-handoff-moderno-busca-impulsar-innovacion-tecnologica-MI29219522' . "\n" .
		'Forbes Centroamerica | ' . $u . 'forbes-logo-ca.jpg | https://www.linkedin.com/posts/forbes-centroam%C3%A9rica_edwin-hern%C3%A1ndez-ingeniero-hondure%C3%B1o-activity-7427840173238923264-op2g' . "\n" .
		'El Mundo | ' . $u . 'elmundo_logo-generica.jpg-scaled.jpg | https://elmundo.hn/inventor-hondureno-del-handoff-moderno-con-17-patentes/' . "\n" .
		'Swissinfo | ' . $u . 'SWI_swissinfo_logo-scaled.png | https://www.swissinfo.ch/spa/hondure%C3%B1o-creador-del-handoff-moderno-busca-impulsar-innovaci%C3%B3n-tecnol%C3%B3gica-en-su-pa%C3%ADs/90907756' . "\n" .
		'VoyageMIA | ' . $u . 'VoyageMia.jpg | https://voyagemia.com/interview/meet-edwin-hernandez-of-boca-raton/' . "\n" .
		'IAM Media |  | https://www.iam-media.com/article/meet-the-wireless-inventor-licensing-apple-nokia-ericsson-6gs-edge';
}

/**
 * Parse the press textarea into usable items.
 *
 * @return array List of array( name, image, link ).
 */
function eglatone_press_items() {
	$raw = get_theme_mod( 'eglatone_press_items', eglatone_press_defaults() );

	$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
	$items = array();

	foreach ( (array) $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line ) );

		$name  = isset( $parts[0] ) ? $parts[0] : '';
		$image = isset( $parts[1] ) ? $parts[1] : '';
		$link  = isset( $parts[2] ) ? $parts[2] : '';

		if ( '' === $name && '' === $image ) {
			continue;
		}

		$items[] = array(
			'name'  => $name,
			'image' => $image ? esc_url_raw( $image ) : '',
			'link'  => $link ? esc_url_raw( $link ) : '',
		);
	}

	return $items;
}

/**
 * Customizer controls for the press strip.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_press_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'eglatone_press', array(
		'title'       => esc_html__( 'Press Mentions', 'eglatone' ),
		'panel'       => 'eglatone_theme_options',
		'priority'    => 8,
		'description' => esc_html__( 'A scrolling row of publication logos, shown just below the header image.', 'eglatone' ),
	) );

	$wp_customize->add_setting( 'eglatone_press_option', array(
		'default'           => 'homepage',
		'sanitize_callback' => 'eglatone_hero_sanitize_visibility',
	) );

	$wp_customize->add_control( 'eglatone_press_option', array(
		'label'   => esc_html__( 'Show this section', 'eglatone' ),
		'section' => 'eglatone_press',
		'type'    => 'select',
		'choices' => array(
			'disabled'    => esc_html__( 'Disabled', 'eglatone' ),
			'homepage'    => esc_html__( 'Homepage / Frontpage', 'eglatone' ),
			'entire-site' => esc_html__( 'Entire Site', 'eglatone' ),
		),
	) );

	$wp_customize->add_setting( 'eglatone_press_label', array(
		'default'           => esc_html__( 'Featured in:', 'eglatone' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'eglatone_press_label', array(
		'label'   => esc_html__( 'Label', 'eglatone' ),
		'section' => 'eglatone_press',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'eglatone_press_items', array(
		'default'           => eglatone_press_defaults(),
		'sanitize_callback' => 'wp_kses_post',
	) );

	$wp_customize->add_control( 'eglatone_press_items', array(
		'label'       => esc_html__( 'Publications', 'eglatone' ),
		'description' => esc_html__( 'One per line:  Name | image URL | article URL. Upload logos to the Media Library and paste their URLs. A line with no image shows the name as text.', 'eglatone' ),
		'section'     => 'eglatone_press',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'eglatone_press_speed', array(
		'default'           => 40,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'eglatone_press_speed', array(
		'label'       => esc_html__( 'Seconds per pass', 'eglatone' ),
		'description' => esc_html__( 'Higher is slower. Hovering pauses it.', 'eglatone' ),
		'section'     => 'eglatone_press',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 10, 'max' => 240, 'step' => 5 ),
	) );

	$wp_customize->add_setting( 'eglatone_press_logo_height', array(
		'default'           => 40,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'eglatone_press_logo_height', array(
		'label'       => esc_html__( 'Logo height in pixels', 'eglatone' ),
		'description' => esc_html__( 'Every logo is scaled to fit the same box, whatever its original shape, so the row stays even.', 'eglatone' ),
		'section'     => 'eglatone_press',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 18, 'max' => 80, 'step' => 2 ),
	) );

	$wp_customize->add_setting( 'eglatone_press_grayscale', array(
		'default'           => true,
		'sanitize_callback' => 'eglatone_hero_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'eglatone_press_grayscale', array(
		'label'       => esc_html__( 'Show logos in grey, colour on hover', 'eglatone' ),
		'description' => esc_html__( 'Keeps a row of mismatched logos looking like one set.', 'eglatone' ),
		'section'     => 'eglatone_press',
		'type'        => 'checkbox',
	) );
}
add_action( 'customize_register', 'eglatone_press_customize_register', 12 );

/**
 * Checkbox sanitizer (the theme's own is registered later than this file runs).
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function eglatone_hero_sanitize_checkbox( $value ) {
	return ! empty( $value );
}

/**
 * Render the press strip.
 */
function eglatone_press_strip() {
	if ( ! eglatone_check_section( get_theme_mod( 'eglatone_press_option', 'homepage' ) ) ) {
		return;
	}

	$items = eglatone_press_items();

	if ( empty( $items ) ) {
		return;
	}

	$label = get_theme_mod( 'eglatone_press_label', esc_html__( 'Featured in:', 'eglatone' ) );
	$speed = (int) get_theme_mod( 'eglatone_press_speed', 40 );
	$speed = max( 10, min( 240, $speed ) );

	$classes = array( 'eglatone-press', 'section' );

	if ( get_theme_mod( 'eglatone_press_grayscale', true ) ) {
		$classes[] = 'is-grayscale';
	}

	/*
	 * Every logo gets an identical box. The slot is wider than it is tall so
	 * wordmarks (swissinfo, El Mundo) and square marks (Yahoo, Forbes) both sit
	 * comfortably; object-fit keeps each one undistorted inside it.
	 */
	$logo_h = (int) get_theme_mod( 'eglatone_press_logo_height', 40 );
	$logo_h = max( 18, min( 80, $logo_h ) );
	$slot_w = (int) round( $logo_h * 3.4 );

	$style = sprintf( '--press-logo-h: %dpx; --press-slot-w: %dpx;', $logo_h, $slot_w );
	?>
	<div id="eglatone-press-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" style="<?php echo esc_attr( $style ); ?>">
		<?php if ( $label ) : ?>
			<div class="press-label"><?php echo esc_html( $label ); ?></div>
		<?php endif; ?>

		<div class="press-viewport">
			<ul class="press-track">
				<?php foreach ( $items as $item ) : ?>
					<li class="press-item">
						<?php if ( $item['link'] ) : ?>
							<a class="press-link" href="<?php echo esc_url( $item['link'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php else : ?>
							<span class="press-link">
						<?php endif; ?>

						<?php if ( $item['image'] ) : ?>
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy">
						<?php else : ?>
							<span class="press-name"><?php echo esc_html( $item['name'] ); ?></span>
						<?php endif; ?>

						<?php if ( $item['link'] ) : ?>
							</a>
						<?php else : ?>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul><!-- .press-track -->
		</div><!-- .press-viewport -->
	</div><!-- #eglatone-press-section -->
	<?php
}
