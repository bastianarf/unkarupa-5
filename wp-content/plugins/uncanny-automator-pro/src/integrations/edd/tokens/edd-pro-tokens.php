<?php

namespace Uncanny_Automator_Pro;

/**
 * Class EDD_PRO_TOKENS
 * @package Uncanny_Automator_Pro
 */
class EDD_PRO_TOKENS {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'EDD';

	/**
	 * EDD_PRO_TOKENS constructor.
	 */
	public function __construct() {
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_edd_pro_trigger_tokens' ], 20, 6 );
	}

	/**
	 * @param $value
	 * @param $pieces
	 * @param $recipe_id
	 * @param $trigger_data
	 * @param $user_id
	 * @param $replace_args
	 *
	 * @return mixed
	 */
	public function parse_edd_pro_trigger_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		if ( $pieces ) {
			if ( in_array( 'EDDDISCOUNTCODE', $pieces ) || in_array( 'EDDPRODPURCHDISCOUNT', $pieces ) ) {
				global $wpdb;
				$trigger_id     = $pieces[0];
				$trigger_meta   = $pieces[2];
				$trigger_log_id = isset( $replace_args['trigger_log_id'] ) ? absint( $replace_args['trigger_log_id'] ) : 0;
				$entry          = $wpdb->get_var( "SELECT meta_value
													FROM {$wpdb->prefix}uap_trigger_log_meta
													WHERE meta_key = '{$trigger_meta}'
													AND automator_trigger_log_id = {$trigger_log_id}
													AND automator_trigger_id = {$trigger_id}
													LIMIT 0,1" );

				$value = maybe_unserialize( $entry );
			}
		}

		return $value;
	}
}