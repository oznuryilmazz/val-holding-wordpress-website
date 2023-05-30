<?php // phpcs:ignoreFile ?>
<?php
	
	$updated = isset($_REQUEST['updated']) ? sanitize_text_field(wp_unslash($_REQUEST['updated'])) : '';
	$success = isset($_REQUEST['success']) ? sanitize_text_field(wp_unslash($_REQUEST['success'])) : '';
	$error = isset($_REQUEST['error']) ? sanitize_text_field(wp_unslash($_REQUEST['error'])) : '';	
	
?>

<div class="newsletters newsletters-management-login">
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

	$management_password = $this -> get_option('management_password');
	$col = (empty($management_password)) ? 12 : 6;

	?>

	<div class="row">
		<?php if (!empty($management_password)) : ?>
			<div class="col-md-<?php echo esc_html( $col); ?>">
				<div class="newsletters newsletters_management_login">
					<h2><?php esc_html_e('Login', 'wp-mailinglist'); ?></h2>
					<p><?php esc_html_e('Login with your email and password below for access:', 'wp-mailinglist'); ?></p>
					<form action="<?php echo add_query_arg(array('newsletters_method' => "management_loginp", 'method' => "login"), $this -> get_managementpost(true)); ?>" method="post">
						<div class="form-group <?php echo (!empty($errors['emailp'])) ? 'has-error' : ''; ?>">
							<label for="emailp" class="control-label"><?php esc_html_e('Email Address:', 'wp-mailinglist'); ?></label>
							<input class="form-control" type="text" name="email" value="<?php echo esc_attr(wp_unslash($email)); ?>" id="emailp" />
						</div>

						<div class="form-group <?php echo (!empty($errors['password'])) ? 'has-error' : ''; ?>">
							<label for="password" class="control-label"><?php esc_html_e('Password', 'wp-mailinglist'); ?></label>
							<input class="form-control" type="password" name="password" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_POST['password']))); ?>" id="password" />
						</div>

						<div class="form-group">
							<button value="1" type="submit" name="login" class="newsletters_button btn btn-primary">
								<?php esc_html_e('Log In', 'wp-mailinglist'); ?>
							</button>
						</div>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<div class="col-md-<?php echo esc_html( $col); ?>">
			<div class="newsletters <?php echo esc_html($this -> pre); ?>" id="subscriberauthloginformdiv">
				<h2><?php esc_html_e('Send Login Link', 'wp-mailinglist'); ?></h2>
				<p><?php esc_html_e('Please fill in your subscriber email address below to get a login link.', 'wp-mailinglist'); ?></p>
			    <form class="form-inline" id="subscriberauthloginform" action="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=management_login&method=login', get_permalink($this -> get_managementpost()))); ?>" method="post">
				    <div class="form-group <?php echo (!empty($errors['email'])) ? 'has-error' : ''; ?>">
			        	<label for="email" class="control-label"><?php esc_html_e('Email Address:', 'wp-mailinglist'); ?></label>
						<input class="form-control" type="text" placeholder="<?php echo esc_attr(wp_unslash(__('Enter email address', 'wp-mailinglist'))); ?>" name="email" value="<?php echo esc_attr(wp_unslash($email)); ?>" id="email" />
				    </div>
				    
				    <div class="form-group">
			        	<button value="1" type="submit" name="authenticate" class="newsletters_button btn btn-primary" id="authenticate">
			        		<?php esc_html_e('Send Link', 'wp-mailinglist'); ?>
			        	</button>
				    </div>
			    </form>
			</div>
		</div>
	</div>
</div>