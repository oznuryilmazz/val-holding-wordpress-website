<?php

/* This is a once-off file which is used upon installation to insert templates into the database */

$email_templates = array();

$email_templates['posts'] = array(
	'subject'					=>	false,
	'message'					=>	
	'<div class="wpmlposts">
		[newsletters_post_loop]
			<div class="wpmlpost">
				<h3>[newsletters_post_anchor][newsletters_post_title][/newsletters_post_anchor]</h3>
				[newsletters_post_date_wrapper]<p><small>Posted on [newsletters_post_date format="F jS, Y"] by [newsletters_post_author]</small></p>[/newsletters_post_date_wrapper]
				<div class="wpmlpost_content">
					[newsletters_post_thumbnail]
					[newsletters_post_excerpt]
				</div>
			</div>
			<hr style="visibility:hidden; clear:both;" />
		[/newsletters_post_loop]
	</div>',
);

$email_templates['latestposts'] = array(
	'subject'					=>	false,
	'message'					=>	
	'<div class="wpmlposts">
		[newsletters_post_loop]
			<h2>[newsletters_category_heading]</h2>
			<div class="wpmlpost">
				<h3>[newsletters_post_anchor][newsletters_post_title][/newsletters_post_anchor]</h3>
				[newsletters_post_date_wrapper]<p><small>Posted on [newsletters_post_date format="F jS, Y"] by [newsletters_post_author]</small></p>[/newsletters_post_date_wrapper]
				<div class="wpmlpost_content">
					[newsletters_post_thumbnail]
					[newsletters_post_excerpt]
				</div>
			</div>
			<hr style="visibility:hidden; clear:both;" />
		[/newsletters_post_loop]
	</div>',
);

$email_templates['sendas'] = array(
	'subject'					=>	false,
	'message'					=>	
	'<div class="newsletter_posts">
		[newsletters_post_loop]
			<div class="newsletter_post newsletter_sendas">
				[newsletters_post_date_wrapper]<p><small>Posted on [newsletters_post_date format="F jS, Y"] by [newsletters_post_author]</small></p>[/newsletters_post_date_wrapper]
				<div class="newsletter_post_content">
					[newsletters_post_excerpt]
				</div>
			</div>
		[/newsletters_post_loop]
	</div>',
);

/* Subscriber confirmation email */
$email_templates['confirm'] = array(
	'subject'					=>	"Confirm Subscription",
	'message'					=>	"Good day,\r\n\r\nThank you for subscribing to the mailing list/s: [newsletters_mailinglist].\r\nPlease click the link below to activate/confirm your subscription.\r\n\r\n[newsletters_activate]\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

/* Bounce notification email to the administrator */
$email_templates['bounce'] = array(
	'subject'					=>	"Email Bounced",
	'message'					=>	"Good day,\r\n\r\nAn email has bounced.\r\nThe email address is: [newsletters_email].\r\nTotal bounces for this email/subscriber: [newsletters_bouncecount].\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

/* Unsubscribe notification to the administrator */
$email_templates['unsubscribe'] = array(
	'subject'					=>	"Unsubscription",
	'message'					=>	"Good day Administrator,\r\n\r\nA subscriber has unsubscribed from a mailing list.\r\nThe mailing list is: [newsletters_mailinglist].\r\nThe subscriber email is: [newsletters_email].\r\n\r\n[newsletters_unsubscribecomments]\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

$email_templates['unsubscribeuser'] = array(
	'subject'					=>	"You are unsubscribed",
	'message'					=>	"Your e-mail has been removed from our database.\r\nYou will no longer receive communication from us.\r\nThanks for your readership, and we hope you'll visit us again!\r\n\r\nWas this a mistake? If it was, you can [newsletters_resubscribe]",
);

$email_templates['unsubscribeconfirm'] = array(
	'subject'					=>	"Confirm Unsubscribe",
	'message'					=>	"Good day,\r\n\r\nPlease confirm your unsubscribe:\r\n\r\n[newsletters_unsubscribelink]\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

/* Expiration notification email to the subscriber */
$email_templates['expire'] = array(
	'subject'					=>	"Subscription Expired",
	'message'					=>	"Good Day,\r\n\r\nYour subscription has expired.\r\nThe mailing list is: [newsletters_mailinglist].\r\nPlease click the link below to renew your subscription.\r\n\r\n[newsletters_activate]\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

/* New order notification email sent to the administrator */
$email_templates['order'] = array(
	'subject'					=>	"Paid Subscription",
	'message'					=>	"Good day Administrator,\r\n\r\nYou have received a paid mailing list subscription order.\r\nThe mailing list is: [newsletters_mailinglist].\r\nThe subscriber email is: [newsletters_email].\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

/* Schedule notification email sent to the administrator */
$email_templates['schedule'] = array(
	'subject'					=>	"Email Cron Fired",
	'message'					=>	"Good day Administrator,\r\n\r\nYour email cron has been fired as scheduled.\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

$email_templates['import_complete'] = array(
	'subject'					=>	"Import Completed",
	'message'					=>	"Good day,\r\n\r\nAll the subscribers you imported have finished importing in the background.\r\n\r\nAll the best,\r\n[newsletters_blogname]"
);

$email_templates['queue_complete'] = array(
	'subject'					=>	"Queue Sending Completed",
	'message'					=>	"Good day,\r\n\r\nAll queued emails have been sent out.\r\n\r\nAll the best,\r\n[newsletters_blogname]"
);

/* Subscribe notification email sent to the administrator */
$email_templates['subscribe'] = array(
	'subject'					=>	"New Subscription",
	'message'					=>	"Good day Administrator,\r\n\r\nA user/visitor has just subscribed to: [newsletters_mailinglist].\r\nThe email address of this subscriber is: [newsletters_email].\r\n\r\n[newsletters_customfields]\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

$email_templates['authenticate'] = array(
	'subject'					=>	"Authenticate Email Address",
	'message'					=>	"Please authenticate your subscriber account by clicking the link below.\r\n\r\n[newsletters_authenticate]\r\n\r\nOnce you authenticate, you can manage your subscriptions and additional subscriber information.\r\n\r\nAll the best,\r\n[newsletters_blogname]",
);

$email_templates = apply_filters('newsletters_email_templates', $email_templates);
	
?>