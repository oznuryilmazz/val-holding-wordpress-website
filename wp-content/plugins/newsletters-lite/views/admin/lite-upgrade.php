<?php // phpcs:ignoreFile ?>
<!-- LITE Upgrade -->

<?php

$plugin_link = "https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin";

global $Db, $Mailinglist, $Subscriber;

/* List Limits */
$Db -> model = $Mailinglist -> model;
$list_count = $Db -> count();
$lists = $list_count;
$lists_percentage = (($lists / 1) * 100);

/* Subscriber Limits */
$Db -> model = $Subscriber -> model;
$subscriber_count = $Db -> count();
$subscribers = $subscriber_count;
$subscribers_percentage = (($subscribers / 500) * 100);

/* Email Limits */
$newsletters_lite = new newsletters_lite();
$emails = $newsletters_lite -> lite_current_emails_all(1000, 'monthly');
$emails_percentage = (($emails / 1000) * 100);

?>

<div class="wrap newsletters about-wrap">
	<h1><?php esc_html_e('Upgrade to Tribulant Newsletters PRO', 'wp-mailinglist'); ?></h1>
	<div class="about-text">
		<?php echo sprintf(__('Thank you for installing the %s. You are using the Tribulant Newsletters LITE plugin which contains all of the powerful features of the PRO plugin but with some limits. You can upgrade to Tribulant Newsletters PRO by submitting a serial key. If you do not have a serial key, you can buy one now.', 'wp-mailinglist'), '<a href="' . $plugin_link . '" target="_blank">' . __('Tribulant Newsletters plugin', 'wp-mailinglist') . '</a>', $this -> version); ?>
	</div>
	<div class="newsletters-badge">
		<div>
			<i class="fa fa-envelope fa-fw" style="font-size: 72px !important; color: white;"></i>
		</div>
		<?php echo sprintf('Version %s', $this -> version); ?>
	</div>
	
	<div class="changelog newsletters-changelog">
		<h3><?php esc_html_e('Upgrade to Tribulant Newsletters PRO', 'wp-mailinglist'); ?></h3>
		<p><?php esc_html_e('You can click "Submit Serial" below to activate Tribulant Newsletters PRO.', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('Alternatively, click "Buy PRO Now" to open the plugin page and buy a serial key to submit.', 'wp-mailinglist'); ?></p>
		<p>
			<a class="button button-primary button-hero" href="<?php echo esc_url_raw($plugin_link); ?>" target="_blank"><?php esc_html_e('Buy PRO Now', 'wp-mailinglist'); ?></a>
			<p><?php esc_html_e('or', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> submitserial)) ?>" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=<?php echo esc_html($this -> pre); ?>serialkey&security=<?php echo esc_html( wp_create_nonce('serialkey')); ?>'}); return false;"><?php esc_html_e('submit a serial key', 'wp-mailinglist'); ?></a></p>
		</p>
	</div>
		
	<div class="changelog newsletters-changelog">
		<div class="feature-section three-col">
			<div class="col">
				<h4><?php esc_html_e('Current Limits', 'wp-mailinglist'); ?></h4>
				<p><?php esc_html_e('Your current limits in Tribulant Newsletters LITE:', 'wp-mailinglist'); ?></p>
				<ul>
					<li><?php echo sprintf(__('<strong>%s of 1</strong> (%s&#37;) mailing lists used', 'wp-mailinglist'), $lists, $lists_percentage); ?></li>
					<li><?php echo sprintf(__('<strong>%s of 500</strong> (%s&#37;) subscribers used', 'wp-mailinglist'), $subscribers, $subscribers_percentage); ?></li>
					<li><?php echo sprintf(__('<strong>%s of 1000</strong> (%s&#37;) emails per month', 'wp-mailinglist'), $emails, $emails_percentage); ?></li>
                    <li><?php esc_html_e('No API mail type', 'wp-mailinglist'); ?></li>
                    <li><?php esc_html_e('No drag and drop newsletter & template builder', 'wp-mailinglist'); ?></li>
                    <li><?php esc_html_e('No dynamic custom fields', 'wp-mailinglist'); ?></li>
				</ul>
			</div>
			<div class="col">
				<h4><?php esc_html_e('Benefits of PRO', 'wp-mailinglist'); ?></h4>
				<p><?php esc_html_e('Tribulant Newsletters PRO gives these benefits:', 'wp-mailinglist'); ?></p>
				<ul>
					<li><?php echo sprintf(__('PRO, %s', 'wp-mailinglist'), '<a href="https://tribulant.com/support/" target="_blank">' . __('priority support', 'wp-mailinglist') . '</a>'); ?></li>
					<li><?php esc_html_e('Unlimited mailing lists', 'wp-mailinglist'); ?></li>
					<li><?php esc_html_e('Unlimited subscribers', 'wp-mailinglist'); ?></li>
					<li><?php esc_html_e('Unlimited email sending', 'wp-mailinglist'); ?></li>
                    <li><?php esc_html_e('API mail type', 'wp-mailinglist'); ?></li>
                    <li><?php esc_html_e('Drag and drop newsletter & template builder', 'wp-mailinglist'); ?></li>
                    <li><?php esc_html_e('Dynamic custom fields', 'wp-mailinglist'); ?></li>
				</ul>
			</div>
			<div class="col last-feature">
				<h4><?php esc_html_e('Upgrade to PRO', 'wp-mailinglist'); ?></h4>
				<p><?php esc_html_e('Upgrading to Tribulant Newsletters PRO is quick and easy by clicking the button below:', 'wp-mailinglist'); ?></p>
				<p><a href="<?php echo esc_url_raw($plugin_link); ?>" class="button button-primary" target="_blank"><?php esc_html_e('Buy PRO Now', 'wp-mailinglist'); ?></a></p>
				<p><?php esc_html_e('Once you have purchased a serial key, simply submit it to activate Tribulant Newsletters PRO:', 'wp-mailinglist'); ?></p>
				<p><a class="button button-secondary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> submitserial)) ?>" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=<?php echo esc_html($this -> pre); ?>serialkey&security=<?php echo esc_html( wp_create_nonce('serialkey')); ?>'}); return false;"><?php esc_html_e('Submit Serial', 'wp-mailinglist'); ?></a></p>
			</div>
		</div>
	</div>
	
	<div class="changelog newsletters-changelog">
		<h3><?php esc_html_e('About Tribulant', 'wp-mailinglist'); ?></h3>
		<p><a href="https://tribulant.com" target="_blank"><img style="max-width: 300px;" src="<?php echo esc_url_raw($this -> url()); ?>/images/logo.png" alt="tribulant" /></a></p>
		<p><?php esc_html_e('At Tribulant, we strive to provide the best WordPress plugins on the market.', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('We are a full-time business developing, promoting and supporting WordPress plugins to the community.', 'wp-mailinglist'); ?></p>
		<p>
			<a class="button button-primary button-large" target="_blank" href="https://tribulant.com"><?php esc_html_e('Visit Our Site', 'wp-mailinglist'); ?></a>
		</p>
		
		<h3><?php esc_html_e('Find Us On Social Networks', 'wp-mailinglist'); ?></h3>
		<p>
			<!-- Facebook Like -->
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=229106274013";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			
			<div class="fb-like" data-href="https://www.facebook.com/tribulantsoftware" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
			
			<!-- Twitter Follow -->
			<a href="https://twitter.com/tribulant" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @tribulant</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			
			<!-- Google+ Follow -->
			<!-- Place this tag in your head or just before your close body tag. -->
			<script src="https://apis.google.com/js/platform.js" async defer></script>
			
			<!-- Place this tag where you want the widget to render. -->
			<div class="g-follow" data-annotation="none" data-height="20" data-href="//plus.google.com/u/0/116807944061700692613" data-rel="publisher"></div>
		</p>
	</div>
</div>