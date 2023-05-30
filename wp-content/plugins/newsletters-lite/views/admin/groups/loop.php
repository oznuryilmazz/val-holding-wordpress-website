<?php // phpcs:ignoreFile ?>

<form onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected groups?', 'wp-mailinglist'); ?>')) { return false; }" action="?page=<?php echo esc_html( $this -> sections -> groups); ?>&amp;method=mass" method="post">
		<?php wp_nonce_field($this -> sections -> groups . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" class="widefat" style="width:auto;">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete Selected', 'wp-mailinglist'); ?></option>
				</select>
				<button value="1" type="submit" class="button-secondary" name="execute">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 5;
		
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
                    <th><?php esc_html_e('Lists', 'wp-mailinglist'); ?></th>
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
                    <th><?php esc_html_e('Lists', 'wp-mailinglist'); ?></th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($groups)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No groups were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($groups as $group) : ?>
						<?php $class = ($class == 'alternate') ? '' : 'alternate'; ?>
						<tr class="<?php echo esc_html( $class); ?>" id="grouprow<?php echo esc_html( $group -> id); ?>">
							<th class="check-column"><input id="checklist<?php echo esc_html( $group -> id); ?>" type="checkbox" name="groupslist[]" value="<?php echo esc_attr($group -> id); ?>" /></th>
							<td><label for="checklist<?php echo esc_html( $group -> id); ?>"><?php echo esc_html( $group -> id); ?></label></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> groups); ?>&amp;method=view&amp;id=<?php echo esc_html( $group -> id); ?>" title="<?php esc_html_e('View the details of this group.', 'wp-mailinglist'); ?>"><?php echo esc_html($group -> title); ?></a></strong>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> groups . '&amp;method=save&amp;id=' . $group -> id)); ?> |</span>
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> groups . '&amp;method=delete&amp;id=' . $group -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this group?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?> |</span>
									<span class="view"><?php echo ( $Html -> link(__('View', 'wp-mailinglist'), '?page=' . $this -> sections -> groups . '&amp;method=view&amp;id=' . $group -> id)); ?></span>
								</div>
							</td>
		                    <td>
		                    	<?php echo ( $Html -> link($Mailinglist -> count(array('group_id' => $group -> id)), '?page=' . $this -> sections -> groups . '&amp;method=view&amp;id=' . $group -> id . '#mailinglists')); ?>
		                    </td>
							<td><abbr title="<?php echo esc_html( $Html -> gen_date("Y-m-d H:i:s", strtotime($group -> modified))); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($group -> modified))); ?></abbr></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php esc_html_e('- Per Page -', 'wp-mailinglist'); ?></option>
						<?php $p = 5; ?>
						<?php while ($p < 100) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'groupsperpage']) && $_COOKIE[$this -> pre . 'groupsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'groupsperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'groupsperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'groupsperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>groupsperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>listssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>lists" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>