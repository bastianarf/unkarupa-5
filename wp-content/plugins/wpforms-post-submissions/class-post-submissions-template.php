<?php

/**
 * Post Submissions form template.
 *
 * @since 1.0.0
 */
class WPForms_Template_Post_Submission extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'Blog Post Submission Form', 'wpforms-post-submissions' );
		$this->slug        = 'post_submission';
		$this->description = esc_html__( 'User-submitted content made easy. Allow your users to submit guest blog posts in WordPress. You can add and remove fields as needed.', 'wpforms-post-submissions' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = array(
			'title'   => esc_html__( 'Don&#39;t Forget', 'wpforms-post-submissions' ),
			'message' => esc_html__( 'Additional post submission options are available in the settings panel.', 'wpforms-post-submissions' ),
		);
		$this->data        = array(
			'field_id' => '11',
			'fields'   => array(
				'1'  => array(
					'id'            => '1',
					'type'          => 'divider',
					'label'         => esc_html__( 'Author Details', 'wpforms-post-submissions' ),
					'description'   => esc_html__( 'Please enter your contact details, so we can give you proper credit for this blog post.', 'wpforms-post-submissions' ),
					'label_disable' => '1',
				),
				'2'  => array(
					'id'       => '2',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-post-submissions' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				),
				'3'  => array(
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-post-submissions' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'4'  => array(
					'id'          => '4',
					'type'        => 'textarea',
					'label'       => esc_html__( 'Short Author Bio', 'wpforms-post-submissions' ),
					'description' => esc_html__( 'Please keep it below 300 characters.', 'wpforms-post-submissions' ),
					'required'    => '1',
					'size'        => 'medium',
				),
				'5'  => array(
					'id'            => '5',
					'type'          => 'divider',
					'label'         => esc_html__( 'Create a Blog Post', 'wpforms-post-submissions' ),
					'description'   => esc_html__( 'Please submit your guest blog posts by using the fields below.', 'wpforms-post-submissions' ),
					'label_disable' => '1',
				),
				'6'  => array(
					'id'       => '6',
					'type'     => 'text',
					'label'    => esc_html__( 'Post Title', 'wpforms-post-submissions' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'7'  => array(
					'id'       => '7',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Post Content', 'wpforms-post-submissions' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'8'  => array(
					'id'            => '8',
					'type'          => 'file-upload',
					'label'         => esc_html__( 'Featured Image', 'wpforms-post-submissions' ),
					'description'   => esc_html__( 'Please make sure the dimensions are (600 x 300px).', 'wpforms-post-submissions' ),
					'required'      => '1',
					'size'          => 'medium',
					'extensions'    => 'jpg,jpeg,png,gif',
					'media_library' => '1',
				),
				'9'  => array(
					'id'       => '9',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Post Excerpt', 'wpforms-post-submissions' ),
					'required' => '1',
					'size'     => 'small',
				),
				'10' => array(
					'id'               => '10',
					'type'             => 'select',
					'label'            => esc_html__( 'Category', 'wpforms-post-submissions' ),
					'choices'          => array(
						'1' => array(
							'label' => esc_html__( 'First Choice', 'wpforms-post-submissions' ),
							'value' => '',
						),
					),
					'required'         => '1',
					'size'             => 'medium',
					'dynamic_choices'  => 'taxonomy',
					'dynamic_taxonomy' => 'category',
				),
			),
			'settings' => array(
				'antispam'                    => '1',
				'confirmation_message_scroll' => '1',
				'submit_text'                 => esc_html__( 'Submit', 'wpforms-post-submissions' ),
				'submit_text_processing'      => esc_html__( 'Sending...', 'wpforms-post-submissions' ),
				'post_submissions'            => '1',
				'post_submissions_title'      => '6',
				'post_submissions_content'    => '7',
				'post_submissions_excerpt'    => '9',
				'post_submissions_featured'   => '8',
				'post_submissions_type'       => 'post',
				'post_submissions_status'     => 'pending',
				'post_submissions_author'     => 'current_user',
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data
	 *
	 * @return bool
	 */
	public function template_modal_conditional( $form_data ) {

		return true;
	}
}

new WPForms_Template_Post_Submission;
