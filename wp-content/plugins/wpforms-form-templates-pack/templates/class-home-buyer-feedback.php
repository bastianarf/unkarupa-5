<?php

/**
 * Home Buyer Feedback Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Home_Buyer_Feedback extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Home Buyer Feedback Form', 'wpforms-form-templates-pack' );
		$this->slug = 'home-buyer-feedback';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'radio',
					'label'   => esc_html__( 'Are you a first-time home buyer, or have you purchased a home before?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'First time home buyer', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Purchased home before', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2 => array(
					'id'      => '2',
					'type'    => 'radio',
					'label'   => esc_html__( 'How did you begin your most recent search for a home?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Searched the internet', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Drove through neighborhoods looking for home sales', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Contacted real estate agent', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Attended open house event', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Searched magazines', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Searched newspapers', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3 => array(
					'id'      => '3',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Which sources of information did you use during your most recent search for a home?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Internet', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Newspapers', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Magazines', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Open houses', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Real estate agents', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Yard signs', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'      => '5',
					'type'    => 'radio',
					'label'   => esc_html__( 'Which of the following internet websites did you find most helpful during your most recent search for a home?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Realtor.com', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Real estate companies\' websites', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Social networking websites', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Zillow', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Redfin', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
				),
				6 => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Where did you find the home you most recently purchased?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Newspaper', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Open house', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Internet', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Magazine', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Real estate agent', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Yard sign', 'wpforms-form-templates-pack' ),
						),
					),
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

new WPForms_Template_Home_Buyer_Feedback;
