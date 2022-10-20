<?php

/**
 * Form Abandonment.
 *
 * @since 1.0.0
 */
class WPForms_Form_Abandonment {

	/**
	 * Primary class constructor.
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

		// Admin related Actions/Filters.
		add_action( 'wpforms_builder_enqueues', array( $this, 'admin_enqueues' ) );
		add_filter( 'wpforms_builder_settings_sections', array( $this, 'settings_register' ), 20, 2 );
		add_action( 'wpforms_form_settings_panel_content', array( $this, 'settings_content' ), 20, 2 );
		add_action( 'wpforms_form_settings_notifications_single_after', array( $this, 'notification_settings' ), 10, 2 );
		add_filter( 'wpforms_entries_table_counts', array( $this, 'entries_table_counts' ), 10, 2 );
		add_filter( 'wpforms_entries_table_views', array( $this, 'entries_table_views' ), 10, 3 );
		add_filter( 'wpforms_entries_table_column_status', array( $this, 'entries_table_column_status' ), 10, 2 );
		add_filter( 'wpforms_entry_details_sidebar_details_status', array( $this, 'entries_details_sidebar_status' ), 10, 3 );
		add_filter( 'wpforms_entry_details_sidebar_actions_link', array( $this, 'entries_details_sidebar_actions' ), 10, 3 );
		add_action( 'wp_ajax_wpforms_form_abandonment', array( $this, 'process_entries' ) );
		add_action( 'wp_ajax_nopriv_wpforms_form_abandonment', array( $this, 'process_entries' ) );
		add_filter( 'wpforms_entry_email_process', array( $this, 'process_email' ), 50, 5 );
		add_action( 'wpforms_process_complete', array( $this, 'process_complete' ), 10, 4 );

		// Front-end related Actions/.
		add_action( 'wpforms_frontend_container_class', array( $this, 'form_container_class' ), 10, 2 );
		add_action( 'wpforms_wp_footer', array( $this, 'frontend_enqueues' ) );
	}

	/*****************************
	 * Admin-side functionality. *
	 *****************************/

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueues() {

		$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WPFORMS_DEBUG' ) && WPFORMS_DEBUG ) ) ? '' : '.min';

		wp_enqueue_script(
			'wpforms-builder-form-abandonment',
			plugin_dir_url( __FILE__ ) . 'assets/js/admin-builder-form-abandonment' . $suffix . '.js',
			array( 'jquery' ),
			WPFORMS_FORM_ABANDONMENT_VERSION,
			false
		);
	}

	/**
	 * Form Abandonment settings register section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections  Settings page sections list.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	public function settings_register( $sections, $form_data ) {

		$sections['form_abandonment'] = esc_html__( 'Form Abandonment', 'wpforms-form-abandonment' );

		return $sections;
	}

	/**
	 * Form Abandonment settings content.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $instance WPForms_Builder_Panel_Settings class instance.
	 */
	public function settings_content( $instance ) {

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-form_abandonment">';

		echo '<div class="wpforms-panel-content-section-title">';
		esc_html_e( 'Form Abandonment', 'wpforms-form-abandonment' );
		echo '<a href="https://wpforms.com/docs/how-to-install-and-use-form-abandonment-with-wpforms/" target="_blank" rel="noopener noreferrer"><i class="fa fa-question-circle wpforms-help-tooltip" title="' . esc_attr__( 'Click here for documentation on Form Abandonment addon', 'wpforms-form-abandonment' ) . '"></i></a>';
		echo '</div>';

		wpforms_panel_field(
			'checkbox',
			'settings',
			'form_abandonment',
			$instance->form_data,
			esc_html__( 'Enable form abandonment lead capture', 'wpforms-form-abandonment' )
		);

		wpforms_panel_field(
			'radio',
			'settings',
			'form_abandonment_fields',
			$instance->form_data,
			'',
			array(
				'options' => array(
					''    => array(
						'label' => esc_html__( 'Save only if email address or phone number is provided', 'wpforms-form-abandonment' ),
					),
					'all' => array(
						'label'   => esc_html__( 'Always save abandoned entries', 'wpforms-form-abandonment' ),
						'tooltip' => esc_html__( 'We believe abandoned form entries are only helpful if you have some way to contact the user. However this option is good for users that have anonymous form submissions.', 'wpforms-form-abandonment' ),
					),
				),
			)
		);

		wpforms_panel_field(
			'checkbox',
			'settings',
			'form_abandonment_duplicates',
			$instance->form_data,
			esc_html__( 'Prevent duplicate abandon entries', 'wpforms-form-abandonment' ),
			array(
				'tooltip' => esc_html__( 'When checked only the most recent abandoned entry from the user is saved. See the Form Abandonment documentation for more info regarding this setting.', 'wpforms-form-abandonment' ),
			)
		);

		echo '</div>';
	}

