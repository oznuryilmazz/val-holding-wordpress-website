<?php // phpcs:ignoreFile ?>

<form action="?page=<?php echo esc_html( $this -> sections -> clicks); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you want to apply this action to the selected clicks?', 'wp-mailinglist'); ?>')) { return false; }">
		<?php wp_nonce_field($this -> sections -> clicks . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft actions">
				<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> links)) ?>" class="button"><i class="fa fa-link"></i> <?php esc_html_e('Links', 'wp-mailinglist'); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
				</select>
				<button value="1" type="submit" name="apply" class="button">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' : sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		$colspan = 7;
		
		?>
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-link_id <?php echo ($orderby == "link_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=link_id&order=' . (($orderby == "link_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Link/Referer', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('History', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkall" value="1" id="checkall" /></td>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? esc_html($otherorder) : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-link_id <?php echo ($orderby == "link_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=link_id&order=' . (($orderby == "link_id") ? esc_html($otherorder) : "asc"))); ?>">
							<span><?php esc_html_e('Link/Referer', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('History', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($clicks)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No clicks were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = false; ?>
					<?php foreach ($clicks as $click) : ?>
						<tr class="<?php $class = (empty($class)) ? 'alternate' : false; ?>">
							<th class="check-column"><input type="checkbox" name="clicks[]" value="<?php echo esc_attr($click -> id); ?>" id="clicks_<?php echo esc_html($click -> id); ?>" /></th>
							<td>
								<?php if (!empty($click -> subscriber_id)) : ?>
									<?php
									
									$Db -> model = $Subscriber -> model;
									$subscriber = $Db -> find(array('id' => $click -> subscriber_id));
									
									?>
									
									<a href="?page=<?php echo esc_html( $this -> sections -> clicks); ?>&amp;subscriber_id=<?php echo esc_html($subscriber -> id); ?>" class="row-title"><?php echo esc_html($subscriber -> email); ?></a>
								<?php elseif (!empty($click -> user_id)) : ?>
									<?php $user = $this -> userdata($click -> user_id); ?>
									<?php esc_html_e('User:', 'wp-mailinglist'); ?> <a href="" class="row-title"><?php echo esc_html($user -> display_name); ?></a>
									<br/><small><?php echo esc_html($user -> user_email); ?></small>
								<?php else : ?>
									<?php esc_html_e('None', 'wp-mailinglist'); ?>
								<?php endif; ?>
								<div class="row-actions">
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> clicks . '&amp;method=delete&amp;id=' . $click -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this click?', 'wp-mailinglist') . "')) { return false; }", 'class' => "delete"))); ?></span>
								</div>
							</td>
							<td>
								<?php if (!empty($click -> link_id)) : ?>
									<?php $link = $this -> Link() -> find(array('id' => $click -> link_id)); ?>
									<?php echo esc_url_raw(  $Html -> link($link -> link, $link -> link, array('target' => "_blank"))); ?>
								<?php elseif (!empty($click -> referer)) : ?>
									<?php echo esc_html( $this -> Click() -> referer_name($click -> referer)); ?>
								<?php else : ?>
									<?php esc_html_e('None', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($click -> history_id)) : ?>
									<?php
									
									$history = $this -> History() -> find(array('id' => $click -> history_id));
									
									?>
									<a href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=view&amp;id=<?php echo esc_html( $history -> id); ?>"><?php echo esc_html( $history -> subject); ?></a>
								<?php else : ?>
									<?php esc_html_e('None', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
							<td>
								<abbr title="<?php echo esc_html($click -> created); ?>"><?php echo esc_html( $Html -> gen_date("Y-m-d", strtotime($click -> created))); ?></abbr>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'clicksperpage']) && $_COOKIE[$this -> pre . 'clicksperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html($p); ?>"><?php echo esc_html($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'clicksperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'clicksperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'clicksperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>clicksperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				</script>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
	</form>