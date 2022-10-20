<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input type="text" name="apikey" class="wpforms-required"
	placeholder="<?php printf( /* translators: %s - current provider name. */ esc_attr__( '%s API Key *', 'wpforms-getresponse' ), esc_html( $args['provider_name'] ) ); ?>">
<input type="text" name="label"
	placeholder="<?php printf( /* translators: %s - current provider name. */ esc_attr__( '%s Account Name', 'wpforms-getresponse' ), esc_html( $args['provider_name'] ) ); ?>">
<p class="description">
	<?php
	printf(
		wp_kses(
			/* translators: %s - URL to the GetResponse Integrations and API page. */
			__( 'An API key is required to access GetResponse web services. You can find it in <a href="%s" target="_blank" rel="noopener noreferrer">My Account &gt; Integrations &amp; API &gt; API</a>.', 'wpforms-getresponse' ),
			[
				'a' => [
					'href'   => [],
					'target' => [],
					'rel'    => [],
				],
			]
		),
		'https://app.getresponse.com/api'
	);
	?>
</p>
<p class="error hidden">
	<?php esc_html_e( 'Something went wrong while performing an AJAX request.', 'wpforms-getresponse' ); ?>
</p>
