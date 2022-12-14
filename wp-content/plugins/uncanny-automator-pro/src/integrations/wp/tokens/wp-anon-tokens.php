<?php

namespace Uncanny_Automator_Pro;


use Uncanny_Automator\Wp_Tokens;

/**
 * Class WP_Anon_Tokens
 * @package Uncanny_Automator_Pro
 */
class WP_Anon_Tokens extends Wp_Tokens {


	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WP';

	/**
	 * WP_Anon_Tokens constructor.
	 */
	public function __construct() {
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_wp_post_token' ], 20, 6 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_anonusercommented_token' ], 20, 6 );
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
	 * @param array $tokens
	 * @param array $args
	 *
	 * @return array
	 */
	public function wp_possible_tokens( $tokens = [], $args = [] ) {
		$trigger_integration = $args['integration'];
		$trigger_meta        = $args['meta'];

		$fields = [
			[
				'tokenId'         => 'username',
				'tokenName'       => __( 'Username', 'uncanny-automator' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => $trigger_meta,
			],
			[
				'tokenId'         => 'user_id',
				'tokenName'       => __( 'User ID', 'uncanny-automator' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => $trigger_meta,
			],
			[
				'tokenId'         => 'first_name',
				'tokenName'       => __( 'First name', 'uncanny-automator' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => $trigger_meta,
			],
			[
				'tokenId'         => 'last_name',
				'tokenName'       => __( 'Last name', 'uncanny-automator' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => $trigger_meta,
			],
			[
				'tokenId'         => 'email',
				'tokenName'       => __( 'User email', 'uncanny-automator' ),
				'tokenType'       => 'email',
				'tokenIdentifier' => $trigger_meta,
			],
		];

		$tokens = array_merge( $tokens, $fields );

		return $tokens;
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
	public function parse_anonusercreated_token( $value, $pieces, $recipe_id, $trigger_data, $user_id = 0, $replace_args = array() ) {
		$piece = 'ANONUSERCREATED';
		if ( $pieces ) {
			if ( in_array( $piece, $pieces ) ) {
				global $uncanny_automator;

				if ( $trigger_data ) {
					foreach ( $trigger_data as $trigger ) {
						$trigger_id = $trigger['ID'];

						$meta_field = $pieces[2];

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

	public function parse_wp_post_token( $value, $pieces, $recipe_id, $trigger_data, $user_id = 0, $replace_args = array() ) {
		$tokens = [
			'WPPOSTTYPES',
			'WPPOSTTYPES_ID',
			'WPPOSTTYPES_URL',
			'WPPOSTTYPES_THUMB_URL',
			'WPPOSTTYPES_THUMB_ID',
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
						if ( ! empty( $meta_value ) ) {
							$value = maybe_unserialize( $meta_value );
						}
					}
				}
			}
		}

		return $value;
	}

	public function parse_anonusercommented_token( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		$tokens = [
			'COMMENTID',
			'COMMENTAUTHOR',
			'COMMENTAUTHOREMAIL',
			'COMMENTAUTHORWEB',
			'COMMENTCONTENT',
			'WPPOSTTYPES',
			'WPPOSTTYPES_URL',
			'WPPOSTTYPES_ID',
			'COMMENTAPPROVED_COMMENTERNAME',
			'COMMENTAPPROVED_COMMENTEREMAIL',
			'COMMENTAPPROVED_COMMENTERWEBSITE',
			'COMMENTAPPROVED_COMMENT',
			'COMMENTAPPROVED_ID',
			'COMMENTAPPROVED_URL',
			'COMMENTAPPROVED',
		];

		global $wpdb;

		if ( $pieces && isset( $pieces[2] ) ) {
			$meta_field = $pieces[2];
			if ( ! empty( $meta_field ) && in_array( $meta_field, $tokens ) ) {
				if ( $trigger_data ) {
					foreach ( $trigger_data as $trigger ) {
						$trigger_id = $trigger['ID'];
						$meta_value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}uap_trigger_log_meta WHERE meta_key LIKE %s AND automator_trigger_id=%d  ORDER BY ID DESC LIMIT 0,1", "%%$meta_field", $trigger_id ) );

						if ( ! empty( $meta_value ) ) {
							$value = maybe_unserialize( $meta_value );
						}
					}
				}
			}
		}

		return $value;
	}

}
