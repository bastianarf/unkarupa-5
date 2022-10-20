<?php

/**
 * Offline Forms.
 *
 * @since 1.0.0
 */
class WPForms_Offline_Forms {

	/**
	 * Key used to save and retrieve from form_data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const KEY = 'offline_form';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Perform all required add_action() and add_filter().
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {

		add_action( 'wpforms_form_settings_general', array( $this, 'admin_register_setting' ) );
		add_action( 'wpforms_wp_footer', array( $this, 'frontend_enqueue' ) );
	}

	/**
	 * Register an option for forms to enable offline functionality.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $settings
	 */
	public function admin_register_setting( $settings ) {

		wpforms_panel_field(
			'checkbox',
			'settings',
			self::KEY,
			$settings->form_data,
			esc_html__( 'Enable offline mode for the form', 'wpforms-offline-forms' ),
			array(
				'tooltip' => esc_html__( 'This will allow users to submit forms even without internet connection. Form data will be saved into their browsers and will give them ability to resubmit once internet is back with just 1 click of a button.', 'wpforms-offline-forms' ),
			)
		);
	}

	/**
	 * Enqueue assets on front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms
	 */
	public function frontend_enqueue( $forms ) {

		$offline_forms = array();

		// Check if form(s) on a page has an enabled offline functionality.
		foreach ( $forms as $form ) {
			if ( self::is_enabled( $form ) ) {
				$offline_forms[] = (int) $form['id'];
			}
		}

		if ( empty( $offline_forms ) ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		/*
		 * JavaScript.
		 */
		wp_enqueue_script(
			'wpforms-offline-forms-store',
			WPFORMS_OF_URL . "assets/js/store2{$min}.js",
			array( 'wpforms' ),
			'2.5.3'
		);
		wp_enqueue_script(
			'wpforms-offline-forms-deserialize',
			WPFORMS_OF_URL . "assets/js/jquery.deserialize{$min}.js",
			array( 'wpforms' ),
			'2.0.0-rc1'
		);
		wp_enqueue_script(
			'wpforms-offline-forms',
			WPFORMS_OF_URL . "assets/js/wpforms-offline-forms{$min}.js",
			array( 'wpforms' ),
			WPFORMS_OF_VERSION
		);

		$data = apply_filters(
			'wpforms_offline_forms_frontend_localize_data',
			array(
				'offline_form_ids'          => $offline_forms,
				'check_connection_url'      => WPFORMS_OF_URL . 'assets/connection.txt', // should point to own domain unless CORS is configured.
				'check_connection_interval' => 30, // seconds.
				'check_connection_timeout'  => 3, // seconds.
				'text_offline'              => wpautop( esc_html__( "You appear to be offline right now. In order to save your progress, please submit this form.\n\nYour data will be saved in your browser, even if you close this page. Once your internet connection is restored, be sure to return here and complete the form submission.", 'wpforms-offline-forms' ) ),
				'text_restore_single'       => '<p>' . esc_html__( 'We\'ve saved your information from your previous attempt to complete this form. You can restore your saved data when your internet connection is active.', 'wpforms-offline-forms' ) . '</p>',
				'text_restore_plural'       => '<p>' . esc_html__( 'We see you previously attempted to complete this form. Would you like to restore then to submit one by one? After restoring, your browser will no longer have this data saved.', 'wpforms-offline-forms' ) . '</p>',
				'text_restore_btn'          => esc_html__( 'Restore', 'wpforms-offline-forms' ),
				'text_clear_btn'            => esc_html__( 'Clear', 'wpforms-offline-forms' ),
				'text_clear_all_btn'        => esc_html__( 'Clear All', 'wpforms-offline-forms' ),
			)
		);

		wp_localize_script( 'wpforms-offline-forms', 'wpforms_offline_forms', $data );
	}

	/**
	 * Helper function that checks if offline form functionality is enabled on a form.
	 * Making it static to be able to reuse elsewhere without class initialization.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data
	 *
	 * @return bool
	 */
	public function is_enabled( $form_data ) {
		return ! empty( $form_data['settings'][ self::KEY ] );
	}
}

new WPForms_Offline_Forms;
