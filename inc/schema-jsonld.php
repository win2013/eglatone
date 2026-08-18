<?php
/**
 * Schema (JSON-LD) editor
 *
 * Moves the hand-maintained <script type="application/ld+json"> block out of
 * header.php and into an editable, validated admin screen at
 * Settings > Schema (JSON-LD).
 *
 * @package Eglatone
 */

/**
 * Option that holds the last VALID JSON-LD document.
 */
define( 'EGLATONE_SCHEMA_OPTION', 'eglatone_jsonld_schema' );

/**
 * Option that holds an invalid submission so the editor can redisplay it.
 */
define( 'EGLATONE_SCHEMA_DRAFT_OPTION', 'eglatone_jsonld_schema_draft' );

/**
 * Option that toggles output on/off.
 */
define( 'EGLATONE_SCHEMA_ENABLED_OPTION', 'eglatone_jsonld_enabled' );

/**
 * The schema that shipped in header.php, used to seed the option on first run
 * so nothing is lost during the migration.
 *
 * @return string
 */
function eglatone_schema_default_jsonld() {
	return <<<'JSONLD'
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Dr. Edwin A. Hernandez",
  "alternateName": [
    "Edwin Hernandez",
    "Dr. E",
    "Edwin Hernandez-Mondragon",
    "Edwin A. Hernandez-Mondragon"
  ],
  "honorificSuffix": "Ph.D.",
  "hasOccupation": {
    "@type": "Occupation",
    "name": "Technology Leader and Entrepreneur",
    "educationRequirements": "PhD in Computer Engineering"
  },
  "url": "https://edwinhernandez.com",
  "image": [
    "https://edwinhernandez.com/wp-content/uploads/2024/04/cropped-Dr-Edwin-tuxedo-go-global-scaled-1-666x575.jpeg",
    "https://edwinhernandez.com/wp-content/uploads/2019/07/cropped-EGLA-TV-STUDIO-EDWIN-youtube-scaled.jpg",
    "https://edwinhernandez.com/wp-content/uploads/2026/05/Innovation-Tech-Today-Press-Hit-Edwin-scaled.png",
    "https://edwinhernandez.com/wp-content/uploads/2024/10/ABC-Edwin-H-scaled.png"
  ],
  "jobTitle": "Founder & Chief Technology Officer",
  "worksFor": {
    "@type": "Organization",
    "name": "EGLA CORP",
    "url": "https://eglacorp.com"
  },
  "alumniOf": [
    {
      "@type": "CollegeOrUniversity",
      "name": "University of Florida",
      "url": "https://www.ufl.edu"
    },
    {
      "@type": "CollegeOrUniversity",
      "name": "Instituto Tecnológico de Costa Rica",
      "url": "https://www.tec.ac.cr"
    }
  ],
  "description": "Dr. Edwin A. Hernandez is a technology leader, inventor, entrepreneur, expert witness testifying in multi-million dollar patent and intellectual property cases, and host of the TECHEDTV podcast. Founder of EGLA Corp and the EGLAVATOR technology incubator, lead investor in several ventures including at PointsKash. Dr. Edwin Hernandez is also member of the FAU Technical Advisory Board, and co-founder of Honduras Global. Dr. Edwin is inventor to 17 US Patents and others in Europe with licensing deals with Apple, Google, Samsung, Verizon, Nokia, Ericsson, and many others. Dr. Edwin is working on MEVIA and the MEVIA Operating System (MEVIA OS), as well as Model Context Protocol services.",
  "knowsAbout": [
    "Wireless Communications",
    "Patent Licensing",
    "Intellectual Property",
    "Artificial Intelligence",
    "Multimedia Streaming",
    "Expert Witness Testimony",
    "Startup Incubation",
    "Technology Investing",
    "Distributed Systems",
    "5G",
    "Wearable Computers",
    "Low Power",
    "Damages Expert",
    "Trade Secrets",
    "Code Reviews",
    "Machine Learning",
    "Patents",
    "Embedded Software",
    "IoT",
    "Augmented Reality",
    "Cryptography",
    "WiFi",
    "Patent Litigation",
    "Software Engineering",
    "Technical Leadership"
  ],
  "award": [
    "Fulbright Scholar",
    "IEEE Senior Member",
    "Mobile World Congress Judge"
  ],
  "subjectOf": [
    {
      "@type": "VideoObject",
      "name": "TechedTV at MWC2026",
      "description": "Dr. Edwin Hernandez takes us on a fascinating journey through the Mobile World Congress 2026 in Barcelona, showcasing cutting-edge technologies that are set to redefine our daily lives and industries (0:00). From advanced robotics to groundbreaking quantum computing, this event highlighted the rapid pace of innovation",
      "thumbnailUrl": "https://i.ytimg.com/vi/HxkCr4MJuzA/default.jpg",
      "uploadDate": "2026-03-03",
      "embedUrl": "https://www.youtube.com/embed/HxkCr4MJuzA",
      "url": "https://www.youtube.com/watch?v=HxkCr4MJuzA"
    }
  ],
  "email": "mailto:edwin@edwinhernandez.com",
  "telephone": "+1-561-306-4996",
  "sameAs": [
    "https://www.linkedin.com/in/edwinhm/",
    "https://x.com/edwinhm",
    "https://eglacorp.com",
    "https://eglavator.com",
    "https://egla.ai/",
    "https://github.com/win2013/",
    "https://teched.tv",
    "https://instagram.com/dredwinhm/",
    "https://youtube.com/techedtv",
    "https://youtube.com/techedtves",
    "https://podcasts.apple.com/us/podcast/techedtv-podcast-with-dr-edwin-hernandez/id1522127589",
    "https://scholar.google.com/citations?user=QsPG_EoAAAAJ&hl=en"
  ]
}
JSONLD;
}

