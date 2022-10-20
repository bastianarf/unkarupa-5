<?php

/**
 * Check Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Check_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Check Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'check-request';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Requested By', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4  => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date Requested', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5  => array(
					'id'            => '5',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date Needed', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Purpose of Funds', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				11 => array(
					'id'     => '11',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Amount Requested', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'name',
					'label'    => esc_html__( 'Approved By', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'name',
					'label'    => esc_html__( 'Make Payable To', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'     => '10',
					'type'   => 'address',
					'label'  => esc_html__( 'Payable to Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
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

new WPForms_Template_Check_Request;
