<?php

/**
 * Basic Mortgage Application Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Basic_Mortgage_Application extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Basic Mortgage Application Form', 'wpforms-form-templates-pack' );
		$this->slug = 'basic-mortgage-application';
		$this->data = array(
			'field_id' => 19,
			'fields'   => array(
				1  => array(
					'id'      => '1',
					'type'    => 'select',
					'label'   => esc_html__( 'Purpose of Mortgage or Loan', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Home Loan', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Refinance', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Debt Consolidation Loan', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Second Mortgage', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Home Equity Loan', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Home Improvement Loan', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Bad Credit Home Mortgage', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				2  => array(
					'id'     => '2',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Loan Amount', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				3  => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'Type of Property', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'House', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Condo', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Town House', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Duplex', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Land', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4  => array(
					'id'     => '4',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Estimated Price or Value of Property', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				5  => array(
					'id'     => '5',
					'type'   => 'address',
					'label'  => esc_html__( 'Property Location', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				6  => array(
					'id'      => '6',
					'type'    => 'select',
					'label'   => esc_html__( 'Down Payment Percentage', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( '0-5%', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '6-10%', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '10% or more', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				7  => array(
					'id'      => '7',
					'type'    => 'select',
					'label'   => esc_html__( 'Current Mortgage Company', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Company A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Company B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Company C', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				8  => array(
					'id'      => '8',
					'type'    => 'select',
					'label'   => esc_html__( 'Would you like a Referral to a Real Estate Agent?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'First Choice', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Second Choice', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Third Choice', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				9  => array(
					'id'    => '9',
					'type'  => 'number',
					'label' => esc_html__( 'Age', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Occupation', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				12 => array(
					'id'     => '12',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Household Income', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				13 => array(
					'id'      => '13',
					'type'    => 'select',
					'label'   => esc_html__( 'Credit Rating', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'First Choice', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Second Choice', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Third Choice', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				14 => array(
					'id'            => '14',
					'type'          => 'divider',
					'label'         => esc_html__( 'Contact Information', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				15 => array(
					'id'       => '15',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				16 => array(
					'id'     => '16',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				17 => array(
					'id'     => '17',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				18 => array(
					'id'       => '18',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
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

new WPForms_Template_Basic_Mortgage_Application;
