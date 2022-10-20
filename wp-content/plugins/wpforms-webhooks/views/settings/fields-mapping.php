<?php
/**
 * Fields mapping table.
 *
 * @var array $args
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-field-map-table">
	<h3><?php echo wp_kses_post( $args['title'] ); ?></h3>
	<table>
		<tbody>
		<?php
		foreach ( $args['meta'] as $key => $value ) :

			$flds_name   = [
				'source' => '',
				'custom' => '',
				'secure' => '',
			];
			$extra_class = '';
			$is_custom   = false;

			$key = ( false !== $value ) ? \WPFormsWebhooks\Helpers\Formatting::sanitize_header_name( $key ) : '';

			if ( ! wpforms_is_empty_string( $key ) ) {
				$is_custom = ( 0 === strpos( $key, 'custom_' ) && is_array( $value ) );

				if ( $is_custom ) {
					$key                 = substr_replace( $key, '', 0, 7 );
					$value['value']      = ! empty( $value['secure'] ) ? \WPForms\Helpers\Crypto::decrypt( $value['value'] ) : $value['value'];
					$flds_name['custom'] = sprintf( '%1$s[custom_%2$s][value]', $args['name'], $key );
					$flds_name['secure'] = sprintf( '%1$s[custom_%2$s][secure]', $args['name'], $key );

					$extra_class = ' field-is-custom-value';

				} else {
					$flds_name['source'] = sprintf( '%1$s[%2$s]', $args['name'], $key );
				}
			}
		?>
			<tr>
				<td class="key">
					<input type="text" value="<?php echo esc_attr( $key ); ?>" placeholder="<?php esc_attr_e( 'Enter a parameter key&hellip;', 'wpforms-webhooks' ); ?>" class="http-key-source">
				</td>
				<td class="field<?php echo esc_attr( $extra_class ); ?>">
					<div class="wpforms-field-map-wrap">
						<div class="wpforms-field-map-wrap-l">
							<select class="key-destination wpforms-field-map-select" name="<?php echo esc_attr( $flds_name['source'] ); ?>" data-name="<?php echo esc_attr( $args['name'] ); ?>" data-suffix="[{source}]" data-field-map-allowed="<?php echo esc_attr( $args['allowed_types'] ); ?>" data-custom-value-support="true">
								<option value=""><?php esc_html_e( '--- Select Field ---', 'wpforms-webhooks' ); ?></option>
								<?php
								if ( ! empty( $args['fields'] ) ) {
									foreach ( $args['fields'] as $field_id => $field ) {
										$label    = ! empty( $field['label'] )
													? $field['label']
													: sprintf( /* translators: %d - field ID. */
														__( 'Field #%d', 'wpforms-webhooks' ),
														absint( $field_id )
													);
										$selected = ! $is_custom ? selected( $value, $field_id, false ) : '';
										printf( '<option value="%s" %s>%s</option>', esc_attr( $field['id'] ), esc_attr( $selected ), esc_html( $label ) );
									}
								}
								?>
								<option value="custom_value" class="wpforms-field-map-option-custom-value"><?php esc_html_e( 'Add Custom Value', 'wpforms-webhooks' ); ?></option>
							<select>
							<input class="wpforms-field-map-custom-value" name="<?php echo esc_attr( $flds_name['custom'] ); ?>" data-suffix="[custom_{source}][value]" type="<?php echo esc_attr( $is_custom && ! empty( $value['secure'] ) ? 'password' : 'text' ); ?>" placeholder="<?php esc_html_e( 'Custom Value', 'wpforms-webhooks' ); ?>" value="<?php echo esc_attr( $is_custom ? $value['value'] : '' ); ?>" <?php readonly( $is_custom && false !== $value['value'] && ! empty( $value['secure'] ), true, true ); ?>>
							<a href="#" class="wpforms-field-map-custom-value-close fa fa-close"></a>
						</div>
						<div class="wpforms-field-map-wrap-r">
							<label class="wpforms-field-map-is-secure">
								<input class="wpforms-field-map-is-secure-checkbox" name="<?php echo esc_attr( $flds_name['secure'] ); ?>" data-suffix="[custom_{source}][secure]" type="checkbox" value="1" <?php checked( $is_custom && false !== $value['value'] && ! empty( $value['secure'] ), true, true ); ?> <?php disabled( $is_custom && false !== $value['value'] && ! empty( $value['secure'] ), true, true ); ?>>
								<?php esc_html_e( 'Secure?', 'wpforms-webhooks' ); ?>
							</label>
						</div>
					</div>
				</td>
				<td class="actions">
					<a class="add" href="#"><i class="fa fa-plus-circle"></i></a>
					<a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
