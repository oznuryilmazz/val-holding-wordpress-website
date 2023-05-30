<?php // phpcs:ignoreFile ?>

<form onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected mailing lists?', 'wp-mailinglist'); ?>')) { return false; }" action="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=mass" method="post">
		<?php wp_nonce_field($this -> sections -> lists . '_mass'); ?>
		<div class="tablenav">
			<div class="alignleft">
				<select name="action" style="width:auto;" onchange="change_action(this.value); return false;">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete Selected', 'wp-mailinglist'); ?></option>
					<option value="merge"><?php esc_html_e('Merge', 'wp-mailinglist'); ?></option>
					<optgroup label="<?php esc_html_e('Private', 'wp-mailinglist'); ?>">
						<option value="private"><?php esc_html_e('Set as private', 'wp-mailinglist'); ?></option>
						<option value="notprivate"><?php esc_html_e('Set as NOT private', 'wp-mailinglist'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_html_e('Opt-In', 'wp-mailinglist'); ?>">
						<option value="singleopt"><?php esc_html_e('Set as single opt-in', 'wp-mailinglist'); ?></option>
						<option value="doubleopt"><?php esc_html_e('Set as double opt-in', 'wp-mailinglist'); ?></option>
					</optgroup>
                    <option value="setgroup"><?php esc_html_e('Set Group', 'wp-mailinglist'); ?></option>
				</select>
				
				<span id="mergeactiondiv" style="display:none;">
					<label for="list_title"><?php esc_html_e('New list title:', 'wp-mailinglist'); ?></label>
					<input type="text" name="list_title" value="" id="list_title" />
				</span>
                
                <span id="setgroupactiondiv" style="display:none;">
                	<?php if ($groupsselect = $this -> Group() -> select()) : ?>
                		<label>
	                		<?php esc_html_e('Group:', 'wp-mailinglist'); ?>
	                    	<select name="setgroup_id" id="setgroup_id" class="action">
	                        	<?php foreach ($groupsselect as $group_id => $group_title) : ?>
	                            	<option value="<?php echo esc_attr($group_id); ?>"><?php echo esc_html( $group_title); ?></option>
	                            <?php endforeach; ?>
	                        </select>
                		</label>
                    <?php else : ?>
                    	<?php esc_html_e('No groups are available.', 'wp-mailinglist'); ?>
                    <?php endif; ?>
                </span>
                
                <button value="1" type="submit" class="button" name="execute">
                	<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
                </button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
        
        <script type="text/javascript">
		function change_action(action) {		
			jQuery('span[id$="actiondiv"]').hide();
			jQuery('#' + action + 'actiondiv').show();	
		}
		</script>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 0;
		
		?>
        
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></td>
					<?php $colspan++; ?>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th class="column-title <?php echo ($orderby == "title") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=title&order=' . (($orderby == "title") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Title', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
					<th><?php esc_html_e('Fields', 'wp-mailinglist'); ?></th>
					<?php $colspan++; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
	                    <th class="column-group_id <?php echo ($orderby == "group_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=group_id&order=' . (($orderby == "group_id") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Group', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-privatelist <?php echo ($orderby == "privatelist") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_html($Html -> retainquery('orderby=privatelist&order=' . (($orderby == "privatelist") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Private', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-paid <?php echo ($orderby == "paid") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=paid&order=' . (($orderby == "paid") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Paid', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th><?php esc_html_e('Subscriptions', 'wp-mailinglist'); ?></th>
					<?php $colspan++; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
						<?php $colspan++; ?>
					<?php endif; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="" value="" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_html($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
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
					<th><?php esc_html_e('Fields', 'wp-mailinglist'); ?></th>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
	                    <th class="column-group_id <?php echo ($orderby == "group_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=group_id&order=' . (($orderby == "group_id") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Group', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-privatelist <?php echo ($orderby == "privatelist") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=privatelist&order=' . (($orderby == "privatelist") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Private', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th class="column-paid <?php echo ($orderby == "paid") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
							<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=paid&order=' . (($orderby == "paid") ? $otherorder : "asc"))); ?>">
								<span><?php esc_html_e('Paid', 'wp-mailinglist'); ?></span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					<?php endif; ?>
					<th><?php esc_html_e('Subscriptions', 'wp-mailinglist'); ?></th>
					<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
						<th><?php esc_html_e('Shortcode', 'wp-mailinglist'); ?></th>
					<?php endif; ?>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($mailinglists)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No mailing lists were found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($mailinglists as $list) : ?>
					<?php $class = ($class == 'alternate') ? '' : 'alternate'; ?>
						<tr class="<?php echo esc_html( $class); ?>" id="listrow<?php echo esc_html( $list -> id); ?>">
							<th class="check-column"><input id="checklist<?php echo esc_html( $list -> id); ?>" type="checkbox" name="mailinglistslist[]" value="<?php echo esc_attr($list -> id); ?>" /></th>
							<td><label for="checklist<?php echo esc_html( $list -> id); ?>"><?php echo esc_html( $list -> id); ?></label></td>
							<td>
								<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=view&amp;id=<?php echo esc_html( $list -> id); ?>" title="<?php esc_html_e('View the details of this mailing list', 'wp-mailinglist'); ?>">
									<?php echo esc_html($list -> title); ?>
								</a>
								<?php if (!empty($list -> default)) : ?>
									<small>(<?php esc_html_e('Default', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> lists . '&method=cleardefault&id=' . $list -> id)) ?>"><i class="fa fa-times"></i></a>)</small>
								<?php endif; ?>
								<?php if (!empty($list -> adminemail)) : ?>
									<br/><small>(<?php esc_html_e('Admin Email:', 'wp-mailinglist'); ?> <strong><?php echo esc_html( $list -> adminemail); ?>)</strong></small>
								<?php endif; ?>
								<?php if (!empty($list -> doubleopt) && $list -> doubleopt == "N") : ?>
									<br/><small>(<?php esc_html_e('Single Opt-In', 'wp-mailinglist'); ?>)</small>
								<?php endif; ?>
								<div class="row-actions">
									<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=save&amp;id=' . $list -> id)); ?> |</span>
									<span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=delete&amp;id=' . $list -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this mailing list?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?> |</span>
									<?php if (empty($list -> default)) : ?>
										<span class="view"><?php echo ( $Html -> link(__('Set Default', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=default&amp;id=' . $list -> id)); ?> |</span>
									<?php endif; ?>
									<span class="view"><?php echo ( $Html -> link(__('View', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list -> id)); ?> |</span>
									<span class="edit"><?php echo ( $Html -> link(__('Offsite', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=offsite&amp;listid=' . $list -> id)); ?> |</span>
									<span class="edit"><?php echo ( $Html -> link(__('Add Subscriber', 'wp-mailinglist'), '?page=' . $this -> sections -> subscribers . '&amp;method=save&amp;mailinglist_id=' . $list -> id)); ?></span>
								</div>
							</td>
							<td><label for="checklist<?php echo esc_html( $list -> id); ?>"><?php echo esc_html( $FieldsList -> count_by_list($list -> id)); ?></label></td>
							<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
			                    <td>
			                    	<?php if (!empty($list -> group_id)) : ?>
			                        	<?php echo ( $Html -> link(esc_html($this -> Group() -> field('title', array('id' => $list -> group_id))), '?page=' . $this -> sections -> groups . '&amp;method=view&amp;id=' . $list -> group_id)); ?>
			                        <?php else : ?>
			                        	<?php esc_html_e('none', 'wp-mailinglist'); ?>
			                        <?php endif; ?>
			                    </td>
								<td><label for="checklist<?php echo esc_html( $list -> id); ?>"><span class="<?php echo (empty($list -> privatelist) || $list -> privatelist == "N") ? 'newsletters_error"><i class="fa fa-times"></i>' : 'newsletters_success"><i class="fa fa-check"></i>'; ?></span></label></td>
								<td>
									<label for="checklist<?php echo esc_html( $list -> id); ?>"><span class="<?php echo (empty($list -> paid) || $list -> paid == "N") ? 'newsletters_error"><i class="fa fa-times"></i>' : 'newsletters_success"><i class="fa fa-check"></i>'; ?></span></label>
									<?php if (!empty($list -> paid) && $list -> paid == "Y") : ?>
										<?php 
										
										$intervals = array(
											'daily'			=>	__('Daily', 'wp-mailinglist'),
											'weekly'		=>	__('Weekly', 'wp-mailinglist'),
											'monthly'		=>	__('Monthly', 'wp-mailinglist'),
											'2months'		=>	__('Every Two Months', 'wp-mailinglist'),
											'3months'		=>	__('Every Three Months', 'wp-mailinglist'),
											'biannually'	=>	__('Twice Yearly (Six Months)', 'wp-mailinglist'),
											'9months'		=>	__('Every Nine Months', 'wp-mailinglist'),
											'yearly'		=>	__('Yearly', 'wp-mailinglist'),
											'once'			=>	__('Once Off', 'wp-mailinglist'),
										);
										
										?>
										<small>(<?php echo esc_html( $Html -> currency() . '' . number_format($list -> price, 2, '.', '') . ' ' . $intervals[$list -> interval]); ?>)</small>
									<?php endif; ?>	
								</td>
							<?php endif; ?>
							<td><label for="checklist<?php echo esc_html( $list -> id); ?>"><b><?php echo esc_html( $SubscribersList -> count(array('list_id' => $list -> id))); ?></b> (<?php echo esc_html( $SubscribersList -> count(array('list_id' => $list -> id, 'active' => "Y"))); ?> <?php esc_html_e('active', 'wp-mailinglist'); ?>)</label></td>
							<?php if (apply_filters($this -> pre . '_admin_mailinglists_groupcolumn', true)) : ?>
								<td>
									<code>[newsletters_subscribe list="<?php echo esc_html( $list -> id); ?>"]</code>
									<button type="button" class="button button-secondary button-small copy-button" data-clipboard-text="[newsletters_subscribe list=<?php echo esc_html( $list -> id); ?>]">
										<i class="fa fa-clipboard fa-fw"></i>
									</button>
								</td>
							<?php endif; ?>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'listsperpage']) && $_COOKIE[$this -> pre . 'listsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'listsperpage'])) : ?>
							<option selected="selected" value="<?php echo esc_attr((int) $_COOKIE[$this -> pre . 'listsperpage']); ?>"><?php echo (int) $_COOKIE[$this -> pre . 'listsperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>listsperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
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