<?php // phpcs:ignoreFile ?>

<?php if (empty($ajax)) : ?>
	<div class="wrap newsletters">
		<h1><?php esc_html_e('Create Subscribe Form', 'wp-mailinglist'); ?></h1>
		<div style="min-width:400px;" id="newsletters_forms_createform_wrapper">
<?php endif; ?>
		<?php
			
		if ($this -> language_do()) {
			$languages = $this -> language_getlanguages();
		}	
			
		?>

		<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>

		<form onsubmit="newsletters_forms_createform(); return false;" action="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_forms_createform')) ?>" method="post" id="newsletters_forms_createform">
			<?php wp_nonce_field($this -> sections -> forms . '_createform'); ?>
			<p>
				<label for="Subscribeform_title" style="font-weight:bold;"><?php esc_html_e('Title', 'wp-mailinglist'); ?></label>
				<?php if ($this -> language_do()) : ?>
					<div id="title-tabs">
						<ul>
							<?php foreach ($languages as $language) : ?>
								<li><a href="#title-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php foreach ($languages as $language) : ?>
							<div id="title-tabs-<?php echo esc_html( $language); ?>">
								<input type="text" class="widefat" name="Subscribeform[title][<?php echo esc_html( $language); ?>]" value="" id="Subscribeform_title_<?php echo esc_html( $language); ?>" />
							</div>
						<?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#title-tabs').tabs();
						}
					});
					</script>
				<?php else : ?>
					<input class="widefat" type="text" name="Subscribeform[title]" value="" id="Subscribeform_title" />
				<?php endif; ?>
			</p>
			
			<p class="submit">
				<button type="submit" value="1" id="newsletters_forms_createform_submit" name="createform" class="button button-primary">
					<span id="newsletters_forms_createform_loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
					<?php esc_html_e('Create Form', 'wp-mailinglist'); ?></button>
				</button>
			</p>
		</form>
<?php if (empty($ajax)) : ?>
		</div>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery.colorbox.resize();
	
	<?php if (!empty($success)) : ?>
		jQuery.colorbox.close(); 
		parent.location = '<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $this -> Subscribeform() -> insertid)) ?>';
	<?php endif; ?>
	
	<?php if ($this -> language_do()) : ?>
		newsletters_focus('#Subscribeform_title_<?php echo esc_html( $languages[0]); ?>');
	<?php else : ?>
		newsletters_focus('#Subscribeform_title');
	<?php endif; ?>
});

function newsletters_forms_createform() {
	jQuery('#newsletters_forms_createform_submit').prop('disabled', true);
	jQuery('#newsletters_forms_createform_loading').show();
	
	jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_forms_createform&method=ajax',
		method: "POST",
		data: jQuery('#newsletters_forms_createform').serialize(),
	}).done(function(response) {
		jQuery('#newsletters_forms_createform_wrapper').html(response);
	}).error(function(response) {
		alert('<?php esc_html_e('Ajax call failed, please try again', 'wp-mailinglist'); ?>');
	}).always(function(response) {
		jQuery('#newsletters_forms_createform_submit').prop('disabled', false);
		jQuery('#newsletters_forms_createform_loading').hide();
	});
	
	return false;
}
</script>