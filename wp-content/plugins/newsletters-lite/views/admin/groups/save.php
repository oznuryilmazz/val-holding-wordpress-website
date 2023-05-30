<!-- Save a Group -->
<?php // phpcs:ignoreFile ?>

<?php
	
if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
}	
	
?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Save a Group', 'wp-mailinglist'); ?></h2>
	<form action="?page=<?php echo esc_html( $this -> sections -> groups); ?>&amp;method=save" method="post" id="groupform">
		<?php wp_nonce_field($this -> sections -> groups . '_save'); ?>
		<?php echo ( $Form -> hidden('Group[id]')); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmlGroup.title"><?php esc_html_e('Group Title', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="group-title-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#group-title-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="group-title-tabs-<?php echo esc_html( $language); ?>">
										<input placeholder="<?php echo esc_attr(wp_unslash(__('Enter group title here', 'wp-mailinglist'))); ?>" type="text" class="widefat" name="Group[title][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $this -> Group() -> data -> title))); ?>" id="Group_title_<?php echo esc_html( $language); ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#group-title-tabs').tabs();
								}
							});
							</script>
						<?php else : ?>
							<?php echo ( $Form -> text('Group[title]', array('placeholder' => __('Enter group title here', 'wp-mailinglist')))); ?>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<?php echo ( $Form -> submit(__('Save Group', 'wp-mailinglist'))); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing', 'wp-mailinglist'); ?></label>
			</div>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php if ($this -> language_do()) : ?>
		newsletters_focus('#Group_title_<?php echo esc_html( $languages[0]); ?>');
	<?php else : ?>
		newsletters_focus('#wpmlGroup\\.title');
	<?php endif; ?>
});
</script>