/**
 * Current stored schema, falling back to the shipped default.
 *
 * @return string
 */
function eglatone_get_schema_jsonld() {
	$stored = get_option( EGLATONE_SCHEMA_OPTION, false );

	if ( false === $stored ) {
		return eglatone_schema_default_jsonld();
	}

	return (string) $stored;
}

/**
 * Register settings.
 */
function eglatone_schema_register_settings() {
	register_setting( 'eglatone_schema_group', EGLATONE_SCHEMA_OPTION, array(
		'type'              => 'string',
		'sanitize_callback' => 'eglatone_sanitize_schema_jsonld',
		'default'           => eglatone_schema_default_jsonld(),
	) );

	register_setting( 'eglatone_schema_group', EGLATONE_SCHEMA_ENABLED_OPTION, array(
		'type'              => 'boolean',
		'sanitize_callback' => 'eglatone_sanitize_schema_enabled',
		'default'           => 1,
	) );
}
add_action( 'admin_init', 'eglatone_schema_register_settings' );

/**
 * Sanitize the enabled checkbox.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function eglatone_sanitize_schema_enabled( $value ) {
	return empty( $value ) ? 0 : 1;
}

/**
 * Validate the submitted JSON.
 *
 * Invalid JSON is never stored as the live value: the previous valid document
 * is kept and the raw submission is parked in a draft option so the editor can
 * show it back with an error instead of throwing the work away.
 *
 * @param string $input Raw textarea contents.
 * @return string
 */
function eglatone_sanitize_schema_jsonld( $input ) {
	$input = is_string( $input ) ? trim( wp_unslash( $input ) ) : '';

	// Empty means "output nothing" - that is a valid choice.
	if ( '' === $input ) {
		delete_option( EGLATONE_SCHEMA_DRAFT_OPTION );

		return '';
	}

	$decoded = json_decode( $input, true );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		update_option( EGLATONE_SCHEMA_DRAFT_OPTION, $input, false );

		add_settings_error(
			EGLATONE_SCHEMA_OPTION,
			'eglatone_schema_invalid',
			sprintf(
				/* translators: %s: JSON parser error message. */
				esc_html__( 'Not saved - the JSON is invalid: %s. Your text is still in the editor below; the previously saved schema is still live on the site.', 'eglatone' ),
				esc_html( json_last_error_msg() )
			),
			'error'
		);

		return eglatone_get_schema_jsonld();
	}

	if ( ! is_array( $decoded ) ) {
		update_option( EGLATONE_SCHEMA_DRAFT_OPTION, $input, false );

		add_settings_error(
			EGLATONE_SCHEMA_OPTION,
			'eglatone_schema_not_object',
			esc_html__( 'Not saved - the schema must be a JSON object or an array of objects.', 'eglatone' ),
			'error'
		);

		return eglatone_get_schema_jsonld();
	}

	// Valid: clear any parked draft.
	delete_option( EGLATONE_SCHEMA_DRAFT_OPTION );

	add_settings_error(
		EGLATONE_SCHEMA_OPTION,
		'eglatone_schema_saved',
		esc_html__( 'Schema saved and validated.', 'eglatone' ),
		'success'
	);

	return $input;
}

