<?php
/**
 * Webhooks panel section.
 *
 * @var array $args
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-panel-content-section wpforms-panel-content-section-webhooks">
	<div class="wpforms-panel-content-section-title">
		<?php esc_html_e( 'Webhooks', 'wpforms-webhooks' ); ?>
		<button type="button" class="<?php echo esc_attr( $args['add_new_btn_classes'] ); ?>" data-block-type="webhook" data-next-id="<?php echo absint( $args['next_id'] ); ?>"><?php esc_html_e( 'Add New Webhook', 'wpforms-webhooks' ); ?></button>
	</div>

	<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['enable_control_html'];
		echo $args['webhooks_html'];
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</div>
