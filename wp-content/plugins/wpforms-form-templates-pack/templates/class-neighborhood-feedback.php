<?php

/**
 * Neighborhood Feedback Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Neighborhood_Feedback extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Neighborhood Feedback Form', 'wpforms-form-templates-pack' );
		$this->slug = 'neighborhood-feedback';
		$this->data = array(
			'field_id' => 20,
			'fields'   => array(
				1  => array(
					'id'      => '1',
					'type'    => 'radio',
					'label'   => esc_html__( 'How many of your neighbors do you know?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'All of them', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Most of them', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Some of them', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'A few of them', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'None of them', 'wpforms-form-templates-pack' ),
						),
					),
				),
				2  => array(
					'id'      => '2',
					'type'    => 'radio',
					'label'   => esc_html__( 'How strong is the sense of community in this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely strong', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very strong', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately strong', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly strong', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all strong', 'wpforms-form-templates-pack' ),
						),
					),
				),
				3  => array(
					'id'      => '3',
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
							'label' => esc_html__( 'Not at all often', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4  => array(
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
							'label' => esc_html__( 'Not at all often', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5  => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'If you do not participate in activities in this neighborhood, why not?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'If you do not participate in activities in this neighborhood, why not?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'What types of events would you attend if they were held in this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'What types of activities would you participate in if they were available in this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'      => '9',
					'type'    => 'radio',
					'label'   => esc_html__( 'How often do you visit the parks in this neighborhood?', 'wpforms-form-templates-pack' ),
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
							'label' => esc_html__( 'Not at all often', 'wpforms-form-templates-pack' ),
						),
					),
				),
				10 => array(
					'id'      => '10',
					'type'    => 'radio',
					'label'   => esc_html__( 'Are you satisfied with the parks in this neighborhood, neither satisfied nor dissatisfied with them, or dissatisfied with them?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely satisfied', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Moderately satisfied', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Slightly satisfied', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Neither satisfied nor dissatisfied', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Slightly dissatisfied', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Extremely dissatisfied', 'wpforms-form-templates-pack' ),
						),
					),
				),
				11 => array(
					'id'      => '11',
					'type'    => 'radio',
					'label'   => esc_html__( 'How well are the streets in this neighborhood maintained?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely well', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very well', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately well', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly well', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all well', 'wpforms-form-templates-pack' ),
						),
					),
				),
				12 => array(
					'id'      => '12',
					'type'    => 'radio',
					'label'   => esc_html__( 'How safe do you feel in this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely safe', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very safe', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately safe', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly safe', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all safe', 'wpforms-form-templates-pack' ),
						),
					),
				),
				13 => array(
					'id'      => '13',
					'type'    => 'radio',
					'label'   => esc_html__( 'How clean is this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely clean', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very clean', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately clean', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly clean', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all clean', 'wpforms-form-templates-pack' ),
						),
					),
				),
				14 => array(
					'id'      => '14',
					'type'    => 'radio',
					'label'   => esc_html__( 'How proud are you to live in this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely proud', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very proud', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately proud', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly proud', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all proud', 'wpforms-form-templates-pack' ),
						),
					),
				),
				15 => array(
					'id'    => '15',
					'type'  => 'textarea',
					'label' => esc_html__( 'What do you like most about this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				16 => array(
					'id'    => '16',
					'type'  => 'textarea',
					'label' => esc_html__( 'What do you like least about this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				17 => array(
					'id'    => '17',
					'type'  => 'textarea',
					'label' => esc_html__( 'What changes would most improve this neighborhood?', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				18 => array(
					'id'      => '18',
					'type'    => 'radio',
					'label'   => esc_html__( 'How promising is the future of this neighborhood?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely promising', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Very promising', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Moderately promising', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Slightly promising', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Not at all promising', 'wpforms-form-templates-pack' ),
						),
					),
				),
				19 => array(
					'id'      => '19',
					'type'    => 'radio',
					'label'   => esc_html__( 'Overall, are you satisfied with your experience living in this neighborhood, neither satisfied nor dissatisfied with it, or dissatisfied with it?', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Extremely satisfied', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Moderately satisfied', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Slightly satisfied', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Neither satisfied or dissatisfied', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Slightly dissatisfied', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Moderately dissatisfied', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Extremely dissatisfied', 'wpforms-form-templates-pack' ),
						),
					),
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

new WPForms_Template_Neighborhood_Feedback;
