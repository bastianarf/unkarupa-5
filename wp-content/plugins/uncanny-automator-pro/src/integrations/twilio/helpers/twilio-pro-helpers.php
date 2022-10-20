<?php

namespace Uncanny_Automator_Pro;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * Class Twilio_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Twilio_Pro_Helpers {

	/**
	 * @var Twilio_Pro_Helpers
	 */
	public $options;

	/**
	 * @var Twilio_Pro_Helpers
	 */
	public $pro;

	/**
	 * @var string
	 */
	public $setting_tab;
	/**
	 * @var bool
	 */
	public $load_options;

	/**
	 * Twilio_Pro_Helpers constructor.
	 */
	public function __construct() {
		// Selectively load options
		if ( method_exists( '\Uncanny_Automator\Automator_Helpers_Recipe', 'maybe_load_trigger_options' ) ) {
			global $uncanny_automator;
			$this->load_options = $uncanny_automator->helpers->recipe->maybe_load_trigger_options( __CLASS__ );
		}

		$this->setting_tab = 'twilio_api';
		add_filter( 'uap_settings_tabs', array( $this, 'add_twilio_api_settings' ), 15 );
		add_action( 'update_option_uap_automator_twilio_api_auth_token', array( $this, 'twilio_oauth_update' ), 100, 3 );
		add_action( 'add_option_uap_automator_twilio_api_auth_token', array( $this, 'twilio_oauth_new' ), 100, 2 );
		add_action( 'init', array( $this, 'twilio_oauth_save' ), 200 );

		// Add twillio disconnect action.
		add_action( 'wp_ajax_uoa_twillio_disconnect', array( $this, 'uoa_twillio_disconnect' ), 100 );
	}

	/**
	 * @param Twilio_Pro_Helpers $options
	 */
	public function setOptions( Twilio_Pro_Helpers $options ) {
		$this->options = $options;
	}

	/**
	 * @param Twilio_Pro_Helpers $pro
	 */
	public function setPro( Twilio_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}

	/**
	 *
	 * @param string $to
	 * @param string $message
	 * @param string $user_id
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws TwilioException
	 */
	public function send_sms( $to, $message, $user_id ) {
		list( $account_sid, $auth_token ) = self::get_twilio_token();

		if ( empty( $account_sid ) || empty( $auth_token ) ) {
			return array(
				'result'  => false,
				'message' => __( 'Twilio credentails has expired.', 'uncanny-automator-pro' ),
			);
		}

		$phone_number = trim( get_option( 'uap_automator_twilio_api_phone_number', '' ) );
		if ( empty( $phone_number ) ) {
			return array(
				'result'  => false,
				'message' => __( 'Twilio number is missing.', 'uncanny-automator-pro' ),
			);
		}

		$to = self::validate_phone_number( $to );
		if ( ! $to ) {
			return array(
				'result'  => false,
				'message' => __( 'To number is not valid.', 'uncanny-automator-pro' ),
			);
		}

		try {
			$twilio  = new Client( $account_sid, $auth_token );
			$message = $twilio->messages->create(
				$to,
				array(
					'body' => $message,
					'from' => $phone_number,
				)
			);

			update_user_meta( $user_id, '_twilio_sms_', $message->sid );

			return array(
				'result'  => true,
				'message' => '',
			);
		} catch ( RestException $exception ) {
			return array(
				'result'  => false,
				'message' => __( $exception->getMessage(), 'uncanny-automator-pro' ),
			);
		}

	}

	/**
	 * @param $phone
	 *
	 * @return false|mixed|string|string[]
	 */
	private function validate_phone_number( $phone ) {
		// Allow +, - and . in phone number
		$filtered_phone_number = filter_var( $phone, FILTER_SANITIZE_NUMBER_INT );
		// Remove "-" from number
		$phone_to_check = str_replace( '-', '', $filtered_phone_number );

		// Check the lenght of number
		// This can be customized if you want phone number from a specific country
		if ( strlen( $phone_to_check ) < 10 || strlen( $phone_to_check ) > 14 ) {
			return false;
		} else {
			return $phone_to_check;
		}
	}

