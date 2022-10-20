<?php

/**
 * Fitness Interest Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Fitness_Interest extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Fitness Interest Form', 'wpforms-form-templates-pack' );
		$this->slug = 'fitness-interest';
		$this->data = array(
			'field_id' => 7,
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
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'      => '6',
					'type'    => 'radio',
					'label'   => esc_html__( 'Best time to call', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Mornings', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Afternoons', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Evenings', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Weekends', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4 => array(
					'id'      => '4',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Which of the following are important to you?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Fat reduction and weight loss', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Energy and endurance', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Building lean muscle mass', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Healthier digestive system', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Vitality, longevity, and positive outlook', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Comments', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Fitness_Interest;
