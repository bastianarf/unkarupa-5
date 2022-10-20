<?php

/**
 * To Do List Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_To_Do_List extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'To Do List Form', 'wpforms-form-templates-pack' );
		$this->slug = 'to-do-list';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Item', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2 => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'Priority', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Low', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Normal', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'High', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6 => array(
					'id'      => '6',
					'type'    => 'select',
					'label'   => esc_html__( 'Category', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Personal', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Home', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Business', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				5 => array(
					'id'            => '5',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Due Date', 'wpforms-form-templates-pack' ),
					'format'        => 'date',
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

new WPForms_Template_To_Do_List;
