<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Integromat_Tokens
 *
 * @package Uncanny_Automator_Pro
 */
class Integromat_Tokens {


	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'INTEGROMAT';

	public function __construct() {

		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_integromat_token' ], 20, 6 );
	}

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $code
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $code ) {

		if ( self::$integration === $code ) {
			$status = true;
		}

		return $status;
	}


	/**
	 * @param $value
	 * @param $pieces
	 * @param $recipe_id
	 * @param $trigger_data
	 *
	 * @return mixed
	 */
	public function parse_integromat_token( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		$piece = 'ANON_INTEGROMATWEBHOOKS';
		if ( $pieces ) {
			if ( in_array( $piece, $pieces ) ) {
				global $uncanny_automator;
				//$user_id = 0;
				if ( $trigger_data && isset( $trigger_data['trigger_id'] ) ) {
					$trigger_id = $trigger_data['trigger_id'];
					$meta_field = $pieces[3];
					$meta_value = $this->get_form_data_from_trigger_meta( $meta_field, $trigger_id );
					if ( is_array( $meta_value ) ) {
						$value = join( ', ', $meta_value );
					} else {
						$value = $meta_value;
					}
				} else {
					foreach ( $trigger_data as $trigger ) {
						if ( isset( $trigger['ID'] ) ) {
							$trigger_id = $trigger['ID'];
							$meta_field = $pieces[3];
							$meta_value = $this->get_form_data_from_trigger_meta( $meta_field, $trigger_id );
							if ( is_array( $meta_value ) ) {
								$value = join( ', ', $meta_value );
							} else {
								$value = $meta_value;
							}
						}
					}
				}
			}
		}

		return $value;
	}

	/**
	 * @param $meta_key
	 * @param $trigger_id
	 *
	 * @return mixed|string
	 */
	public function get_form_data_from_trigger_meta( $meta_key, $trigger_id ) {
		global $wpdb;
		if ( empty( $meta_key ) || empty( $trigger_id ) ) {
			return '';
		}

		$meta_value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}uap_trigger_log_meta WHERE meta_key = %s AND automator_trigger_id = %d ORDER BY ID DESC LIMIT 0,1", $meta_key, $trigger_id ) );
		if ( ! empty( $meta_value ) ) {
			return maybe_unserialize( $meta_value );
		}

		return '';
	}
}