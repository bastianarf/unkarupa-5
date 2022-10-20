<?php

/**
 * Prayer Request Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Prayer_Request extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Prayer Request Form', 'wpforms-form-templates-pack' );
		$this->slug = 'prayer-request';
		$this->data = array(
			'field_id' => 6,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'     => '3',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				4 => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'text',
					'label' => esc_html__( 'Prayer request / intention', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Prayer_Request;