	/**
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_twilio_api_settings( $tabs ) {
		$is_uncannyowl_twilio_settings_expired = get_option( '_uncannyowl_twilio_settings_expired', false );
		$tab_url                               = admin_url( 'edit.php' ) . '?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab;
		$tabs[ $this->setting_tab ]            = array(
			'name'           => __( 'Twilio', 'uncanny-automator-pro' ),
			'title'          => __( 'Twilio API settings', 'uncanny-automator-pro' ),
			'description'    => sprintf(
				'<p>%1$s</p>',
				sprintf(
					__( "To view API credentials visit %1\$s. It's really easy, we promise! Visit %2\$s for simple instructions.", 'uncanny-automator-pro' ),

					'<a href="' . Utilities::utm_parameters( 'https://www.twilio.com/console/', 'settings', 'twilio-credentials' ) . '" target="_blank">https://www.twilio.com/console/</a>',

					'<a href="' . Utilities::utm_parameters( 'https://automatorplugin.com/knowledge-base/twilio/', 'settings', 'twilio-kb_article' ) . '" target="_blank">https://automatorplugin.com/knowledge-base/twilio/</a>'
				)
			) . $this->get_user_info(),
			'is_pro'         => true,
			'is_expired'     => $is_uncannyowl_twilio_settings_expired,
			'settings_field' => 'uap_automator_twilio_api_settings',
			'wp_nonce_field' => 'uap_automator_twilio_api_nonce',
			'save_btn_name'  => 'uap_automator_twilio_api_save',
			'save_btn_title' => __( 'Save API details', 'uncanny-automator-pro' ),
			'fields'         => array(
				'uap_automator_twilio_api_account_sid'  => array(
					'title'       => __( 'Account SID:', 'uncanny-automator-pro' ),
					'type'        => 'text',
					'css_classes' => '',
					'placeholder' => '',
					'default'     => '',
					'required'    => true,
					'custom_atts' => array( 'autocomplete' => 'off' ),
				),
				'uap_automator_twilio_api_auth_token'   => array(
					'title'       => __( 'Auth token:', 'uncanny-automator-pro' ),
					'type'        => 'text',
					'css_classes' => '',
					'placeholder' => '',
					'default'     => '',
					'required'    => true,
					'custom_atts' => array( 'autocomplete' => 'off' ),
				),
				'uap_automator_twilio_api_phone_number' => array(
					'title'       => __( 'Twilio number:', 'uncanny-automator-pro' ),
					'type'        => 'text',
					'css_classes' => '',
					'placeholder' => '+15017122661',
					'default'     => '',
					'required'    => true,
					'custom_atts' => array( 'autocomplete' => 'off' ),
				),
			),
		);

		return $tabs;
	}

	/**
	 * To get twilio access tokens
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws TwilioException
	 */
	public static function get_twilio_token() {

		$get_transient = get_transient( '_uncannyowl_twilio_settings' );

		if ( false !== $get_transient ) {

			$tokens = explode( '|', $get_transient );

			return array( $tokens[0], $tokens[1] );

		} else {

			$oauth_settings        = get_option( '_uncannyowl_twilio_settings' );
			$current_refresh_token = isset( $oauth_settings['account_sid'] ) ? $oauth_settings['account_sid'] : '';
			if ( empty( $current_refresh_token ) ) {
				update_option( '_uncannyowl_twilio_settings_expired', true );

				return array( '', '' );
			}

			$account_sid  = trim( get_option( 'uap_automator_twilio_api_account_sid', '' ) );
			$auth_token   = trim( get_option( 'uap_automator_twilio_api_auth_token', '' ) );
			$phone_number = trim( get_option( 'uap_automator_twilio_api_phone_number', '' ) );
			if ( isset( $account_sid ) && isset( $auth_token )
				 && strlen( $account_sid ) > 0
				 && strlen( $auth_token ) > 0
				 && strlen( $phone_number ) > 0
				 && strlen( $phone_number ) > 0
			) {
				try {
					$client                      = new Client( $account_sid, $auth_token );
					$account                     = $client->account->fetch();
					$tokens_info                 = array();
					$tokens_info['account_sid']  = $account_sid;
					$tokens_info['auth_token']   = $auth_token;
					$tokens_info['phone_number'] = $phone_number;
					update_option( '_uncannyowl_twilio_settings', $tokens_info );
					set_transient( '_uncannyowl_twilio_settings', $tokens_info['account_sid'] . '|' . $tokens_info['auth_token'], 60 * 50 );
					delete_option( '_uncannyowl_twilio_settings_expired' );

					return array( $account_sid, $auth_token );
				} catch ( RestException $exception ) {
					update_option( '_uncannyowl_twilio_settings', array() );
					update_option( '_uncannyowl_twilio_settings_expired', true );

					return array( '', '' );
				}
			} else {
				// Empty settings
				update_option( '_uncannyowl_twilio_settings', array() );
				update_option( '_uncannyowl_twilio_settings_expired', true );

				return array( '', '' );
			}
		}
	}