	/**
	 * Add select to form notification settings.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPForms_Builder_Panel_Settings $settings WPForms_Builder_Panel_Settings class instance.
	 * @param int                             $id       Subsection ID.
	 */
	public function notification_settings( $settings, $id ) {

		// Only display if they have turned on form abandonment for the form and have saved.
		if ( ! $this->has_form_abandonment( $settings->form_data ) ) {
			return;
		}

		wpforms_panel_field(
			'checkbox',
			'notifications',
			'form_abandonment',
			$settings->form_data,
			esc_html__( 'Enable for abandoned forms entries', 'wpforms-form-abandonment' ),
			array(
				'parent'     => 'settings',
				'subsection' => $id,
				'tooltip'    => wp_kses(
					__( 'When enabled this notification will <em>only</em> be sent for abandoned form entries. This setting should only be used with <strong>new</strong> notifications.', 'wpforms-form-abandonment' ),
					array(
						'em'     => array(),
						'strong' => array(),
					)
				),
			)
		);
	}

	/**
	 * Lookup and store counts for abandoned entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $counts    Entries count list.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	public function entries_table_counts( $counts, $form_data ) {

		if ( $this->has_form_abandonment( $form_data ) ) {
			$counts['abandoned'] = wpforms()->entry->get_entries(
				array(
					'form_id' => absint( $form_data['id'] ),
					'status'  => 'abandoned',
				),
				true
			);
		}

		return $counts;
	}

	/**
	 * Create view for abandoned entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $views     Filters for entries various states.
	 * @param array $form_data Form data.
	 * @param array $counts    Entries count list.
	 *
	 * @return array
	 */
	public function entries_table_views( $views, $form_data, $counts ) {

		if ( $this->has_form_abandonment( $form_data ) ) {

			$base = add_query_arg(
				array(
					'page'    => 'wpforms-entries',
					'view'    => 'list',
					'form_id' => absint( $form_data['id'] ),
				),
				admin_url( 'admin.php' )
			);

			$current   = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$abandoned = '&nbsp;<span class="count">(<span class="abandoned-num">' . $counts['abandoned'] . '</span>)</span>';

			$views['abandoned'] = sprintf(
				'<a href="%s"%s>%s</a>',
				esc_url( add_query_arg( 'status', 'abandoned', $base ) ),
				$current === 'abandoned' ? ' class="current"' : '',
				esc_html__( 'Abandoned', 'wpforms-form-abandonment' ) . $abandoned
			);
		}

		return $views;
	}

	/**
	 * Enable the Status column for forms that are using form abandonment.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $show      Whether to show the Status column or not.
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	public function entries_table_column_status( $show, $form_data ) {

		if ( $this->has_form_abandonment( $form_data ) ) {
			return true;
		}

		return $show;
	}

	/**
	 * Enable the displaying status for forms that are using form abandonment
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $show      Whether to show the Status column or not.
	 * @param object $entry     Entry information.
	 * @param array  $form_data Form data.
	 *
	 * @return bool
	 */
	public function entries_details_sidebar_status( $show, $entry, $form_data ) {

		if ( $this->has_form_abandonment( $form_data ) ) {
			return true;
		}

		return $show;
	}

	/**
	 * For abandoned entries remove the link to resend email notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $links     List of links in sidebar.
	 * @param object $entry     Entry information.
	 * @param array  $form_data Form data.
	 *
	 * @return array
	 */
	public function entries_details_sidebar_actions( $links, $entry, $form_data ) {

		if ( $this->has_form_abandonment( $form_data ) ) {
			unset( $links['notifications'] );
		}

		return $links;
	}

