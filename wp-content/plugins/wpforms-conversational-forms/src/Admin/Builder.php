<?php

namespace WPFormsConversationalForms\Admin;

/**
 * Conversational Forms builder functionality.
 *
 * @since 1.0.0
 */
class Builder {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		\add_action( 'wpforms_builder_enqueues_before', array( $this, 'enqueue_scripts' ) );
		\add_filter( 'wpforms_builder_settings_sections', array( $this, 'register_settings' ), 30, 2 );
		\add_action( 'wpforms_form_settings_panel_content', array( $this, 'settings_content' ), 30, 2 );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_media();

		\wp_enqueue_script(
			'wpforms-admin-builder-conversational-forms',
			\wpforms_conversational_forms()->url . "assets/js/admin-builder-conversational-forms{$min}.js",
			array( 'jquery', 'wpforms-builder', 'wpforms-utils' ),
			\WPFORMS_CONVERSATIONAL_FORMS_VERSION,
			true
		);

		\wp_localize_script(
			'wpforms-admin-builder-conversational-forms',
			'wpforms_admin_builder_conversational_forms',
			array(
				'nonce' => \wp_create_nonce( 'wpforms_admin_builder_conversational_forms_nonce' ),
				'i18n'  => array(
					'enable_prevent_modal'             => \esc_html__( 'Conversational Forms cannot be enabled if Form Pages is enabled at the same time.', 'wpforms-conversational-forms' ),
					'enable_prevent_modal_ok'          => \esc_html__( 'OK', 'wpforms-conversational-forms' ),
					'logo_preview_alt'                 => \esc_html__( 'Form Logo', 'wpforms-conversational-forms' ),
					'logo_selection_frame_title'       => \esc_html__( 'Select or Upload Form Custom Logo', 'wpforms-conversational-forms' ),
					'logo_selection_frame_button_text' => \esc_html__( 'Use this media', 'wpforms-conversational-forms' ),
				),
			)
		);

