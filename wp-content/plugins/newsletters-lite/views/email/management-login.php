<?php // phpcs:ignoreFile ?>
<p><?php esc_html_e('Please authenticate your subscriber account by clicking the link below.', 'wp-mailinglist'); ?></p>
<p><a href="<?php echo esc_url_raw($Html -> retainquery('method=loginauth&email=' . $email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true))); ?>"><?php esc_html_e('Authenticate Email Address Now', 'wp-mailinglist'); ?></a></p>
<p><?php esc_html_e('Once you authenticate, you can manage your subscriptions and additional subscriber information.', 'wp-mailinglist'); ?></p>