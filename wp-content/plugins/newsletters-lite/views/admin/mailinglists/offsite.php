<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h2><?php esc_html_e('Offsite HTML Code', 'wp-mailinglist'); ?></h2>
	<div>
		<p><?php esc_html_e('Put the code below in the <code><HEAD></HEAD></code> section of your site', 'wp-mailinglist'); ?></p>
		<textarea onclick="this.select();" rows="5" cols="45">
            Check README for more information and examples.
        </textarea>
		
		<p><?php esc_html_e('Use the code below to create an opt-in form on any website', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('Place the code into the HTML of your site', 'wp-mailinglist'); ?></p>
		<textarea onclick="this.select();" rows="5" cols="45"><?php echo wp_kses_post($html); ?></textarea>
	</div>
</div>