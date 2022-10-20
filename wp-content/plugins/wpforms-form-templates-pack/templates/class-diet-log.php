<?php

/**
 * Diet Log Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Diet_Log extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Diet Log Form', 'wpforms-form-templates-pack' );
		$this->slug = 'diet-log';
		$this->data = array(
			'field_id' => 10,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'select',
					'label'   => esc_html__( 'Diet Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Atkins', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'South Beach', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Paleo', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Keto', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				2 => array(
					'id'      => '2',
					'type'    => 'select',
					'label'   => esc_html__( 'Meal Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Breakfast', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Lunch', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Dinner', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Snack', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'textarea',
					'label' => esc_html__( 'What did you eat?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'            => '4',
					'type'          => 'date-time',
					'label'         => esc_html__( 'Date / Time', 'wpforms-form-templates-pack' ),
					'format'        => 'date-time',
					'size'          => 'medium',
					'date_format'   => 'm/d/Y',
					'date_type'     => 'datepicker',
					'time_format'   => 'g:i A',
					'time_interval' => '30',
				),
				5 => array(
					'id'    => '5',
					'type'  => 'number',
					'label' => esc_html__( 'Grams of Carbohydrates', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'number',
					'label' => esc_html__( 'Grams of Fat', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7 => array(
					'id'    => '7',
					'type'  => 'number',
					'label' => esc_html__( 'Grams of Protein', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8 => array(
					'id'    => '8',
					'type'  => 'number',
					'label' => esc_html__( 'Milligrams of Sodium', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9 => array(
					'id'    => '9',
					'type'  => 'number',
					'label' => esc_html__( 'Total Calories', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Diet_Log;
