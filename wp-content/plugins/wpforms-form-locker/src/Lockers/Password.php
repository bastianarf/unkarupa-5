<?php

namespace WPFormsLocker\Lockers;

/**
 * Lock a form using an admin-defined password.
 *
 * @since 1.0.0
 */
class Password {

	/**
	 * Current form information.
	 *
	 * @var array $form_data
	 *
	 * @since 1.0.0
	 */
	public $form_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Locker hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		\add_filter( 'wpforms_frontend_load', array( $this, 'display_form' ), 10, 2 );
		\add_filter( 'wpforms_process_initial_errors', array( $this, 'submit_form' ), 10, 2 );
	}

	/**
	 * Set current form information for internal use.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form information.
	 */
	protected function set_form_data( $form_data ) {
		$this->form_data = $form_data;
	}

	/**
	 * On form display actions.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $load_form Indicates whether a form should be loaded.
	 * @param array $form_data Form information.
	 *
	 * @return bool
	 */
	public function display_form( $load_form, $form_data ) {

		$this->set_form_data( $form_data );

		if ( ! $this->is_locked() && $this->get_unlocking_value() ) {
			\add_action( 'wpforms_display_submit_before', array( $this, 'add_password_field' ) );
		}

		if ( ! $this->is_locked() ) {
			return $load_form;
		}

		\add_action( 'wpforms_frontend_not_loaded', array( $this, 'locked_html' ), 10, 2 );

		return false;
	}

	/**
	 * On form submit actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $errors Form submit errors.
	 * @param array $form_data Form information.
	 *
	 * @return array
	 */
	public function submit_form( $errors, $form_data ) {

		$this->set_form_data( $form_data );

		if ( $this->is_locked() ) {
			$form_id = ! empty( $this->form_data['id'] ) ? $this->form_data['id'] : 0;

			$errors[ $form_id ]['form_locker'] = 'password';
		}

		return $errors;
	}

	/**
	 * Locked form HTML.
	 *
	 * @since 1.0.0
	 */
	public function locked_html() {

		$message = $this->get_locked_message();

		$locked_id       = 'locked-' . $this->form_data['id'];
		$action          = \esc_url( \remove_query_arg( 'wpforms' ) );
		$unlocking_value = $this->get_unlocking_value();
		$classes         = 'wpforms-password-locked wpforms-container';
		if ( \wpforms_setting( 'disable-css', '1' ) == '1' ) {
			$classes .= ' wpforms-container-full';
		}

		// Add the password form to the frontend forms to make 'Submit' button JS work correctly.
		\wpforms()->frontend->forms[ $locked_id ] = array( 'id' => $locked_id );

		?>
		<div class="<?php echo \esc_attr( $classes ); ?>" id="wpforms-<?php echo \esc_attr( $locked_id ); ?>">

			<?php if ( $message ) : ?>
				<p class="form-locked-message"><?php echo wp_kses_post( wpautop( $message ) ); ?></p>
			<?php endif; ?>

			<form id="wpforms-form-<?php echo \esc_attr( $locked_id ); ?>" class="wpforms-validate wpforms-form" data-formid="<?php echo \esc_attr( $locked_id ); ?>" method="post" enctype="multipart/form-data" action="<?php echo \esc_attr( $action ); ?>">

				<?php if ( ! empty( $unlocking_value ) ) : ?>
					<div class="wpforms-error-container"><?php \esc_html_e( 'The password is incorrect.', 'wpforms-form-locker' ); ?></div>
				<?php endif; ?>

				<div class="wpforms-field-container">
					<div id="wpforms-<?php echo \esc_attr( $locked_id ); ?>-field_form_locker_password-container" class="wpforms-field wpforms-field-password" data-field-id="form_locker_password">
						<label class="wpforms-field-label" for="wpforms-<?php echo \esc_attr( $locked_id ); ?>-field_form_locker_password">
							<?php \esc_html_e( 'Password', 'wpforms-form-locker' ); ?>
							<span class="wpforms-required-label">*</span>
						</label>
						<input type="password" id="wpforms-<?php echo \esc_attr( $locked_id ); ?>-field_form_locker_password" class="wpforms-field-medium wpforms-field-required" name="wpforms[form_locker_password]" required>
					</div>
				</div>

				<input type="hidden" name="wpforms[form_locker_form_id]" value="<?php echo \absint( $this->form_data['id'] ); ?>">

				<div class="wpforms-submit-container">
					<button type="submit" name="wpforms[submit]" class="wpforms-submit" id="wpforms-submit-<?php echo \esc_attr( $locked_id ); ?>" value="wpforms-submit" data-alt-text="<?php \esc_html_e( 'Sending...', 'wpforms-form-locker' ); ?>">
						<?php echo esc_html( apply_filters( 'wpforms_form_locker_submit_label', __( 'Submit', 'wpforms-form-locker' ) ) ); ?>
					</button>
				</div>

			</form>
		</div>
		<?php
	}

	/**
	 * Get locked form message from an admin area.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_locked_message() {
		return ! empty( $this->form_data['settings']['form_locker_password_message'] ) ? $this->form_data['settings']['form_locker_password_message'] : '';
	}

	/**
	 * Check if the form has a locker configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function has_locker() {

		if ( empty( $this->form_data['settings']['form_locker_password_enable'] ) ) {
			return false;
		}
		if ( empty( $this->form_data['settings']['form_locker_password'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the form meets a condition to be locked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_locked() {

		if ( ! $this->has_locker() ) {
			return false;
		}

		$password  = $this->get_unlocking_value();
		$reference = ! empty( $this->form_data['settings']['form_locker_password'] ) ? $this->form_data['settings']['form_locker_password'] : '';

		if ( $reference === $password ) {
			return false;
		}

		if ( \wp_create_nonce( $reference ) === $password ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the value unlocking the form.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_unlocking_value() {

		$form_id = ! empty( $_POST['wpforms']['form_locker_form_id'] ) ? \absint( $_POST['wpforms']['form_locker_form_id'] ) : 0;

		if ( empty( $form_id ) ) {
			$form_id = ! empty( $_POST['wpforms']['id'] ) ? \absint( $_POST['wpforms']['id'] ) : 0;
		}

		if ( \absint( $this->form_data['id'] ) !== $form_id ) {
			return '';
		}

		return $this->get_unsanitized_password();
	}

	/**
	 * Get a non-sanitized submitted form password.
	 * Use with caution.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_unsanitized_password() {
		return ! empty( $_POST['wpforms']['form_locker_password'] ) ? $_POST['wpforms']['form_locker_password'] : '';
	}

	/**
	 * Add a password field to the form to process locked form as normal.
	 *
	 * @since 1.0.0
	 */
	public function add_password_field() {

		$password = \wp_create_nonce( $this->get_unlocking_value() );

		echo '<input type="hidden" name="wpforms[form_locker_password]" value="' . \esc_attr( $password ) . '">';
	}
}
