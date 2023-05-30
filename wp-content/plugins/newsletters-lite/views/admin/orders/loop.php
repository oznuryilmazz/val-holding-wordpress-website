<?php // phpcs:ignoreFile ?>

<form action="?page=<?php echo esc_html( $this -> sections -> orders); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action?', 'wp-mailinglist'); ?>')) { return false; }" id="ordersform">
		<?php wp_nonce_field($this -> sections -> orders . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft">
				<select name="action" style="width:auto;" class="widefat">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
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
		
		$colspan = 6;
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-list_id <?php echo ($orderby == "list_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=list_id&order=' . (($orderby == "list_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (empty($hide_subscriber)) : ?>
						<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_html($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th class="column-amount <?php echo ($orderby == "amount") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=amount&order=' . (($orderby == "amount") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Amount', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-pmethod <?php echo ($orderby == "pmethod") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=pmethod&order=' . (($orderby == "pmethod") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Payment Method', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-list_id <?php echo ($orderby == "list_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=list_id&order=' . (($orderby == "list_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (empty($hide_subscriber)) : ?>
						<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_html($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<th class="column-amount <?php echo ($orderby == "amount") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=amount&order=' . (($orderby == "amount") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Amount', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-pmethod <?php echo ($orderby == "pmethod") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=pmethod&order=' . (($orderby == "pmethod") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Payment Method', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($orders)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No orders were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php $class = ''; ?>
					<?php foreach ($orders as $order) : ?>
						<?php $subscriber = $Subscriber -> get($order -> subscriber_id, false); ?>
						<?php $mailinglist = $Mailinglist -> get($order -> list_id, false); ?>
						<tr id="orderrow<?php echo esc_html( $order -> id); ?>" class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" name="orderslist[]" value="<?php echo esc_html( $order -> id); ?>" id="checklist<?php echo esc_html( $order -> id); ?>" /></th>
							<td><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> orders . '&method=view&id=' . $order -> id)) ?>"><?php echo esc_html( $order -> id); ?></a></td>
							<td>
								<strong><a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=view&amp;id=<?php echo esc_html( $mailinglist -> id); ?>" title="<?php esc_html_e('View the details of this mailinglist', 'wp-mailinglist'); ?>"><?php echo esc_html($mailinglist -> title); ?></a></strong>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> orders . '&amp;method=save&amp;id=' . $order -> id)); ?> |</span>
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> orders . '&amp;method=delete&amp;id=' . $order -> id, array('class' => "submitdelete", 'onclick' => "if (!confirm('" . __('Are you sure you want to delete this order? Linked subscription will be removed as well.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
									<span class="view"><?php echo ( $Html -> link(__('View Order', 'wp-mailinglist'), '?page=' . $this -> sections -> orders . '&amp;method=view&amp;id=' . $order -> id)); ?></span>
								</div>
							</td>
							<?php if (empty($hide_subscriber)) : ?>
								<td><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>" title="<?php esc_html_e('View the details of this subscriber', 'wp-mailinglist'); ?>"><?php echo esc_html( $subscriber -> email); ?></a></td>
							<?php endif; ?>
							<td><label for="checklist<?php echo esc_html( $order -> id); ?>"><strong><?php echo esc_html( $Html -> currency()); ?><?php echo number_format($order -> amount, 2, '.', ''); ?></strong></label></td>
							<td><label for="checklist<?php echo esc_html( $order -> id); ?>"><?php echo (!empty($order -> pmethod) && $order -> pmethod == "2co") ? '2CheckOut' : 'PayPal'; ?></label></td>
							<td><label for="checklist<?php echo esc_html( $order -> id); ?>"><abbr title="<?php echo esc_html( $order -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($order -> modified))); ?></abbr></label></td>
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
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'ordersperpage']) && $_COOKIE[$this -> pre . 'ordersperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo wp_kses_post($s); ?>"><?php echo wp_kses_post($s); ?> <?php esc_html_e('orders', 'wp-mailinglist'); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'ordersperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'ordersperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'ordersperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>	
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo esc_html($this -> pre); ?>ordersperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo esc_html($this -> pre); ?>orderssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			document.cookie = "<?php echo esc_html($this -> pre); ?>orders" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
		}
		</script>
	</form>