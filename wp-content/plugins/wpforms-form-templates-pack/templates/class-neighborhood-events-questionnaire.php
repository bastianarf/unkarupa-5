<?php

/**
 * Neighborhood Events Questionnaire Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Neighborhood_Events_Questionnaire extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Neighborhood Events Questionnaire Form', 'wpforms-form-templates-pack' );
		$this->slug = 'neighborhood-events-questionnaire';
		$this->data = array(
			'field_id' => 7,
			'fields'   => array(
				1 => array(
					'id'      => '1',
					'type'    => 'radio',
					'label'   => esc_html__( 'How often do you attend events in this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely often', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very often', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately often', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly often', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Never', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2 => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'If you do not attend events in this neighborhood, why not?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3 => array(
					'id'    => '3',
					'type'  => 'textarea',
					'label' => esc_html__( 'What types of events would you attend if they were held in this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'How often do you participate in activities in this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely often', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very often', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately often', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly often', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Never', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'If you do not participate in activities in this neighborhood, why not?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6 => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'What types of activities would you participate in if they were available in this neighborhood?', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Neighborhood_Events_Questionnaire;
