<?php // phpcs:ignoreFile ?>

<form action="?page=<?php echo esc_html( $this -> sections -> templates); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected snippets?', 'wp-mailinglist'); ?>')) { return false; }" id="templatesform" name="templatesform">
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" class="widefat" style="width:auto;">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
				</select>
				<button value="1" type="submit" class="button-secondary action" name="execute">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 6;
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" id="checkboxall" name="" value="" /></td>
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
					<th class="column-sent <?php echo ($orderby == "sent") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=sent&order=' . (($orderby == "sent") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Sent', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
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
					<td class="check-column"><input type="checkbox" id="checkboxall" name="" value="" /></td>
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
					<th class="column-sent <?php echo ($orderby == "sent") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=sent&order=' . (($orderby == "sent") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Sent', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($templates)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No snippets were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($templates as $template) : ?>
					<?php $class = ($class == "alternate") ? '' : 'alternate'; ?>
						<tr class="<?php echo esc_html( $class); ?>" id="templaterow<?php echo esc_html( $template -> id); ?>">
							<th class="check-column"><input id="checklist<?php echo esc_html( $template -> id); ?>" type="checkbox" name="templateslist[]" value="<?php echo esc_html( $template -> id); ?>" /></th>
							<td><label for="checklist<?php echo esc_html( $template -> id); ?>"><?php echo esc_html( $template -> id); ?></label></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> templates); ?>&amp;method=view&amp;id=<?php echo esc_html( $template -> id); ?>" title="<?php esc_html_e('View this template', 'wp-mailinglist'); ?>"><?php echo esc_html($template -> title); ?></a></strong>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> templates_save . '&amp;id=' . $template -> id)); ?> |</span>
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), $this -> url . '&amp;method=delete&amp;id=' . $template -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this template?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?> |</span>
									<span class="view"><?php echo ( $Html -> link(__('View', 'wp-mailinglist'), $this -> url . '&amp;method=view&amp;id=' . $template -> id)); ?> |</span>
									<span class="edit"><?php echo ( $Html -> link(__('Send', 'wp-mailinglist'), admin_url('admin.php?page=' . $this -> sections -> send . '&method=template&id=' . $template -> id))); ?></span>
								</div>
							</td>
							<td><label for="checklist<?php echo esc_html( $template -> id); ?>"><?php echo esc_html( $template -> sent); ?></label></td>
							<td>
								<code>[newsletters_snippet id="<?php echo esc_html( $template -> id); ?>"]</code>
								<button type="button" class="button button-secondary button-small copy-button" data-clipboard-text="[newsletters_snippet id=<?php echo esc_html( $template -> id); ?>]">
									<i class="fa fa-clipboard fa-fw"></i>
								</button>
							</td>
							<td><label for="checklist<?php echo esc_html( $template -> id); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($template -> modified))); ?></label></td>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'templatesperpage']) && $_COOKIE[$this -> pre . 'templatesperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'templatesperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'templatesperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'templatesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>templatesperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>templatessorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>templates" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>