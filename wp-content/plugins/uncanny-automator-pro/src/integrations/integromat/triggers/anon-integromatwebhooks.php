<?php

namespace Uncanny_Automator_Pro;

use WP_REST_Response;

/**
 * Class ANON_INTEGROMATWEBHOOKS
 * @package Uncanny_Automator_Pro
 */
class ANON_INTEGROMATWEBHOOKS {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'INTEGROMAT';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'ANON_INTEGROMATWEBHOOKS';
		$this->trigger_meta = 'WEBHOOKID';//'INTEGROMATWEBHOOK';
		$this->define_trigger();

		add_action( 'rest_api_init', [ $this, 'init_rest_api' ] );
		add_action( 'rest_api_init', [ $this, 'init_rest_api_samples' ] );
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$type_options = [
			[
				'value' => 'text',
				'text'  => __( 'Text', 'uncanny-automator-pro' )
			],
			[
				'value' => 'email',
				'text'  => __( 'Email', 'uncanny-automator-pro' )
			],
			[
				'value' => 'url',
				'text'  => __( 'URL', 'uncanny-automator-pro' )
			],
			[
				'value' => 'int',
				'text'  => __( 'Integer', 'uncanny-automator-pro' )
			],
			[
				'value' => 'float',
				'text'  => __( 'Float', 'uncanny-automator-pro' )
			],
		];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/integromat/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Anonymous trigger - WP Integromat */
			'sentence'            => sprintf( __( 'Receive data from Integromat {{webhook:%1$s}}', 'uncanny-automator-pro' ), 'WEBHOOK_DATA' ),
			/* translators: Anonymous trigger - WP Integromat */
			'select_option_name'  => __( 'Receive data from Integromat {{webhook}}', 'uncanny-automator-pro' ),
			'action'              => 'uncanny_automator_pro_integromat_webhook',
			'priority'            => 10,
			'accepted_args'       => 2,
			'type'                => 'anonymous',
			'validation_function' => array( $this, 'save_data' ),
			//'options'           => [],
			'options_group'       => [
				'WEBHOOK_DATA' => [
					[
						'input_type'        => 'text',
						'option_code'       => 'WEBHOOK_URL',
						'label'             => __( 'Webhook URL', 'uncanny-automator-pro' ),
						'description'       => __( 'Send your webhook to this URL. Supports PUT, GET and POST methods.', 'uncanny-automator-pro' ),
						'required'          => true,
						'read_only'         => true,
						'copy_to_clipboard' => true,
						'default_value'     => '',
						'is_ajax'           => true,
						'endpoint'          => 'webhook_url_ANON_INTEGROMATWEBHOOKS',
					],
					[
						'option_code'       => 'WEBHOOK_FIELDS',
						'input_type'        => 'repeater',
						'label'             => __( 'Fields', 'uncanny-automator-pro' ),
						'description'       => sprintf( __( 'Manually specify the data that will be received or click the "%1$s" button to listen for a sample webhook.', 'uncanny-automator-pro' ), __( 'Get samples', 'uncanny-automator-pro' ) ),
						'required'          => true,
						'fields'            => [
							$uncanny_automator->helpers->recipe->field->text_field( 'KEY', __( 'Key', 'uncanny-automator-pro' ), false, 'text', '', true ),
							[
								'option_code' => 'VALUE_TYPE',
								'label'       => __( 'Value type', 'uncanny-automator-pro' ),
								'input_type'  => 'select',
								'required'    => true,
								'options'     => $type_options,
							]
						],
						'add_row_button'    => __( 'Add pair', 'uncanny-automator-pro' ),
						'remove_row_button' => __( 'Remove pair', 'uncanny-automator-pro' ),
					],
				],
			],
			'buttons'             => [
				[
					'show_in'     => 'WEBHOOK_DATA',
					/* translators: Button. Non-personal infinitive verb */
					'text'        => __( 'Get samples', 'uncanny-automator-pro' ),
					'css_classes' => 'uap-btn uap-btn--red',
					'on_click'    => $this->get_samples_js(),
					'modules'     => [ 'modal', 'markdown' ]
				],
			],
			'can_log_in_new_user' => false,
			'inline_css'          => $this->inline_css(),
			'filter_tokens'       => $this->filter_tokens_js()
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 * Anonymous JS function invoked as callback when clicking
	 * the custom button "Send test". The JS function requires
	 * the JS module "modal". Make sure it's included in
	 * the "modules" array
	 *
	 * @return string The JS code, with or without the <script> tags
	 */
	public function get_samples_js() {
		// Start output
		ob_start();

		// It's optional to add the <script> tags
		// This must have only one anonymous function
		?>

        <script>

            // Do when the user clicks on send test
            function ($button, data, modules) {
                // Create a configuration object
                let config = {
                    // In milliseconds, the time between each call
                    timeBetweenCalls: 1 * 1000,
                    // In milliseconds, the time we're going to check for samples
                    checkingTime: 60 * 1000,
                    // Links
                    links: {
                        noResultsSupport: "<?php echo Utilities::utm_parameters( 'https://automatorplugin.com/knowledge-base/google-sheets/', 'no_samples', 'get_help_link' ); ?>",
                    },
                    // i18n
                    i18n: {
                        checkingHooks: "<?php printf( __( "We're checking for a new hook. We'll keep trying for %s seconds.", 'uncanny-automator-pro' ), '{{time}}' ); ?>",
                        noResultsTrouble: "<?php _e( "We had trouble finding a sample.", 'uncanny-automator-pro' ); ?>",
                        noResultsSupport: "<?php _e( "See more details or get help", 'uncanny-automator-pro' ); ?>",
                        samplesModalTitle: "<?php _e( "Here is the data we've collected", 'quickbooks-training' ); ?>",
                        samplesModalWarning: "<?php /* translators: 1. Button */ printf( __( 'Clicking on \"%1$s\" will remove your current fields and will use the ones on the table above instead.', 'uncanny-automator-pro' ), '{{confirmButton}}' ); ?>",
                        samplesTableValueType: "<?php _e( "Value type", 'uncanny-automator-pro' ); ?>",
                        samplesTableReceivedData: "<?php _e( "Received data", 'uncanny-automator-pro' ); ?>",
                        samplesModalButtonConfirm: "<?php /* translators: Non-personal infinitive verb */ _e( "Use these fields", 'uncanny-automator-pro' ); ?>",
                        samplesModalButtonCancel: "<?php /* translators: Non-personal infinitive verb */ _e( "Do nothing", 'uncanny-automator-pro' ); ?>",
                    }
                }

                // Create the variable we're going to use to know if we have to keep doing calls
                let foundResults = false;

                // Get the date when this function started
                let startDate = new Date();

                // Create array with the data we're going to send
                let dataToBeSent = {
                    action: 'get_samples_ANON_INTEGROMATWEBHOOKS',
                    nonce: UncannyAutomator.nonce,

                    recipe_id: UncannyAutomator.recipe.id,
                    item_id: data.item.id,
                    webhook_url: data.values.WEBHOOK_URL
                };

                // Add notice to the item
                // Create notice
                let $notice = jQuery('<div/>', {
                    'class': 'item-options__notice item-options__notice--warning'
                });

                // Add notice message
                $notice.html(config.i18n.checkingHooks.replace('{{time}}', parseInt(config.checkingTime / 1000)));

                // Get the notices container
                let $noticesContainer = jQuery('.item[data-id="' + data.item.id + '"] .item-options__notices');

                // Add notice
                $noticesContainer.html($notice);

                // Create the function we're going to use recursively to
                // do check for the samples
                var getSamples = function () {
                    // Do AJAX call
                    jQuery.ajax({
                        method: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: dataToBeSent,

                        // Set the checking time as the timeout
                        timeout: config.checkingTime,

                        success: function (response) {
                            // Get new date
                            let currentDate = new Date();

                            // Define the default value of foundResults
                            let foundResults = false;

                            // Check if the response was successful
                            if (response.success) {
                                // Check if we got the rows from a sample
                                if (response.samples.length > 0) {
                                    // Update foundResults
                                    foundResults = true;
                                }
                            }

                            // Check if we have to do another call
                            let shouldDoAnotherCall = false;

                            // First, check if we don't have results
                            if (!foundResults) {
                                // Check if we still have time left
                                if ((currentDate.getTime() - startDate.getTime()) <= config.checkingTime) {
                                    // Update result
                                    shouldDoAnotherCall = true;
                                }
                            }

                            if (shouldDoAnotherCall) {
                                // Wait and do another call
                                setTimeout(function () {
                                    // Invoke this function again
                                    getSamples();
                                }, config.timeBetweenCalls);
                            } else {
                                // Add loading animation to the button
                                $button.removeClass('uap-btn--loading uap-btn--disabled');

                                // Check if it has results
                                if (foundResults) {
                                    // Remove notice
                                    $notice.remove();

                                    // Iterate samples and create an array with the rows
                                    let rows = [];
                                    let keys = {}
                                    jQuery.each(response.samples, function (index, sample) {
                                        // Iterate keys
                                        jQuery.each(sample, function (index, row) {
                                            // Check if the we already added this key
                                            if (typeof keys[row.key] !== 'undefined') {
                                                // Then just append the value
                                                // rows[ keys[ row.key ] ].data = rows[ keys[ row.key ] ].data + ', ' + row.data;
                                            } else {
                                                // Add row and save the index
                                                keys[row.key] = rows.push(row);
                                            }
                                        });
                                    });

                                    // Create table with the sample data
                                    let $sample = jQuery('<div><table><tbody></tbody></table></div>');

                                    // Add header
                                    /*
									let $sampleHeader = jQuery( '<thead><tr><th>' + config.i18n.samplesTableKey + '</th><th>' + config.i18n.samplesTableValueType + '</th><th>' + config.i18n.samplesTableReceivedData + '</th></tr></thead>' );
									$sample.find( 'table' ).append( $sampleHeader );
									*/

                                    // Get the body of the $sample table
                                    let $sampleBody = $sample.find('tbody');

                                    // Iterate the received sample and add rows
                                    jQuery.each(rows, function (index, row) {
                                        // Create row
                                        let $row = jQuery('<tr><td class="ANON-INTEGROMATWEBHOOKS-sample-table-td-key">' + row.key + '</td><td>' + UncannyAutomator.i18n.tokens.tokenType[row.type] + '</td><td class="ANON-INTEGROMATWEBHOOKS-sample-table-td-data">' + row.data + '</td></tr>');

                                        // Append row
                                        $sampleBody.append($row);
                                    });

                                    // Create modal box
                                    let modal = new modules.Modal({
                                        title: config.i18n.samplesModalTitle,
                                        content: $sample.html(),
                                        warning: config.i18n.samplesModalWarning.replace('{{confirmButton}}', '<strong>"' + config.i18n.samplesModalButtonConfirm + '"</strong>'),
                                        buttons: {
                                            cancel: config.i18n.samplesModalButtonCancel,
                                            confirm: config.i18n.samplesModalButtonConfirm,
                                        }
                                    }, {
                                        size: 'large'
                                    });

                                    // Set modal events
                                    modal.setEvents({
                                        onConfirm: function () {
                                            // Get the field with the fields (WEBHOOK_DATA)
                                            let webhookFields = data.item.options.WEBHOOK_DATA.fields[1];

                                            // Remove all the current fields
                                            webhookFields.fieldRows = [];

                                            // Add new rows. Iterate rows from the sample
                                            jQuery.each(rows, function (index, row) {
                                                // Add row
                                                webhookFields.addRow({
                                                    KEY: row.key,
                                                    VALUE_TYPE: row.type
                                                }, false);
                                            });

                                            // Render again
                                            webhookFields.reRender();

                                            // Destroy modal
                                            modal.destroy();
                                        },
                                    });
                                } else {
                                    // Change the notice type
                                    $notice.removeClass('item-options__notice--warning').addClass('item-options__notice--error');

                                    // Create a new notice message
                                    let noticeMessage = config.i18n.noResultsTrouble;

                                    // Change the notice message
                                    $notice.html(noticeMessage + ' ');

                                    // Add help link
                                    let $noticeHelpLink = jQuery('<a/>', {
                                        target: '_blank',
                                        href: config.links.noResultsSupport
                                    }).text(config.i18n.noResultsSupport);
                                    $notice.append($noticeHelpLink);
                                }
                            }
                        },

                        statusCode: {
                            403: function () {
                                location.reload();
                            }
                        },

                        fail: function (response) {
                        }
                    });
                }

                // Add loading animation to the button
                $button.addClass('uap-btn--loading uap-btn--disabled');

                // Try to get samples
                getSamples();
            }

        </script>

		<?php

		// Get output
		$output = ob_get_clean();

		// Return output
		return $output;
	}

