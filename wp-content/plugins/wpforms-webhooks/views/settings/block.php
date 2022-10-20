<?php
/**
 * Webhook settings block.
 *
 * @var array $args
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( $args['block_classes'] ); ?>" data-block-type="webhook" data-block-id="<?php echo absint( $args['id'] ); ?>">
	<div class="wpforms-builder-settings-block-header">
		<div class="wpforms-builder-settings-block-actions">
			<button class="wpforms-builder-settings-block-edit"><i class="fa fa-pencil"></i></button>
			<button class="wpforms-builder-settings-block-toggle"><?php echo wp_kses_post( $args['toggle_state'] ); ?></button>
			<button class="wpforms-builder-settings-block-delete"><i class="fa fa-times-circle"></i></button>
		</div>

		<div class="wpforms-builder-settings-block-name-holder">
			<span class="wpforms-builder-settings-block-name"><?php echo esc_html( $args['name'] ); ?></span>

			<div class="wpforms-builder-settings-block-name-edit">
				<input type="text" name="settings[webhooks][<?php echo absint( $args['id'] ); ?>][name]" value="<?php echo esc_attr( $args['name'] ); ?>">
			</div>
		</div>
	</div><!-- .wpforms-builder-settings-block-header -->

	<div class="wpforms-builder-settings-block-content" <?php echo wp_kses_post( $args['closed_state'] ); ?>>

		<?php echo $args['fields']; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	</div><!-- .wpforms-builder-settings-block-content -->

</div><!-- .wpforms-builder-settings-block -->
