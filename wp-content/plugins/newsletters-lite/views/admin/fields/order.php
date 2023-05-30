<?php // phpcs:ignoreFile ?>


<div class="wrap newsletters">
	<h2><?php esc_html_e('Order Custom Fields', 'wp-mailinglist'); ?></h2>
	
	<div class="subsubsub" style="float:none;"><?php echo ( $Html -> link(__('&larr; Manage All Fields', 'wp-mailinglist'), $this -> url)); ?></div>
	<p><?php esc_html_e('Drag and drop the custom fields below to order them.', 'wp-mailinglist'); ?></p>

	<?php if (!empty($fields)) : ?>
		<div id="message" class="updated fade" style="width:30.8%; display:none;"></div>
		<div>
			<ul id="fields">
				<?php foreach ($fields as $field) : ?>
					<li id="fields_<?php echo esc_html( $field -> id); ?>" class="newsletters_lineitem"><?php echo esc_html($field -> title); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		
		<script type="text/javascript">
		var request_fields = false;
		jQuery(document).ready(function() {
			jQuery("ul#fields").sortable({
				placeholder: 'newsletters-placeholder',
            	revert: 100,
            	distance: 5,
				start: function(event, ui) {
					if (request_fields) { request_fields.abort(); }
					jQuery('#message').slideUp();
				},
				update: function(event, ui) {
					var request_fields = jQuery.post(newsletters_ajaxurl + "action=newsletters_order_fields&security=<?php echo esc_html( wp_create_nonce('order_fields')); ?>", jQuery('ul#fields').sortable('serialize'), function(response) {
						jQuery('#message').html('<p><i class="fa fa-check"></i> ' + response + '</p>').fadeIn();
					});
				}
			});
		});
		</script>
	<?php else : ?>
		<p class="newsletters_error"><?php esc_html_e('No custom fields were found', 'wp-mailinglist'); ?></p>
	<?php endif; ?>
</div>