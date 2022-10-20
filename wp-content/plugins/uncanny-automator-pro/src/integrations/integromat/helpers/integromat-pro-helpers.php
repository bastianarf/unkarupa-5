<?php


namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Integromat_Helpers;
/**
 * Class Integromat_Pro_Helpers
 * @package Uncanny_Automator_Pro
 */
class Integromat_Pro_Helpers extends Integromat_Helpers{

	public $load_options;

	/**
	 * Integromat_Pro_Helpers constructor.
	 */
	public function __construct() {
		// Selectively load options
		if ( method_exists( '\Uncanny_Automator\Automator_Helpers_Recipe', 'maybe_load_trigger_options' ) ) {
			global $uncanny_automator;
			$this->load_options = $uncanny_automator->helpers->recipe->maybe_load_trigger_options( __CLASS__ );
		} else {
			$this->load_options = true;
		}

		add_action( 'wp_ajax_webhook_url_ANON_INTEGROMATWEBHOOKS', array( $this, 'webhook_url_ajax' ), 15 );
		add_action( 'wp_ajax_get_samples_ANON_INTEGROMATWEBHOOKS', array( $this, 'get_samples_ajax' ), 15 );

	}

	/**
	 * @param Integromat_Pro_Helpers $pro
	 */
	public function setPro( Integromat_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}

	/**
	 *
	 */
	public function webhook_url_ajax() {
		global $uncanny_automator;

		// Nonce and post object validation
		$uncanny_automator->utilities->ajax_auth_check( $_POST );

		// Get recipe id
		$recipe_id = absint( $_POST['recipe_id'] );
		// Get item id
		$item_id = absint( $_POST['item_id'] );

		// Get webhook url
		$webhook_url = $this->get_webhook_url( $recipe_id, $item_id );

		// Output webhook url
		echo wp_json_encode( $webhook_url );

		die();
	}

	/**
	 *
	 */
	public function get_samples_ajax() {
		global $uncanny_automator;

		// Nonce and post object validation
		$uncanny_automator->utilities->ajax_auth_check( $_POST );

		$recipe_id   = $_POST['recipe_id'];
		$item_id     = $_POST['item_id'];
		$webhook_url = $_POST['webhook_url'];
		$option_name = 'transient_uap-' . $recipe_id . '-' . $item_id;
		$response    = (object) [
			'success' => false,
			'samples' => []
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
			}
		} else {

			update_option( $option_name, $option_name );
		}

		// Output response
		echo json_encode( $response );

		die();
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
}
