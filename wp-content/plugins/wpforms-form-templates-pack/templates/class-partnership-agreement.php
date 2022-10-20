<?php

/**
 * Partnership Agreement Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Partnership_Agreement extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Partnership Agreement Form', 'wpforms-form-templates-pack' );
		$this->slug = 'partnership-agreement';
		$this->data = array(
			'field_id' => 16,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Partner 1 Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'       => '2',
					'type'     => 'text',
					'label'    => esc_html__( 'Partner 1 Company', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3  => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Partner 1 Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'phone',
					'label'    => esc_html__( 'Partner 1 Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'       => '5',
					'type'     => 'address',
					'label'    => esc_html__( 'Partner 1 Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				6  => array(
					'id'       => '6',
					'type'     => 'name',
					'label'    => esc_html__( 'Partner 2 Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				7  => array(
					'id'       => '7',
					'type'     => 'text',
					'label'    => esc_html__( 'Partner 2 Company', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'email',
					'label'    => esc_html__( 'Partner 2 Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				15 => array(
					'id'     => '15',
					'type'   => 'phone',
					'label'  => esc_html__( 'Partner 2 Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				9  => array(
					'id'       => '9',
					'type'     => 'address',
					'label'    => esc_html__( 'Partner 2 Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Partnership Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				11 => array(
					'id'    => '11',
					'type'  => 'textarea',
					'label' => esc_html__( 'Terms of Agreement', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				12 => array(
					'id'            => '12',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Agreement valid until', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				13 => array(
					'id'    => '13',
					'type'  => 'text',
					'label' => esc_html__( 'Partnership Contribution', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				14 => array(
					'id'            => '14',
					'type'          => 'date-time',
					'label'         => esc_html__( 'The present partnership agreement form is signed today under the rule of the present laws and regulations', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
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

new WPForms_Template_Partnership_Agreement;
