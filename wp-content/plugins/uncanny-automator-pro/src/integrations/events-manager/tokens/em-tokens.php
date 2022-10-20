<?php

namespace Uncanny_Automator_Pro;

/**
 * Class EM_TOKENS
 * @package Uncanny_Automator_Pro
 */
class EM_TOKENS {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'EVENTSMANAGER';

	/**
	 * EM_TOKENS constructor.
	 */
	public function __construct() {
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_em_trigger_tokens' ], 20, 6 );
	}

	function parse_em_trigger_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id = 0, $replace_args ) {
		$tokens = [
			'SELECTEDEVENT',
			'SELECTEDEVENT_ID',
			'SELECTEDEVENT_URL',
			'SELECTEDEVENT_STARTDATE',
			'SELECTEDEVENT_ENDDATE',
			'SELECTEDEVENT_LOCATION',
			'SELECTEDEVENT_AVAILABLESPACES',
			'SELECTEDEVENT_CONFIRMEDSPACES',
			'SELECTEDEVENT_PENDINGSPACES',
			'EMUNREGISTER',
			'EMUNREGISTER_ID',
			'EMUNREGISTER_URL',
			'EMUNREGISTER_STARTDATE',
			'EMUNREGISTER_ENDDATE',
			'EMUNREGISTER_LOCATION',
			'EMUNREGISTER_AVAILABLESPACES',
			'EMUNREGISTER_CONFIRMEDSPACES',
			'EMUNREGISTER_PENDINGSPACES',
			'EMEVENTS',
		];

		if ( $pieces && isset( $pieces[2] ) ) {
			$meta_field = $pieces[2];
			if ( ! empty( $meta_field ) && in_array( $meta_field, $tokens ) ) {
				if ( $trigger_data ) {
					global $wpdb;
					foreach ( $trigger_data as $trigger ) {
						$trigger_id = $trigger['ID'];
						$meta_key   = $trigger_id . ':' . $trigger['meta']['code'] . ':' . $meta_field;
						$meta_value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}uap_trigger_log_meta WHERE meta_key = %s AND automator_trigger_id = %d ORDER BY ID DESC LIMIT 0,1", $meta_key, $trigger_id ) );
						if ( ! empty( $meta_value ) && ! is_numeric( $meta_value ) ) {
							$value = maybe_unserialize( $meta_value );
						} else {
							$value = $meta_value;
						}
					}
				}
			}
		}

		return $value;
	}
}