	/**
	 * Action when settings updated, it will redirect user to 3rd party for OAuth connect.
	 *
	 * @param string|array $old_value
	 * @param string|array $new_value
	 * @param string $option
	 *
	 * @throws ConfigurationException
	 * @throws TwilioException
	 */
	public function twilio_oauth_update( $old_value, $new_value, $option ) {
		if ( $option === 'uap_automator_twilio_api_auth_token' && $old_value !== $new_value ) {
			$this->oauth_redirect();
		}
	}

	/**
	 * Action when settings added, it will redirect user to 3rd party for OAuth connect.
	 *
	 * @param string|array $old_value
	 * @param string|array $new_value
	 * @param string $option
	 */
	public function twilio_oauth_new( $option, $new_value ) {
		if ( $option === 'uap_automator_twilio_api_auth_token' && ! empty( $new_value ) ) {
			$this->oauth_redirect();
		}
	}

	/**
	 * Action when settings added, it will redirect user to 3rd party for OAuth connect.
	 */
	public function twilio_oauth_save() {
		if ( isset( $_POST['uap_automator_twilio_api_account_sid'] ) && ! empty( $_POST['uap_automator_twilio_api_account_sid'] ) && isset( $_POST['uap_automator_twilio_api_auth_token'] ) && ! empty( $_POST['uap_automator_twilio_api_auth_token'] ) ) {
			update_option( 'uap_automator_twilio_api_account_sid', $_POST['uap_automator_twilio_api_account_sid'] );
			update_option( 'uap_automator_twilio_api_auth_token', $_POST['uap_automator_twilio_api_auth_token'] );
			update_option( 'uap_automator_twilio_api_phone_number', $_POST['uap_automator_twilio_api_phone_number'] );
			$this->oauth_redirect();
		}
	}

