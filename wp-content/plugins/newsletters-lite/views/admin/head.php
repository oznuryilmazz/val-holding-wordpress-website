<?php // phpcs:ignoreFile ?>
<!-- Admin Head File for Newsletter plugin -->

<?php
	
$page = isset($_GET['page']) ? esc_html($_GET['page']) : '';
	
?>

<script type="text/javascript">
	
var $ajaxnonce_posts_by_category = '<?php echo esc_html(wp_create_nonce('posts_by_category')) ?>';
	
var wpmlAjax = '<?php echo esc_url_raw($this -> url()); ?>/<?php echo esc_html($this -> plugin_name); ?>-ajax.php';
<?php if ($this -> language_do()) : ?>
	var newsletters_ajaxurl = '<?php echo esc_url_raw( admin_url('admin-ajax.php?lang=' . $this -> language_current() . '&')) ?>';
<?php else : ?>
	var newsletters_ajaxurl = '<?php echo esc_url_raw( admin_url('admin-ajax.php?')) ?>';
<?php endif; ?>
var wpmlUrl = '<?php echo esc_url_raw($this -> url()); ?>';

<?php if (true || !empty($page) && in_array($page, (array) $this -> sections)) : ?>
	jQuery.noConflict();
	$ = jQuery.noConflict();

	jQuery(document).ready(function() {					
		
		if (typeof ClipboardJS !== 'undefined' && typeof ClipboardJS == "function") {
			var clipboard = new ClipboardJS('.newsletters .copy-button');
			
			clipboard.on('success', function(e) {								
				var button_id = e.trigger;
				jQuery(button_id).tooltip({items: button_id, content: "<?php esc_html_e('Copied!', 'wp-mailinglist'); ?>", tooltipClass: 'newsletters-ui-tooltip'});
				jQuery(button_id).tooltip("enable");
				jQuery(button_id).tooltip("open");
				
				jQuery(button_id).on('mouseout', function() {
					jQuery(button_id).tooltip('destroy');
				});
			
			    e.clearSelection();
			});
		}
		
		jQuery('.newsletters #doaction, .newsletters #doaction2').on('click', function(event) {
			if (!confirm('<?php esc_html_e('Are you sure you want to apply this action?', 'wp-mailinglist'); ?>')) {
				event.preventDefault();
				return false;
			}
		});
		
		// Color Pickers
		if (jQuery.isFunction(jQuery.fn.wpColorPicker)) {
			jQuery('.color-picker').each(function() {
				jQuery(this).wpColorPicker();
			});
		}
		
		// Select2
		<?php if (!empty($page) && in_array($page, (array) $this -> sections)) : ?>
			if (jQuery.isFunction(jQuery.fn.select2)) {
				jQuery('.newsletters select, .newsletters_select2').not('select[class*="gjs"]').not('#gjs select').not('.gjs-select select').not('.noselect').select2();
				
				jQuery('.newsletters select[name="perpage"]').select2({
					tags: true
				});
			}
		<?php endif; ?>
		
		// Tooltips
		if (jQuery.isFunction(jQuery.fn.tooltip)) {			
			jQuery(".wpmlhelp a").tooltip({
				tooltipClass: 'newsletters-ui-tooltip',
				content: function () {
		            return jQuery(this).prop('title');
		        },
		        show: {
			        delay: 500
		        }, 
		        close: function (event, ui) {
		            ui.tooltip.hover(
			            function () {
			                jQuery(this).stop(true).fadeTo(400, 1);
			            },    
			            function () {
			                jQuery(this).fadeOut("400", function () {
			                    jQuery(this).remove();
			                })
			            }
		            );
		        }
			});
		}
		
		<?php
			
		$admin_mode = get_user_option('newsletters_admin_mode', get_current_user_id());
		if (empty($admin_mode)) $admin_mode = 'standard';
			
		?>
		
		newsletters_admin_mode_switcher('<?php echo esc_html( $admin_mode); ?>', false);
		
		jQuery('.newsletters-admin-mode-standard').click(function() { newsletters_admin_mode_switcher('standard', true); return false; });
		jQuery('.newsletters-admin-mode-advanced').click(function() { newsletters_admin_mode_switcher('advanced', true); return false; });
	});
	
	function newsletters_admin_mode_switcher(mode, savemode) {		
		if (mode == "standard") {
			jQuery('.advanced-setting').hide();
			jQuery('.newsletters-admin-mode-standard').addClass('active');
			jQuery('.newsletters-admin-mode-advanced').removeClass('active');
		} else if (mode == "advanced") {
			jQuery('.advanced-setting').show();
			jQuery('.newsletters-admin-mode-advanced').addClass('active');
			jQuery('.newsletters-admin-mode-standard').removeClass('active');
		}
		
		if (savemode == true) {
			jQuery.ajax({
				method: "POST",
				data: {
					mode: mode
				},
				url: newsletters_ajaxurl + 'action=newsletters_admin_mode&security=<?php echo esc_html( wp_create_nonce('admin_mode')) ?>',
			}).done(function (response) {
				//all good...
			});
		}
	}
<?php endif; ?>
</script>