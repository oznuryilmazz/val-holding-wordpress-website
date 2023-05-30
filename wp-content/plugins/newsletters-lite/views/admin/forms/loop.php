<?php // phpcs:ignoreFile ?>

<form onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected forms?', 'wp-mailinglist'); ?>')) { return false; }" action="?page=<?php echo esc_html( $this -> sections -> forms); ?>&amp;method=mass" method="post">
	<?php wp_nonce_field($this -> sections -> forms . '_mass'); ?>
	<div class="tablenav">
		<div class="alignleft">
			<select name="action" style="width:auto;">
				<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
				<option value="delete"><?php esc_html_e('Delete Selected', 'wp-mailinglist'); ?></option>
			</select>            
            <button value="1" type="submit" class="button" name="execute">
            	<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
            </button>
		</div>
		<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
	</div>
	
	<?php
		
	$orderby = (empty($_GET['orderby'])) ? 'modified' : sanitize_text_field(wp_unslash($_GET['orderby']));
	$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
	$otherorder = ($order == "desc") ? 'asc' : 'desc';
	
	$colspan = 4;
	
	?>
	
	<table class="widefat">
		<thead>
			<tr>
				<?php ob_start(); ?>
				<td class="check-column"><input type="checkbox" name="checkboxall" id="checkboxall" value="1" /></td>
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
				<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th>
					<?php esc_html_e('Subscriptions', 'wp-mailinglist'); ?>
				</th>
				<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<?php 
					
				$cols_output = ob_get_clean(); 
				echo wp_kses_post($cols_output);
				
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?php echo wp_kses_post($cols_output); ?>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($forms)) : ?>
				<?php foreach ($forms as $form) : ?>
					<?php $class = ($class == 'alternate') ? '' : 'alternate'; ?>
					<tr class="<?php echo esc_html( $class); ?>" id="form_row_<?php echo esc_html($form -> id); ?>">
						<th class="check-column"><input type="checkbox" name="forms[]" value="<?php echo esc_attr($form -> id); ?>" id="form_check_<?php echo esc_html($form -> id); ?>" /></th>
						<td><label for="form_check_<?php echo esc_html($form -> id); ?>"><?php echo esc_html($form -> id); ?></label></td>
						<td>
							<a class="row-title" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form -> id)) ?>"><?php echo wp_kses_post( wp_unslash(esc_html($form -> title))) ?></a>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form -> id)) ?>"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a> |</span>
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=settings&id=' . $form -> id)) ?>"><?php esc_html_e('Settings', 'wp-mailinglist'); ?></a> |</span>
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=preview&id=' . $form -> id)) ?>"><?php esc_html_e('Preview', 'wp-mailinglist'); ?></a> |</span>
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=codes&id=' . $form -> id)) ?>"><?php esc_html_e('Embed/Codes', 'wp-mailinglist'); ?></a> |</span>
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=subscriptions&id=' . $form -> id)) ?>"><?php esc_html_e('Subscriptions', 'wp-mailinglist'); ?></a> |</span>
								<span class="delete"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=delete&id=' . $form -> id)) ?>" class="submitdelete" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this form?', 'wp-mailinglist'); ?>')) { return false; }"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a></span>
							</div>
						</td>
						<td>
							<code>[newsletters_subscribe form=<?php echo esc_html($form -> id); ?>]</code>
							<button type="button" class="button button-secondary button-small copy-button" data-clipboard-text="[newsletters_subscribe form=<?php echo esc_html($form -> id); ?>]">
								<i class="fa fa-clipboard fa-fw"></i>
							</button>
							<div class="row-actions">
								<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=codes&id=' . $form -> id)) ?>"><?php esc_html_e('More embedding options', 'wp-mailinglist'); ?></a></span>
							</div>
						</td>
						<td>
							<?php
								
							$Db -> model = $SubscribersList -> model;
							echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> forms . '&method=subscriptions&id=' . $form -> id) . '">' . $Db -> count(array('form_id' => $form -> id)) . '</a>';	
								
							?>
						</td>
						<td>
							<label for="form_check_<?php echo esc_html($form -> id); ?>"><abbr title="<?php echo esc_attr(wp_unslash($form -> modified)); ?>"><?php echo esc_html( $Html -> gen_date(null, strtotime($form -> modified))); ?></abbr></label>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="no-items">
					<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php echo sprintf(__('No forms available, %s', 'wp-mailinglist'), '<a onclick="jQuery.colorbox({title:\'' . __('Create a New Form', 'wp-mailinglist') . '\', href:\'' . admin_url('admin-ajax.php?action=newsletters_forms_createform') . '\'}); return false;" href="' . admin_url('admin.php?page=' . $this -> sections -> forms . '&amp;method=save') . '">' . __('add one', 'wp-mailinglist') . '</a>'); ?></td>
				</tr>
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
						<option <?php echo (!empty($_COOKIE[$this -> pre . 'formsperpage']) && $_COOKIE[$this -> pre . 'formsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
						<?php $p += 5; ?>
					<?php endwhile; ?>
					<?php if (isset($_COOKIE[$this -> pre . 'formsperpage'])) : ?>
						<option selected="selected" value="<?php echo esc_attr((int) $_COOKIE[$this -> pre . 'formsperpage']); ?>"><?php echo esc_attr((int) $_COOKIE[$this -> pre . 'formsperpage']); ?></option>
					<?php endif; ?>
				</select>
			<?php endif; ?>
			
			<script type="text/javascript">
			function change_perpage(perpage) {				
				if (perpage != "") {
					document.cookie = "<?php echo esc_html($this -> pre); ?>formsperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
			}
			
			function change_sorting(field, dir) {
				document.cookie = "<?php echo esc_html($this -> pre); ?>formssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				document.cookie = "<?php echo esc_html($this -> pre); ?>forms" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
			}
			</script>
		</div>
		<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
	</div>
</form>