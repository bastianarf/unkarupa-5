<?php

/**
 * Cancellation Survey Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Cancellation_Survey extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Cancellation Survey Form', 'wpforms-form-templates-pack' );
		$this->slug = 'cancellation-survey';
		$this->data = array(
			'field_id' => 9,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'radio',
					'label'   => esc_html__( 'How long had you been using the subscription / service?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Less than a month', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '1-6 months', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '1-3 years', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Over 3 years', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Never used', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2 => array(
					'id'      => '2',
					'type'    => 'radio',
					'label'   => esc_html__( 'How often did you use the subscription / service?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Once a week', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '2 to 3 times a month', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Once a month', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Less than once a month', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3 => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'Overall, how satisfied were you with the subscription / service?', 'wpforms-form-templates-pack' ),
					'choices' => array(
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
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'What was the primary reason for canceling your subscription / service?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'No longer need it', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'It didn\'t meet my needs', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Found an alternative', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Quality was less than expected', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Ease of use was less than expected', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Access to the service was less than expected', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Customer service was less than expected', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'If other, please specify', 'wpforms-form-templates-pack' ),
					'size'  => 'small',
				),
				6 => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Would you use the product / service in the future?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Definitely', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Probably', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Not sure', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Probably not', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Definitely not', 'wpforms-form-templates-pack' ),
						),
					),
				),
				7 => array(
					'id'      => '7',
					'type'    => 'radio',
					'label'   => esc_html__( 'Would you recommend our product / service to colleagues or contacts within your industry?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Definitely', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Probably', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Not sure', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Probably not', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Definitely not', 'wpforms-form-templates-pack' ),
						),
					),
				),
				8 => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'What could we do to improve our subscription / service?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Cancellation_Survey;
