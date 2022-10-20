<?php

/**
 * Recipe Catalog Form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Recipe_Catalog extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Recipe Catalog Form', 'wpforms-form-templates-pack' );
		$this->slug = 'recipe-catalog';
		$this->data = array(
			'field_id' => 12,
			'fields'   => array(
				1  => array(
					'id'    => '1',
					'type'  => 'text',
					'label' => esc_html__( 'Recipe Name', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				2  => array(
					'id'    => '2',
					'type'  => 'textarea',
					'label' => esc_html__( 'Description', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				3  => array(
					'id'      => '3',
					'type'    => 'select',
					'label'   => esc_html__( 'Cuisine', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1  => array(
							'label' => esc_html__( 'African', 'wpforms-form-templates-pack' ),
						),
						2  => array(
							'label' => esc_html__( 'American', 'wpforms-form-templates-pack' ),
						),
						3  => array(
							'label' => esc_html__( 'Asian', 'wpforms-form-templates-pack' ),
						),
						10 => array(
							'label' => esc_html__( 'Caribbean', 'wpforms-form-templates-pack' ),
						),
						9  => array(
							'label' => esc_html__( 'Chinese', 'wpforms-form-templates-pack' ),
						),
						8  => array(
							'label' => esc_html__( 'French', 'wpforms-form-templates-pack' ),
						),
						7  => array(
							'label' => esc_html__( 'Greek', 'wpforms-form-templates-pack' ),
						),
						6  => array(
							'label' => esc_html__( 'Indian', 'wpforms-form-templates-pack' ),
						),
						5  => array(
							'label' => esc_html__( 'Italian', 'wpforms-form-templates-pack' ),
						),
						4  => array(
							'label' => esc_html__( 'Japanese', 'wpforms-form-templates-pack' ),
						),
						14 => array(
							'label' => esc_html__( 'Mexican', 'wpforms-form-templates-pack' ),
						),
						13 => array(
							'label' => esc_html__( 'Middle Eastern', 'wpforms-form-templates-pack' ),
						),
						12 => array(
							'label' => esc_html__( 'Moroccan', 'wpforms-form-templates-pack' ),
						),
						11 => array(
							'label' => esc_html__( 'Spanish', 'wpforms-form-templates-pack' ),
						),
						15 => array(
							'label' => esc_html__( 'Thai', 'wpforms-form-templates-pack' ),
						),
						16 => array(
							'label' => esc_html__( 'Other', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				4  => array(
					'id'      => '4',
					'type'    => 'select',
					'label'   => esc_html__( 'Dish type', 'wpforms-form-templates-pack' ),
					'choices' => array(
						1 => array(
							'label' => esc_html__( 'Appetizer', 'wpforms-form-templates-pack' ),
						),
						2 => array(
							'label' => esc_html__( 'Beverage', 'wpforms-form-templates-pack' ),
						),
						3 => array(
							'label' => esc_html__( 'Bread', 'wpforms-form-templates-pack' ),
						),
						8 => array(
							'label' => esc_html__( 'Dessert', 'wpforms-form-templates-pack' ),
						),
						7 => array(
							'label' => esc_html__( 'Main', 'wpforms-form-templates-pack' ),
						),
						6 => array(
							'label' => esc_html__( 'Salad', 'wpforms-form-templates-pack' ),
						),
						5 => array(
							'label' => esc_html__( 'Soup', 'wpforms-form-templates-pack' ),
						),
						4 => array(
							'label' => esc_html__( 'Side dish', 'wpforms-form-templates-pack' ),
						),
					),
					'size'    => 'medium',
				),
				6  => array(
					'id'    => '6',
					'type'  => 'text',
					'label' => esc_html__( 'Primary ingredient', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				5  => array(
					'id'    => '5',
					'type'  => 'textarea',
					'label' => esc_html__( 'Ingredients', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				7  => array(
					'id'    => '7',
					'type'  => 'textarea',
					'label' => esc_html__( 'Instructions', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				8  => array(
					'id'    => '8',
					'type'  => 'text',
					'label' => esc_html__( 'Prep time', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				9  => array(
					'id'    => '9',
					'type'  => 'text',
					'label' => esc_html__( 'Cook time', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				10 => array(
					'id'    => '10',
					'type'  => 'text',
					'label' => esc_html__( 'Number of servings', 'wpforms-form-templates-pack' ),
					'size'  => 'medium',
				),
				11 => array(
					'id'    => '11',
					'type'  => 'text',
					'label' => esc_html__( 'Source', 'wpforms-form-templates-pack' ),
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

new WPForms_Template_Recipe_Catalog;
