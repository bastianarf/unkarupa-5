<?php

/**
 * Call for Proposals form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Call_For_Proposals extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Call for Proposals Form', 'wpforms-form-templates-pack' );
		$this->slug = 'call-for-proposals';
		$this->data = array(
			'field_id' => 16,
			'fields'   => array(
				1  => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'text',
					'label' => esc_html__( 'Job Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'    => '3',
					'type'  => 'text',
					'label' => esc_html__( 'Company or Organization\'s Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				4  => array(
					'id'    => '4',
					'type'  => 'textarea',
					'label' => esc_html__( 'Biography', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5  => array(
					'id'     => '5',
					'type'   => 'phone',
					'label'  => esc_html__( 'Phone', 'wpforms-form-templates-pack' ),
					'format' => 'us',
					'size'   => 'medium',
				),
				14 => array(
					'id'       => '14',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				7  => array(
					'id'    => '7',
					'type'  => 'text',
					'label' => esc_html__( 'Proposal Title', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'textarea',
					'label' => esc_html__( 'Short Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'textarea',
					'label' => esc_html__( 'Abstract', 'wpforms-form-templates-pack' ),
					'size'  => 'large',
				),
				15 => array(
					'id'      => '15',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Topics', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Topic A', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Topic B', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Topic C', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Topic D', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Topic E', 'wpforms-form-templates-pack' ),
						),
					),
				),
				10 => array(
					'id'      => '10',
					'type'    => 'select',
					'label'   => esc_html__( 'Session Type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Presentation', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Panel', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Workshop', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				11 => array(
					'id'      => '11',
					'type'    => 'select',
					'label'   => esc_html__( 'Audience Level', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Novice', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Intermediate', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Expert', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				13 => array(
					'id'    => '13',
					'type'  => 'textarea',
					'label' => esc_html__( 'Additional Information', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Call_For_Proposals;
