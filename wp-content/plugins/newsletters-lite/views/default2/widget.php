<?php // phpcs:ignoreFile ?>
<!-- Subscribe Form -->

<?php do_action('newsletters_subscribe_before_form', $instance); ?>

<?php $currentusersubscribed = $this -> get_option('currentusersubscribed'); ?>
<?php if (!empty($currentusersubscribed)) : ?>
	<?php if (is_user_logged_in()) : ?>
		<?php $current_user = wp_get_current_user(); ?>
		<?php global $wpdb; ?>
		<?php if ($wpdb -> get_row("SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `email` = '" . $current_user -> user_email . "' AND `user_id` = '" . $current_user -> ID . "'")) : ?>
			<div class="alert alert-success">
				<i class="fa fa-check"></i> <?php echo sprintf(__('You are already subscribed with email address %s. Go to the %s page to manage your subscription.', 'wp-mailinglist'), '<strong>' . $current_user -> user_email . '</strong>', '<a href="' . $this -> get_managementpost(true) . '">' . __('manage subscriptions', 'wp-mailinglist') . '</a>'); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<form action="<?php echo esc_url_raw($action); ?>" method="post" id="<?php echo esc_html( $widget_id); ?>-form" class="newsletters-form" enctype="multipart/form-data">

	<?php $hidden_values = array('ajax', 'scroll', 'captcha', 'list', 'lists'); ?>
	<?php foreach ($instance as $ikey => $ival) : ?>
		<?php if (!empty($ikey) && in_array($ikey, $hidden_values)) : ?>
			<input type="hidden" name="instance[<?php echo esc_html( $ikey); ?>]" value="<?php echo esc_attr(wp_unslash(esc_html($ival))); ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
	
	<?php do_action('newsletters_subscribe_inside_form_top', $instance); ?>

	<div id="<?php echo esc_html( $widget_id); ?>-fields" class="newsletters-form-fields">
		<?php $list_id = (empty($_POST['list_id'])) ? $instance['list'] :  sanitize_text_field(wp_unslash($_POST['list_id'])); ?>
		<?php if ($fields = $FieldsList -> fields_by_list($list_id)) : ?>
			<?php foreach ($fields as $field) : ?>
				<?php $this -> render_field($field -> id, true, $widget_id, true, true, $instance, false, $errors); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-list-checkbox').on('click', function() { newsletters_refreshfields('<?php echo esc_html( $widget_id); ?>'); });
			jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-list-select').on('change', function() { newsletters_refreshfields('<?php echo esc_html( $widget_id); ?>'); });
		});
		</script>
	</div>
	
	<?php if ($captcha_type = $this -> use_captcha(esc_html($instance['captcha']))) : ?>
		<?php if ($captcha_type == "rsc") : ?>
			<div class="form-group<?php echo (!empty($errors['captcha_code'])) ? ' has-error' : ''; ?> newsletters-fieldholder newsletters-captcha newsletters-captcha-wrapper">
		    	<?php 
		    	
		    	$captcha = new ReallySimpleCaptcha();
		    	$captcha -> bg = $Html -> hex2rgb($this -> get_option('captcha_bg')); 
		    	$captcha -> fg = $Html -> hex2rgb($this -> get_option('captcha_fg'));
		    	$captcha_size = $this -> get_option('captcha_size');
		    	$captcha -> img_size = array($captcha_size['w'], $captcha_size['h']);
		    	$captcha -> char_length = $this -> get_option('captcha_chars');
		    	$captcha -> font_size = $this -> get_option('captcha_font');
		    	$captcha_word = $captcha -> generate_random_word();
		    	$captcha_prefix = mt_rand();
		    	$captcha_filename = $captcha -> generate_image($captcha_prefix, $captcha_word);
		        $captcha_file = plugins_url() . '/really-simple-captcha/tmp/' . $captcha_filename; 
		    	
		    	?>
		    	
		    	<label class="control-label" for="<?php echo esc_html($this -> pre); ?>captcha_code"><?php esc_html_e('Please fill in the code below:', 'wp-mailinglist'); ?></label>
	            <img class="newsletters-captcha-image" src="<?php echo esc_url_raw($captcha_file); ?>" alt="captcha" />
	            <input size="<?php echo esc_attr(wp_unslash($captcha -> char_length)); ?>" <?php echo esc_html( $Html -> tabindex('newsletters-' . $widget_id)); ?> class="form-control <?php echo esc_html($this -> pre); ?>captchacode <?php echo esc_html($this -> pre); ?>text <?php echo (!empty($errors['captcha_code'])) ? 'newsletters_fielderror' : ''; ?>" type="text" name="captcha_code" id="<?php echo esc_html($this -> pre); ?>captcha_code" value="" />
	            <input type="hidden" name="captcha_prefix" value="<?php echo esc_attr($captcha_prefix); ?>" />
		    	
		    	<?php if (!empty($errors['captcha_code'])) : ?>
					<div id="newsletters-<?php echo esc_html( $number); ?>-captcha-error" class="newsletters-field-error alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post( wp_unslash($errors['captcha_code'])) ?>
					</div>
				<?php endif; ?>
			</div>
		<?php elseif ($captcha_type == "recaptcha") : ?>
			<?php $recaptcha_type = $this -> get_option('recaptcha_type'); ?>
			<div class="newsletters-captcha-wrapper form-group newsletters-fieldholder <?php echo ($recaptcha_type == "invisible") ? 'newsletters-fieldholder-hidden hidden' : ''; ?>">
				<div id="newsletters-<?php echo esc_html( $widget_id); ?>-recaptcha-challenge" class="newsletters-recaptcha-challenge my-1"></div>
				
				<?php if (!empty($errors['captcha_code'])) : ?>
					<div id="newsletters-<?php echo esc_html( $number); ?>-captcha-error" class="newsletters-field-error alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post( wp_unslash($errors['captcha_code'])) ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
    <?php endif; ?>
    
    <div class="newslettername-wrapper" style="display:none;">
    	<input type="text" name="newslettername" value="" id="<?php echo esc_html( $widget_id); ?>newslettername" class="newslettername" />
    </div>
	
	<div class="clearfix"></div>
	
	<div id="<?php echo esc_html( $widget_id); ?>-submit" class="form-group newsletters-fieldholder newsletters_submit">
		<span id="newsletters_buttonwrap">
			<button <?php echo esc_html( $Html -> tabindex($widget_id)); ?> type="submit" class="button btn btn-primary" name="subscribe" id="<?php echo esc_html( $widget_id); ?>-button">
				<span id="<?php echo esc_html( $widget_id); ?>-loading" class="newsletters-loading-wrapper" style="display:none;">
					<i class="fa fa-refresh fa-spin fa-fw"></i>
				</span>
				<?php echo esc_attr(wp_unslash(esc_html($instance['button']))); ?>
			</button>
		</span>
	</div>

	<div class="row">	
		<div class="newsletters-progress col-md-12" style="display:none;">
			<div class="progress">
				<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
					<span class="sr-only">0% Complete</span>
				</div>
			</div>
		</div>
	</div>
