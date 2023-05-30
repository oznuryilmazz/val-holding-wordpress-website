<script type="text/javascript">
var unsubscribe_comments = "";
	
jQuery(document).ready(function() {	
	var hash = window.location.hash;
	var thash = hash.substring(hash.lastIndexOf('#'), hash.length);
	
	if (thash != "") {
		jQuery('#managementtabs').find('a[href*='+ thash + ']').trigger('click').closest('li').addClass('active');
	}
	
	jQuery('#managementtabs').find('a.nav-link[href^="#"]').on('click', function(e) {
		e.preventDefault();
		jQuery(this).tab('show');
		e.stopImmediatePropagation();
	});
});

function wpmlmanagement_savefields(form) {
	
	jQuery('#savefieldsbutton').prop('disabled', true);
	var formdata = jQuery('#subscribersavefieldsform').serialize();	
	jQuery('#savefieldsloading').show();
	
	jQuery('div.newsletters-field-error', form).slideUp();
	jQuery(form).find('.newsletters_fielderror').removeClass('newsletters_fielderror');
}

function newsletters_management_activate(subscriber_id, mailinglist_id, activate) {	
	if (activate == "Y") {
		jQuery('#activatelink' + mailinglist_id + ' a').addClass('disabled').html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php esc_html_e('Activating...', 'wp-mailinglist'); ?>');	
	} else {
		jQuery('tr#currentsubscription' + mailinglist_id).fadeOut(1000, function() { jQuery(this).remove(); });
		jQuery('#activatelink' + mailinglist_id + ' a').addClass('disabled').html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php esc_html_e('Removing...', 'wp-mailinglist'); ?>');
	}

	jQuery.post(newsletters_ajaxurl + "action=newsletters_managementactivate&security=<?php echo esc_html( wp_create_nonce('managementactivate')); ?>", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id, 'activate':activate, 'comments':unsubscribe_comments}, function(response) {
		jQuery('#currentsubscriptions').html(response);
		wpmlmanagement_reloadsubscriptions("new", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
	});
}

function wpmlmanagement_subscribe(subscriber_id, mailinglist_id) {
	jQuery('.subscribebutton').prop('disabled', true);
	jQuery('#subscribenowlink' + mailinglist_id + ' a').addClass('disabled').html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php esc_html_e('Subscribing...', 'wp-mailinglist'); ?>');
	
	jQuery.post(newsletters_ajaxurl + "action=managementsubscribe&security=<?php echo esc_html( wp_create_nonce('managementsubscribe')); ?>", {'subscriber_id':subscriber_id, 'mailinglist_id':mailinglist_id}, function(response) {
		wpmlmanagement_reloadsubscriptions("current", subscriber_id);
		wpmlmanagement_reloadsubscriptions("customfields", subscriber_id);
		jQuery('#newsubscriptions').html(response);
		jQuery('.subscribebutton').prop('disabled', false);
	});
}

function wpmlmanagement_reloadsubscriptions(divs, subscriber_id) {
	if (divs == "both" || divs == "current") {		
		jQuery.post(newsletters_ajaxurl + "action=managementcurrentsubscriptions&security=<?php echo esc_html( wp_create_nonce('managementcurrentsubscriptions')); ?>", {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#currentsubscriptions').html(response);
		});
	}
	
	if (divs == "both" || divs == "new") {		
		jQuery.post(newsletters_ajaxurl + "action=managementnewsubscriptions&security=<?php echo esc_html( wp_create_nonce('managementnewsubscriptions')); ?>", {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#newsubscriptions').html(response);
		});
	}
	
	if (divs == "both" || divs == "customfields") {
		jQuery.post(newsletters_ajaxurl + 'action=managementcustomfields&security=<?php echo esc_html( wp_create_nonce('managementcustomfields')); ?>', {'subscriber_id':subscriber_id}, function(response) {
			jQuery('#savefields').html(response);
		});	
	}
}
</script>