		\wp_enqueue_style(
			'wpforms-conversational-forms-admin-builder',
			\wpforms_conversational_forms()->url . "assets/css/admin-builder-conversational-forms{$min}.css",
			array(),
			\WPFORMS_CONVERSATIONAL_FORMS_VERSION
		);
	}

	/**
	 * Register settings area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Settings area sections.
	 *
	 * @return array
	 */
	public function register_settings( $sections ) {

		$sections['conversational_forms'] = \esc_html__( 'Conversational Forms', 'wpforms-conversational-forms' );

		return $sections;
	}

	/**
	 * Settings content.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 */
	public function settings_content( $instance ) {

		$permalink_enabled = ! empty( get_option( 'permalink_structure' ) );

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-conversational_forms">';

		echo '<div class="wpforms-panel-content-section-title">';

		esc_html_e( 'Conversational Forms', 'wpforms-conversational-forms' );

		if ( $permalink_enabled ) {
			echo '<a href="' . esc_url( home_url( $instance->form->post_name ) ) . '" id="wpforms-conversational-forms-preview-conversational-form" target="_blank">' . esc_html__( 'Preview Conversational Form', 'wpforms-conversational-forms' ) . '</a>';
		}

		echo '</div><!-- .wpforms-panel-content-section-title -->';

		// Display 'configure permalink' notice.
		if ( ! $permalink_enabled ) {

			echo '<p class="wpforms-alert wpforms-alert-info">' .
				sprintf(
					wp_kses(
				        /* translators: %s - Permalink Settings page URL. */
						__( 'Heads up! To use Conversational Forms, please configure your site\'s permalinks on the WordPress <a href="%s">Permalink Settings</a> page.', 'wpforms-conversational-forms' ),
						[
							'a' => [
								'href' => [],
							],
						]
					),
					esc_url( admin_url( 'options-permalink.php' ) )
				) .
				'</p>' .
			'</div><!-- .wpforms-panel-content-section-conversational_forms -->';

			return;
		}

		\wpforms_panel_field(
			'checkbox',
			'settings',
			'conversational_forms_enable',
			$instance->form_data,
			\esc_html__( 'Enable Conversational Form Mode', 'wpforms-conversational-forms' )
		);

		echo '<div id="wpforms-conversational-forms-content-block">';

		\wpforms_panel_field(
			'text',
			'settings',
			'conversational_forms_title',
			$instance->form_data,
			\esc_html__( 'Conversational Form Title', 'wpforms-conversational-forms' )
		);

		\wpforms_panel_field(
			'tinymce',
			'settings',
			'conversational_forms_description',
			$instance->form_data,
			\esc_html__( 'Message', 'wpforms-conversational-forms' ),
			array(
				'tinymce' => array(
					'editor_height' => 175,
				),
				'tooltip' => \esc_html__( 'This content will display below the Conversational Form Title, above the form.', 'wpforms-conversational-forms' ),
			)
		);

		\wpforms_panel_field(
			'text',
			'settings',
			'conversational_forms_page_slug',
			$instance->form_data,
			\esc_html__( 'Permalink', 'wpforms-conversational-forms' ),
			array(
				'value'       => isset( $instance->form->post_name ) ? \esc_html( \urldecode( $instance->form->post_name ) ) : '',
				'after_label' => '<div class="wpforms-conversational-forms-page-slug-container">
                                 <span class="conversational-forms-page-slug-pre-url wpforms-one-third">' . \trailingslashit( \home_url() ) . '</span>',
				'after'       => $this->get_page_slug_buttons_html( $instance ) . '</div><!-- .wpforms-conversational-forms-page-slug-container -->',
				'tooltip'     => \esc_html__( 'This is the URL for your Conversational Form.', 'wpforms-conversational-forms' ),
			)
		);

		\wpforms_panel_field(
			'text',
			'settings',
			'conversational_forms_custom_logo',
			$instance->form_data,
			\esc_html__( 'Header Logo', 'wpforms-conversational-forms' ),
			array(
				'readonly'    => true,
				'after_label' => $this->get_custom_logo_preview_html( $instance->form_data ),
				'after'       => $this->get_custom_logo_buttons_html(),
				'tooltip'     => \esc_html__( 'This is a custom logo displayed above the form title.', 'wpforms-conversational-forms' ),
			)
		);

		\wpforms_panel_field(
			'checkbox',
			'settings',
			'conversational_forms_brand_disable',
			$instance->form_data,
			\esc_html__( 'Hide WPForms Branding', 'wpforms-conversational-forms' )
		);

		$color_options = $this->get_color_options( $instance );

		\wpforms_panel_field(
			'radio',
			'settings',
			'conversational_forms_color_scheme',
			$instance->form_data,
			\esc_html__( 'Color Scheme', 'wpforms-conversational-forms' ),
			array(
				'default' => isset( $color_options[0]['value'] ) ? $color_options[0]['value'] : '#ffffff',
				'options' => $color_options,
				'tooltip' => \esc_html__( 'This is the color of the submit button and the page background.', 'wpforms-conversational-forms' ),
			)
		);

		\wpforms_panel_field(
			'radio',
			'settings',
			'conversational_forms_progress_bar',
			$instance->form_data,
			\esc_html__( 'Progress Bar', 'wpforms-conversational-forms' ),
			array(
				'default' => 'percentage',
				'options' => array(
					'percentage' => array(
						'label' => \esc_html__( 'Percentage', 'wpforms-conversational-forms' ),
					),
					'proportion' => array(
						'label' => \esc_html__( 'Proportion', 'wpforms-conversational-forms' ),
					),
				),
				'tooltip' => \esc_html__( 'This is a Progress Bar style.', 'wpforms-conversational-forms' ),
			)
		);

		echo '</div><!-- #wpforms-conversational-forms-content-block -->';

		echo '</div><!-- .wpforms-panel-content-section-conversational_forms -->';
	}

	/**
	 * Get available color options for the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 *
	 * @return array
	 */
	public function get_color_options( $instance ) {

		$color_options = array(
			array(
				'label' => '<span class="conversational-forms-color-scheme-color blue"></span>',
				'value' => '#448ccb',
			),
			array(
				'label' => '<span class="conversational-forms-color-scheme-color dark-blue"></span>',
				'value' => '#1a3c5a',
			),
			array(
				'label' => '<span class="conversational-forms-color-scheme-color teal"></span>',
				'value' => '#4aa891',
			),
			array(
				'label' => '<span class="conversational-forms-color-scheme-color purple"></span>',
				'value' => '#9178b3',
			),
			array(
				'label' => '<span class="conversational-forms-color-scheme-color light"></span>',
				'value' => '#cccccc',
			),
			array(
				'label' => '<span class="conversational-forms-color-scheme-color dark"></span>',
				'value' => '#363636',
			),
		);

		$custom_color = ! empty( $instance->form_data['settings']['conversational_forms_color_scheme'] ) ? \sanitize_hex_color( $instance->form_data['settings']['conversational_forms_color_scheme'] ) : '';

		if ( empty( $custom_color ) || \wp_list_filter( $color_options, array( 'value' => $custom_color ) ) ) {
			$custom_color = '#ffffff';
		}

		$color_options[] = array(
			'label' => '<span></span>',
			'value' => $custom_color,
		);

		return $color_options;
	}

	/**
	 * Form custom logo preview HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return false|string
	 */
	public function get_custom_logo_preview_html( $form_data ) {

		$custom_logo_id = ! empty( $form_data['settings']['conversational_forms_custom_logo'] ) ? $form_data['settings']['conversational_forms_custom_logo'] : '';

		$custom_logo_url = wp_get_attachment_image_src( $custom_logo_id, 'medium' );
		$custom_logo_url = empty( $custom_logo_url ) ? wp_get_attachment_image_src( $custom_logo_id, 'full' ) : $custom_logo_url;
		$custom_logo_url = isset( $custom_logo_url[0] ) ? $custom_logo_url[0] : '';

		\ob_start();

		?>
		<div class="wpforms-conversational-forms-custom-logo-container" <?php echo $custom_logo_url ? '' : 'style="display: none;"'; ?>>
			<a href="#" class="wpforms-conversational-forms-custom-logo-delete">
				<?php if ( $custom_logo_url ) : ?>
					<img src="<?php echo \esc_url( $custom_logo_url ); ?>" alt="<?php \esc_html_e( 'Form Logo', 'wpforms-conversational-forms' ); ?>" />
				<?php endif; ?>
			</a>
		</div>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Form custom logo control buttons HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return false|string
	 */
	public function get_custom_logo_buttons_html() {

		\ob_start();

		?>
		<p>
			<a href="#" class="wpforms-conversational-forms-custom-logo-upload wpforms-btn wpforms-btn-lightgrey">
				<?php \esc_html_e( 'Upload Image', 'wpforms-conversational-forms' ); ?>
			</a>
		</p>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Conversational Form slug control buttons HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 *
	 * @return false|string
	 */
	public function get_page_slug_buttons_html( $instance ) {

		\ob_start();

		?>
		<a href="<?php echo \esc_url( \home_url( $instance->form->post_name ) ); ?>" class="wpforms-conversational-forms-page-slug-view wpforms-btn wpforms-btn-lightgrey" target="_blank">
			<?php \esc_html_e( 'View', 'wpforms-conversational-forms' ); ?>
		</a>
		<?php

		return \ob_get_clean();
	}
}
