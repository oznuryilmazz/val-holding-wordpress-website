<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h2><?php esc_html_e('Submit Serial Key', 'wp-mailinglist'); ?></h2>
	
	<p>
		<?php esc_html_e('Please submit a serial key in the form below.', 'wp-mailinglist'); ?><br/>
		<?php echo sprintf(__('You can obtain the serial key from your %s.', 'wp-mailinglist'), '<a href="http://tribulant.com/downloads/" target="_blank">' . __('downloads section', 'wp-mailinglist') . '</a>'); ?><br/>
	</p>
	
	<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
	
	<form action="?page=<?php echo $this -> sections -> submitserial; ?>" method="post">
		<?php wp_nonce_field($this -> sections -> submitserial); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="serial"><?php esc_html_e('Serial Key', 'wp-mailinglist'); ?></label></th>
					<td>
						<input style="width:320px;" class="widefat" type="text" name="serial" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_POST['serial']))); ?>" id="serial" />
					</td>
				</tr>
			</tbody>
		</table>
	
		<p class="submit">
			<button value="1" type="submit" class="button button-primary" name="submit">
				<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Submit Serial Key', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
</div>