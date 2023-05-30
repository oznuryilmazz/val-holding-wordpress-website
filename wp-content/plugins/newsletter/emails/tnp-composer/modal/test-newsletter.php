<div class="tnp-modal2"
     id="test-newsletter-modal"
     aria-hidden="true">
    <div class="tnp-modal2__content"
         role="dialog"
         style="width: 600px">
        <header class="tnp-modal2__header">
            <h2><?php _e( "Send a test", 'newsletter' ) ?></h2>
            <span class="tnp-modal2__close"
                  data-tnp-modal-close
                  aria-label="Close modal"></span>
        </header>
        <div class="tnp-modal2__body">

            <form id="test-newsletter-form">
                <h4><?php _e( "Send a test to", 'newsletter' ) ?></h4>
                <input name="email"
                       type="email"
                       placeholder="<?php _e( "Email", 'newsletter' ) ?>"
                       id="test-newsletter-email">
                <button class="button-secondary"
                        type="submit">
					<?php _e( "Send", 'newsletter' ) ?>
                </button>
            </form>

            <div class="tnp-separator"><?php _e( "or", 'newsletter' ) ?></div>

            <div class="test-subscribers">
				<?php if ( ! empty( NewsletterUsers::instance()->get_test_users() ) ): ?>
                    <h4><?php _e( "Send a test to test subscribers", 'newsletter' ) ?></h4>
                    <ul>
						<?php foreach ( NewsletterUsers::instance()->get_test_users() as $user ) { ?>
                            <li><?php echo $user->email ?></li>
						<?php } ?>
                    </ul>
                    <button class="button-secondary"
                            onclick="tnpc_test()"><?php _e( "Send", 'newsletter' ) ?></button>
				<?php endif; ?>
                <p style="float: right">
                    <a href="https://www.thenewsletterplugin.com/documentation/subscribers#test"
                       target="_blank">
						<?php _e( 'Read more about test subscribers', 'newsletter' ) ?></a>
                </p>
            </div>

        </div>
    </div>
</div>