</form>

<?php do_action('newsletters_subscribe_after_form', $instance); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php 
	
	$ajax = esc_html($instance['ajax']);
	$scroll = esc_html($instance['scroll']);
	
	?>
	<?php if (!empty($ajax) && $ajax == "Y") : ?>
		var $progress = jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-progress');
		var $progressbar = jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-progress .progress-bar');
		var $progresspercent = jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-progress .sr-only');
	
		jQuery('#<?php echo esc_html( $widget_id); ?>-form').submit(function() {
			jQuery('#<?php echo esc_html( $widget_id); ?>-loading').show();
			
			if (jQuery('#<?php echo esc_html( $widget_id); ?>-form :file').length > 0) {
				$progress.show();
			}
			
			jQuery('#<?php echo esc_html( $widget_id); ?>-button, #<?php echo esc_html( $widget_id); ?>-form :button').prop('disabled', true);
			jQuery('#<?php echo esc_html( $widget_id); ?>-form .newsletters-fieldholder :input').attr('readonly', true);
			jQuery('div.newsletters-field-error', this).slideUp();
			jQuery(this).find('.newsletters_fielderror').removeClass('newsletters_fielderror');
		});
		
		if (jQuery.isFunction(jQuery.fn.ajaxForm)) {
			jQuery('#<?php echo esc_html( $widget_id); ?>-form').ajaxForm({
				url: newsletters_ajaxurl + 'action=wpmlsubscribe&widget=<?php echo esc_html( $widget); ?>&widget_id=<?php echo esc_html( $widget_id); ?>&number=<?php echo esc_html( $number); ?>&security=<?php echo esc_html( wp_create_nonce('subscribe')); ?>',
				data: jQuery('#<?php echo esc_html( $widget_id); ?>-form').serialize(),
				type: "POST",
				cache: false,
				beforeSend: function() {
			        var percentVal = '0%';
			        $progressbar.width(percentVal)
			        $progresspercent.html(percentVal);
			    },
			    uploadProgress: function(event, position, total, percentComplete) {
			        var percentVal = percentComplete + '%';
			        $progressbar.width(percentVal)
			        $progresspercent.html(percentVal);
			    },
				complete: function(xhr) {
					var percentVal = '100%';
			        $progressbar.width(percentVal)
			        $progresspercent.html(percentVal);
				},
				success: function(response) {				
					jQuery('#<?php echo esc_html( $widget_id); ?>-wrapper').html(response);
					<?php if (!empty($scroll)) : ?>
						wpml_scroll(jQuery('#<?php echo esc_html( $widget_id); ?>'));
					<?php endif; ?>
				}
			});
		}
	<?php endif; ?>
	
	if (jQuery.isFunction(jQuery.fn.select2)) {
		jQuery('.newsletters select').select2();
	}
	
	$recaptcha_element = jQuery('#newsletters-<?php echo esc_html( $widget_id); ?>-recaptcha-challenge');

	if (typeof grecaptcha !== 'undefined') {
		var recaptcha_options = {
			sitekey: newsletters.recaptcha_sitekey,
			theme: newsletters.recaptcha_theme,
			type: 'image',
			size: (newsletters.recaptcha_type === 'invisible' ? 'invisible' : 'normal'),
			callback: function() {							
				if (newsletters.recaptcha_type === 'invisible') {
					$form.submit();
				}
			},
			'expired-callback': function() {							
				if (typeof $recaptcha_id !== 'undefined') {
					grecaptcha.reset($recaptcha_id);
				}
			}
		};
		
		if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.render !== 'undefined') {
			$recaptcha_id = grecaptcha.render($recaptcha_element[0], recaptcha_options, true);
			$recaptcha_loaded = true;
		}
	}
	
	jQuery('input:not(:button,:submit),textarea,select').focus(function(element) {
		jQuery(this).removeClass('newsletters_fielderror').nextAll('div.newsletters-field-error').slideUp();	
	});
	
	$postpage = jQuery('.newsletters-management, .entry-content, .post-entry, .entry, .page-entry, .page-content');
	$form = $postpage.find('#<?php echo esc_html( $widget_id); ?>-form');
	$divs = $form.find('.newsletters-fieldholder:not(.newsletters_submit, .hidden)');
	for (var i = 0; i < $divs.length; i += 2) {
		$divs.slice(i, i + 2).wrapAll('<div class="row"></div>');
	}
	jQuery($divs).wrap('<div class="col-md-6"></div>');
});
</script>