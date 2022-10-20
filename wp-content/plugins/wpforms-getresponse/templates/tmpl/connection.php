<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection" data-connection_id="{{ data.connection.id }}">
	<input type="hidden" class="wpforms-builder-provider-connection-id"
			name="providers[{{ data.provider }}][{{ data.connection.id }}][id]"
			value="{{ data.connection.id }}">

	<div class="wpforms-builder-provider-connection-title">
		{{ data.connection.name }}

		<button class="wpforms-builder-provider-connection-delete js-wpforms-builder-provider-connection-delete" type="button">
			<i class="fa fa-times-circle"></i>
		</button>

		<input type="hidden"
				id="wpforms-builder-getresponse-provider-{{ data.connection.id }}-name"
				name="providers[{{ data.provider }}][{{ data.connection.id }}][name]"
				value="{{ data.connection.name }}">
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-getresponse-provider-accounts">
		<h4><?php esc_html_e( 'Select Account', 'wpforms-getresponse' ); ?><span class="required">*</span></h4>

		<select class="js-wpforms-builder-getresponse-provider-connection-account wpforms-required" name="providers[{{ data.provider }}][{{ data.connection.id }}][account_id]"<# if ( _.isEmpty( data.accounts ) ) { #> disabled<# } #>>
			<option value="" selected disabled><?php esc_html_e( '--- Select Account ---', 'wpforms-getresponse' ); ?></option>
			<# _.each( data.accounts, function( account, option_id ) { #>
				<option value="{{ option_id }}" data-option_id="{{ option_id }}"<# if ( _.isMatch( data.connection, { account_id: option_id } ) ) { #> selected<# } #>>
					{{ account }}
				</option>
			<# } ); #>
		</select>
	</div>

	<# if ( ! _.isEmpty( data.accounts ) ) { #>
		<div class="wpforms-builder-provider-connection-block wpforms-builder-getresponse-provider-actions">
			<h4><?php esc_html_e( 'Action To Perform', 'wpforms-getresponse' ); ?><span class="required">*</span></h4>

			<select class="js-wpforms-builder-getresponse-provider-connection-action wpforms-required" name="providers[{{ data.provider }}][{{ data.connection.id }}][action]"<# if ( _.isEmpty( data.connection.account_id ) ) { #> disabled<# } #>>
				<option value="" selected disabled><?php esc_html_e( '--- Select Action ---', 'wpforms-getresponse' ); ?></option>
				<option value="subscriber_subscribe"<# if ( 'subscriber_subscribe' === data.connection.action ) { #> selected<# } #>><?php esc_html_e( 'Subscriber: Create or Update', 'wpforms-getresponse' ); ?></option>
				<option value="subscriber_unsubscribe"<# if ( 'subscriber_unsubscribe' === data.connection.action ) { #> selected<# } #>><?php esc_html_e( 'Subscriber: Unsubscribe', 'wpforms-getresponse' ); ?></option>
			</select>
		</div>
	<# } #>

	<!-- Here is where sub-templates will put its compiled HTML. -->
	<div class="wpforms-builder-getresponse-provider-actions-data"></div>

	<# if ( ! _.isEmpty( data.accounts ) ) { #>
		{{{ data.conditional }}}
	<# } #>
</div>