	/**
	 * Process the abandoned entries via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function process_entries() {

		// Make sure we have required data.
		if ( empty( $_POST['forms'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error();
		}

		// User UID is required.
		if ( empty( $_COOKIE['_wpfuuid'] ) ) {
			wp_send_json_error();
		}

		// Grab posted data and decode.
		$data  = json_decode( stripslashes( $_POST['forms'] ) ); // phpcs:ignore
		$forms = array();

		// Compile all posted data into an array.
		foreach ( $data as $form_id => $form ) {

			$fields    = array();
			$form_vars = '';

			foreach ( $form as $post_input_data ) {
				$form_vars .= $post_input_data->name . '=' . rawurlencode( $post_input_data->value ) . '&';
			}

			parse_str( $form_vars, $fields );

			$forms[ $form_id ] = $fields['wpforms'];
		}

		// Go through the data for each form abandoned (if multiple) and process.
		foreach ( $forms as $form_id => $entry ) {

			wpforms()->process->fields = array();

			// Get the form settings for this form.
			$form = wpforms()->form->get( $form_id );

			// Form must be real and active (published).
			if ( ! $form || 'publish' !== $form->post_status ) {
				wp_send_json_error();
			}

			// If the honeypot was triggers we assume this is a spammer.
			if ( ! empty( $entry['hp'] ) ) {
				wp_send_json_error();
			}

			// Formatted form data.
			$form_data = apply_filters( 'wpforms_process_before_form_data_form_abandonment', wpforms_decode( $form->post_content ), $entry );

			// Pre-process filter.
			$entry = apply_filters( 'wpforms_process_before_filter_form_abandonment', $entry, $form_data );

			$exists          = false;
			$avoid_dupes     = ! empty( $form_data['settings']['form_abandonment_duplicates'] );
			$fields_required = empty( $form_data['settings']['form_abandonment_fields'] );
			$email_phone     = false;

			// Format fields.
			foreach ( $form_data['fields'] as $field ) {

				$field_id     = $field['id'];
				$field_type   = $field['type'];
				$field_submit = isset( $entry['fields'][ $field_id ] ) ? $entry['fields'][ $field_id ] : '';

				if ( ! apply_filters( 'wpforms_form_abandonment_process_entries_save_password', false, $form_data ) && 'password' === $field_type ) {
					continue;
				}

				// Don't support these fields for abandonment tracking.
				if ( in_array( $field_type, array( 'file-upload', 'signature' ), true ) ) {
					continue;
				}

				if ( in_array( $field_type, array( 'phone', 'email' ), true ) && ! is_array( $field_submit ) && ! empty( $field_submit ) ) {
					$email_phone = true;
				}

				if ( in_array( $field_type, array( 'email' ), true ) && is_array( $field_submit ) && ! empty( $field_submit['primary'] ) ) {
					$email_phone = true;
				}

				do_action( "wpforms_process_format_{$field_type}", $field_id, $field_submit, $form_data );
			}

			// If the form has phone/email required but neither is present then stop processing.
			if ( $fields_required && ! $email_phone ) {
				continue;
			}

			// Post-process filter.
			$fields = apply_filters( 'wpforms_process_filter_form_abandonment', wpforms()->process->fields, $entry, $form_data );

			// Post-process hook.
			do_action( 'wpforms_process_form_abandonment', $fields, $entry, $form_data );

			// Here we check to see if the user has had another abandoned entry
			// for this form within the last hour. If so, then update the
			// existing entry instead of creating a new one.
			if ( $avoid_dupes ) {

				global $wpdb;

				$user_uuid      = ! empty( $_COOKIE['_wpfuuid'] ) ? $_COOKIE['_wpfuuid'] : ''; //phpcs:ignore
				$hours_interval = 1 + (int) get_option( 'gmt_offset' );
				$exists         = $wpdb->get_row( // phpcs:ignore
					$wpdb->prepare(
						"SELECT entry_id FROM {$wpdb->prefix}wpforms_entries WHERE `form_id` = %d AND `user_uuid` = %s AND `status` = 'abandoned' AND `date` >= DATE_SUB(%s,INTERVAL %d HOUR) LIMIT 1;",
						absint( $form_id ),
						preg_replace( '/[^a-z0-9_\s-]+/i', '', $user_uuid ),
						current_time( 'mysql' ),
						$hours_interval
					)
				);
			}

			if ( ! empty( $exists ) ) {
				/*
				 * Updating a previous abandoned entry made within the last hour.
				 */

				$entry_id = $exists->entry_id;

				// Prepare the args to be updated.
				$data = array(
					'viewed' => 0,
					'fields' => wp_json_encode( $fields ),
					'date'   => date( 'Y-m-d H:i:s' ),
				);

