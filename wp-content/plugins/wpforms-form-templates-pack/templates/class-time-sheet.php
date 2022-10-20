<?php

/**
 * Time Sheet Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Time_Sheet extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Time Sheet Form', 'wpforms-form-templates-pack' );
		$this->slug = 'time-sheet';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Employee Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'select',
					'label'   => esc_html__( 'Project / Client', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Acme Corp', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'John Doe LLC', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				3 => array(
					'id'            => '3',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date / Time', 'wpforms-form-templates-pack' ),
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
					'label' => esc_html__( 'Hours', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'     => '4',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Hourly Rate', 'wpforms-form-templates-pack' ),
					'format' => 'user',
					'size'   => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Notes', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Time_Sheet;
