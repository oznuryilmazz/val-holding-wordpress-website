<?php // phpcs:ignoreFile ?>
<?php
	
$updated = sanitize_text_field(wp_unslash($_REQUEST['updated']));
$success = sanitize_text_field(wp_unslash($_REQUEST['success']));
$error = sanitize_text_field(wp_unslash($_REQUEST['error']));
	
?>

<div class="newsletters newsletters-management-login">
	<p><?php esc_html_e('Please fill in your subscriber email address below to manage your subscriptions.', 'wp-mailinglist'); ?></p>
	
	<?php if (!empty($errors)) : ?>
		<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	<?php endif; ?>
	
	<?php if (!empty($updated)) : ?>
		<?php if (!empty($success)) : ?>
			<div class="alert alert-success">
				<i class="fa fa-check"></i> <?php echo wp_kses_post( wp_unslash($success)) ?>
			</div>
		<?php endif; ?>
		<?php if (!empty($error)) : ?>
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post( wp_unslash($error)) ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php
	
	$email = (!empty($_POST['email'])) ? sanitize_text_field(wp_unslash($_POST['email'])) : false;
	$email = (!empty($_GET['email'])) ? sanitize_text_field(wp_unslash($_GET['email'])) : $email;
	
	?>
	
	<div class="newsletters <?php echo esc_html($this -> pre); ?>" id="subscriberauthloginformdiv">
	    <form id="subscriberauthloginform" action="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=management_login&method=login', get_permalink($this -> get_managementpost()))); ?>" method="post">
	        <label><?php esc_html_e('Email Address:', 'wp-mailinglist'); ?></label>
	        <input type="text" placeholder="<?php echo esc_attr(wp_unslash(__('Enter email address', 'wp-mailinglist'))); ?>" name="email" value="<?php echo esc_attr(wp_unslash($email)); ?>" id="email" />
	        <button value="1" type="submit" name="authenticate" class="newsletters_button ui-button-primary" id="authenticate">
	        	<?php esc_html_e('Log In', 'wp-mailinglist'); ?>
	        </button>
	    </form>
	</div>
	
	<script type="text/javascript">jQuery(document).ready(function() { jQuery('input#authenticate').button(); });</script>
</div>