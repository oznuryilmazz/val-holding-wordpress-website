<?php // phpcs:ignoreFile ?>

<form action="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected templates?', 'wp-mailinglist'); ?>')) { return false; }" id="themesform">
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
		
		$colspan = 4;
		
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
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($themes)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No templates were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($themes as $theme) : ?>
					<?php $class = ($class == "alternate") ? '' : 'alternate'; ?>
						<tr class="<?php echo esc_html( $class); ?>" id="templaterow<?php echo esc_html( $theme -> id); ?>">
							<th class="check-column"><input id="checklist<?php echo esc_html( $theme -> id); ?>" type="checkbox" name="themeslist[]" value="<?php echo esc_html( $theme -> id); ?>" /></th>
							<td><label for="checklist<?php echo esc_html( $theme -> id); ?>"><?php echo esc_html( $theme -> id); ?></label></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=save&amp;id=<?php echo esc_html( $theme -> id); ?>"><?php echo esc_html( $theme -> title); ?></a></strong>
								<?php echo (!empty($theme -> defsystem) && $theme -> defsystem == "Y") ? ' <small>(' . __('System Default', 'wp-mailinglist') . ' <a onclick="if (!confirm(\'' . __('Are you sure you want to remove this template as the system default?', 'wp-mailinglist') . '\')) { return false; }" class="" href="' . admin_url('admin.php?page=' . $this -> sections -> themes . '&method=remove_defaultsystem&id=' . $theme -> id) . '"><i class="fa fa-times"></i></a>)</small> ' . $Html -> help(__('This template is used for system emails such as confirmation, unsubscribe, authentication and other system notifications.', 'wp-mailinglist')) : ''; ?>
								<?php echo (!empty($theme -> def) && $theme -> def == "Y") ? ' <small>(' . __('Send Default', 'wp-mailinglist') . ' <a onclick="if (!confirm(\'' . __('Are you sure you want to remove this template as the sending default?', 'wp-mailinglist') . '\')) { return false; }" class="" href="' . admin_url('admin.php?page=' . $this -> sections -> themes . '&method=remove_default&id=' . $theme -> id) . '"><i class="fa fa-times"></i></a>)</small> ' . $Html -> help(sprintf(__('This template is used for sending and will be pre-selected under %s > Create Newsletter for example.', 'wp-mailinglist'), $this -> name)) : ''; ?>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . '&amp;method=save&amp;id=' . $theme -> id)); ?> |</span>
									<span class="edit"><?php echo ( $Html -> link(__('Duplicate', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . '&amp;method=duplicate&amp;id=' . $theme -> id)); ?> |</span>
		                            <?php if (empty($theme -> def) || $theme -> def == "N") : ?><span class="edit"><?php echo ( $Html -> link(__('Set as Send Default', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . '&amp;method=default&amp;id=' . $theme -> id)); ?> |</span><?php endif; ?>
		                            <?php if (empty($theme -> defsystem) || $theme -> defsystem == "N") : ?><span class="edit"><?php echo ( $Html -> link(__('Set as System Default', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . '&method=defaultsystem&id=' . $theme -> id)); ?> |</span><?php endif; ?>
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . '&amp;method=delete&amp;id=' . $theme -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this template?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?> |</span>
									<span class="view"><?php echo ( $Html -> link(__('Preview', 'wp-mailinglist'), "", array('onclick' => "jQuery(this).colorbox({iframe:true, width:'80%', height:'80%', href:'" . home_url() . '/?' . $this -> pre . 'method=themepreview&id=' . $theme -> id . "'}); return false;", 'title' => __('Template Preview: ', 'wp-mailinglist') . $theme -> title))); ?></span>
								</div>
							</td>
							<td><label for="checklist<?php echo esc_html( $theme -> id); ?>"><abbr title="<?php echo esc_html( $theme -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($theme -> modified))); ?></abbr></label></td>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'themesperpage']) && $_COOKIE[$this -> pre . 'themesperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'themesperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'themesperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'themesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>themesperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>themessorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>themes" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>