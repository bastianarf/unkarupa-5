<?php


namespace Uncanny_Automator_Pro;


/**
 * Class Uoa_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Uoa_Pro_Helpers {
	/**
	 * @var Uoa_Pro_Helpers
	 */
	public $options;
	/**
	 * @var Uoa_Pro_Helpers
	 */
	public $pro;

	/**
	 * @param Uoa_Pro_Helpers $pro
	 */
	public function setPro( Uoa_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}

	/**
	 * @param Uoa_Pro_Helpers $options
	 */
	public function setOptions( Uoa_Pro_Helpers $options ) {
		$this->options = $options;
	}

	/**
	 * Wp_Pro_Helpers constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_webhook_url_UOA_ANON_WEBHOOKS', array( $this, 'webhook_url_ajax' ), 15 );
		add_action( 'wp_ajax_get_samples_UOA_ANON_WEBHOOKS', array( $this, 'get_samples_ajax' ), 15 );

	}

	/**
	 * @param string $recipe_id
	 * @param string $item_id
	 *
	 * @return string
	 */
	private function get_webhook_url( $recipe_id = '', $item_id = '' ) {
		// Get webhook url
		return sprintf( site_url( '/wp-json/uap/v2/uap-%s-%s' ), $recipe_id, $item_id );
	}

	/**
	 *
	 */
	public function webhook_url_ajax() {
		global $uncanny_automator;

		// Nonce and post object validation
		$uncanny_automator->utilities->ajax_auth_check( $_POST );

		// Get recipe id
		$recipe_id = $_POST['recipe_id'];
		// Get item id
		$item_id = $_POST['item_id'];

		// Get webhook url
		$webhook_url = $this->get_webhook_url( $recipe_id, $item_id );

		// Output webhook url
		echo json_encode( $webhook_url );

		die();
	}

	/**
	 *
	 */
	public function get_samples_ajax() {
		global $uncanny_automator;

		// Nonce and post object validation
		$uncanny_automator->utilities->ajax_auth_check( $_POST );
		
		$recipe_id     = $_POST['recipe_id'];
		$item_id       = $_POST['item_id'];
		$webhook_url   = $_POST['webhook_url'];
		$option_name   = 'transient_uap-' . $recipe_id . '-' . $item_id;
		$option_expiry = 'expiry_uap-' . $recipe_id . '-' . $item_id;
		$response      = (object) [
			'success' => false,
			'samples' => [],
		];
		// Check if transit exists.
		$saved_hook = get_option( $option_name );

		if ( ! empty( $saved_hook ) ) {
			$fields = get_option( $option_name . '_fields' );
			if ( ! empty( $fields ) ) {
				$response = (object) [
					'success' => true,
					'samples' => [ $fields ],
				];
				delete_option( $option_name . '_fields' );
				delete_option( $option_name );
				delete_option( $option_expiry );
			}
		} else {

			update_option( $option_name, $option_name );
			update_option( $option_expiry, current_time( 'U' ) );
		}

		// Output response
		echo json_encode( $response );

		die();
	}
}