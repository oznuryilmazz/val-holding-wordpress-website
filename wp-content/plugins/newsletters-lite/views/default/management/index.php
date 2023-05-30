<?php // phpcs:ignoreFile ?>
<?php $subscribedlists = array(); ?>
<?php

if (!empty($subscriber -> subscriptions)) {
	foreach ($subscriber -> subscriptions as $subscription) {
		$subscribedlists[] = $subscription -> mailinglist -> id; 	
	}
	
	$_POST['list_id'] = $subscribedlists;
}

do_action('newsletters_management_before');
$logouturl = $Html -> retainquery('method=logout', get_permalink($this -> get_managementpost()));

$updated = sanitize_text_field(wp_unslash($_REQUEST['updated']));
$success = sanitize_text_field(wp_unslash($_REQUEST['success']));
$error = sanitize_text_field(wp_unslash($_REQUEST['error']));

?>

<div class="newsletters newsletters-management">
	<?php if (is_user_logged_in()) : ?>
		<?php $current_user = wp_get_current_user(); ?>
		<?php $Db -> model = $Subscriber -> model; ?>
		<?php if ($current_user -> user_email == $subscriber -> email && $Db -> find(array('email' => $current_user -> user_email, 'user_id' => $current_user -> ID))) : ?>
			<div class="ui-state-highlight ui-corner-all">
				<p><i class="fa fa-check"></i> <?php esc_html_e('Your email is linked to your user account.', 'wp-mailinglist'); ?></p>
			</div>
			<?php $logouturl = wp_logout_url(get_permalink()); ?>
		<?php endif; ?>
	<?php endif; ?>
	
	<p class="managementemail">
		<?php esc_html_e('Your email address is:', 'wp-mailinglist'); ?> <strong><?php echo wp_kses_post( wp_unslash($subscriber -> email)) ?></strong>
	    <span class="managementlogout"><a class="newsletters_button ui-button-error" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to logout?', 'wp-mailinglist'); ?>')) { return false; }" href="<?php echo wp_kses_post($logouturl); ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Logout', 'wp-mailinglist'); ?></a></span>
	</p>
	
	<?php if (!empty($updated)) : ?>
		<?php if (!empty($success)) : ?>
			<div class="ui-state-highlight ui-corner-all">
				<p><i class="fa fa-check"></i> <?php echo wp_kses_post( wp_unslash($success)) ?></p>
			</div>
		<?php endif; ?>
		<?php if (!empty($error)) : ?>
			<div class="ui-state-error ui-corner-all">
				<p><i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post( wp_unslash($error)) ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<div class="<?php echo esc_html($this -> pre); ?>">
		<div class="newsletters-tabs" id="managementtabs">
			<ul>
		    	<?php if ($this -> get_option('managementshowsubscriptions') == "Y") : ?><li><a href="#managementtabs1"><?php esc_html_e('Current', 'wp-mailinglist'); ?></a></li><?php endif; ?>
		        <?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?><li><a href="#managementtabs2"><?php esc_html_e('Subscribe', 'wp-mailinglist'); ?></a></li><?php endif; ?>
		        <?php if ($this -> get_option('managementcustomfields') == "Y") : ?><li><a href="#managementtabs3"><?php esc_html_e('Profile', 'wp-mailinglist'); ?></a></li><?php endif; ?>
		        <?php do_action('newsletters_management_tabs_list', $subscriber); ?>
		    </ul>
		    
		    <?php if ($this -> get_option('managementshowsubscriptions') == "Y") : ?>
			    <div id="managementtabs1">
			    	<div id="currentsubscriptions">
						<?php $this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default'); ?>
			        </div>
			        
			        <br class="clear" />
			    </div>
			<?php endif; ?>
		    
		   	<?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?>    
		        <div id="managementtabs2">
					<?php $otherlists = array(); ?>
		            <?php if ($mailinglists = $Mailinglist -> select(false)) : ?>
		                <?php foreach ($mailinglists as $list_id => $list_title) : ?>
		                    <?php if (empty($subscribedlists) || (!empty($subscribedlists) && !in_array($list_id, $subscribedlists))) : ?>
		                        <?php $otherlists[] = $list_id; ?>
		                    <?php endif; ?>
		                <?php endforeach; ?>
		            <?php endif; ?>
		            
		            <?php if (true || !empty($otherlists)) : ?>
		                <div id="newsubscriptions">
		                    <?php $this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'otherlists' => $otherlists), true, 'default'); ?>
		                </div>
		            <?php endif; ?>
		            <br class="clear" />
		        </div>
	        <?php endif; ?>    
		    
		    <?php if ($this -> get_option('managementcustomfields') == "Y") : ?>
		        <div id="managementtabs3">
					<?php
		            
		            $fields = $FieldsList -> fields_by_list(sanitize_text_field(wp_unslash($_POST['list_id'])), "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
		            
		            ?>
		            <div id="savefields" class="newsletters <?php echo esc_html($this -> pre); ?>widget widget_newsletters <?php echo esc_html($this -> pre); ?>">
		                <?php $this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default'); ?>
		            </div>
		            
		            <br class="clear" />
		        </div>
		    <?php endif; ?>
		    
		    <?php do_action('newsletters_management_tabs_content', $subscriber); ?>
		</div>
		
		<br class="clear" />
	</div>
</div>

<?php do_action('newsletters_management_after'); ?>