	/**
	 * Anonymous JS function used to filter the tokens of this item
	 * This function will receive an object with the tokens, and it must
	 * return an object with the same structure
	 *
	 * @return string The JS code, with or without the <script> tags
	 */
	public function filter_tokens_js() {
		// Start output
		ob_start();

		// It's optional to add the <script> tags
		// This must have only one anonymous function
		?>

        <script>

            // Filters tokens
            // We will use this function to use to overwrite the tokenType of
            // the tokens created using the "key" fields with the value of
            // the "value_type" fields
            function (tokensGroup, item) {
                // Create an object where the property name is the row ID,
                // and the value is the token object of the "Value type" field on that row
                const valueTypeByRowIndex = {}
                tokensGroup.tokens.forEach((token) => {
                    // Check if the one of the tokens we're searching
                    if (token.fieldId == 'VALUE_TYPE') {
                        // Parse the token to get the tokens parts
                        const tokenParts = token.id.split(':');

                        // Get the row index
                        const rowIndex = tokenParts[3];

                        // Add it to the valueTypeByRowIndex object
                        valueTypeByRowIndex[rowIndex] = token;
                    }
                });

                // Remove the tokens created using the "Value type" field
                tokensGroup.tokens = tokensGroup.tokens.filter((token) => {
                    // Return only the ones that aren't VALUE_TYPE tokens
                    return token.fieldId !== 'VALUE_TYPE';
                });

                // Iterate tokens
                tokensGroup.tokens = tokensGroup.tokens.map((token) => {
                    // Check if it's a KEY field
                    if (token.fieldId == 'KEY') {
                        // Parse the token to get the tokens parts
                        const tokenParts = token.id.split(':');

                        // Get the row index
                        const rowIndex = tokenParts[3];

                        // Get the VALUE_TYPE token related to this token
                        const relatedValueTypeToken = valueTypeByRowIndex[rowIndex];

                        // Use the value of that token to overwrite the tokenType of
                        // this "Key" token
                        token.type = relatedValueTypeToken.value;

                        // Change the fieldId, this token is custom now
                        token.fieldId = 'CUSTOM';

                        // Update the token id. Use:
                        // ITEM_ID : TRIGGER_ID : REPEATER_FIELD_ID : TOKEN_VALUE
                        token.id = tokenParts[0] + ':' + tokenParts[1] + ':' + tokenParts[2] + ':' + token.value;

                        // Also add the key name as part of the token name
                        // It will be useful here
                        token.name = '<?php printf(
						/* translators: 1. Field ID (number), 2. Field name */
							__( 'Field #%1$s %2$s' ), '{{indexRow}}', '{{tokenValue}}' ); ?>'.replace('{{indexRow}}', (parseInt(rowIndex) + 1)).replace('{{tokenValue}}', '<strong>' + token.value + '</strong>');
                    }

                    // Return token
                    return token;
                });

                return tokensGroup;
            }

