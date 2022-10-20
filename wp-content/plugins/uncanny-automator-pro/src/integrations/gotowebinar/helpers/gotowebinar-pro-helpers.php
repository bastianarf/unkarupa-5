<?php


namespace Uncanny_Automator_Pro;


/**
 * Class Gotowebinar_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Gotowebinar_Pro_Helpers {

	/**
	 * @var Gotowebinar_Pro_Helpers
	 */
	public $options;
	/**
	 * @var Gotowebinar_Pro_Helpers
	 */
	public $pro;

	/**
	 * @var Gotowebinar_Pro_Helpers
	 */
	public $setting_tab;

	/**
	 * @var bool
	 */
	public $load_options;

	/**
	 * Gotowebinar_Pro_Helpers constructor.
	 */
	public function __construct() {
		$this->setting_tab = 'gtw_api';
		// Selectively load options
		if ( method_exists( '\Uncanny_Automator\Automator_Helpers_Recipe', 'maybe_load_trigger_options' ) ) {
			global $uncanny_automator;
			$this->load_options = $uncanny_automator->helpers->recipe->maybe_load_trigger_options( __CLASS__ );
		} else {
			$this->load_options = true;
		}

		add_filter( 'uap_settings_tabs', [ $this, 'add_gtw_api_settings' ], 15 );
		add_action( 'update_option_uap_automator_gtw_api_consumer_secret', [ $this, 'gtw_oauth_update' ], 100, 3 );
		add_action( 'add_option_uap_automator_gtw_api_consumer_secret', [ $this, 'gtw_oauth_new' ], 100, 2 );
		add_action( 'init', [ $this, 'validate_oauth_tokens' ], 100, 3 );
		add_action( 'init', [ $this, 'gtw_oauth_save' ], 200 );
	}

	/**
	 * @param Gotowebinar_Pro_Helpers $options
	 */
	public function setOptions( Gotowebinar_Pro_Helpers $options ) {
		$this->options = $options;
	}

	/**
	 * @param Gotowebinar_Pro_Helpers $pro
	 */
	public function setPro( Gotowebinar_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}


	/**
	 * @param null $label
	 * @param string $option_code
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function get_webinars( $label = null, $option_code = 'GTWWEBINAR', $args = [] ) {
		if ( ! $this->load_options ) {
			global $uncanny_automator;

			return $uncanny_automator->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = __( 'Webinar', 'uncanny-automator-pro' );
		}

		$args = wp_parse_args( $args,
			array(
				'uo_include_any' => false,
				'uo_any_label'   => __( 'Any Webinar', 'uncanny-automator-pro' ),
			)
		);

		$token        = key_exists( 'token', $args ) ? $args['token'] : true;
		$is_ajax      = key_exists( 'is_ajax', $args ) ? $args['is_ajax'] : false;
		$target_field = key_exists( 'target_field', $args ) ? $args['target_field'] : '';
		$end_point    = key_exists( 'endpoint', $args ) ? $args['endpoint'] : '';
		$options      = [];
		global $uncanny_automator;

		if ( $uncanny_automator->helpers->recipe->load_helpers ) {
			list( $access_token, $organizer_key ) = self::get_webinar_token();

			if ( ! empty( $access_token ) ) {
				$current_time            = current_time( 'Y-m-d\TH:i:s\Z' );
				$current_time_plus_years = date( 'Y-m-d\TH:i:s\Z', strtotime( '+2 year', strtotime( $current_time ) ) );
				// get webinars
				$json_feed = wp_remote_get( 'https://api.getgo.com/G2W/rest/v2/organizers/' . $organizer_key . '/webinars?fromTime=' . $current_time . '&toTime=' . $current_time_plus_years . '&page=0&size=200', [
					'headers' => [
						'Authorization' => $access_token,
					],
				] );

				$json_response = wp_remote_retrieve_response_code( $json_feed );
				// prepare webinar lists
				if ( 200 === $json_response ) {
					$jsondata = json_decode( preg_replace( '/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', wp_remote_retrieve_body( $json_feed ) ), true );
					$jsondata = isset( $jsondata['_embedded']['webinars'] ) ? $jsondata['_embedded']['webinars'] : [];

					if ( count( $jsondata ) > 0 ) {
						foreach ( $jsondata as $key1 => $webinar ) {
							$webinar_key                            = (string) $webinar['webinarKey'];
							$options[ $webinar_key . "-objectkey" ] = $webinar['subject'];
						}
					}
				}
			}
		}

		$option = [
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'supports_tokens' => $token,
			'is_ajax'         => $is_ajax,
			'fill_values_in'  => $target_field,
			'endpoint'        => $end_point,
			'options'         => $options,
		];

		return apply_filters( 'uap_option_get_webinars', $option );
	}

	/**
	 * For registering user to webinar action method.
	 *
	 * @param string $user_id
	 * @param string $webinar_key
	 *
	 * @return array
	 */
	public static function gtw_register_user( $user_id, $webinar_key ) {
		$user = get_userdata( $user_id );
		if ( is_wp_error( $user ) ) {
			return [
				'result'  => false,
				'message' => __( 'GoToWebinar user not found.', 'uncanny-automator-pro' )
			];
		}
		$customer_first_name = $user->first_name;
		$customer_last_name  = $user->last_name;
		$customer_email      = $user->user_email;

		if ( ! empty( $customer_email ) ) {
			$customer_email_parts = explode( '@', $customer_email );
			$customer_first_name  = empty( $customer_first_name ) ? $customer_email_parts[0] : $customer_first_name;
			$customer_last_name   = empty( $customer_last_name ) ? $customer_email_parts[0] : $customer_last_name;
		}

		list( $access_token, $organizer_key ) = self::get_webinar_token();

		if ( empty( $access_token ) ) {
			return [
				'result'  => false,
				'message' => __( 'GoToWebinar credentails has expired.', 'uncanny-automator-pro' )
			];
		}

		// API register call
		$response = wp_remote_post( "https://api.getgo.com/G2W/rest/v2/organizers/{$organizer_key}/webinars/{$webinar_key}/registrants?resendConfirmation=true", [
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [
				'Authorization' => $access_token,
				'Content-type'  => 'application/json',
			],
			'body'        => json_encode( [
				'firstName' => $customer_first_name,
				'lastName'  => $customer_last_name,
				'email'     => $customer_email,
			] ),

		] );

		if ( ! is_wp_error( $response ) ) {
			if ( 201 === wp_remote_retrieve_response_code( $response ) || 409 === wp_remote_retrieve_response_code( $response ) ) {
				$jsondata = json_decode( $response['body'], true, 512, JSON_BIGINT_AS_STRING );
				if ( isset( $jsondata['joinUrl'] ) ) {
					update_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_registrantKey', $jsondata['registrantKey'] );
					update_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_joinUrl', $jsondata['joinUrl'] );

					return [ 'result' => true, 'message' => __( 'Successfully registered', 'uncanny-automator-pro' ) ];
				}
			} else {
				$jsondata = json_decode( $response['body'], true, 512, JSON_BIGINT_AS_STRING );

				return [ 'result' => false, 'message' => __( $jsondata['description'], 'uncanny-automator-pro' ) ];
			}
		} else {
			return [
				'result'  => false,
				'message' => __( "The GoToWebinar API returned an error.", 'uncanny-automator-pro' )
			];
		}
	}

	/**
	 * For un-registering user to webinar action method.
	 *
	 * @param string $user_id
	 * @param string $webinar_key
	 *
	 * @return array
	 */
	public static function gtw_unregister_user( $user_id, $webinar_key ) {

		list( $access_token, $organizer_key ) = self::get_webinar_token();

		if ( empty( $access_token ) ) {
			return [
				'result'  => false,
				'message' => __( 'GoToWebinar credentails has expired.', 'uncanny-automator-pro' )
			];
		}

		$user_registrant_key = get_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_registrantKey', true );

		if ( empty( $user_registrant_key ) ) {
			return [
				'result'  => false,
				'message' => __( 'User was not registered for webinar.', 'uncanny-automator-pro' )
			];
		}

		// API register call
		$response = wp_remote_post( "https://api.getgo.com/G2W/rest/v2/organizers/{$organizer_key}/webinars/{$webinar_key}/registrants/{$user_registrant_key}", [
			'method'      => 'DELETE',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [
				'Authorization' => $access_token,
				'Content-type'  => 'application/json',
			],
		] );

		if ( ! is_wp_error( $response ) ) {
			if ( 201 === wp_remote_retrieve_response_code( $response ) || 204 === wp_remote_retrieve_response_code( $response ) ) {
				delete_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_registrantKey' );
				delete_user_meta( $user_id, '_uncannyowl_gtw_webinar_' . $webinar_key . '_joinUrl' );

				return [ 'result' => true, 'message' => __( 'Successfully registered', 'uncanny-automator-pro' ) ];
			} else {
				$jsondata = json_decode( $response['body'], true, 512, JSON_BIGINT_AS_STRING );

				return [ 'result' => false, 'message' => __( $jsondata['description'], 'uncanny-automator-pro' ) ];
			}
		} else {
			return [
				'result'  => false,
				'message' => __( "The GoToWebinar API returned an error.", 'uncanny-automator-pro' )
			];
		}
	}

	/**
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_gtw_api_settings( $tabs ) {
		$is_uncannyowl_gtw_settings_expired = get_option( '_uncannyowl_gtw_settings_expired', false );
		$tab_url                            = admin_url( 'edit.php' ) . '?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab;
		$tabs[ $this->setting_tab ]         = [
			'name'           => __( 'GoToWebinar', 'uncanny-automator-pro' ),
			'title'          => __( 'GoToWebinar API settings', 'uncanny-automator-pro' ),
			'description'    => sprintf(
				'<p>%1$s</p><p>%2$s</p>',

				sprintf(
					__( "Connecting to GoToWebinar requires setting up an application and getting 2 values from inside your account. It's really easy, we promise! Visit %1\$s for simple instructions.", 'uncanny-automator-pro' ),

					'<a href="' . Utilities::utm_parameters( 'https://automatorplugin.com/knowledge-base/gotowebinar/', 'settings', 'gotowebinar-kb_article' ) . '" target="_blank">https://automatorplugin.com/knowledge-base/gotowebinar/</a>'
				),

				sprintf(
					__( 'When you are asked to enter a "Redirect URL", use this: %1$s', 'uncanny-automator-pro' ),
					'<strong>' . $tab_url . '</strong>'
				)
			),
			'is_pro'         => true,
			'is_expired'     => $is_uncannyowl_gtw_settings_expired,
			'settings_field' => 'uap_automator_gtw_api_settings',
			'wp_nonce_field' => 'uap_automator_gtw_api_nonce',
			'save_btn_name'  => 'uap_automator_gtw_api_save',
			'save_btn_title' => __( 'Save API details', 'uncanny-automator-pro' ),
			'fields'         => [
				'uap_automator_gtw_api_consumer_key'    => [
					'title'       => __( 'Consumer key:', 'uncanny-automator-pro' ),
					'type'        => 'text',
					'css_classes' => '',
					'placeholder' => '',
					'default'     => '',
					'required'    => true,
					'custom_atts' => [ 'autocomplete' => 'off' ],
				],
				'uap_automator_gtw_api_consumer_secret' => [
					'title'       => __( 'Consumer secret:', 'uncanny-automator-pro' ),
					'type'        => 'text',
					'css_classes' => '',
					'placeholder' => '',
					'default'     => '',
					'required'    => true,
					'custom_atts' => [ 'autocomplete' => 'off' ],
				],
			],
		];

		return $tabs;
	}

	/**
	 * To get webinar access token and organizer key
	 *
	 * @return array
	 */
	public static function get_webinar_token() {

		$get_transient = get_transient( '_uncannyowl_gtw_settings' );

		if ( false !== $get_transient ) {

			$tokens = explode( '|', $get_transient );

			return [ $tokens[0], $tokens[1] ];

		} else {

			$oauth_settings        = get_option( '_uncannyowl_gtw_settings' );
			$current_refresh_token = isset( $oauth_settings['refresh_token'] ) ? $oauth_settings['refresh_token'] : '';
			if ( empty( $current_refresh_token ) ) {
				update_option( '_uncannyowl_gtw_settings_expired', true );

				return [ '', '' ];
			}
			$consumer_key    = trim( get_option( 'uap_automator_gtw_api_consumer_key', '' ) );
			$consumer_secret = trim( get_option( 'uap_automator_gtw_api_consumer_secret', '' ) );
			//do response
			$response = wp_remote_post( 'https://api.getgo.com/oauth/v2/token', [
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret ),
					'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
				],
				'body'    => [
					'refresh_token' => $current_refresh_token,
					'grant_type'    => 'refresh_token',
				],
			] );

			if ( ! is_wp_error( $response ) ) {
				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {

					//get new access token and refresh token
					$jsondata = json_decode( $response['body'], true );

					$tokens_info                  = [];
					$tokens_info['access_token']  = $jsondata['access_token'];
					$tokens_info['refresh_token'] = $jsondata['refresh_token'];
					$tokens_info['organizer_key'] = $jsondata['organizer_key'];
					$tokens_info['account_key']   = $jsondata['account_key'];

					update_option( '_uncannyowl_gtw_settings', $tokens_info );
					set_transient( '_uncannyowl_gtw_settings', $tokens_info['access_token'] . '|' . $tokens_info['organizer_key'], 60 * 50 );
					delete_option( '_uncannyowl_gtw_settings_expired' );

					//return the array
					return [ $tokens_info['access_token'], $tokens_info['organizer_key'] ];

				} else {
					// Empty settings
					update_option( '_uncannyowl_gtw_settings', [] );
					update_option( '_uncannyowl_gtw_settings_expired', true );

					return [ '', '' ];
				}
			} else {
				// Empty settings
				update_option( '_uncannyowl_gtw_settings', [] );
				update_option( '_uncannyowl_gtw_settings_expired', true );

				return [ '', '' ];
			}
		}
	}

	/**
	 * Action when settings updated, it will redirect user to 3rd party for OAuth connect.
	 *
	 * @param string|array $old_value
	 * @param string|array $new_value
	 * @param string $option
	 */
	public function gtw_oauth_update( $old_value, $new_value, $option ) {
		if ( $option === 'uap_automator_gtw_api_consumer_secret' && $old_value !== $new_value ) {
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
	public function gtw_oauth_new( $option, $new_value ) {
		if ( $option === 'uap_automator_gtw_api_consumer_secret' && ! empty( $new_value ) ) {
			$this->oauth_redirect();
		}
	}

	/**
	 * Action when settings added, it will redirect user to 3rd party for OAuth connect.
	 */
	public function gtw_oauth_save() {
		if ( isset( $_POST['uap_automator_gtw_api_consumer_key'] ) && ! empty( $_POST['uap_automator_gtw_api_consumer_key'] ) && isset( $_POST['uap_automator_gtw_api_consumer_secret'] ) && ! empty( $_POST['uap_automator_gtw_api_consumer_secret'] ) ) {
			update_option( 'uap_automator_gtw_api_consumer_key', $_POST['uap_automator_gtw_api_consumer_key'] );
			update_option( 'uap_automator_gtw_api_consumer_secret', $_POST['uap_automator_gtw_api_consumer_secret'] );
			$this->oauth_redirect();
		}
	}

	/**
	 *
	 */
	private function oauth_redirect() {

		$consumer_key    = trim( get_option( 'uap_automator_gtw_api_consumer_key', '' ) );
		$consumer_secret = trim( get_option( 'uap_automator_gtw_api_consumer_secret', '' ) );
		if ( isset( $consumer_key ) && isset( $consumer_secret ) && strlen( $consumer_key ) > 0 && strlen( $consumer_secret ) > 0 ) {
			$tab_url    = admin_url( 'edit.php' ) . '?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab;
			$oauth_link = "https://api.getgo.com/oauth/v2/authorize?response_type=code&client_id=" . $consumer_key . "&state=" . $this->setting_tab;// . '&redirect_uri=' . urlencode( $tab_url );
			wp_redirect( $oauth_link );
			die;
		}
	}

	/**
	 * Callback function for OAuth redirect verification.
	 */
	public function validate_oauth_tokens() {

		if ( isset( $_REQUEST['code'] ) && ! empty( $_REQUEST['code'] ) && isset( $_REQUEST['state'] ) && $_REQUEST['state'] === $this->setting_tab ) {
			$consumer_key    = trim( get_option( 'uap_automator_gtw_api_consumer_key', '' ) ); //msAteMmZCVflQDKK6TUsOJOKIfRFDyEL
			$consumer_secret = trim( get_option( 'uap_automator_gtw_api_consumer_secret', '' ) );//'kqUuc9tdfPEK0yLB
			$code            = $_REQUEST['code'];
			$response        = wp_remote_post( 'https://api.getgo.com/oauth/v2/token', [
				'headers' => [
					'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
					'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret ),
					'Accept'        => 'application/json',
				],
				'body'    => [
					'code'       => $code,
					'grant_type' => 'authorization_code',
				],
			] );

			if ( ! is_wp_error( $response ) ) {
				// On success
				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {

					//lets get the response and decode it
					$jsondata = json_decode( $response['body'], true );

					$tokens_info                  = [];
					$tokens_info['access_token']  = $jsondata['access_token'];
					$tokens_info['refresh_token'] = $jsondata['refresh_token'];
					$tokens_info['organizer_key'] = $jsondata['organizer_key'];
					$tokens_info['account_key']   = $jsondata['account_key'];

					//update the options
					update_option( '_uncannyowl_gtw_settings', $tokens_info );
					delete_option( '_uncannyowl_gtw_settings_expired' );
					//set the transient
					set_transient( '_uncannyowl_gtw_settings', $tokens_info['access_token'] . '|' . $tokens_info['organizer_key'], 60 * 50 );
					wp_safe_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab . '&connect=1' ) );
					die;

				} else {
					// On Error
					wp_safe_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab . '&connect=1' ) );
					die;
				}
			} else {
				// On Error
				wp_safe_redirect( admin_url( 'edit.php?post_type=uo-recipe&page=uncanny-automator-settings&tab=' . $this->setting_tab . '&connect=1' ) );
				die;
			}
		}
	}
}
