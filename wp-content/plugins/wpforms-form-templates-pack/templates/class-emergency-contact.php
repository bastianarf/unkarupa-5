<?php

/**
 * Emergency Contact Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Emergency_Contact extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Emergency Contact Form', 'wpforms-form-templates-pack' );
		$this->slug = 'emergency-contact';
		$this->data = array(
			'field_id' => 15,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Your Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'     => '2',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				3  => array(
					'id'            => '3',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date of Birth', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'dropdown',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'     => '5',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Primary Care Provider', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'            => '7',
					'type'          => 'divider',
					'label'         => esc_html__( 'Emergency Contact', 'wpforms-form-templates-pack' ),
					'label_disable' => '1',
				),
				8  => array(
					'id'       => '8',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				9  => array(
					'id'     => '9',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				10 => array(
					'id'     => '10',
					'type'   => 'phone',
					'label'  => esc_html__( 'Home Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				11 => array(
					'id'     => '11',
					'type'   => 'phone',
					'label'  => esc_html__( 'Work Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				12 => array(
					'id'     => '12',
					'type'   => 'phone',
					'label'  => esc_html__( 'Cell Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				14 => array(
					'id'    => '14',
					'type'  => 'text',
					'label' => esc_html__( 'Relationship to the patient', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Emergency_Contact;
