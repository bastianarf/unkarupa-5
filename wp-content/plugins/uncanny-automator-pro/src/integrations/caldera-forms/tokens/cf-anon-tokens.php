<?php

namespace Uncanny_Automator_Pro;

use Caldera_Forms_Forms;
use Uncanny_Automator\Cf_Tokens;

/**
 * Class Cf_Anon_Tokens
 *
 * @package Uncanny_Automator_Pro
 */
class Cf_Anon_Tokens extends Cf_Tokens {


	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'CF';

	public function __construct() {
		add_filter( 'automator_maybe_trigger_cf_anoncfforms_tokens', [ $this, 'cf_possible_tokens' ], 20, 2 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_cf_token' ], 20, 6 );

	}

	/**
	 * Only load this integration and its triggers and actions if the related
	 * plugin is active
	 *
	 * @param $status
	 * @param $code
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $code ) {

		if ( self::$integration === $code ) {
			if ( class_exists( 'Caldera_Forms' ) ) {
				$status = true;
			} else {
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * @param array $tokens
	 * @param array $args
	 *
	 * @return array
	 */
	public function cf_possible_tokens( $tokens = [], $args = [] ) {
		$form_id             = $args['value'];
		$trigger_integration = $args['integration'];
		$trigger_meta        = $args['meta'];
		$fields              = [];
		if ( empty( $form_id ) ) {
			return $tokens;
		}

		$form = Caldera_Forms_Forms::get_form( $form_id );

		if ( ! empty( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( $field['type'] !== 'html' && $field['type'] !== 'summary' && $field['type'] !== 'section_break' && $field['type'] !== 'button' ) {
					$input_id    = $field['ID'];
					$input_title = $field['label'];
					$token_id    = "$form_id|$input_id";
					$token_type  = $field['type'];
					$fields[]    = [
						'tokenId'         => $token_id,
						'tokenName'       => $input_title,
						'tokenType'       => $token_type,
						'tokenIdentifier' => $trigger_meta,
					];
				}
			}
			$tokens = array_merge( $tokens, $fields );
		}

		return $tokens;
	}

	/**
	 * @param $value
	 * @param $pieces
	 * @param $recipe_id
	 * @param $trigger_data
	 *
	 * @return mixed
	 */
	public function parse_cf_token( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		if ( $pieces ) {
			if ( in_array( 'ANONCFFORMS', $pieces ) ) {

				$token_info = explode( '|', $pieces[2] );
				$form_id    = $token_info[0];
				$meta_key   = $token_info[1];
				//$user_id    = get_current_user_id();
				if ( isset( $_POST['formId'] ) && $_POST['formId'] === $form_id && isset( $_POST[ $meta_key ] ) ) {
					if ( is_array( $_POST[ $meta_key ] ) ) {
						$value = implode( ', ', $_POST[ $meta_key ] );
					} else {
						$value = $_POST[ $meta_key ];
					}
				}
			}
		}

		return $value;
	}
}