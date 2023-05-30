<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$module = Newsletter::instance();
$controls = new NewsletterControls();

if (!$controls->is_action()) {
    $controls->data = $module->get_options('smtp');
} else {


    if ($controls->is_action('save') || $controls->is_action('test')) {

        if ($controls->data['enabled'] && empty($controls->data['host'])) {
            $controls->errors = 'The host must be set to enable the SMTP';
        }

        if (empty($controls->errors)) {
            $module->save_options($controls->data, 'smtp');
	        $controls->add_message_saved();
        }

	    if ($controls->is_action('test')) {

		    $message = NewsletterMailerAddon::get_test_message($controls->data['test_email']);
		    $r  = (new NewsletterDefaultSMTPMailer($controls->data))->send( $message );

		    if (is_wp_error($r)) {
			    $controls->errors = $r->get_error_message();
			    $controls->errors .= '<br><a href="https://www.thenewsletterplugin.com/documentation/?p=15170" target="_blank"><strong>' . __('Read more', 'newsletter') . '</strong></a>.';
		    } else {
			    $controls->messages = 'Success.';
		    }
	    }

    }

}

/*if (empty($controls->data['enabled']) && !empty($controls->data['host'])) {
	$controls->warnings[] = 'SMTP configured but NOT enabled.';
}*/

$is_invalid_configuration = empty( $controls->data['host'] );
$is_disabled              = empty( $controls->data['enabled'] );

?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

	<div id="tnp-heading">

        <h2>SMTP (obsolete)</h2>
        <p>
            This feature is obsolete. Use a third party SMTP plugin or our SMTP addon. <a href="https://www.thenewsletterplugin.com/the-new-smtp-addon" target="_blank">Read our blog post with all you need to know</a>.
        </p>
    </div>

    <div id="tnp-body">

	    <?php if ( $is_disabled || $is_invalid_configuration ): ?>
            <a href="" id="smtp-show-form"><?php _e( 'Show hidden options', 'newsletter' ) ?></a>
            <script>
                document.getElementById('smtp-show-form').addEventListener('click', function (e) {
                    e.preventDefault();
                    document.getElementById('smtp-form').classList.toggle('hidden');
                });
            </script>
	    <?php endif; ?>

	    <?php $body_classes = ( $is_disabled || $is_invalid_configuration ) ? 'hidden' : '' ?>
        <form method="post"
              action=""
              class="<?php echo $body_classes ?>"
              id="smtp-form">
		    <?php $controls->init(); ?>

            <table class="form-table">
                <tr>
                    <th>Enable the SMTP?</th>
                    <td><?php $controls->yesno( 'enabled' ); ?></td>
                </tr>
                <tr>
                    <th>SMTP host/port</th>
                    <td>
                        host: <?php $controls->text( 'host', 30 ); ?>
                        port: <?php $controls->text( 'port', 6 ); ?>
					    <?php $controls->select( 'secure', array(
						    ''    => 'No secure protocol',
						    'tls' => 'TLS protocol',
						    'ssl' => 'SSL protocol'
					    ) ); ?>
                        <p class="description">
                            Leave port empty for default value (25).<br>
                            To use GMail, do not set the SMTP here but use a <a
                                    href="https://wordpress.org/plugins/search/smtp+gmail/" target="_blank">SMTP plugin
                                which supprts oAuth 2.0</a><br>
                            On GoDaddy TRY to use "relay-hosting.secureserver.net".
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>Authentication</th>
                    <td>
                        user: <?php $controls->text( 'user', 30 ); ?>
                        password: <?php $controls->password( 'pass', 30 ); ?>
                        <p class="description">
                            If authentication is not required, leave "user" field blank.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>Insecure SSL Connections</th>
                    <td>
					    <?php $controls->yesno( 'ssl_insecure' ); ?> <a
                                href="https://www.thenewsletterplugin.com/?p=21989" target="_blank">Read more</a>.
                    </td>
                </tr>
                <tr>
                    <th>Test email address</th>
                    <td>
					    <?php $controls->text_email( 'test_email', 30 ); ?>
					    <?php $controls->button( 'test', 'Save and send test email' ); ?>
                        <p class="description">
                            If the test reports a "connection failed", review your settings and, if correct, contact
                            your provider to unlock the connection (if possible).
                        </p>
                    </td>
                </tr>
            </table>

            <p>
			    <?php $controls->button_save(); ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
