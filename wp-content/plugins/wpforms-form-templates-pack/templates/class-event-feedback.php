<?php

/**
 * Event Feedback Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Event_Feedback extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Event Feedback Form', 'wpforms-form-templates-pack' );
		$this->slug = 'event-feedback';
		$this->data = array(
			'field_id' => 22,
			'fields'   => array(
				18 => array(
					'id'       => '18',
					'type'     => 'radio',
					'label'    => esc_html__( 'Please rate your overall experience at our event', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Very satisfied', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Satisfied', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Neutral', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Unsatisfied', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Very Unsatisfied', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				19 => array(
					'id'       => '19',
					'type'     => 'radio',
					'label'    => esc_html__( 'Would you return again next year?', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( 'Yes', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'No', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Undecided', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
				),
				20 => array(
					'id'    => '20',
					'type'  => 'textarea',
					'label' => esc_html__( 'What could we have improved?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				21 => array(
					'id'    => '21',
					'type'  => 'textarea',
					'label' => esc_html__( 'Do you have any additional feedback?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Event_Feedback;