	/**
	 * @throws ConfigurationException
	 * @throws TwilioException
	 */
	private function oauth_redirect() {
		$account_sid  = trim( get_option( 'uap_automator_twilio_api_account_sid', '' ) );
		$auth_token   = trim( get_option( 'uap_automator_twilio_api_auth_token', '' ) );
		$phone_number = trim( get_option( 'uap_automator_twilio_api_phone_number', '' ) );
		if ( isset( $account_sid ) && isset( $auth_token ) && strlen( $account_sid ) > 0 && strlen( $auth_token ) > 0 && strlen( $phone_number ) > 0 && strlen( $phone_number ) > 0 ) {
			try {
				$client                      = new Client( $account_sid, $auth_token );
				$account                     = $client->account->fetch();
				$tokens_info                 = array();
				$tokens_info['account_sid']  = $account_sid;
				$tokens_info['auth_token']   = $auth_token;
				$tokens_info['phone_number'] = $phone_number;
				update_option( '_uncannyowl_twilio_settings', $tokens_info );
				set_transient( '_uncannyowl_twilio_settings', $tokens_info['account_sid'] . '|' . $tokens_info['auth_token'], 60 * 50 );
				delete_option( '_uncannyowl_twilio_settings_expired' );
				wp_safe_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab . '&connect=1' ) );
				die;
			} catch ( RestException $exception ) {
				wp_safe_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab . '&connect=2' ) );
				die;
			}
		}
	}

	/**
	 * @param string $option_code
	 * @param string $label
	 * @param bool $tokens
	 * @param string $type
	 * @param string $default
	 * @param bool
	 * @param string $description
	 * @param string $placeholder
	 *
	 * @return mixed
	 */
	public function textarea_field( $option_code = 'TEXT', $label = null, $tokens = true, $type = 'text', $default = null, $required = true, $description = '', $placeholder = null ) {

		if ( ! $label ) {
			$label = __( 'Text', 'uncanny-automator-pro' );
		}

		if ( ! $description ) {
			$description = '';
		}

		if ( ! $placeholder ) {
			$placeholder = '';
		}

		$option = array(
			'option_code'      => $option_code,
			'label'            => $label,
			'description'      => $description,
			'placeholder'      => $placeholder,
			'input_type'       => $type,
			'supports_tokens'  => $tokens,
			'required'         => $required,
			'default_value'    => $default,
			'supports_tinymce' => false,
		);

		return apply_filters( 'uap_option_text_field', $option );
	}

	/**
	 * Returns the html of the user connected in Twillio API.
	 *
	 * @return string The html of the user.
	 */
	public function get_user_info() {

		$accounts = $this->get_twillio_accounts_connected();
		if ( ! empty( $accounts ) ) {
			return $this->get_user_html( $accounts );
		}

		return '';
	}

	/**
	 * Constructs the html of the user connected in Twillio API.
	 *
	 * @param array $accounts The accounts connected found in Twillio Response.
	 * @return string The complete html display of user info.
	 */
	public function get_user_html( $accounts = array() ) {
		ob_start();
		$this->get_inline_stylesheet();
		?>
		<?php if ( ! empty( $accounts ) ) : ?>
			<div class="uoa-twillio-user-info">
				<?php foreach ( $accounts as $account ) : ?>
					<div class="uoa-twillio-user-info__item">
						<div class="uoa-twillio-user-info__item-name">
							<?php echo esc_html( $account['friendly_name'] ); ?>
						</div>
						<div class="uoa-twillio-user-info__item-type">
							<?php echo esc_html( $account['type'] ); ?>
						</div>
						<div class="uoa-twillio-user-info__item-status">
							<?php echo esc_html( $account['status'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p>
				<?php
				$disconnect_uri = add_query_arg(
					array(
						'action' => 'uoa_twillio_disconnect',
						'nonce'  => wp_create_nonce( 'uoa_twillio_disconnect' ),
					),
					admin_url( 'admin-ajax.php' )
				);
				?>
				<a title="<?php esc_attr_e( 'Disconnect', 'uncanny-automator-pro' ); ?>" href="<?php echo esc_url( $disconnect_uri ); ?>" class="uo-settings-btn uo-settings-btn--error">
					<?php esc_html_e( 'Disconnect', 'uncanny-automator-pro' ); ?>
				</a>

			</p>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Outputs an inline CSS to format our disconnect button and user info.
	 *
	 * @return void
	 */
	public function get_inline_stylesheet() {
		?>
		<style>
			.uo-settings-content-description a.uo-settings-btn--error {
				color: #e94b35;
			}
			.uo-settings-content-description a.uo-settings-btn--error:focus,
			.uo-settings-content-description a.uo-settings-btn--error:active,
			.uo-settings-content-description a.uo-settings-btn--error:hover {
				color: #fff;
			}

			.uoa-twillio-user-info {
				margin: 20px 0;
				color: #1f304c;
			}
			.uoa-twillio-user-info__item {
				margin-bottom: 10px;
				display: flex;
				flex-wrap: nowrap;
				align-items: center;
				justify-content: space-between;
				max-width: 285px;
			}
			.uoa-twillio-user-info__item-name {
				font-weight: 700;
			}
			.uoa-twillio-user-info__item-type,
			.uoa-twillio-user-info__item-status {
				border: 1px solid;
				border-radius: 20px;
				display: inline-block;
				font-size: 12px;
				padding: 2.5px 10px 3px;
				text-transform: capitalize;
			}
		</style>
		<?php
	}

	/**
	 * Get the Twillio Accounts connected using the account id and auth token.
	 * This functions sends an http request with Basic Authentication to Twillio API.
	 *
	 * @return array $twillio_accounts The twillio accounts connected.
	 */
	public function get_twillio_accounts_connected() {

		$endpoint_url = 'https://api.twilio.com/2010-04-01/Accounts.json';

		$twillio_accounts = array();

		$sid      = get_option( 'uap_automator_twilio_api_account_sid' );
		$token    = get_option( 'uap_automator_twilio_api_auth_token' );
		$settings = get_option( '_uncannyowl_twilio_settings' );

		if ( empty( $sid ) || empty( $token ) || empty( $settings ) ) {
			return array();
		}

		// Return the transient if its available.
		$accounts_saved = get_transient( 'uap_automator_twilio_api_accounts_response' );
		if ( false !== $accounts_saved ) {
			return $accounts_saved;
		}

		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $sid . ':' . $token ),
			),
		);
		// Start the request.
		$request = wp_remote_request( $endpoint_url, $args );
		// Get the status code.
		$status_code = wp_remote_retrieve_response_code( $request );
		// Get the response body.
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		if ( 200 === $status_code ) {
			if ( ! empty( $response ) ) {
				$accounts = isset($response->accounts) ? $response->accounts : array();
				foreach ( $accounts as $account ) {
					$twillio_accounts[] = array(
						'status'        => $account->status,
						'friendly_name' => $account->friendly_name,
						'type'          => $account->type,
					);
				}
				// Update the transient.
				set_transient( 'uap_automator_twilio_api_accounts_response', $twillio_accounts, DAY_IN_SECONDS );
			}
		}

		return $twillio_accounts;
	}

	/**
	 * Callback function to hook wp_ajax_uoa_twillio_disconnect.
	 * Deletes all the option and transients then redirect the user back to the settings page.
	 *
	 * @return void.
	 */
	public function uoa_twillio_disconnect() {

		if ( wp_verify_nonce( filter_input( INPUT_GET, 'nonce', FILTER_DEFAULT ), 'uoa_twillio_disconnect' ) ) {

			// Remove option
			$option_keys = array(
				'_uncannyowl_twilio_settings',
				'_uncannyowl_twilio_settings_expired',
				'uap_automator_twilio_api_auth_token',
				'uap_automator_twilio_api_phone_number',
				'uap_automator_twilio_api_account_sid',
			);

			foreach ( $option_keys as $option_key ) {
				delete_option( $option_key );
			}

			// Remove transients.
			$transient_keys = array(
				'_uncannyowl_twilio_settings',
				'uap_automator_twilio_api_accounts_response',
			);

			foreach ( $transient_keys as $transient_key ) {
				delete_transient( $transient_key );
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type' => 'uo-recipe',
					'page'      => 'uncanny-automator-settings',
					'tab'       => 'twilio_api',
				),
				admin_url( 'edit.php' )
			)
		);

		exit;
	}
}
