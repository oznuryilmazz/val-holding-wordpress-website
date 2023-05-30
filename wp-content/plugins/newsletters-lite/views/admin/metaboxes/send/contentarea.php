<?php // phpcs:ignoreFile ?>
<?php
												
$settings = array(
	'media_buttons'		=>	true,
	'textarea_name'		=>	'contentarea[' . $contentarea -> number . ']',
	'textarea_rows'		=>	10,
	'quicktags'			=>	true,
	'entities'			=>	"",
	'entity_encoding'	=>	"raw",
);

wp_editor(wp_unslash($contentarea -> content), 'contentarea' . $contentarea -> number, $settings); 

?>
<table id="post-status-info" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td id="wp-word-count">
				<span id="word-count">
					<?php echo sprintf(__('Use shortcode %s to display this content', 'wp-mailinglist'), '<code>[newsletters_content id="' . $contentarea -> number . '"]</code>'); ?>
					<br/><?php echo sprintf(__('And use %s to conditionally display if it is available.', 'wp-mailinglist'), '<code>[newsletters_if newsletters_content id="' . $contentarea -> number . '"]...[/newsletters_if]</code>'); ?>
				</span>
			</td>
			<td class="autosave-info">
				<span id="autosave" style="display:none;"></span>
			</td>
		</tr>
	</tbody>
</table>

<p>
	<a href="" onclick="if (confirm('<?php esc_html_e('Are you sure you want to remove this content area?', 'wp-mailinglist'); ?>')) { deletecontentarea('<?php echo esc_html( $contentarea -> number); ?>', '<?php echo esc_html( $contentarea -> history_id); ?>'); } return false;" class="button button-secondary"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
</p>

<script type="text/javascript">
contentarea++;
</script>