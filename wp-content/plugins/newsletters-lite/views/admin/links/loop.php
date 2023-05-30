<?php // phpcs:ignoreFile ?>

<form action="?page=<?php echo esc_html( $this -> sections -> links); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you want to apply this action to the selected links?', 'wp-mailinglist'); ?>')) { return false; }">
		<?php wp_nonce_field($this -> sections -> links . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft actions">
				<a href="?page=<?php echo esc_html( $this -> sections -> clicks); ?>" class="button"><i class="fa fa-mouse-pointer"></i> <?php esc_html_e('Clicks', 'wp-mailinglist'); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
					<option value="reset"><?php esc_html_e('Reset', 'wp-mailinglist'); ?></option>
				</select>
				<button value="1" type="submit" name="apply" class="button">
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
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-link <?php echo ($orderby == "link") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=link&order=' . (($orderby == "link") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Link', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Clicks', 'wp-mailinglist'); ?></th>
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
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-link <?php echo ($orderby == "link") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=link&order=' . (($orderby == "link") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Link', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Clicks', 'wp-mailinglist'); ?></th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($orde) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($links)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No links were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = false; ?>
					<?php foreach ($links as $link) : ?>
						<tr class="<?php $class = (empty($class)) ? 'alternate' : false; ?>">
							<th class="check-column"><input type="checkbox" name="links[]" value="<?php echo esc_html($link -> id); ?>" id="links_<?php echo esc_html($link -> id); ?>" /></th>
							<td>
								<a href="" class="row-title"><?php echo ( $Html -> link($link -> link, $link -> link, array('target' => "_blank"))); ?></a>
								<div class="row-actions">
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> links . '&amp;method=delete&amp;id=' . $link -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this link?', 'wp-mailinglist') . "')) { return false; }", 'class' => "delete"))); ?> |</span>
									<span class="view"><?php echo ( $Html -> link(__('Open Link', 'wp-mailinglist'), $link -> link, array('target' => "_blank"))); ?></span>
								</div>
							</td>
							<td>
								<?php echo ( $Html -> link($this -> Click() -> count(array('link_id' => $link -> id)), '?page=' . $this -> sections -> clicks . '&amp;link_id=' . $link -> id)); ?>
							</td>
							<td>
								<abbr title="<?php echo esc_html($link -> modified); ?>"><?php echo esc_html( $Html -> gen_date("Y-m-d", strtotime($link -> modified))); ?></abbr>
							</td>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'linksperpage']) && $_COOKIE[$this -> pre . 'linksperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo esc_html( $p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'linksperpage'])) : ?>
							<option selected="selected" value="<?php echo esc_atr(sanitize_text_field(wp_unslash($_COOKIE[$this -> pre . 'linksperpage']))); ?>"><?php echo esc_attr(sanitize_text_field(wp_unslash($_COOKIE[$this -> pre . 'linksperpage']))); ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>linksperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_unslash(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>