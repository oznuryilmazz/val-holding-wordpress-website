<?php // phpcs:ignoreFile ?>
<?php

$paidsubscriptions = $this -> get_option('subscriptions');
$saveipaddress = $this -> get_option('saveipaddress');

?>

<?php /*<?php if (!empty($subscribers)) : ?>*/ ?>
	<form action="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=mass" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected subscribers?', 'wp-mailinglist'); ?>')) { return false; };" method="post" id="subscribersform" name="subscribersform">
		<div class="tablenav">
			<div class="alignleft">
                <?php if (!empty($paidsubscriptions) && $paidsubscriptions == "Y") : ?>
                	<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=check-expired" class="button"><i class="fa fa-hourglass-end"></i> <?php esc_html_e('Check Expired', 'wp-mailinglist'); ?></a>
                <?php endif; ?>
                <a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> importexport)) ?>"><i class="fa fa-hdd-o"></i> <?php esc_html_e('Import/Export', 'wp-mailinglist'); ?></a>
                <a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes')) ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Unsubscribes', 'wp-mailinglist'); ?></a>
				<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces')) ?>"><i class="fa fa-ban"></i> <?php esc_html_e('Bounces', 'wp-mailinglist'); ?></a>
				<select class="widefat" style="width:auto;" name="action" onchange="action_change(this.value);">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
					<option value="unsubscribe"><?php esc_html_e('Unsubscribe', 'wp-mailinglist'); ?></option>
					<?php if (!empty($saveipaddress)) : ?>
						<option value="getcountry"><?php esc_html_e('Get Country by IP', 'wp-mailinglist'); ?></option>
					<?php endif; ?>
					<optgroup label="<?php esc_html_e('Mandatory Status', 'wp-mailinglist'); ?>">
						<option value="mandatory"><?php esc_html_e('Set as Mandatory', 'wp-mailinglist'); ?></option>
						<option value="notmandatory"><?php esc_html_e('Set as not Mandatory', 'wp-mailinglist');?></option>
					</optgroup>
					<optgroup label="<?php esc_html_e('Format', 'wp-mailinglist'); ?>">
						<option value="html"><?php esc_html_e('Set as HTML', 'wp-mailinglist'); ?></option>
						<option value="text"><?php esc_html_e('Set as TEXT', 'wp-mailinglist'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_html_e('Status', 'wp-mailinglist'); ?>">
						<option value="active"><?php esc_html_e('Activate', 'wp-mailinglist'); ?></option>
						<option value="inactive"><?php esc_html_e('Deactivate', 'wp-mailinglist'); ?></option>
					</optgroup>
					<optgroup  label="<?php esc_html_e('Mailing Lists', 'wp-mailinglist'); ?>">
						<option value="assignlists"><?php esc_html_e('Add Lists (appends)...', 'wp-mailinglist'); ?></option>
						<option value="setlists"><?php esc_html_e('Set Lists (overwrites)...', 'wp-mailinglist'); ?></option>
						<option value="dellists"><?php esc_html_e('Remove Lists...', 'wp-mailinglist'); ?></option>
					</optgroup>
				</select>
				<button value="1" type="submit" name="execute" class="button">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<div id="listsdiv" style="display:none;">
			<?php if ($lists = $Mailinglist -> select(true)) : ?>
				<p>
					<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="1" id="checkboxall" onclick="jqCheckAll(this, false, 'lists');" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label><br/>
					<?php foreach ($lists as $lid => $lval) : ?>
						<label><input type="checkbox" name="lists[]" value="<?php echo esc_html( $lid); ?>" /> <?php echo esc_html($lval); ?> (<?php echo esc_html( $SubscribersList -> count(array('list_id' => $lid))); ?> <?php esc_html_e('subscribers', 'wp-mailinglist'); ?>)</label><br/>
					<?php endforeach; ?>
				</p>
			<?php else : ?>
				<p class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></p>
			<?php endif; ?>
		</div>
        
        <?php
	        
	    $saveipaddress = $this -> get_option('saveipaddress');
		
		$orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$cols = array(
			'id'					=>	__('ID', 'wp-mailinglist'),
			'gravatars'				=>	__('Image', 'wp-mailinglist'),
			'email'					=>	__('Email Address', 'wp-mailinglist'),
			'device'				=>	__('Device', 'wp-mailinglist'),
			'registered'			=>	__('User', 'wp-mailinglist'),
			'mandatory'				=>	__('Mandatory', 'wp-mailinglist'),
			'ip_address'			=>	__('IP Address', 'wp-mailinglist'),
			'country'				=>	__('Country', 'wp-mailinglist'),
			'format'				=>	__('Format', 'wp-mailinglist'),
			'lists'					=>	__('List(s)', 'wp-mailinglist'),
			'bouncecount'			=>	__('Bounces', 'wp-mailinglist'),
		);
		
		if (empty($saveipaddress)) {
			unset($cols['ip_address']);
			unset($cols['country']);
		}
		
		$screen_custom = $this -> get_option('screenoptions_subscribers_custom');
		
		if ($screen_fields = $this -> get_option('screenoptions_subscribers_fields')) {
			global $Db, $Field;
			$columns = array();
			
			foreach ($screen_fields as $field_id) {
				$Db -> model = $Field -> model;
				if ($field = $Db -> find(array('id' => $field_id))) {
					$columns[$field -> slug] = $field;
					$cols[$field -> slug] = esc_html($field -> title);
				}
			}
		}
		
		$cols['modified'] = __('Date', 'wp-mailinglist');
		
		$cols = apply_filters('newsletters_admin_subscribers_table_columns', $cols);
		$colspan = count($cols);
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<?php ob_start(); ?>
					<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<?php
						
					if (!empty($cols)) {
						foreach ($cols as $column_name => $column_value) {
							switch ($column_name) {
								case 'gravatars'				:
									if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) {
										$style = "";
										if ($column_name == "lists") {
											$style = "width:20%;";
										} elseif ($column_name == "gravatars") {
											$style = "width:4em;";
										}
										
										$show_avatars = get_option('show_avatars');
										if ($column_name != "gravatars" || ($column_name == "gravatars" && !empty($show_avatars))) {
											?>
											
											<th style="<?php echo esc_html( $style); ?>" class="column-<?php echo esc_html( $column_name); ?>"><?php echo esc_html( $column_value); ?></th>
											
											<?php
										}
									}
									break;
								case 'device'					:
								case 'mandatory'				:
								case 'ip_address'				:
								case 'country'					:
								case 'format'					:
									if (empty($screen_custom) || (!empty($screen_custom) && !in_array($column_name, $screen_custom))) {
										break;
									}
								default							:
									?>
									
									<th class="column-<?php echo esc_html( $column_name); ?> <?php echo ($orderby == $column_name) ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
										<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=' . $column_name . '&order=' . (($orderby == $column_name) ? $otherorder : "asc"))); ?>">
											<span><?php echo esc_html( $column_value); ?></span>
											<span class="sorting-indicator"></span>
										</a>
									</th>
									
									<?php
									break;
							}
						}
					}	
					
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
			<tbody id="the-list">
				<?php if (!empty($subscribers)) : ?>
					<?php $class = ''; ?>
					<?php foreach ($subscribers as $subscriber) : ?>
						<?php $updatediv = (empty($update)) ? 'subscribers' : $update; ?>
						<tr id="subscriberrow<?php echo esc_html( $subscriber -> id); ?>" class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" id="checklist<?php echo esc_html( $subscriber -> id); ?>" name="subscriberslist[]" value="<?php echo esc_html( $subscriber -> id); ?>" /></th>
							<?php
								
							foreach ($cols as $column_name => $column_value) {
								switch ($column_name) {
									case 'id'								:
										?><td><label for="checklist<?php echo esc_html( $subscriber -> id); ?>"><?php echo esc_html( $subscriber -> id); ?></label></td><?php
										break;
									case 'gravatars'						:
										$show_avatars = get_option('show_avatars');
									
										?>
										<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom) && !empty($show_avatars)) : ?>
											<td>
												<label for="checklist<?php echo esc_html( $subscriber -> id); ?>"><?php echo  wp_kses_post($Html -> get_gravatar($subscriber -> email)); ?></label>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'email'							:
										?>
										<td>
											<strong><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>" title="<?php esc_html_e('View the details of this subscriber', 'wp-mailinglist'); ?>" class="row-title"><?php echo esc_html( $subscriber -> email); ?></a></strong>
											<?php if (!empty($subscriber -> format) && $subscriber -> format == "text") : ?>
												<?php echo ( $Html -> help(__('Plain TEXT format', 'wp-mailinglist'), '<i class="fa fa-font"></i>')); ?>
											<?php endif; ?>
											<div class="row-actions">
												<span class="edit"><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=save&amp;id=<?php echo esc_html( $subscriber -> id); ?>"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a> |</span>
												<span class="delete"><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=delete&amp;id=<?php echo esc_html( $subscriber -> id); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this subscriber?', 'wp-mailinglist'); ?>')) { return false; }" class="submitdelete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a> |</span>
												<span class="edit"><span class="newsletters_warning"><a onclick="if (!confirm('<?php esc_html_e('Are you sure you want to unsubscribe this subscriber?', 'wp-mailinglist'); ?>')) { return false; }" href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=unsubscribe&amp;id=<?php echo esc_html( $subscriber -> id); ?>"><?php esc_html_e('Unsubscribe', 'wp-mailinglist'); ?></a></span> |</span>
												<span class="view"><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>"><?php esc_html_e('View', 'wp-mailinglist'); ?></a></span>
											</div>
										</td>
										<?php
										break;
									case 'registered'						:
										?>
										<?php if (apply_filters($this -> pre . '_admin_subscribers_registeredcolumn', true)) : ?>
											<td><label for="checklist<?php echo esc_html( $subscriber -> id); ?>"><?php echo (empty($subscriber -> registered) || $subscriber -> registered == "N") ? '<span class="newsletters_error"><i class="fa fa-times"></i>' : '<span class="newsletters_success"><a href="' . get_edit_user_link($subscriber -> user_id) . '"><i class="fa fa-check"></i></a>'; ?></span></label></td>
										<?php endif; ?>
										<?php
										break;
									case 'mandatory'						:
										?>
										<?php if (!empty($screen_custom) && in_array('mandatory', $screen_custom)) : ?>
											<td>
												<label for="checklist<?php echo esc_html( $subscriber -> id); ?>">
													<?php if (!empty($subscriber -> mandatory) && $subscriber -> mandatory == "Y") : ?>
														<span class="newsletters_error"><i class="fa fa-check"></i></span>
													<?php else : ?>
														<span class="newsletters_success"><i class="fa fa-times"></i></span>
													<?php endif; ?>
												</label>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'ip_address'						:
										?>
										<?php if (!empty($screen_custom) && in_array('ip_address', $screen_custom)) : ?>
											<td>
												<?php echo esc_html( $subscriber -> ip_address); ?>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'country'							:
										?>
										<?php if (!empty($screen_custom) && in_array('country', $screen_custom)) : ?>
											<td>
												<span id="newsletters_subscriber_<?php echo esc_html( $subscriber -> id); ?>_country"><?php echo esc_html($Html -> flag_by_country($subscriber -> country)); ?></span>
														
												<?php if (empty($subscriber -> country)) : ?>
													<a href="" onclick="newsletters_get_country(this); return false;" data-subscriber-id="<?php echo esc_html( $subscriber -> id); ?>" id="newsletters_subscriber_<?php echo esc_html( $subscriber -> id); ?>_get_country"><i class="fa fa-question fa-fw"></i></a>
												<?php endif; ?>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'device'							:
										?>
										<?php if (!empty($screen_custom) && in_array('device', $screen_custom)) : ?>
											<td>
												<label for="checklist<?php echo esc_html( $subscriber -> id); ?>">
													<?php 
														
													switch ($subscriber -> device) {
														case 'mobile'					:
															echo ( $Html -> help(__('Mobile', 'wp-mailinglist'), '<i class="fa fa-mobile"></i>'));
															break;
														case 'tablet'					:
															echo ( $Html -> help(__('Tablet', 'wp-mailinglist'), '<i class="fa fa-tablet"></i>'));
															break;
														case 'desktop'					:
														default 						:
															echo ( $Html -> help(__('Desktop', 'wp-mailinglist'), '<i class="fa fa-desktop"></i>'));
															break;	
													}
													
													?>
												</label>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'format'							:
										?>
										<?php if (!empty($screen_custom) && in_array('format', $screen_custom)) : ?>
											<td>
												<?php if (empty($subscriber -> format) || $subscriber -> format == "html") : ?>
													<?php esc_html_e('HTML', 'wp-mailinglist'); ?>
												<?php elseif ($subscriber -> format == "text") : ?>
													<?php esc_html_e('TEXT', 'wp-mailinglist'); ?>
												<?php endif; ?>
											</td>
										<?php endif; ?>
										<?php
										break;
									case 'lists'							:
										?>
										<td>
											<?php if (!empty($subscriber -> Mailinglist)) : ?>
												<?php $m = 1; ?>
												<?php foreach ($subscriber -> Mailinglist as $list) : ?>
													<?php echo ( $Html -> link(esc_html($list -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $list -> id)); ?> <?php echo ($SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $list -> id)) == "Y") ? '<span class="newsletters_success">' . $Html -> help(__('Active', 'wp-mailinglist'), '<i class="fa fa-check"></i>') : '<span class="newsletters_error">' . $Html -> help(__('Inactive', 'wp-mailinglist'), '<i class="fa fa-times"></i>'); ?></span>
													<?php 

													if (!empty($list -> paid) && $list -> paid == "Y") {	
														if ($Mailinglist -> has_expired($subscriber -> id, $list -> id)) {
															echo '<small>(' . __('Expired', 'wp-mailinglist') . ' ' . $Html -> help(__('Send an expiration notification email to this subscriber right now.', 'wp-mailinglist'), '<i class="fa fa-envelope fa-sm"></i>', admin_url('admin.php?page=' . $this -> sections -> subscribers . '&id=' . $subscriber -> id . '&list_id=' . $list -> id . '&method=check-expired')) . ')</small>';
														} else {
															if ($expiration_date = $Mailinglist -> gen_expiration_date($subscriber -> id, $list -> id)) {
																echo '<small>' . sprintf(__('(Expires %s)', 'wp-mailinglist'), $Html -> gen_date(false, strtotime($expiration_date))) . '</small>';
															}
														}
													}
														
													?>
													<?php if ($m < count($subscriber -> Mailinglist)) : ?>
														<?php echo ', '; ?>
													<?php endif; ?>
													<?php $m++; ?>
												<?php endforeach; ?>
											<?php else : ?>
												<?php esc_html_e('none', 'wp-mailinglist'); ?>
											<?php endif; ?>
										</td>
										<?php
										break;
									case 'bouncecount'						:
										?>
										<td><?php echo esc_html( $subscriber -> bouncecount); ?></td>
										<?php
										break;
									case 'modified'							:
										?>
										<td><label for="checklist<?php echo esc_html( $subscriber -> id); ?>"><abbr title="<?php echo esc_html( $subscriber -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($subscriber -> modified))); ?></abbr></label></td>
										<?php
										break;
									default									:
										?>
										<td>
				                        	<?php if (!empty($subscriber -> {$column_name}) && !empty($columns[$column_name]) || $subscriber -> {$column_name} == "0") : ?>
				                        		<?php $column = $columns[$column_name]; ?>
				                        		<?php $newfieldoptions = $column -> newfieldoptions; ?>
												<?php if ($column -> type == "radio" || $column -> type == "select") : ?>
				                                    <?php echo esc_html($newfieldoptions[$subscriber -> {$column -> slug}]); ?>
				                                <?php elseif ($column -> type == "checkbox") : ?>
				                                    <?php $supoptions = maybe_unserialize($subscriber -> {$column -> slug}); ?>
				                                    <?php if (!empty($supoptions) && is_array($supoptions)) : ?>
				                                        <?php foreach ($supoptions as $supopt) : ?>
				                                            &raquo;&nbsp;<?php echo esc_html($newfieldoptions[$supopt]); ?><br/>
				                                        <?php endforeach; ?>
				                                    <?php else : ?>
				                                        <?php esc_html_e('none', 'wp-mailinglist'); ?>
				                                    <?php endif; ?>
				                                <?php elseif ($column -> type == "file") : ?>
				                                	<?php echo wp_kses_post( $Html -> file_custom_field($subscriber -> {$column -> slug})); ?>
				                                <?php elseif ($column -> type == "pre_country") : ?>
				                                    <?php echo esc_html( $this -> Country() -> field('value', array('id' => $subscriber -> {$column -> slug}))); ?>
				                                <?php elseif ($column -> type == "pre_date") : ?>
				                                	<?php if (is_serialized($subscriber -> {$column -> slug})) : ?>
					                                    <?php $date = maybe_unserialize($subscriber -> {$column -> slug}); ?>
					                                    <?php if (!empty($date) && is_array($date)) : ?>
					                                        <?php echo esc_html( $date['y']); ?>-<?php echo esc_html( $date['m']); ?>-<?php echo esc_html( $date['d']); ?>
					                                    <?php endif; ?>
					                                <?php else : ?>
					                                	<?php echo esc_html( $Html -> gen_date(false, strtotime($subscriber -> {$column -> slug}))); ?>
					                                <?php endif; ?>
				                                <?php elseif ($column -> type == "pre_gender") : ?>
				                                	<?php echo (!empty($subscriber -> {$column -> slug}) && $subscriber -> {$column -> slug} == "male") ? __('Male', 'wp-mailinglist') : __('Female', 'wp-mailinglist'); ?>
				                                <?php else : ?>
				                                    <?php echo esc_html( $subscriber -> {$column -> slug}); ?>
				                                <?php endif; ?>
				                            <?php else : ?>
				                            	<?php do_action('newsletters_admin_subscribers_table_column_output', $column_name, $subscriber); ?>
				                            <?php endif; ?>
										</td>
										<?php
										break;
								}
							}	
								
							?>										
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No subscribers were found', 'wp-mailinglist'); ?></td>
					</tr>
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
							<option <?php echo (isset($_COOKIE[$this -> pre . 'subscribersperpage']) && $_COOKIE[$this -> pre . 'subscribersperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo wp_kses_post($s); ?>"><?php echo wp_kses_post($s); ?> <?php esc_html_e('subscribers', 'wp-mailinglist'); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'subscribersperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'subscribersperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'subscribersperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo esc_html($this -> pre); ?>subscribersperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo esc_html($this -> pre); ?>subscriberssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			document.cookie = "<?php echo esc_html($this -> pre); ?>subscribers" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
		}
		
		function action_change(action) {
			jQuery('#listsdiv').hide();
		
			if (action != "") {
				if (action == "assignlists" || action == "setlists" || action == "dellists") {
					jQuery('#listsdiv').show();
				}
			}
		}
		</script>
<?php /*<?php else : ?>
	<p class="newsletters_error"><?php esc_html_e('No subscribers were found', 'wp-mailinglist'); ?></p>
<?php endif; ?>*/ ?>