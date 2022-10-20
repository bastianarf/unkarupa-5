<?php

namespace Uncanny_Automator_Pro;

use FrmField;
use FrmForm;

/**
 * Class Fi_Anon_Tokens
 * @package Uncanny_Automator_Pro
 */
class Fi_Anon_Tokens {


	/**
	 * Fi_Anon_Tokens constructor.
	 */
	public function __construct() {
		add_filter( 'automator_maybe_trigger_fi_anonfiform_tokens', [ $this, 'fi_possible_tokens' ], 20, 2 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'fi_token' ], 20, 6 );
	}

	/**
	 * Prepare tokens.
	 *
	 * @param array $tokens .
	 * @param array $args .
	 *
	 * @return array
	 */
	public function fi_possible_tokens( $tokens = [], $args = [] ) {
		$form_id      = $args['value'];
		$trigger_meta = $args['meta'];

		$form_ids = [];
		if ( ! empty( $form_id ) && 0 !== $form_id && is_numeric( $form_id ) ) {
			$form = FrmForm::getOne( $form_id );
			if ( $form ) {
				$form_ids[] = $form->id;
			}
		}

		if ( empty( $form_ids ) ) {
			$s_query                = [
				[
					'or'               => 1,
					'parent_form_id'   => null,
					'parent_form_id <' => 1,
				],
			];
			$s_query['is_template'] = 0;
			$s_query['status !']    = 'trash';

			$forms = FrmForm::getAll( $s_query, '', ' 0, 999' );
			foreach ( $forms as $form ) {
				$form_ids[] = $form->id;
			}
		}

		if ( ! empty( $form_ids ) ) {
			foreach ( $form_ids as $form_id ) {
				$fields = [];
				$meta   = FrmField::get_all_for_form( $form_id );
				if ( is_array( $meta ) ) {
					foreach ( $meta as $field ) {
						$input_id    = $field->id;
						$input_title = $field->name . ( $field->description !== '' ? ' (' . $field->description . ') ' : '' );
						$token_id    = "$form_id|$input_id";
						$fields[]    = [
							'tokenId'         => $token_id,
							'tokenName'       => $input_title,
							'tokenType'       => $field->type,
							'tokenIdentifier' => $trigger_meta,
						];
					}
				}
				$tokens = array_merge( $tokens, $fields );
			}
		}

		return $tokens;
	}

	/**
	 * Parse the token.
	 *
	 * @param string $value .
	 * @param array $pieces .
	 * @param string $recipe_id .
	 *
	 * @param $trigger_data
	 * @param $user_id
	 * @param $replace_args
	 *
	 * @return null|string
	 */
	public function fi_token( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		if ( $pieces ) {
			if ( in_array( 'ANONFIFORM', $pieces, true ) || in_array( 'ANONFISUBMITFIELD', $pieces, true ) || in_array( 'FIUPDATEFIELD', $pieces, true ) || in_array( 'FISUBMITFORM', $pieces, true ) ) {
				global $wpdb;
				$trigger_id     = $pieces[0];
				$trigger_meta   = $pieces[1];
				$field          = $pieces[2];
				if ( $pieces[2] === 'ANONFIFORM' ) {
					if ( isset( $trigger_data[0]['meta']['ANONFIFORM_readable'] ) ) {
						$value = $trigger_data[0]['meta']['ANONFIFORM_readable'];
					}
				} elseif( $pieces[2] === 'ANONFISUBMITFIELD' ) {
					if ( isset( $trigger_data[0]['meta']['ANONFISUBMITFIELD_readable'] ) ) {
						$value = $trigger_data[0]['meta']['ANONFISUBMITFIELD_readable'];
					}
				} elseif( $pieces[2] === 'SUBVALUE' ) {
					if ( isset( $trigger_data[0]['meta']['SUBVALUE'] ) ) {
						$value = $trigger_data[0]['meta']['SUBVALUE'];
					}
				} elseif( $pieces[2] === 'FIFORM' ) {
					if ( isset( $trigger_data[0]['meta']['FIFORM_readable'] ) ) {
						$value = $trigger_data[0]['meta']['FIFORM_readable'];
					}
				} else {
					$trigger_log_id = isset( $replace_args['trigger_log_id'] ) ? absint( $replace_args['trigger_log_id'] ) : 0;
					$entry          = $wpdb->get_var( "SELECT meta_value
													FROM {$wpdb->prefix}uap_trigger_log_meta
													WHERE meta_key = '$trigger_meta'
													AND automator_trigger_log_id = $trigger_log_id
													AND automator_trigger_id = $trigger_id
													LIMIT 0, 1" );
					$entry          = maybe_unserialize( $entry );
					$to_match       = "{$trigger_id}:{$trigger_meta}:{$field}";
					if ( is_array( $entry ) && key_exists( $to_match, $entry ) ) {
						$value = $entry[ $to_match ];
					}
				}
			}
		}

		return $value;
	}
}