<?php

namespace WPFormsLocker;

use WPFormsLocker\Lockers\UniqueAnswer;

/**
 * Various admin functionality.
 *
 * @since 1.0.0
 */
class Admin {

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

		// Admin form builder enqueues.
		\add_action( 'wpforms_builder_enqueues_before', array( $this, 'admin_builder_enqueues' ) );

		// Add Unique Answer toggle setting to selected core fields.
		\add_action( 'wpforms_field_options_bottom_advanced-options', array( $this, 'field_unique_answer_toggle' ), 10, 2 );

		// Register form builder settings area.
		\add_filter( 'wpforms_builder_settings_sections', array( $this, 'builder_settings_register' ), 30, 2 );

		// Form builder settings content.
		\add_action( 'wpforms_form_settings_panel_content', array( $this, 'builder_settings_content' ), 30, 2 );
	}

	/**
	 * Enqueues for the admin form builder.
	 *
	 * @since 1.0.0
	 */
	public function admin_builder_enqueues() {

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_script(
			'wpforms-admin-builder-form-locker',
			\wpforms_form_locker()->url . "assets/js/admin-builder-form-locker{$min}.js",
			array( 'jquery', 'wpforms-builder', 'wpforms-utils' ),
			\WPFORMS_FORM_LOCKER_VERSION,
			true
		);

		\wp_localize_script( 'wpforms-admin-builder-form-locker', 'wpforms_admin_builder_form_locker', array(
			'date_format' => \get_option( 'date_format' ),
			'time_format' => \get_option( 'time_format' ),
		) );

		\wp_enqueue_script(
			'wpforms-flatpickr',
			\WPFORMS_PLUGIN_URL . 'assets/js/flatpickr.min.js',
			array( 'jquery' ),
			'4.6.3',
			true
		);

		\wp_enqueue_script(
			'wpforms-jquery-timepicker',
			\WPFORMS_PLUGIN_URL . 'assets/js/jquery.timepicker.min.js',
			array( 'jquery' ),
			'1.11.5',
			true
		);

		\wp_enqueue_script(
			'wpforms-datepair',
			\wpforms_form_locker()->url . 'assets/js/vendor/datepair.min.js',
			array(),
			'0.4.16',
			true
		);

		\wp_enqueue_script(
			'wpforms-jquery-datepair',
			\wpforms_form_locker()->url . 'assets/js/vendor/jquery.datepair.min.js',
			array( 'jquery', 'wpforms-datepair' ),
			'0.4.16',
			true
		);

		\wp_enqueue_style(
			'wpforms-form-locker-admin-builder',
			\wpforms_form_locker()->url . "assets/css/admin-builder{$min}.css",
			array(),
			'1.11.5'
		);

		\wp_enqueue_style(
			'wpforms-jquery-timepicker',
			\WPFORMS_PLUGIN_URL . 'assets/css/jquery.timepicker.css',
			array(),
			'1.11.5'
		);

		\wp_enqueue_style(
			'wpforms-flatpickr',
			\WPFORMS_PLUGIN_URL . 'assets/css/flatpickr.min.css',
			array(),
			'4.6.3'
		);
	}

	/**
	 * Add setting to core fields to allow limiting to unique answers only.
	 *
	 * This setting gets added to name, email, single text, URL, password, and phone fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $field Field settings.
	 * @param object $instance Field base class instance.
	 */
	public function field_unique_answer_toggle( $field, $instance ) {

		// Limit to our specific field types.
		if ( ! \in_array( $field['type'], UniqueAnswer::get_unique_answer_field_types(), true ) ) {
			return;
		}

		// Create checkbox setting.
		$instance->field_element(
			'row',
			$field,
			array(
				'slug'    => 'unique_answer',
				'content' => $instance->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'unique_answer',
						'value'   => isset( $field['unique_answer'] ) ? '1' : '0',
						'desc'    => \esc_html__( 'Require unique answer', 'wpforms-form-locker' ),
						'tooltip' => \esc_html__( 'Check this option to require only unique answers for the current field.', 'wpforms-form-locker' ),
					),
					false
				),
			)
		);
	}

	/**
	 * Form Locker form builder register settings area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections  Settings area sections.
	 *
	 * @return array
	 */
	public function builder_settings_register( $sections ) {

		$sections['form_locker'] = \esc_html__( 'Form Locker', 'wpforms-form-locker' );

		return $sections;
	}

	/**
	 * Form Locker form builder settings content.
	 *
	 * @since 1.0.0
	 *
	 * @param object $instance Settings panel instance.
	 */
	public function builder_settings_content( $instance ) {

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-form_locker">';

			echo '<div class="wpforms-panel-content-section-title">';
				\esc_html_e( 'Form Locker', 'wpforms-form-locker' );
			echo '</div>';

			\wpforms_panel_field(
				'checkbox',
				'settings',
				'form_locker_password_enable',
				$instance->form_data,
				\esc_html__( 'Enable password protection', 'wpforms-form-locker' ),
				array(
					'before' => '<h2>' . \esc_html__( 'Password', 'wpforms-form-locker' ) . '</h2>',
				)
			);

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_password',
				$instance->form_data,
				\esc_html__( 'Password', 'wpforms-form-locker' )
			);

			\wpforms_panel_field(
				'tinymce',
				'settings',
				'form_locker_password_message',
				$instance->form_data,
				\esc_html__( 'Display Message', 'wpforms-form-locker' ),
				array(
					'tinymce' => array(
						'editor_height' => 175,
					),
					'tooltip' => \esc_html__( 'This message is displayed to visitors above the password form.', 'wpforms-form-locker' ),
				)
			);

			\wpforms_panel_field(
				'checkbox',
				'settings',
				'form_locker_schedule_enable',
				$instance->form_data,
				\esc_html__( 'Enable scheduling', 'wpforms-form-locker' ),
				array(
					'before'  => '<h2>' . \esc_html__( 'Scheduling', 'wpforms-form-locker' ) . '</h2>',
					'tooltip' => \esc_html__( 'Accept form entries during the date/time window below.', 'wpforms-form-locker' ),
				)
			);

			echo '<div id="wpforms-form-locker-schedule-datetime-block"><div class="wpforms-clear">';

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_schedule_start_date',
				$instance->form_data,
				\esc_html__( 'Start Date', 'wpforms-form-locker' ),
				array(
					'class'       => 'wpforms-panel-field-datetime wpforms-one-third',
					'input_class' => 'readonly-active wpforms-datepair-date wpforms-datepair-start',
					'after'       => '<button type="button" class="wpforms-clear-datetime-field" title="' . \esc_html__( 'Clear Start Date', 'wpforms-form-locker' ) . '"><i class="fa fa-times-circle fa-lg"></i></button>',
				)
			);

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_schedule_start_time',
				$instance->form_data,
				\esc_html__( 'Start Time', 'wpforms-form-locker' ),
				array(
					'class'       => 'wpforms-panel-field-datetime wpforms-one-third',
					'input_class' => 'wpforms-datepair-time wpforms-datepair-start',
					'after'       => '<button type="button" class="wpforms-clear-datetime-field" title="' . \esc_html__( 'Clear Start Time', 'wpforms-form-locker' ) . '"><i class="fa fa-times-circle fa-lg"></i></button>',
				)
			);

			echo '</div><div class="wpforms-clear">';

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_schedule_end_date',
				$instance->form_data,
				\esc_html__( 'End Date', 'wpforms-form-locker' ),
				array(
					'class'       => 'wpforms-panel-field-datetime wpforms-one-third',
					'input_class' => 'readonly-active wpforms-datepair-date wpforms-datepair-end',
					'after'       => '<button type="button" class="wpforms-clear-datetime-field" title="' . \esc_html__( 'Clear End Date', 'wpforms-form-locker' ) . '"><i class="fa fa-times-circle fa-lg"></i></button>',
				)
			);

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_schedule_end_time',
				$instance->form_data,
				\esc_html__( 'End Time', 'wpforms-form-locker' ),
				array(
					'class'       => 'wpforms-panel-field-datetime wpforms-one-third',
					'input_class' => 'wpforms-datepair-time wpforms-datepair-end',
					'after'       => '<button type="button" class="wpforms-clear-datetime-field" title="' . \esc_html__( 'Clear End Time', 'wpforms-form-locker' ) . '"><i class="fa fa-times-circle fa-lg"></i></button>',
				)
			);

			echo '</div></div><!-- End of #wpforms-form-locker-schedule-datetime-block -->';

			\wpforms_panel_field(
				'tinymce',
				'settings',
				'form_locker_schedule_message',
				$instance->form_data,
				\esc_html__( 'Closed Message', 'wpforms-form-locker' ),
				array(
					'tinymce' => array(
						'editor_height' => 175,
					),
					'tooltip' => \esc_html__( 'This message is displayed to visitors when the form is closed.', 'wpforms-form-locker' ),
				)
			);

			\wpforms_panel_field(
				'checkbox',
				'settings',
				'form_locker_entry_limit_enable',
				$instance->form_data,
				\esc_html__( 'Enable limit on total form entries', 'wpforms-form-locker' ),
				array(
					'before' => '<h2>' . \esc_html__( 'Entry Limit', 'wpforms-form-locker' ) . '</h2>',
				)
			);

			\wpforms_panel_field(
				'text',
				'settings',
				'form_locker_entry_limit',
				$instance->form_data,
				\esc_html__( 'Limit', 'wpforms-form-locker' ),
				array(
					'type' => 'number',
				)
			);

			\wpforms_panel_field(
				'tinymce',
				'settings',
				'form_locker_entry_limit_message',
				$instance->form_data,
				\esc_html__( 'Closed Message', 'wpforms-form-locker' ),
				array(
					'tinymce' => array(
						'editor_height' => 175,
					),
					'tooltip' => \esc_html__( 'This message is displayed to visitors when the form is closed.', 'wpforms-form-locker' ),
				)
			);

			\wpforms_panel_field(
				'checkbox',
				'settings',
				'form_locker_user_enable',
				$instance->form_data,
				\esc_html__( 'Enable restricting entries to logged in users only', 'wpforms-form-locker' ),
				array(
					'before' => '<h2>' . \esc_html__( 'User', 'wpforms-form-locker' ) . '</h2>',
				)
			);

			\wpforms_panel_field(
				'tinymce',
				'settings',
				'form_locker_user_message',
				$instance->form_data,
				\esc_html__( 'Message', 'wpforms-form-locker' ),
				array(
					'tinymce' => array(
						'editor_height' => 175,
					),
					'tooltip' => \esc_html__( 'This message is displayed to logged out visitors in place of the form.', 'wpforms-form-locker' ),
				)
			);

		echo '</div>';
	}
}