/**
 * Add the admin page.
 */
function eglatone_schema_admin_menu() {
	add_options_page(
		esc_html__( 'Schema (JSON-LD)', 'eglatone' ),
		esc_html__( 'Schema (JSON-LD)', 'eglatone' ),
		'manage_options',
		'eglatone-schema',
		'eglatone_schema_settings_page'
	);
}
add_action( 'admin_menu', 'eglatone_schema_admin_menu' );

/**
 * Render the admin page.
 */
function eglatone_schema_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$draft   = get_option( EGLATONE_SCHEMA_DRAFT_OPTION, '' );
	$value   = ( '' !== $draft ) ? $draft : eglatone_get_schema_jsonld();
	$enabled = (int) get_option( EGLATONE_SCHEMA_ENABLED_OPTION, 1 );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Schema (JSON-LD)', 'eglatone' ); ?></h1>

		<p class="description">
			<?php esc_html_e( 'This JSON-LD block is printed inside the <head> of every page. It must be a single JSON object, or an array of objects. Invalid JSON will not be saved.', 'eglatone' ); ?>
			<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Test it with Google Rich Results', 'eglatone' ); ?></a>
		</p>

		<?php settings_errors( EGLATONE_SCHEMA_OPTION ); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'eglatone_schema_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Output schema', 'eglatone' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( EGLATONE_SCHEMA_ENABLED_OPTION ); ?>" value="1" <?php checked( 1, $enabled ); ?>>
							<?php esc_html_e( 'Print this JSON-LD block in the site head', 'eglatone' ); ?>
						</label>
					</td>
				</tr>
			</table>

			<p>
				<button type="button" class="button" id="eglatone-schema-format"><?php esc_html_e( 'Format / check JSON', 'eglatone' ); ?></button>
				<span id="eglatone-schema-status" style="margin-left:10px;font-weight:600;"></span>
			</p>

			<textarea
				id="eglatone-schema-json"
				name="<?php echo esc_attr( EGLATONE_SCHEMA_OPTION ); ?>"
				rows="30"
				class="large-text code"
				spellcheck="false"
				style="font-family:Menlo,Consolas,monospace;font-size:13px;line-height:1.5;white-space:pre;overflow-wrap:normal;overflow-x:auto;"><?php echo esc_textarea( $value ); ?></textarea>

			<?php submit_button( esc_html__( 'Save schema', 'eglatone' ) ); ?>
		</form>
	</div>

	<script>
	( function() {
		var area   = document.getElementById( 'eglatone-schema-json' ),
			button = document.getElementById( 'eglatone-schema-format' ),
			status = document.getElementById( 'eglatone-schema-status' );

		if ( ! area || ! button ) {
			return;
		}

		button.addEventListener( 'click', function() {
			try {
				var parsed = JSON.parse( area.value );

				area.value      = JSON.stringify( parsed, null, 2 );
				status.textContent = <?php echo wp_json_encode( esc_html__( 'Valid JSON - formatted.', 'eglatone' ) ); ?>;
				status.style.color = '#008a20';
			} catch ( e ) {
				status.textContent = <?php echo wp_json_encode( esc_html__( 'Invalid JSON: ', 'eglatone' ) ); ?> + e.message;
				status.style.color = '#d63638';
			}
		} );
	} )();
	</script>
	<?php
}

/**
 * Print the schema in the site head.
 */
function eglatone_schema_output() {
	if ( ! get_option( EGLATONE_SCHEMA_ENABLED_OPTION, 1 ) ) {
		return;
	}

	$json = trim( eglatone_get_schema_jsonld() );

	if ( '' === $json ) {
		return;
	}

	$decoded = json_decode( $json, true );

	if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
		// Never print malformed structured data.
		return;
	}

	/*
	 * Re-encoding escapes forward slashes, which makes a "</script>" sequence
	 * inside a string value impossible - so the block cannot break out of the
	 * script tag.
	 */
	$safe = wp_json_encode( $decoded, JSON_UNESCAPED_UNICODE );

	if ( false === $safe ) {
		return;
	}

	echo "\n<script type=\"application/ld+json\">" . $safe . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'eglatone_schema_output', 20 );
