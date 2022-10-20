<?php

/**
 * Event Planner form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Event_Planner extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Event Planner Form', 'wpforms-form-templates-pack' );
		$this->slug = 'event-planner';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				10 => array(
					'id'       => '10',
					'type'     => 'name',
					'label'    => esc_html__( 'Your Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				11 => array(
					'id'       => '11',
					'type'     => 'email',
					'label'    => esc_html__( 'Your Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				1  => array(
					'id'       => '1',
					'type'     => 'text',
					'label'    => esc_html__( 'Event Name', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'      => '2',
					'type'    => 'select',
					'label'   => esc_html__( 'Event Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Banquet', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Fund Raiser', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Dinner Party', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Wedding', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				3  => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'Status', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Planning', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'In Progress', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Finished', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4  => array(
					'id'       => '4',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Event Description', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				5  => array(
					'id'            => '5',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Event Begin Date and Time ', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				6  => array(
					'id'            => '6',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Event End Date and Time', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				7  => array(
					'id'     => '7',
					'type'   => 'address',
					'label'  => esc_html__( 'Address', 'wpforms-form-templates-pack' ),
					'scheme' => 'us',
					'size'   => 'medium',
				),
				8  => array(
					'id'      => '8',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Requirements', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Staffing', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Catering', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Bar Tending', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Security', 'wpforms-form-templates-pack' ),
						),
					),
				),
				9  => array(
					'id'     => '9',
					'type'   => 'payment-single',
					'label'  => esc_html__( 'Cost Per Person', 'wpforms-form-templates-pack' ),
					'format' => 'user',
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

new WPForms_Template_Event_Planner;
