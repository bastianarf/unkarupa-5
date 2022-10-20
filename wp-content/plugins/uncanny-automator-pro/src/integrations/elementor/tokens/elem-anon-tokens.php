<?php

namespace Uncanny_Automator_Pro;


/**
 * Class Elem_Anon_Tokens
 *
 * @package Uncanny_Automator_Pro
 */
class Elem_Anon_Tokens {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'ELEM';

	public function __construct() {

		//add_filter( 'automator_maybe_trigger_elem_elemform_tokens', [ $this, 'elem_possible_tokens' ], 20, 2 );
		add_filter( 'automator_maybe_parse_token', [ $this, 'elem_token' ], 20, 6 );

		// Save latest form entry in trigger meta for tokens.
		//add_action( 'automator_save_elementor_form_entry', [ $this, 'elem_save_form_entry' ], 10, 3 );
	}

	/**
	 * Only load this integration and its triggers and actions if the related
	 * plugin is active
	 *
	 * @param bool $status status of plugin.
	 * @param string $plugin plugin code.
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $plugin ) {

		if ( self::$integration === $plugin ) {
			if ( defined( 'ELEMENTOR_PRO_PATH' ) ) {
				$status = true;
			} else {
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * Prepare tokens.
	 *
	 * @param array $tokens .
	 * @param array $args .
	 *
	 * @return array
	 */
	public function elem_possible_tokens( $tokens = [], $args = [] ) {
		$form_id      = $args['value'];
		$trigger_meta = $args['meta'];

		if ( ! empty( $form_id ) ) {
			global $wpdb, $uncanny_automator;
			$query      = "SELECT ms.meta_value  FROM {$wpdb->postmeta} ms JOIN {$wpdb->posts} p on p.ID = ms.post_id WHERE ms.meta_key LIKE '_elementor_data' AND ms.meta_value LIKE '%form_fields%' AND p.post_status = 'publish' ";
			$post_metas = $wpdb->get_results( $query );
			$fields = [];
			if ( ! empty( $post_metas ) ) {
				foreach ( $post_metas as $post_meta ) {

					$inner_forms = $uncanny_automator->helpers->recipe->elementor->get_all_inner_forms( json_decode( $post_meta->meta_value ) );
					if ( ! empty( $inner_forms ) ) {
						foreach ( $inner_forms as $form ) {
							if( $form->id == $form_id ){
								if( isset($form->settings) && !empty(isset($form->settings->form_fields))){
									foreach($form->settings->form_fields as $field){
										$input_id    = $field->custom_id;
										$token_id = "$form_id|$input_id";
										$fields[]    = [
											'tokenId'         => $token_id,
											'tokenName'       => isset( $field->field_label ) ? $field->field_label : 'Unknown',
											'tokenType'       => isset( $field->field_type ) ? $field->field_type : 'text',
											'tokenIdentifier' => $trigger_meta,
										];
									}
								}
								$tokens = array_merge( $tokens, $fields );
							}
						}
					}
				}
			}
		}


		return $tokens;
	}

	/**
	 * Parse the token.
	 *
	 * @param $value
	 * @param $pieces
	 * @param $recipe_id
	 * @param $trigger_data
	 * @param $user_id
	 * @param $replace_args
	 *
	 * @return null|string
	 */
	public function elem_token( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {

		$piece = 'ELEMFORM';
		if ( $pieces ) {
			if ( 'ANONELEMSUBMITFIELD' === $pieces[1] || 'ANONELEMSUBMITFORM' === $pieces[1] || 'ELEMSUBMITFIELD' === $pieces[1] ) {
				if ( key_exists( $pieces[2], $trigger_data[0]['meta'] ) ) {
					if ( isset( $trigger_data[0]['meta'][ $pieces[2] . '_readable' ] ) ) {
						$value = $trigger_data[0]['meta'][ $pieces[2] . '_readable' ];
					} else {
						$value = $trigger_data[0]['meta'][ $pieces[2] ];
					}
				}
			}
		}

		return $value;
	}

}