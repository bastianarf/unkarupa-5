<?php

namespace Uncanny_Automator_Pro;


use Uncanny_Automator\Wp_Tokens;

/**
 * Class Wp_Pro_tokens
 * @package Uncanny_Automator_Pro
 */
class Wp_Pro_tokens extends Wp_Tokens {

	/**
	 * Wp_Pro_tokens constructor.
	 */
	public function __construct() {
		add_action( 'uap_wp_comment_approve', array( $this, 'uap_wp_comment_approve' ), 10, 4 );
		add_filter( 'automator_maybe_trigger_wp_wpcommentonpost_tokens', [
			$this,
			'wp_wpcommentonpost_possible_tokens',
		], 20, 2 );

		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_wp_tokens' ], 20, 6 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_wp_user_fields_tokens' ], 21, 6 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'parse_wp_token_roles' ], 21, 6 );
		add_filter( 'automator_maybe_trigger_wp_umetakey_tokens', [ $this, 'wp_usermeta_possible_tokens' ], 20, 2 );
	}

	/**
	 * @param \WP_Comment $comment
	 * @param $recipe_id
	 * @param $trigger_id
	 * @param $args
	 */
	public function uap_wp_comment_approve( \WP_Comment $comment, $recipe_id, $trigger_id, $args ) {
		if ( empty( $comment ) || empty( $recipe_id ) || empty( $trigger_id ) || ! is_array( $args ) ) {
			return;
		}

		global $uncanny_automator;

		foreach ( $args as $trigger_result ) {
			if ( true === $trigger_result['result'] ) {
				$user_id        = (int) $comment->user_id;
				$trigger_log_id = (int) $trigger_result['args']['get_trigger_id'];
				$run_number     = (int) $trigger_result['args']['run_number'];

				$args = [
					'user_id'        => $user_id,
					'trigger_id'     => $trigger_id,
					'meta_key'       => 'comment_id',
					'meta_value'     => $comment->comment_ID,
					'run_number'     => $run_number, //get run number
					'trigger_log_id' => $trigger_log_id,
				];

				$uncanny_automator->insert_trigger_meta( $args );
			}
		}
	}

	/**
	 * @param array $tokens
	 * @param array $args
	 *
	 * @return array
	 */
	public function wp_wpcommentonpost_possible_tokens( $tokens = array(), $args = array() ) {
		$trigger_meta = $args['meta'];
		$fields       = array(
			array(
				'tokenId'         => 'comment',
				'tokenName'       => __( 'Comment', 'uncanny-automator-pro' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => $trigger_meta,
			),
		);

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
	public function parse_wp_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		if ( empty( $pieces ) ) {
			return $value;
		}

		if ( in_array( 'COMMENTPARENT', $pieces ) ) {
			global $wpdb;
			$meta_field = $pieces[2];
			$trigger_id = absint( $pieces[0] );
			$meta_value = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}uap_trigger_log_meta WHERE meta_key LIKE '%{$meta_field}%' AND automator_trigger_id = {$trigger_id} ORDER BY ID DESC LIMIT 0,1" );
			if ( ! empty( $meta_value ) ) {
				return maybe_unserialize( $meta_value );
			}
		}

		if ( ! in_array( 'WPCOMMENTONPOST', $pieces, true ) && ! in_array( 'COMMENTAPPROVED', $pieces, true ) ) {
			return $value;
		}

		$trigger_id     = absint( $pieces[0] );
		$to_replace     = $pieces[2];
		$trigger_log_id = $replace_args['trigger_log_id'];
		$user_id        = $replace_args['user_id'];
		$comment_id     = $this->get_trigger_log_meta_value( 'comment_id', $trigger_id, $trigger_log_id, $user_id );
		$comment        = get_comment( $comment_id );
		if ( ! $comment instanceof \WP_Comment ) {
			return $value;
		}
		switch ( $to_replace ) {
			case 'WPCOMMENTONPOST';
			case 'WPPOSTTYPES';
				$value = get_the_title( $comment->comment_post_ID );
				break;
			case 'WPPOSTTYPES_URL';
				$value = get_permalink( $comment->comment_post_ID );
				break;
			case 'comment';
				$value = $comment->comment_content;
				break;
			case 'WPPOSTTYPES_ID';
			default:
				$value = $comment->comment_post_ID;
				break;
		}

		return $value;
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
	public function parse_wp_token_roles( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		if ( empty( $pieces ) ) {
			return $value;
		}

		if ( ! in_array( 'WPROLEOLD', $pieces, true ) && ! in_array( 'WPROLENEW', $pieces, true ) && ! in_array( 'USERCREATEDWITHROLE', $pieces, true ) ) {
			return $value;
		}

		$meta_key       = join( ':', $pieces );
		$trigger_id     = absint( $pieces[0] );
		$trigger_log_id = $replace_args['trigger_log_id'];
		$user_id        = $replace_args['user_id'];
		$value          = $this->get_trigger_log_meta_value( $meta_key, $trigger_id, $trigger_log_id, $user_id );

		return $value;
	}

	/**
	 * @param $meta_key
	 * @param $trigger_id
	 * @param $trigger_log_id
	 * @param null $user_id
	 *
	 * @return mixed|string
	 */
	public function get_trigger_log_meta_value( $meta_key, $trigger_id, $trigger_log_id, $user_id = null ) {
		if ( empty( $meta_key ) || empty( $trigger_id ) || empty( $trigger_log_id ) ) {
			return '';
		}
		global $wpdb;
		$qry        = $wpdb->prepare( "SELECT meta_value
														FROM {$wpdb->prefix}uap_trigger_log_meta
														WHERE 1 = 1
														AND user_id = %d
														AND meta_key = %s
														AND automator_trigger_id = %d
														AND automator_trigger_log_id = %d
														LIMIT 0,1", $user_id, $meta_key, $trigger_id, $trigger_log_id );
		$meta_value = $wpdb->get_var( $qry );
		if ( ! empty( $meta_value ) ) {
			return maybe_unserialize( $meta_value );
		}

		return '';
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
	public function parse_wp_user_fields_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {

		if ( empty( $pieces ) ) {
			return $value;
		}

		if ( in_array( 'WPUSERPROFILEUPDATED', $pieces, true ) || in_array( 'WPUSERUPDATEDMETA', $pieces, true ) ) {

			$trigger_id     = absint( $pieces[0] );
			$to_replace     = $pieces[2];
			$trigger_log_id = $replace_args['trigger_log_id'];
			$user_id        = $replace_args['user_id'];
			$meta_key       = $trigger_id . ':' . $pieces[1] . ':' . $pieces[2];
			$meta_value     = $this->get_trigger_log_meta_value( $meta_key, $trigger_id, $trigger_log_id, $user_id );
			if ( ! empty( $meta_value ) ) {
				$value = maybe_serialize( $meta_value );
			}
		}

		return $value;
	}

	/**
	 * @param array $tokens
	 * @param array $args
	 *
	 * @return array
	 */
	public function wp_usermeta_possible_tokens( $tokens = array(), $args = array() ) {
		$trigger_meta = $args['meta'];
		$fields       = array(
			array(
				'tokenId'         => 'UMETAVALUE',
				'tokenName'       => __( 'Meta value', 'uncanny-automator-pro' ),
				'tokenType'       => 'text',
				'tokenIdentifier' => 'WPUSERUPDATEDMETA',
			),
		);

		$tokens = array_merge( $tokens, $fields );

		return $tokens;
	}
}
