<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection-block wpforms-builder-getresponse-provider-subscriber-subscribe">
	<h4><?php esc_html_e( 'Create or Update Subscriber', 'wpforms-getresponse' ); ?></h4>

	<div class="wpforms-builder-provider-connection-setting">
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
		<?php echo $args['fields']['name']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="wpforms-builder-provider-connection-settings-group wpforms-builder-getresponse-provider-subscriber-subscribe-tags">
		<div class="wpforms-builder-provider-connection-setting wpforms-builder-getresponse-provider-subscriber-subscribe-tags-add">
			<label for="wpforms-builder-getresponse-provider-{{ data.connection.id }}-subscriber-subscribe-tags-add">
				<?php esc_html_e( 'Tags To Add', 'wpforms-getresponse' ); ?>
			</label>
			<select class="js-wpforms-builder-getresponse-provider-item-select choicesjs-select" name="providers[{{ data.provider }}][{{ data.connection.id }}][tags][add][]" multiple>
					<# if ( _.isEmpty( data.tags ) ) { #><option value="" disabled><?php esc_html_e( '--- No Tags ---', 'wpforms-getresponse' ); ?></option><# } else { #><option value="" disabled><?php esc_html_e( 'Select tag(s)', 'wpforms-getresponse' ); ?></option><# } #>

					<# _.each( data.tags, function( value, key, list ) { #>
					<option value="{{ value }}"<# if ( ! _.isEmpty( data.connection.tag_names ) && _.find( data.connection.tag_names, function( tag ) { return tag == value; } ) ) { #> selected<# } #>>
						{{ value }}
					</option>
					<# } ); #>
			</select>
			<p class="description"><?php esc_html_e( 'Select one or more of the existing tags.', 'wpforms-getresponse' ); ?></p>
		</div>

		<div class="wpforms-builder-provider-connection-setting wpforms-builder-getresponse-provider-item-input">
			<label for="wpforms-builder-getresponse-provider-{{ data.connection.id }}-tags-new">
				<?php esc_html_e( 'New Tags to Add', 'wpforms-getresponse' ); ?>
			</label>
			<input type="text" value="" class="js-wpforms-builder-getresponse-provider-tags-new" name="providers[{{ data.provider }}][{{ data.connection.id }}][tags][new]" placeholder="<?php esc_attr_e( 'e.g., wpforms', 'wpforms-getresponse' ); ?>" maxlength="255">
			<p class="description">
				<?php esc_html_e( 'You can use the English alphabet, numbers, and underscores ("_"). Comma-separated list of tags is accepted. NOTE: tag name should have at least 2 characters.', 'wpforms-getresponse' ); ?>
			</p>
		</div>
	</div>

	<div class="wpforms-builder-provider-connection-setting">
		<label for="wpforms-builder-getresponse-provider-{{ data.connection.id }}-cycle-day">
			<?php esc_html_e( 'Day of Cycle', 'wpforms-getresponse' ); ?>
		</label>
		<input type="text" value="<# if ( _.has( data.connection, 'cycle_day' ) && _.isNumber( data.connection.cycle_day ) ) { #>{{ data.connection.cycle_day }}<# } #>" class="js-wpforms-builder-getresponse-provider-cycle-day" name="providers[{{ data.provider }}][{{ data.connection.id }}][cycle_day]" maxlength="4">
		<p class="description">
			<?php esc_html_e( 'The day on which the contact is in the Autoresponder cycle (0-9999).', 'wpforms-getresponse' ); ?>
		</p>
	</div>

</div>
