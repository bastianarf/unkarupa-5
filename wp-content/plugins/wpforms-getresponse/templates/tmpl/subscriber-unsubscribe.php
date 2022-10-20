<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection-block wpforms-builder-getresponse-provider-subscriber-unsubscribe">
	<h4><?php esc_html_e( 'Unsubscribe', 'wpforms-getresponse' ); ?></h4>

	<div class="wpforms-builder-provider-connection-setting">
		<p class="description before">
			<?php esc_html_e( 'Sometimes you may want to give your users the option to unsubscribe themselves from your list(s) using your own form.', 'wpforms-getresponse' ); ?>
		</p>

		<label for="wpforms-builder-getresponse-provider-{{ data.connection.id }}-lists">
			<?php esc_html_e( 'Select List', 'wpforms-getresponse' ); ?><span class="required">*</span>
		</label>
		<select class="js-wpforms-builder-getresponse-provider-connection-lists wpforms-required" name="providers[{{ data.provider }}][{{ data.connection.id }}][list_id]"<# if ( _.isEmpty( data.lists ) ) { #> disabled<# } #>>

			<# if ( _.isEmpty( data.lists ) ) { #><option value="" selected disabled><?php esc_html_e( '--- No Lists ---', 'wpforms-getresponse' ); ?></option><# } else { #><option value="" selected disabled><?php esc_html_e( '--- Select List ---', 'wpforms-getresponse' ); ?></option><# } #>

			<# _.each( data.lists, function( value, key, list ) { #>
			<option value="{{ key }}"<# if ( data.connection.list_id === key ) { #> selected<# } #>>
				{{ value }}
			</option>
			<# } ); #>
		</select>

		<# if ( _.isEmpty( data.lists ) ) { #>
			<p class="description error-message">
				<?php esc_html_e( 'You have no lists yet. Consider creating at least one.', 'wpforms-getresponse' ); ?>
			</p>
		<# } #>
	</div>

	<div class="wpforms-builder-provider-connection-setting">
		<?php echo $args['fields']['email']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
