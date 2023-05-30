<?php // phpcs:ignoreFile ?>
<?php
	
$subscribedlists = array();

if (!empty($subscriber -> subscriptions)) {
	foreach ($subscriber -> subscriptions as $subscription) {
		$subscribedlists[] = $subscription -> mailinglist -> id; 	
	}
	
	$_POST['list_id'] = $subscribedlists;
}

do_action('newsletters_management_before');
$managementshowprivate = $this -> get_option('managementshowprivate');
$managementdelete = $this -> get_option('managementdelete');
$deleteurl = $Html -> retainquery('method=delete', $this -> get_managementpost(true));
$logouturl = $Html -> retainquery('method=logout', get_permalink($this -> get_managementpost()));

$updated = isset($_REQUEST['updated']) ? esc_html(sanitize_text_field(wp_unslash($_REQUEST['updated']))) : '';
$success = isset($_REQUEST['success']) ? esc_html(sanitize_text_field(wp_unslash($_REQUEST['success']))) : '';
$error = isset($_REQUEST['error']) ? esc_html(sanitize_text_field(wp_unslash($_REQUEST['error']))) : '';

?>

<div class="newsletters newsletters-management">
	<?php if (is_user_logged_in()) : ?>
		<?php $current_user = wp_get_current_user(); ?>
		<?php $Db -> model = $Subscriber -> model; ?>
		<?php if ($current_user -> user_email == $subscriber -> email && $Db -> find(array('email' => $current_user -> user_email, 'user_id' => $current_user -> ID))) : ?>
			<div class="alert alert-success">
				<i class="fa fa-check"></i> <?php esc_html_e('Your email is linked to your user account.', 'wp-mailinglist'); ?>
			</div>
			<?php $logouturl = wp_logout_url(get_permalink()); ?>
		<?php endif; ?>
	<?php endif; ?>
	
	<p class="managementemail">
		<?php esc_html_e('Your email address is:', 'wp-mailinglist'); ?> <strong><?php echo wp_kses_post( wp_unslash($subscriber -> email)) ?></strong>
	    <span class="managementlogout"><a class="newsletters_button btn btn-warning" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to logout?', 'wp-mailinglist'); ?>')) { return false; }" href="<?php echo wp_kses_post($logouturl); ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Logout', 'wp-mailinglist'); ?></a></span>
	    <?php if (!empty($managementdelete)) : ?>
	    	<span class="managementdelete"><a class="newsletters_button btn btn-danger" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete your account?', 'wp-mailinglist'); ?>')) { return false; }" href="<?php echo wp_kses_post($deleteurl); ?>"><i class="fa fa-trash fa-fw"></i> <?php esc_html_e('Delete Account', 'wp-mailinglist'); ?></a></span>
	    <?php endif; ?>
	</p>
	
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
	
	<div class="<?php echo esc_html($this -> pre); ?> newsletters">		
		<div role="tabpanel" id="managementtabs">
		
			<!-- Nav tabs -->
			<?php $activeused = false; ?>
			<ul class="nav nav-tabs" role="tablist">
				<?php if ($this -> get_option('managementshowsubscriptions') == "Y") : ?><li role="presentation" class="nav-item"><a class="nav-link <?php if (empty($activeused)) : $activeused = true; echo ' active'; endif; ?>" aria-controls="current" role="tab" data-toggle="tab" href="#current"><?php _e('Current', 'wp-mailinglist'); ?></a></li><?php endif; ?>
				<?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?><li role="presentation" class="nav-item"><a class="nav-link <?php if (empty($activeused)) : $activeused = true; echo ' active'; endif; ?>" aria-controls="subscribe" role="tab" data-toggle="tab" href="#subscribe"><?php _e('Subscribe', 'wp-mailinglist'); ?></a></li><?php endif; ?>
				<?php if ($this -> get_option('managementcustomfields') == "Y") : ?><li role="presentation" class="nav-item"><a class="nav-link <?php if (empty($activeused)) : $activeused = true; echo ' active'; endif; ?>" aria-controls="profile" role="tab" data-toggle="tab" href="#profile"><?php _e('Profile', 'wp-mailinglist'); ?></a></li><?php endif; ?>
				<?php do_action('newsletters_management_tabs_list', $subscriber); ?>
			</ul>
			
			<!-- Tab panes -->
			<?php $activeused = false; ?>
			<div class="tab-content">
				<?php if ($this -> get_option('managementshowsubscriptions') == "Y") : ?>
				    <div role="tabpanel" class="tab-pane fade <?php if (empty($activeused)) : $activeused = true; echo 'active show'; endif; ?>" id="current">
				    	<div id="currentsubscriptions">
							<?php $this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default'); ?>
				        </div>
				    </div>
				<?php endif; ?>
			    
			   	<?php if ($this -> get_option('managementallownewsubscribes') == "Y") : ?>    
			        <div role="tabpanel" class="tab-pane fade <?php if (empty($activeused)) : $activeused = true; echo 'active show'; endif; ?>" id="subscribe">
						<?php $otherlists = array(); ?>
			            <?php if ($mailinglists = $Mailinglist -> select(((!empty($managementshowprivate)) ? true : false))) : ?>
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
			        </div>
		        <?php endif; ?>    
			    
			    <?php if ($this -> get_option('managementcustomfields') == "Y") : ?>
			        <div role="tabpanel" class="tab-pane fade <?php if (empty($activeused)) : $activeused = true; echo 'active show'; endif; ?>" id="profile">
						<?php
			            
			            $fields = $FieldsList -> fields_by_list(sanitize_text_field(wp_unslash($_POST['list_id'])), "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
			            
			            ?>
			            <div id="savefields" class="newsletters <?php echo esc_html($this -> pre); ?>widget widget_newsletters <?php echo esc_html($this -> pre); ?>">
			                <?php $this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default'); ?>
			            </div>
			        </div>
			    <?php endif; ?>
			    
			    <?php do_action('newsletters_management_tabs_content', $subscriber); ?>
			</div>
		</div>
	</div>
</div>

<?php do_action('newsletters_management_after'); ?>