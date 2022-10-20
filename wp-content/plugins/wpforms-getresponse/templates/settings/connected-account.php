<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="wpforms-clear">
	<span class="label"><?php echo esc_html( $args['label'] ); ?></span>
	<span class="date">
		<?php
		/* translators: %s - Connection date. */
		printf( esc_html__( 'Connected on: %s', 'wpforms-getresponse' ), date_i18n( get_option( 'date_format' ), $args['date'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</span>
	<span class="remove">
		<a href="#" data-provider="<?php echo esc_attr( $args['provider_slug'] ); ?>" data-key="<?php echo esc_attr( $args['key'] ); ?>">
			<?php esc_html_e( 'Disconnect', 'wpforms-getresponse' ); ?>
		</a>
	</span>
</li>
