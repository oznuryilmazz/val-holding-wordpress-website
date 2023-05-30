<!-- Custom Fields Loop -->
<?php // phpcs:ignoreFile ?>

<?php

include $this -> plugin_base() . DS . 'includes' . DS . 'variables.php';

?>

	<form action="?page=<?php echo esc_html( $this -> sections -> fields); ?>&amp;method=mass" method="post" id="Field.form" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action?', 'wp-mailinglist'); ?>')) { return false; };">
		<?php wp_nonce_field($this -> sections -> fields . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft actions">
				<a href="?page=<?php echo esc_html( $this -> sections -> fields); ?>&amp;method=order" title="<?php esc_html_e('Sort/order all your custom fields', 'wp-mailinglist'); ?>" class="button action"><i class="fa fa-sort"></i> <?php esc_html_e('Order Fields', 'wp-mailinglist'); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action" class="widefat" style="width:auto;" onchange="change_action(this.value);">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
					<optgroup  label="<?php esc_html_e('Required', 'wp-mailinglist'); ?>">
						<option value="required"><?php esc_html_e('Set as Required', 'wp-mailinglist'); ?></option>
						<option value="notrequired"><?php esc_html_e('Set as NOT Required', 'wp-mailinglist'); ?></option>
					</optgroup>
					<optgroup  label="<?php esc_html_e('Lists', 'wp-mailinglist'); ?>">
						<option value="lists"><?php esc_html_e('Assign to Specific Lists', 'wp-mailinglist'); ?></option>
						<option value="alllists"><?php esc_html_e('Assign to Always Show', 'wp-mailinglist'); ?></option>
					</optgroup>
				</select>				
				<input class="button-secondary action" type="submit" name="execute" value="<?php esc_html_e('Apply', 'wp-mailinglist'); ?>" class="button" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<span id="lists_div" style="display:none;">
			<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
				<?php foreach ($mailinglists as $list_id => $list_title) : ?>
					<label><input type="checkbox" name="mailinglists[]" value="<?php echo esc_html( $list_id); ?>" id="mailinglists_<?php echo esc_html( $list_id); ?>" /> <?php echo esc_attr($list_title); ?></label><br/>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="newsletters_error"><?php esc_html_e('No lists available', 'wp-mailinglist'); ?></p>
			<?php endif; ?>
		</span>
		
		<script type="text/javascript">
		function change_action(action) {			
			if (action == "lists") {
				jQuery('#lists_div').show();
			} else {
				jQuery('#lists_div').hide();
			}
		}
		</script>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 10;
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Title', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-slug <?php echo ($orderby == "slug") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=slug&order=' . (($orderby == "slug") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Slug', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-type <?php echo ($orderby == "type") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=type&order=' . (($orderby == "type") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Type', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-mailinglists"><?php esc_html_e('List(s)', 'wp-mailinglist'); ?></th>
					<th class="column-shortcode"><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
					<th class="column-required <?php echo ($orderby == "required") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=required&order=' . (($orderby == "required") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Required', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-validation <?php echo ($orderby == "validation") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=validation&order=' . (($orderby == "validation") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Validation', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Title', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-slug <?php echo ($orderby == "slug") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=slug&order=' . (($orderby == "slug") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Slug', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-type <?php echo ($orderby == "type") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=type&order=' . (($orderby == "type") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Type', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('List(s)', 'wp-mailinglist'); ?></th>
					<th><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
					<th class="column-required <?php echo ($orderby == "required") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=required&order=' . (($orderby == "required") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Required', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-validation <?php echo ($orderby == "validation") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=validation&order=' . (($orderby == "validation") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Validation', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($fields)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php echo sprintf(__('No custom fields were found %s', 'wp-mailinglist'), '<a class="button button-small button-secondary" href="' . admin_url('admin.php?page=' . $this -> sections -> fields . '&method=loaddefaults') . '"><i class="fa fa-check"></i> ' . __('load defaults', 'wp-mailinglist') . '</a>'); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = ''; ?>
					<?php $types = $this -> get_option('fieldtypes'); ?>
					<?php foreach ($fields as $field) : ?>
						<tr class="<?php echo $class = ($class == "") ? 'alternate' : ''; ?>" id="Field.row<?php echo esc_html( $field -> id); ?>">
							<th class="check-column">
								<?php if ($field -> slug != "email" && $field -> slug != "list") : ?>
									<input type="checkbox" name="fieldslist[]" id="checklist<?php echo esc_html( $field -> id); ?>" value="<?php echo esc_html( $field -> id); ?>" />
								<?php endif; ?>
							</th>
							<td><label for="checklist<?php echo esc_html( $field -> id); ?>"><?php echo esc_html( $field -> id); ?></label></td>
							<td>
								<strong><a href="?page=<?php echo esc_html( $this -> sections -> fields); ?>&amp;method=save&amp;id=<?php echo esc_html( $field -> id); ?>" title="<?php esc_html_e('Edit this custom field', 'wp-mailinglist'); ?>" class="row-title"><?php echo esc_attr($field -> title); ?></a></strong>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> fields . '&amp;method=save&amp;id=' . $field -> id)); ?><?php if ($field -> slug != "email" && $field -> slug != "list") : ?> |<?php endif; ?></span>
	                                <?php if ($field -> slug != "email" && $field -> slug != "list") : ?>
										<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> fields . '&amp;method=delete&amp;id=' . $field -> id, array('class' => "submitdelete", 'onclick' => "if (!confirm('" . __('Are you sure you want to delete this custom field?', 'wp-mailinglist') . "')) { return false; }"))); ?></span>
	                                <?php endif; ?>
								</div>
							</td>
							<td><label for="checklist<?php echo esc_html( $field -> id); ?>"><?php echo esc_html($field -> slug); ?></label></td>
							<td><label for="checklist<?php echo esc_html( $field -> id); ?>"><?php echo esc_html($Html -> field_type($field -> type, $field -> slug)); ?></label></td>
							<td>
								<?php if (!empty($field -> display) && $field -> display == "always") : ?>
									<?php esc_html_e('All', 'wp-mailinglist'); ?>
								<?php else : ?>
									<?php if ($lists = $FieldsList -> checkedlists_by_field($field -> id)) : ?>
										<?php $l = 1; ?>
										<?php foreach ($lists as $list_id) : ?>
											<?php if ($list_id == "0") : ?>
												<?php esc_html_e('None', 'wp-mailinglist'); ?>
											<?php else : ?>
												<?php if ($list_title = $Mailinglist -> get_title_by_id($list_id)) : ?>
													<a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=view&amp;id=<?php echo esc_html( $list_id); ?>"><?php echo esc_html( $list_title); ?></a>
													<?php if ($l < count($lists)) : ?>, <?php endif; ?>
												<?php endif; ?>
											<?php endif; ?>
											<?php $l++; ?>
										<?php endforeach; ?>
									<?php else : ?>
										<?php esc_html_e('None', 'wp-mailinglist'); ?>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td>
								<code>[newsletters_field name=<?php echo esc_html( $field -> slug); ?>]</code>
								<button type="button" class="button button-secondary button-small copy-button" data-clipboard-text="[newsletters_field name=<?php echo esc_attr(wp_unslash($field -> slug)); ?>]">
									<i class="fa fa-clipboard fa-fw"></i>
								</button>
							</td>
							<td><label for="checklist<?php echo esc_html( $field -> id); ?>"><?php echo (empty($field -> required) || $field -> required == "N") ? '<span class="newsletters_success"><i class="fa fa-times"></i>' : '<span class="newsletters_error"><i class="fa fa-check"></i>'; ?></span></label></td>
							<td>
								<?php if (empty($field -> validation) || $field -> validation == "notempty") : ?>
									<?php esc_html_e('Not Empty', 'wp-mailinglist'); ?>
								<?php else : ?>
									<?php echo esc_html($validation_rules[$field -> validation]['title']); ?>
								<?php endif; ?>
							</td>
							<td><label for="checklist<?php echo esc_html( $field -> id); ?>"><abbr title="<?php echo esc_html( $field -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($field -> modified))); ?></abbr></label></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat alignleft" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php esc_html_e('- Per Page -', 'wp-mailinglist'); ?></option>
						<?php $p = 5; ?>
						<?php while ($p < 100) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'fieldsperpage']) && $_COOKIE[$this -> pre . 'fieldsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'fieldsperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'fieldsperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'fieldsperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>fieldsperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>fieldssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>fields" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>