<?php
/**
 * Custom Controls
 *
 * @package Eglatone
 */

/**
 * Add Custom Controls
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eglatone_custom_controls( $wp_customize ) {
	// Custom control for Important Links.
	class Eglatone_Important_Links_Control extends WP_Customize_Control {
		public $type = 'important-links';

		public function render_content() {
			// Add Theme instruction, Support Forum, Changelog, Donate link, Review, Facebook, Twitter, Google+, Pinterest links.
			$important_links = array(
				'theme_instructions' => array(
					'link'  => esc_url( 'https://edwinhernandez.com/' ),
					'text'  => esc_html__( 'Theme Instructions', 'eglatone' ),
					),
				'support' => array(
					'link'  => esc_url( 'https://edwinhernandez.com/' ),
					'text'  => esc_html__( 'Support', 'eglatone' ),
					),
				'changelog' => array(
					'link'  => esc_url( 'https://edwinhernandez.com/' ),
					'text'  => esc_html__( 'Changelog', 'eglatone' ),
					),
				'facebook' => array(
					'link'  => esc_url( 'https://www.linkedin.com/in/edwinhm/' ),
					'text'  => esc_html__( 'Facebook', 'eglatone' ),
					),
				'twitter' => array(
					'link'  => esc_url( 'https://x.com/edwinhm' ),
					'text'  => esc_html__( 'Twitter', 'eglatone' ),
					),
				'gplus' => array(
					'link'  => esc_url( 'https://plus.google.com/+Catchthemes/' ),
					'text'  => esc_html__( 'Google+', 'eglatone' ),
					),
				'pinterest' => array(
					'link'  => esc_url( 'https://www.linkedin.com/in/edwinhm/' ),
					'text'  => esc_html__( 'Pinterest', 'eglatone' ),
					),
			);

			foreach ( $important_links as $important_link ) {
				echo '<p><a target="_blank" href="' . $important_link['link'] . '" >' . $important_link['text'] . ' </a></p>';
			}
		}
	}

	// Custom control for dropdown category multiple select.
	class Eglatone_Multi_Cat extends WP_Customize_Control {
		public $type = 'dropdown-categories';

		public function render_content() {
			$dropdown = wp_dropdown_categories(
				array(
					'name'             => $this->id,
					'echo'             => 0,
					'hide_empty'       => false,
					'show_option_none' => false,
					'hide_if_empty'    => false,
					'show_option_all'  => esc_html__( 'All Categories', 'eglatone' ),
				)
			);

			$dropdown = str_replace( '<select', '<select multiple = "multiple" style = "height:150px;" ' . $this->get_link(), $dropdown );

			printf(
				'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
				$this->label,
				$dropdown
			);

			echo '<p class="description">' . esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'eglatone' ) . '</p>';
		}
	}

	// Custom control for any note, use label as output description.
	class Eglatone_Note_Control extends WP_Customize_Control {
		public $type = 'description';

		public function render_content() {
			echo '<h2 class="description">' . $this->label . '</h2>';
		}
	}

	class Eglatone_Toggle_Control extends WP_Customize_Control {
		public $type = 'light';

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			?>
			<label>
				<div style="display:flex;flex-direction: row;justify-content: flex-start;">
					<span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo esc_html( $this->label ); ?></span>
					<input id="cb<?php echo esc_attr( $this->instance_number ); ?>" type="checkbox" class="tgl tgl-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>"
											<?php
											$this->link();
											checked( $this->value() );
											?>
					 />
					<label for="cb<?php echo $this->instance_number; ?>" class="tgl-btn"></label>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<?php
		}
	}
}
add_action( 'customize_register', 'eglatone_custom_controls', 1 );
