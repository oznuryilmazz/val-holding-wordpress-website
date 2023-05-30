<?php // phpcs:ignoreFile ?>
<?php
	
$update = $this -> vendor('update');
$version_info = $update -> get_version_info();
	
?>

<script type="text/javascript">
var newsletters_ajaxurl = '<?php echo esc_url_raw( admin_url('admin-ajax.php')) ?>?';
</script>

<h1><i class="fa fa-envelope fa-fw"></i> <?php echo sprintf(__('%s Serial Key', 'wp-mailinglist'), $this -> name); ?></h1>

<?php if (empty($success) || $success == false) : ?>
	<?php if (!$this -> ci_serial_valid()) : ?>
        <p style="width:400px;">
        	<?php echo sprintf(__('You are running %s LITE.', 'wp-mailinglist'), $this -> name); ?>
        	<?php echo sprintf(__('To remove limits, you can submit a serial key or %s.'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">' . __('Upgrade to PRO', 'wp-mailinglist') . '</a>'); ?>
        </p>
        <p style="width:400px;">
	        <?php esc_html_e('Please obtain a serial key from the downloads section in your Tribulant account.', 'wp-mailinglist'); ?>
	        <?php esc_html_e('Once in the downloads section, click the KEY icon to request a serial key.', 'wp-mailinglist'); ?>
	        <a href="https://tribulant.com/downloads/" title="Tribulant Downloads" target="_blank"><?php esc_html_e('Downloads Section', 'wp-mailinglist'); ?></a>
        </p>
    
        <div class="newsletters_error">
            <?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
        </div>
        
        <form onsubmit="wpml_submitserial(this); return false;" action="<?php echo esc_url_raw(home_url()); ?>/index.php?wpmlmethod=submitserial" method="post">
            <p>
	            <input type="text" class="widefat" style="width:400px;" name="serialkey" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_POST['serialkey']))); ?>" /><br/>
            </p>
            <p class="submit">
            	<input type="button" class="button-secondary" name="close" onclick="jQuery.colorbox.close();" value="<?php esc_html_e('Cancel', 'wp-mailinglist'); ?>" />
            	<button value="1" type="submit" class="button-primary" name="submit" id="newsletters_submitserial_button">
            		<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Submit Serial Key', 'wp-mailinglist'); ?>
            		<span style="display:none;" id="wpml_submitserial_loading"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
            	</button>
            </p>
        </form>        
    <?php else : ?>
        <p><?php esc_html_e('Serial Key:', 'wp-mailinglist'); ?> <strong><?php echo esc_html( $this -> get_option('serialkey')); ?></strong></p>
        <p><?php esc_html_e('Your current serial is valid and working.', 'wp-mailinglist'); ?></p>
        
        <?php if (!empty($version_info['dtype']) && $version_info['dtype'] == "single") : ?>
        	<h2><?php esc_html_e('Upgrade to Unlimited', 'wp-mailinglist'); ?></h2>
        	<p><?php esc_html_e('You can upgrade one or more single domain licenses to an unlimited domains license.', 'wp-mailinglist'); ?>
        	<br/><?php esc_html_e('You only pay the difference.', 'wp-mailinglist'); ?></p>
        	<p>
	        	<a class="button" href="https://tribulant.com/items/upgrade/<?php echo esc_html( $version_info['item_id']); ?>" target="_blank"><i class="fa fa-level-up fa-fw"></i> <?php esc_html_e('Upgrade Now', 'wp-mailinglist'); ?></a>
        	</p>
        <?php endif; ?>
        
        <p>
        	<button value="1" type="button" onclick="jQuery.colorbox.close();" name="close" class="button-primary">
        		<i class="fa fa-times fa-fw"></i> <?php esc_html_e('Close', 'wp-mailinglist'); ?>
        	</button>
        	<button value="1" type="button" onclick="if (confirm('<?php esc_html_e('Are you sure you want to delete your serial key?', 'wp-mailinglist'); ?>')) { wpml_deleteserial(); } return false;" name="delete" class="button-secondary">
        		<i class="fa fa-trash fa-fw"></i> <?php esc_html_e('Delete Serial', 'wp-mailinglist'); ?>
        	</button>
        	<span style="display:none;" id="wpml_submitserial_loading"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
        </p>
    <?php endif; ?>
<?php else : ?>
    <p><?php esc_html_e('The serial key is valid and you can now continue using the Newsletter plugin. Thank you for your business and support!', 'wp-mailinglist'); ?></p>
    <p>
	    <button value="1" type="button" onclick="jQuery.colorbox.close(); parent.location = '<?php echo esc_url_raw(rtrim(get_admin_url(), '/')); ?>/admin.php?page=newsletters';" class="button-primary" name="close">
			<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Apply Serial and Close Window', 'wp-mailinglist'); ?>
	    </button>
    </p>
<?php endif; ?>