				// Update.
				wpforms()->entry->update( $entry_id, $data, '', '', array( 'cap' => false ) );

			} else {
				/*
				 * Adding a new abandoned entry.
				 */

				// Get the user details.
				$user_id    = is_user_logged_in() ? get_current_user_id() : 0;
				$user_ip    = wpforms_get_ip();
				$user_agent = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 256 ) : '';
				$user_uuid  = ! empty( $_COOKIE['_wpfuuid'] ) ? $_COOKIE['_wpfuuid'] : '';

				// Prepare the args to be saved.
				$data = array(
					'form_id'    => absint( $form_id ),
					'user_id'    => absint( $user_id ),
					'status'     => 'abandoned',
					'fields'     => wp_json_encode( $fields ),
					'ip_address' => sanitize_text_field( $user_ip ),
					'user_agent' => sanitize_text_field( $user_agent ),
					'user_uuid'  => sanitize_text_field( $user_uuid ),
				);

				// Save.
				$entry_id = wpforms()->entry->add( $data );

				// Send notification emails if configured.
				wpforms()->process->entry_email( $fields, array(), $form_data, $entry_id, 'abandoned' );
			}

			// Boom.
			do_action( 'wpforms_process_complete_form_abandonment', $fields, $entry, $form_data, $entry_id );
		}

		wp_send_json_success();
	}

	/**
	 * Logic that helps decide if we should send abandoned entries notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $process         Whether to process or not.
	 * @param array  $fields          Form fields.
	 * @param array  $form_data       Form data.
	 * @param int    $notification_id Notification ID.
	 * @param string $context         The context of the current email process.
	 *
	 * @return bool
	 */
	public function process_email( $process, $fields, $form_data, $notification_id, $context ) {

		if ( ! $process ) {
			return false;
		}

		if ( 'abandoned' === $context && ! $this->has_form_abandonment( $form_data ) ) {
			// If form abandonment for the form is disabled, never send notifications for form abandonment.
			return false;
		}

		if ( 'abandoned' === $context ) {
			// Notifications triggered due to abandoned entry, don't send unless
			// the notification is enabled for form abandonment.
			if ( empty( $form_data['settings']['notifications'][ $notification_id ]['form_abandonment'] ) ) {
				return false;
			}
		} else {
			// Notifications triggered due to normal entry, don't send if
			// notification is enabled for form abandonment.
			if ( ! empty( $form_data['settings']['notifications'][ $notification_id ]['form_abandonment'] ) ) {
				return false;
			}
		}

		return $process;
	}

	/**
	 * Delete abandoned entries when user completes the form submit.
	 *
	 * @since 1.4.1
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $entry     The post data submitted by the form.
	 * @param array $form_data The information for the form.
	 * @param int   $entry_id  Entry ID.
	 */
	public function process_complete( $fields, $entry, $form_data, $entry_id ) {

		global $wpdb;

		if ( empty( $_COOKIE['_wpfuuid'] ) || empty( $form_data['settings']['form_abandonment'] ) ) {
			return;
		}

		$user_uuid      = $_COOKIE['_wpfuuid']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$hours_interval = 1 + (int) get_option( 'gmt_offset' );

		$wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wpforms_entries WHERE `form_id` = %d AND `user_uuid` = %s AND `status` = 'abandoned' AND `date` >= DATE_SUB(%s,INTERVAL %d HOUR)",
				(int) $form_data['id'],
				preg_replace( '/[^a-z0-9_\s-]+/i', '', $user_uuid ),
				current_time( 'mysql' ),
				$hours_interval
			)
		);
	}

	/****************************
	 * Front-end functionality. *
	 ****************************/

	/**
	 * Add form class if form abandonment is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param array $class     List of HTML classes.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	public function form_container_class( $class, $form_data ) {

		if ( $this->has_form_abandonment( $form_data ) ) {
			$class[] = 'wpforms-form-abandonment';
		}

		return $class;
	}

	/**
	 * Enqueue assets in the frontend. Maybe.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Page forms data and settings.
	 */
	public function frontend_enqueues( $forms ) {

		$enabled = false;
		$global  = apply_filters( 'wpforms_global_assets', wpforms_setting( 'global-assets', false ) );
		$suffix  = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WPFORMS_DEBUG' ) && WPFORMS_DEBUG ) ) ? '' : '.min';

		foreach ( $forms as $form ) {
			if ( $this->has_form_abandonment( $form ) ) {
				$enabled = true;
				break;
			}
		}

		if ( ! $enabled && ! $global ) {
			return;
		}

		/*
		 * If a form on the page has form abandonment enabled or global asset
		 * loading is turned on load mobile-detect lib and our js.
		 */

		// MobileDetect library.
		wp_enqueue_script(
			'wpforms-mobile-detect',
			plugin_dir_url( __FILE__ ) . 'assets/js/vendor/mobile-detect' . $suffix . '.js',
			array(),
			'1.4.3',
			false
		);

		wp_enqueue_script(
			'wpforms-form-abandonment',
			plugin_dir_url( __FILE__ ) . 'assets/js/wpforms-form-abandonment' . $suffix . '.js',
			array( 'jquery', 'wpforms-mobile-detect' ),
			WPFORMS_FORM_ABANDONMENT_VERSION,
			false
		);
		wp_localize_script(
			'wpforms-form-abandonment',
			'wpforms_form_abandonment',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'home_url' => home_url(),
			)
		);
	}

	/*********
	 * Misc. *
	 *********/

	/**
	 * Helper function that checks if form abandonment is enabled on a form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	public function has_form_abandonment( $form_data = array() ) {

		return ! empty( $form_data['settings']['form_abandonment'] );
	}
}

new WPForms_Form_Abandonment();