        </script>

		<?php

		// Get output
		$output = ob_get_clean();

		// Return output
		return $output;
	}

	/**
	 * A piece of CSS that it's added only when this item
	 * is on the recipe
	 *
	 * @return string The CSS, with the CSS tags
	 */
	public function inline_css() {
		// Start output
		ob_start();

		?>

        <style>

            .ANON-INTEGROMATWEBHOOKS-sample-table-td-key {
                color: #1b92e5 !important;
                font-weight: 500 !important;
            }

            .ANON-INTEGROMATWEBHOOKS-sample-table-td-data {
                color: #616161 !important;
                font-style: italic !important;
            }

        </style>

		<?php

		// Get output
		$output = ob_get_clean();

		// Return output
		return $output;
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $assignment_post_id
	 * @param $assignment_meta
	 */
	public function save_data( $param, $recipe ) {

		global $uncanny_automator;
		$user_id = 0;

		$args = [
			'code'           => $this->trigger_code,
			'meta'           => $this->trigger_meta,
			'ignore_post_id' => true,
			'webhook_recipe' => $recipe['ID'],
			'is_webhook'     => true,
			'user_id'        => $user_id,
		];

		$args = $uncanny_automator->maybe_add_trigger_entry( $args, false );

		//Adding an action for other usage of API Data.
		do_action( 'automator_api_received', $param, $recipe );

		// Save trigger meta
		if ( $args ) {
			foreach ( $args as $result ) {
				if ( true === $result['result'] && $result['args']['trigger_id'] && $result['args']['trigger_id'] == $recipe['triggers'][0]['ID'] && $result['args']['get_trigger_id'] ) {
					if ( ! empty( $param['params'] ) ) {
						$run_number = $uncanny_automator->get->trigger_run_number( $result['args']['trigger_id'], $result['args']['get_trigger_id'], $user_id );
						foreach ( $param['params'] as $data ) {
							$save_meta = [
								'user_id'        => $user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'meta_key'       => $data['meta_key'],
								'meta_value'     => $data['meta_value'],
								'run_number'     => $run_number, //get run number
								'trigger_log_id' => $result['args']['get_trigger_id'],
								'ignore_user_id' => true,
							];
							$uncanny_automator->insert_trigger_meta( $save_meta );
						}
					}
					$uncanny_automator->maybe_trigger_complete( $result['args'] );
				}
			}
		}

	}


	/**
	 *
	 */
	public function init_rest_api() {
		global $uncanny_automator;
		$recipes = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );

		$available_hooks = [];
		foreach ( $recipes as $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$option_name = 'uap-' . $recipe['ID'] . '-' . $trigger['ID'];
				$saved_hook  = get_transient( $option_name );

				if ( ! empty( $saved_hook ) ) {
					continue;
				}

				if ( key_exists( 'WEBHOOK_URL', $trigger['meta'] ) && ! empty( $trigger['meta']['WEBHOOK_URL'] ) ) {
					$_hooks = [
						'WEBHOOKID' => sprintf( 'uap-%s-%s', $recipe['ID'], $trigger['ID'] ),
					];
					if ( ! empty( $trigger['meta']['WEBHOOK_FIELDS'] ) ) {
						$_fields = ( json_decode( $trigger['meta']['WEBHOOK_FIELDS'] ) );
						if ( ! empty( $_fields ) ) {
							foreach ( $_fields as $_field ) {
								$_hooks['params'][] = [
									'key'    => $_field->KEY,
									'type'   => $_field->VALUE_TYPE,
									'format' => '',
									'items'  => [
										'type' => 'string',
									],
								];
							}
						}

					}
					$available_hooks[] = $_hooks;
				}
			}
		}

		if ( ! empty( $available_hooks ) ) {
			foreach ( $available_hooks as $hook ) {
				$args = [];
				if ( ! empty( $hook['params'] ) ) {
					foreach ( $hook['params'] as $param ) {
						$args[ $param['key'] ] = [
							'type'     => $param['type'],
							'format'   => $param['format'],
							'required' => true,
							'items'    => [
								'type' => 'string',
							],
						];
					}
				}

				register_rest_route( AUTOMATOR_REST_API_END_POINT, '/' . $hook['WEBHOOKID'],
					[
						'methods'             => [ 'POST', 'GET', 'PUT' ],
						'callback'            => [ $this, 'uap_rest_api_callback' ],
						'args'                => $args,
						'permission_callback' => function () {
							return true;
						},
					] );
			}
		}
	}

	/**
	 *
	 */
	public function init_rest_api_samples() {
		global $uncanny_automator, $wpdb;

		$sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
            FROM  $wpdb->options
            WHERE `option_name` LIKE '%transient_uap-%'
            ORDER BY `option_name`";

		$results    = $wpdb->get_results( $sql );
		$transients = array();

		foreach ( $results as $result ) {
			if ( ! empty( $result->value ) ) {
				$route_value = str_replace( 'transient_', '', $result->value );
				register_rest_route( AUTOMATOR_REST_API_END_POINT, '/' . $route_value,
					[
						'methods'             => [ 'POST', 'GET', 'PUT' ],
						'callback'            => [ $this, 'uap_rest_api_catch' ],
						'args'                => [],
						'permission_callback' => function () {
							return true;
						},
					] );
			}
		}
	}

	/**
	 * @param $data
	 *
	 * @return WP_REST_Response
	 */
	public function uap_rest_api_callback( $data ) {
		global $uncanny_automator;
		$route = $data->get_route();
		//$route_id = explode( '-', $route )[1];

		if ( $route ) {
			$recipes         = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
			$available_hooks = [];
			foreach ( $recipes as $recipe ) {
				foreach ( $recipe['triggers'] as $trigger ) {
					if ( key_exists( 'WEBHOOK_URL', $trigger['meta'] ) && false !== strpos( $trigger['meta']['WEBHOOK_URL'], $route ) ) {
						$_hooks = [
							'WEBHOOKID' => $trigger['ID'],
						];
						if ( ! empty( $trigger['meta']['WEBHOOK_FIELDS'] ) ) {
							$_fields = ( json_decode( $trigger['meta']['WEBHOOK_FIELDS'] ) );
							if ( ! empty( $_fields ) ) {
								foreach ( $_fields as $_field ) {
									$value = $data->get_param( $_field->KEY );
									if ( is_array( $value ) ) {
										$value = serialize( $value );
									}
									$_hooks['params'][] = [
										'key'        => $_field->KEY,
										'type'       => $_field->VALUE_TYPE,
										'format'     => '',
										'meta_key'   => $_field->KEY,
										'meta_value' => $value,
									];
								}
							}
						}
						$available_hooks[] = $_hooks;
						do_action( 'uncanny_automator_pro_integromat_webhook', $_hooks, $recipe );
					}
				}
			}
		}

		return new WP_REST_Response( [ 'status' => 'success' ], 200 );

	}

	/**
	 * @param $data
	 *
	 * @return WP_REST_Response
	 */
	public function uap_rest_api_catch( $data ) {
		global $uncanny_automator;

		$route  = $data->get_route();
		$params = $data->get_params();

		$route_parts = explode( '/', $route );
		$route_parts = end( $route_parts );
		$fields      = [];
		if ( ! empty( $route_parts ) ) {
			if ( false !== get_option( 'transient_' . $route_parts ) ) {
				if ( ! empty( $params ) ) {
					$field = [];
					foreach ( $params as $key => $value ) {
						$field[] = [
							'key'  => $key,
							'type' => 'text',
							'data' => $value,
						];
					}
					$fields = $field;
				}
				update_option( 'transient_' . $route_parts . '_fields', $fields );
			}
		}


		return new WP_REST_Response( [ 'status' => 'success' ], 200 );

	}
}
