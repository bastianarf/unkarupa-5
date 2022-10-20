<?php

/**
 * Post Submissions.
 *
 * @since 1.0.0
 */
class WPForms_Post_Submissions {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'init', array( $this, 'load_template' ), 15, 1 );
		add_action( 'wpforms_builder_enqueues', array( $this, 'admin_enqueues' ) );
		add_filter( 'wpforms_process_before_form_data', array( $this, 'override_file_uploads' ), 10, 2 );
		add_action( 'wpforms_process_complete', array( $this, 'process_post_submission' ), 10, 4 );
		add_filter( 'wpforms_builder_settings_sections', array( $this, 'settings_register' ), 20, 2 );
		add_action( 'wpforms_form_settings_panel_content', array( $this, 'settings_content' ), 20, 2 );
	}

	/**
	 * Load the post submission form template.
	 *
	 * @since 1.0.0
	 */
	public function load_template() {
		require_once plugin_dir_path( __FILE__ ) . 'class-post-submissions-template.php';
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueues() {

		wp_enqueue_script(
			'wpforms-builder-post-submissions',
			plugin_dir_url( __FILE__ ) . 'assets/js/admin-builder-post-submissions' . wpforms_get_min_suffix() . '.js',
			array( 'jquery' ),
			WPFORMS_POST_SUBMISSIONS_VERSION,
			false
		);
	}

	/**
	 * Force file upload fields connected to Post Submissions to use the
	 * WordPress media library.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data
	 * @param array $entry
	 *
	 * @return array
	 */
	public function override_file_uploads( $form_data, $entry ) {

		$settings = $form_data['settings'];
		$fields   = $form_data['fields'];

		// Check for featured image.
		if ( ! empty( $settings['post_submissions_featured'] ) ) {
			$fields[ $settings['post_submissions_featured'] ]['media_library'] = '1';
		}

		// Check for files defined in custom post meta.
		if ( ! empty( $settings['post_submissions_meta'] ) ) {

			foreach ( $settings['post_submissions_meta'] as $id ) {

				if ( ! empty( $fields[ $id ]['type'] ) && 'file-upload' === $fields[ $id ]['type'] ) {
					$fields[ $id ]['media_library'] = '1';
				}
			}
		}

		$form_data['fields'] = $fields;

		return $form_data;
	}

	/**
	 * Validate and process the post submission form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $entry     The post data submitted by the form.
	 * @param array $form_data The information for the form.
	 * @param int   $entry_id  Entry ID.
	 */
	public function process_post_submission( $fields, $entry, $form_data, $entry_id = 0 ) {

		$settings  = $form_data['settings'];
		$post_args = array();

		// Only process if enabled.
		if ( empty( $settings['post_submissions'] ) ) {
			return;
		}

		// Post Title.
		if ( ! empty( $settings['post_submissions_title'] ) || ! empty( $fields[ $settings['post_submissions_title'] ]['value'] ) ) {
			$post_args['post_title'] = $fields[ $settings['post_submissions_title'] ]['value'];
		}

		// Post Content.
		if ( ! empty( $settings['post_submissions_content'] ) || ! empty( $fields[ $settings['post_submissions_content'] ]['value'] ) ) {
			$post_args['post_content'] = $fields[ $settings['post_submissions_content'] ]['value'];
		}

		// Post Excerpt.
		if ( ! empty( $settings['post_submissions_excerpt'] ) || ! empty( $fields[ $settings['post_submissions_excerpt'] ]['value'] ) ) {
			$post_args['post_excerpt'] = $fields[ $settings['post_submissions_excerpt'] ]['value'];
		}

		// Post Type.
		if ( ! empty( $settings['post_submissions_type'] ) ) {
			$post_args['post_type'] = $settings['post_submissions_type'];
		}

		// Post Status.
		if ( ! empty( $settings['post_submissions_status'] ) ) {
			$post_args['post_status'] = $settings['post_submissions_status'];
		}

		// Post Author.
		if ( ! empty( $settings['post_submissions_author'] ) ) {
			if ( 'current_user' === $settings['post_submissions_author'] ) {
				$post_args['post_author'] = get_current_user_id();
				if ( 0 === $post_args['post_author'] ) {
					$form = get_post( $form_data['id'], ARRAY_A ); // phpcs:ignore
					$post_args['post_author'] = $form['post_author'];
				}
			} else {
				$post_args['post_author'] = absint( $settings['post_submissions_author'] );
			}
		} else {
			$post_args['post_author'] = 0;
		}

		// Don't require post title/content to create new post.
		add_filter( 'wp_insert_post_empty_content', '__return_false' );

		// Do it.
		$post_id = wp_insert_post( apply_filters( 'wpforms_post_submissions_post_args', $post_args, $form_data, $fields ) );

		// Check for errors.
		if ( is_wp_error( $post_id ) ) {

			wpforms_log(
				'Post Submission Error',
				$post_id->get_error_message(),
				array(
					'type'    => array( 'error' ),
					'parent'  => $entry_id,
					'form_id' => $form_data['id'],
				)
			);

			return;
		}

		// Featured Image.
		if ( ! empty( $settings['post_submissions_featured'] ) && ! empty( $fields[ $settings['post_submissions_featured'] ] ) ) {
			$file = $fields[ $settings['post_submissions_featured'] ];

			$style = ! empty( $file['style'] ) ? $file['style'] : \WPForms_Field_File_Upload::STYLE_CLASSIC;

			// Modern or classic file uploader?
			switch ( $style ) {
				case \WPForms_Field_File_Upload::STYLE_CLASSIC:
					if ( ! empty( $file['attachment_id'] ) ) {

						update_post_meta( $post_id, '_thumbnail_id', $file['attachment_id'] );

						$filetype = wp_check_filetype( $file['file'], null );

						// Attach featured image to the post.
						wp_insert_attachment(
							array(
								'ID'             => $file['attachment_id'],
								'post_parent'    => $post_id,
								'guid'           => $file['value'],
								'post_mime_type' => $filetype['type'],
							)
						);
					}
					break;

				case \WPForms_Field_File_Upload::STYLE_MODERN:
					if (
						! empty( $file['value_raw'][0]['attachment_id'] ) &&
						! empty( $file['value_raw'][0]['type'] ) &&
						strpos( $file['value_raw'][0]['type'], 'image' ) !== false
					) {
						update_post_meta( $post_id, '_thumbnail_id', $file['value_raw'][0]['attachment_id'] );

						// Attach featured image to the post.
						wp_insert_attachment(
							array(
								'ID'             => $file['value_raw'][0]['attachment_id'],
								'post_parent'    => $post_id,
								'guid'           => $file['value_raw'][0]['value'],
								'post_mime_type' => $file['value_raw'][0]['type'],
							)
						);
					}
					break;
			}

			unset( $file );
		}

		// Post Meta.
		if ( ! empty( $settings['post_submissions_meta'] ) ) {

			foreach ( $settings['post_submissions_meta'] as $key => $id ) {

				if ( empty( $key ) || ( empty( $id ) && '0' !== $id ) ) {
					continue;
				}

				if ( 'file-upload' === $fields[ $id ]['type'] ) {

					$style = ! empty( $fields[ $id ]['style'] ) ? $fields[ $id ]['style'] : \WPForms_Field_File_Upload::STYLE_CLASSIC;

					switch ( $style ) {
						case \WPForms_Field_File_Upload::STYLE_CLASSIC:
							if ( ! empty( $fields[ $id ]['attachment_id'] ) ) {

								update_post_meta( $post_id, esc_html( $key ), $fields[ $id ]['attachment_id'] );

								$filetype = wp_check_filetype( $fields[ $id ]['file'], null );

								// Attach file to the post.
								wp_insert_attachment(
									array(
										'ID'             => $fields[ $id ]['attachment_id'],
										'post_parent'    => $post_id,
										'guid'           => $fields[ $id ]['value'],
										'post_mime_type' => $filetype['type'],
									)
								);
							}
							break;

						case \WPForms_Field_File_Upload::STYLE_MODERN:
							$file_attachments = array();

							if ( ! is_array( $fields[ $id ]['value_raw'] ) ) {
								continue 2; // Get out of the switch and continue 'post_submissions_meta' foreach.
							}

							foreach ( $fields[ $id ]['value_raw'] as $file ) {
								if ( empty( $file['attachment_id'] ) ) {
									continue;
								}

								$file_attachments[] = $file['attachment_id'];

								// Attach file to the post.
								wp_insert_attachment(
									array(
										'ID'             => $file['attachment_id'],
										'post_parent'    => $post_id,
										'guid'           => $file['value'],
										'post_mime_type' => $file['type'],
									)
								);
							}

							if ( ! empty( $file_attachments ) ) {
								// For compatibility with ACF File field need save as media ID.
								$file_attachments = 1 === count( $file_attachments )
									? array_shift( $file_attachments )
									: $file_attachments;
								update_post_meta( $post_id, esc_html( $key ), $file_attachments );
							}
							break;
					}
				} elseif ( ! empty( $fields[ $id ]['value'] ) ) {

					$value = apply_filters( 'wpforms_post_submissions_process_meta', $fields[ $id ]['value'], $key, $id, $fields, $form_data );

					// The Events Calendar.
					if ( in_array( $key, array( '_EventStartDate', '_EventEndDate' ), true ) ) {
						$value = date( 'Y-m-d H:i:s', $fields[ $id ]['unix'] );
					}

					update_post_meta( $post_id, esc_html( $key ), $value );
				}
			}
		}

		// Post Taxonomies.
		foreach ( $fields as $id => $field ) {

			if ( ! empty( $field['dynamic_taxonomy'] ) && ! empty( $field['dynamic_items'] ) ) {

				$terms = array_map( 'absint', explode( ',', $field['dynamic_items'] ) );

				foreach ( $terms as $key => $term ) {

					$exists = term_exists( $term, $field['dynamic_taxonomy'] );

					if ( ! ( $exists !== 0 && $exists !== null ) ) {
						unset( $terms[ $key ] );
					}
				}

				wp_set_object_terms( $post_id, $terms, $field['dynamic_taxonomy'] );
			}
		}

		do_action( 'wpforms_post_submissions_process', $post_id, $fields, $form_data, $entry_id );

		// Associate post id with entry.
		if ( ! empty( $entry_id ) ) {
			wpforms()->entry->update( $entry_id, array( 'post_id' => $post_id ), '', '', array( 'cap' => false ) );
		}
	}

	/**
	 * Post Submissions settings register section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections
	 * @param array $form_data
	 *
	 * @return array
	 */
	public function settings_register( $sections, $form_data ) {

		$sections['post_submissions'] = esc_html__( 'Post Submissions', 'wpforms-post-submissions' );

		return $sections;
	}

	/**
	 * Post Submissions settings content.
	 *
	 * @since 1.0.0
	 *
	 * @param object $instance
	 */
	public function settings_content( $instance ) {

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-post_submissions">';

		echo '<div class="wpforms-panel-content-section-title">';
		esc_html_e( 'Post Submissions', 'wpforms-post-submissions' );
		echo '<a href="https://wpforms.com/docs/how-to-install-and-use-the-post-submissions-addon-in-wpforms" target="_blank" rel="noopener noreferrer"><i class="fa fa-question-circle wpforms-help-tooltip" title="' . esc_attr__( 'Click here for documentation on Post Submissions addon', 'wpforms-post-submissions' ) . '"></i></a>';
		echo '</div>';

		// Toggle.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions',
			$instance->form_data,
			esc_html__( 'Post Submissions', 'wpforms-post-submissions' ),
			array(
				'options' => array(
					''  => esc_html__( 'Off', 'wpforms' ),
					'1' => esc_html__( 'On', 'wpforms' ),
				),
			)
		);

		// Post Title.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_title',
			$instance->form_data,
			esc_html__( 'Post Title', 'wpforms-post-submissions' ),
			array(
				'field_map'   => array( 'text', 'name' ),
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-post-submissions' ),
			)
		);

		// Post Content.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_content',
			$instance->form_data,
			esc_html__( 'Post Content', 'wpforms-post-submissions' ),
			array(
				'field_map'   => array( 'textarea' ),
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-post-submissions' ),
			)
		);

		// Post Excerpt.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_excerpt',
			$instance->form_data,
			esc_html__( 'Post Excerpt', 'wpforms-post-submissions' ),
			array(
				'field_map'   => array( 'textarea', 'text' ),
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-post-submissions' ),
			)
		);

		// Post Featured Image.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_featured',
			$instance->form_data,
			esc_html__( 'Post Featured Image', 'wpforms-post-submissions' ),
			array(
				'field_map'   => array( 'file-upload' ),
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-post-submissions' ),
			)
		);

		// Post Type.
		$types   = get_post_types( apply_filters( 'wpforms_post_submissions_post_type_args', array( 'public' => true ), $instance->form_data ), 'objects' );
		$options = array();
		unset( $types['attachment'] );

		foreach ( $types as $key => $type ) {
			$options[ $key ] = $type->labels->name;
		}

		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_type',
			$instance->form_data,
			esc_html__( 'Post Type', 'wpforms-post-submissions' ),
			array(
				'options' => $options,
				'default' => 'post',
			)
		);

		// Post Status.
		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_status',
			$instance->form_data,
			esc_html__( 'Post Status', 'wpforms-post-submissions' ),
			array(
				'tooltip' => esc_html__( 'Select the default status used for new posts.', 'wpforms-post-submissions' ),
				'options' => get_post_statuses(),
				'default' => 'pending',
			)
		);

		// Post Author.
		$user_args = array(
			'number'  => 999,
			'fields'  => array( 'ID', 'display_name' ),
			'orderby' => 'display_name',
		);
		$users     = new WP_User_Query( apply_filters( 'wpforms_post_submissions_user_args', $user_args ) );
		$options   = array(
			'current_user' => esc_html__( 'Current User', 'wpforms-post-submissions' ),
		);

		if ( ! empty( $users->results ) ) {
			foreach ( $users->results as $user ) {
				$options[ $user->ID ] = $user->display_name;
			}
		}

		wpforms_panel_field(
			'select',
			'settings',
			'post_submissions_author',
			$instance->form_data,
			esc_html__( 'Post Author', 'wpforms-post-submissions' ),
			array(
				'tooltip'     => esc_html__( 'Select the post author used for new posts. Selecting Current User will use the current WordPress user that submits the form.', 'wpforms-post-submissions' ),
				'options'     => $options,
				'placeholder' => esc_html__( '--- Select User ---', 'wpforms-post-submissions' ),
			)
		);
		?>

		<div class="wpforms-field-map-table">
			<h3><?php _e( 'Custom Post Meta', 'wpforms-post-submissions' ); ?></h3>
			<table>
				<tbody>
				<?php
				$fields = wpforms_get_form_fields( $instance->form_data );
				$meta   = ! empty( $instance->form_data['settings']['post_submissions_meta'] ) ? $instance->form_data['settings']['post_submissions_meta'] : array( false );
				foreach ( $meta as $meta_key => $meta_field ) :
					$key  = $meta_field !== false ? preg_replace( '/[^a-zA-Z0-9_\-]/', '', $meta_key ) : '';
					$name = ! empty( $key ) ? 'settings[post_submissions_meta][' . $key . ']' : '';
					?>
					<tr>
						<td class="key">
							<input type="text" class="key-source" value="<?php echo $key; ?>" placeholder="<?php esc_attr_e( 'Enter meta key...', 'wpforms-post-submissions' ); ?>">
						</td>
						<td class="field">
							<select data-name="settings[post_submissions_meta][{source}]" name="<?php echo $name; ?>"
								class="key-destination wpforms-field-map-select" data-field-map-allowed="all-fields">
								<option value=""><?php _e( '--- Select Field ---', 'wpforms-post-submissions' ); ?></option>
								<?php
								if ( ! empty( $fields ) ) {
									foreach ( $fields as $field_id => $field ) {
										$label = ! empty( $field['label'] )
												? $field['label']
												: sprintf( /* translators: %d - field ID. */
													__( 'Field #%d', 'wpforms-post-submissions' ),
													absint( $field_id )
												);
										printf( '<option value="%s" %s>%s</option>', esc_attr( $field['id'] ), selected( $meta_field, $field_id, false ), esc_html( $label ) );
									}
								}
								?>
							</select>
						</td>
						<td class="actions">
							<a class="add" href="#"><i class="fa fa-plus-circle"></i></a>
							<a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php
		echo '</div>';
	}
}

new WPForms_Post_Submissions;
