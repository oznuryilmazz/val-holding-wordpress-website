<!-- Autoresponder Emails -->
<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h1><?php esc_html_e('Autoresponder Emails', 'wp-mailinglist'); ?></h1>
    
    <div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; Back to Autoresponders', 'wp-mailinglist'), "?page=" . $this -> sections -> autoresponders)); ?></div>
    
	<form id="posts-filter" action="<?php echo esc_url_raw($this -> url); ?>" method="post">
    	<select name="filter_status" onchange="changefilter('status', this.value);">
            <option <?php echo (!isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) || $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "" || empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) || (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "all")) ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Autoresponder Emails', 'wp-mailinglist'); ?></option>
            <option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "unsent") ? 'selected="selected"' : ''; ?> value="unsent"><?php esc_html_e('Unsent Autoresponder Emails', 'wp-mailinglist'); ?></option>
            <option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] == "sent") ? 'selected="selected"' : ''; ?> value="sent"><?php esc_html_e('Sent Autoresponder Emails', 'wp-mailinglist'); ?></option>
        </select>
        <select name="filter_autoresponder_id" onchange="changefilter('autoresponder_id', this.value);">
        	<option value=""><?php esc_html_e('All Autoresponders', 'wp-mailinglist'); ?></option>
            <?php if ($autoresponders = $this -> Autoresponder() -> select()) : ?>
            	<?php foreach ($autoresponders as $akey => $aval) : ?>
                	<option <?php echo (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id']) && $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] == $akey) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr(wp_unslash($akey)); ?>"><?php echo esc_attr($aval); ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <a href="?page=<?php echo esc_html( $this -> sections -> autoresponderemails); ?>" class="button"><?php esc_html_e('Filter', 'wp-mailinglist'); ?></a>
        <?php echo ( $Html -> help(__('Filter the autoresponder emails below by status (sent/unsent) and by specific autoresponder to find the email(s) that you might be looking for.', 'wp-mailinglist'))); ?>
        <br class="clear" />
    
    	<?php if (!empty($autoresponderemails)) : ?>
		<ul class="subsubsub">
			<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($autoresponderemails); ?> <?php esc_html_e('autoresponder emails', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $this -> url . '&showall=1')); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponderemails)); ?></li>
			<?php endif; ?>
		</ul>
        <?php endif; ?>            
        <script type="text/javascript">
		function changefilter(field, value) {				
			if (value != "") {
				document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderemailsfilter_" + field + "=" + value + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			} else {
				document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderemailsfilter_" + field + "=" + value + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("-30 days"))); ?> UTC; path=/";
			}
		}
					
		jQuery(document).ready(function() {
			<?php if (!empty($_GET['id'])) : ?>
				changefilter('autoresponder_id', '<?php echo sanitize_text_field(wp_unslash($_GET['id'])); ?>'));
			<?php endif; ?>
			<?php if (!empty($_GET['status'])) : ?>
				changefilter('status', '<?php echo sanitize_text_field(wp_unslash($_GET['status'])); ?>'));
			<?php endif; ?>
		});
		</script>
	</form>
    
    <?php $this -> render('autoresponderemails' . DS . 'loop', array('autoresponderemails' => $autoresponderemails, 'paginate' => $paginate), true, 'admin'); ?>
</div>