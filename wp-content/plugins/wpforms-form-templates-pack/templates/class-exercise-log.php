<?php

/**
 * Exercise Log Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Exercise_Log extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Exercise Log Form', 'wpforms-form-templates-pack' );
		$this->slug = 'exercise-log';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'select',
					'label'   => esc_html__( 'Dropdown', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Running', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Walking', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Swimming', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Spinning', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Weights', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				2 => array(
					'id'            => '2',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date / Time', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				4 => array(
					'id'    => '4',
					'type'  => 'text',
					'label' => esc_html__( 'Calories burned', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Distance traveled', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Maximum pulse', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'Resting pulse', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments on workout', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
			),
			'settings' => array(
				'antispam'                    => '1',
				'confirmation_message_scroll' => '1',
				'submit_text_processing'      => esc_html__( 'Sending...', 'wpforms-form-templates-pack' ),
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}
}

new WPForms_Template_Exercise_Log;
