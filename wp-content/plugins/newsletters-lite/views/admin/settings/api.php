<?php // phpcs:ignoreFile ?>
<!-- API -->

<?php

$api_enable = $this -> get_option('api_enable');
$api_endpoint = admin_url('admin-ajax.php?action=newsletters_api');
$api_key = $this -> get_option('api_key');
$api_hosts = $this -> get_option('api_hosts');

?>

<div class="wrap newsletters">
	<h1><?php esc_html_e('JSON API', 'wp-mailinglist'); ?></h1>
	
	<?php $this -> render('settings-navigation', false, true, 'admin'); ?>
	
	<p><?php esc_html_e('Use the JSON API to perform certain functions via API calls.', 'wp-mailinglist'); ?><br/>
	<?php esc_html_e('It can be from a remote server or from a 3rd party application, plugin, template, etc.', 'wp-mailinglist'); ?></p>
	
	<form action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_api)) ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="api_enable"><?php esc_html_e('Enable API', 'wp-mailinglist'); ?></label></th>
					<td>
						<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#api_div').show(); } else { jQuery('#api_div').hide(); }" <?php echo (!empty($api_enable)) ? 'checked="checked="' : ''; ?> type="checkbox" name="api_enable" value="1" id="api_enable" /> <?php esc_html_e('Yes, enable the API.', 'wp-mailinglist'); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div id="api_div" style="display:<?php echo (!empty($api_enable)) ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for=""><?php esc_html_e('API Endpoint', 'wp-mailinglist'); ?></label></th>
						<td>
							<code><?php echo $api_endpoint; ?></code>
							<span class="howto"><?php esc_html_e('The URL to submit API calls to', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for=""><?php esc_html_e('API Key', 'wp-mailinglist'); ?></label></th>
						<td>
							<code><span id="api_key"><?php echo esc_html( $api_key); ?></span></code>
							<a class="button button-secondary button-small" onclick="if (confirm('<?php esc_html_e('Are you sure you want to generate a new key? The previous key will stop working.', 'wp-mailinglist'); ?>')) { newsletters_api_newkey(); } return false;"><?php esc_html_e('Generate New Key', 'wp-mailinglist'); ?></a>
							<span id="api_key_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
							<span class="howto"><?php esc_html_e('Unique key to use for authentication with the API', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="api_hosts"><?php esc_html_e('Allowed Hosts', 'wp-mailinglist'); ?></label></th>
						<td>
							<select name="api_hosts[]" id="api_hosts" multiple="multiple" style="width:100%;" class="widefat">
								<?php if (!empty($api_hosts)) : ?>
									<?php foreach ($api_hosts as $host) : ?>
										<option selected="selected" value="<?php echo esc_attr(wp_unslash($host)); ?>"><?php echo esc_html( $host); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span class="howto">
								<?php esc_html_e('Specify allowed IPs that can access the API to prevent abuse and spam.', 'wp-mailinglist'); ?><br/>
								<?php esc_html_e('Leave empty/blank to allow all hosts to access the API.', 'wp-mailinglist'); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<p class="submit">
			<button value="1" type="submit" name="save" class="button button-primary">
				<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Save Settings', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	
	<h2><?php esc_html_e('Making an API Call', 'wp-mailinglist'); ?></h2>
	<p><?php esc_html_e('Below is an example of making a JSON API call', 'wp-mailinglist'); ?></p>
	
<div id="apicall">
    Please, check README file for more ingormation.
</div>

	<h2><?php esc_html_e('API Methods', 'wp-mailinglist'); ?></h2>
	
	<!-- Subscribers API methods -->
	<h3><?php esc_html_e('Subscribers', 'wp-mailinglist'); ?></h3>
	
	<h4>subscriber_add</h4>
<div id="subscriber_add">
&lt;?php
$data = array(
    'api_method'        =>   'subscriber_add', 
    'api_key'           =>   '37C1D6053E817258848E507D29CCCE49',
    'api_data'          =>   array(
        'email'             => "email@domain.com",
        'list_id'           =>   array(1,2,3),
    )
); 
 
// Success: {"success":"true","result":{"id":"123"},"method":"subscriber_add"}
// Error: {"success":"false","errormessage":"Subscriber cannot be added"}
?&gt;	
</div>
	
	<h4>subscriber_delete</h4>
<div id="subscriber_delete">
&lt;?php
$data = array(
    'api_method'        =>   'subscriber_delete', 
    'api_key'           =>   '37C1D6053E817258848E507D29CCCE49',
    'api_data'          =>   array(
        'id'                =>   '123',
    )
); 
 
// Success: {"success":"true","result":{"0":"Subscriber 123 has been deleted"},"method":"subscriber_delete"}
// Error: {"success":"false","errormessage":"Subscriber cannot be deleted"}
?&gt;
</div>
	
	<!-- Newsletters API methods -->
	<h3><?php esc_html_e('Newsletters', 'wp-mailinglist'); ?></h3>
	
	<h4>send_newsletter</h4>
	<p class="howto"><?php esc_html_e('Queue a saved history/draft newsletter by ID.', 'wp-mailinglist'); ?></p>
<div id="send_newsletter">
&lt;?php
$data = array(
    'api_method'        =>   'send_email', 
    'api_key'           =>   '37C1D6053E817258848E507D29CCCE49',
    'api_data'          =>   array(
    	'history_id'		=>	"123",
    )
);
// Success: {"success":true,"result":"8 emails have been queued successfully","method":"send_newsletter"}
// Error: {"success":"false","errormessage":"No newsletter was specified"}
?&gt;
</div>
	
	<h4>send_email</h4>
	<p class="howto"><?php esc_html_e('Send a single email to a specific email address.', 'wp-mailinglist'); ?></p>
<div id="send_email">
&lt;?php
$data = array(
    'api_method'        =>   'send_email', 
    'api_key'           =>   '37C1D6053E817258848E507D29CCCE49',
    'api_data'          =>   array(
        'email'             	=> 	"antonie@tribulant.com",
        'subject'               =>	"Test Email",
        'message'				=>	'Test Message',
    )
);                                                   
// Success: {"success":"true","result":{"0":"Email has been sent"},"method":"send_email"}
// Error: {"success":"false","errormessage":"No email was specified"}
?&gt;
</div>
</div>

<style type="text/css">
#apicall, #subscriber_add, #subscriber_delete, #send_newsletter, #send_email {
	position: relative;
	width: 100%;
	height: 300px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
	
	ace.edit('apicall', {mode: 'ace/mode/php', readOnly: true});
	ace.edit('subscriber_add', {mode: 'ace/mode/php', readOnly: true});
	ace.edit('subscriber_delete', {mode: 'ace/mode/php', readOnly: true});
	ace.edit('send_newsletter', {mode: 'ace/mode/php', readOnly: true});
	ace.edit('send_email', {mode: 'ace/mode/php', readOnly: true});
	
	jQuery('#api_hosts').select2({
		tags: true
	});
});
	
function newsletters_api_newkey() {
	jQuery('#api_key_loading').show();

	jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_api_newkey&security=<?php echo esc_html( wp_create_nonce('api_newkey')); ?>',
		success: function(response) {
			jQuery('#api_key_loading').hide();
			jQuery('#api_key').html(response);
		}
	});
}
</script>