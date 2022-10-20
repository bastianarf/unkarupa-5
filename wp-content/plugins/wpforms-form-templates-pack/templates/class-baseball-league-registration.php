<?php

/**
 * Baseball League Registration Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Baseball_League_Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Baseball League Registration Form', 'wpforms-form-templates-pack' );
		$this->slug = 'baseball-league-registration';
		$this->data = array(
			'field_id' => 8,
			'fields'   => array(
				1 => array(
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Player Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				2 => array(
					'id'       => '2',
					'type'     => 'select',
					'label'    => esc_html__( 'Player Age Group', 'wpforms-form-templates-pack' ),
					'choices'  => array(
						1 => array(
							'label' => esc_html__( '6-8 years old', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( '9-12 years old', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( '13-15 years old', 'wpforms-form-templates-pack' ),
						),
					),
					'required' => '1',
					'size'     => 'medium',
				),
				3 => array(
					'id'      => '3',
					'type'    => 'radio',
					'label'   => esc_html__( 'Desired Team', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Tigers', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Orioles', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Yankees', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Rangers', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Astros', 'wpforms-form-templates-pack' ),
						),
					),
				),
				4 => array(
					'id'      => '4',
					'type'    => 'radio',
					'label'   => esc_html__( 'Desired Position', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Pitcher', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Catcher', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Outfield', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Infield', 'wpforms-form-templates-pack' ),
						),
					),
				),
				5 => array(
					'id'       => '5',
					'type'     => 'name',
					'label'    => esc_html__( 'Guardian Name', 'wpforms-form-templates-pack' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				6 => array(
					'id'       => '6',
					'type'     => 'email',
					'label'    => esc_html__( 'Guardian Email', 'wpforms-form-templates-pack' ),
					'required' => '1',
					'size'     => 'medium',
				),
				7 => array(
					'id'       => '7',
					'type'     => 'address',
					'label'    => esc_html__( 'Guardian Address', 'wpforms-form-templates-pack' ),
					'scheme'   => 'us',
					'required' => '1',
					'size'     => 'medium',
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

new WPForms_Template_Baseball_League_Registration;
