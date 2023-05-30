<?php // phpcs:ignoreFile ?>

<form action="<?php echo wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&method=mass'), $this -> sections -> autoresponders . '_mass'); ?>" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you want to execute this action on the selected autoresponders?', 'wp-mailinglist'); ?>')) { return false; }" method="post">
    	<div class="tablenav">
        	<div class="alignleft actions">
            	<a href="?page=<?php echo esc_html( $this -> sections -> autoresponderemails); ?>" class="button"><i class="fa fa-envelope"></i> <?php esc_html_e('Autoresponder Emails', 'wp-mailinglist'); ?></a>
        	</div>
        	<div class="alignleft actions">
				<select name="action">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
                    <option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
                    <optgroup title="<?php esc_html_e('Change Status', 'wp-mailinglist'); ?>">
	                    <option value="activate"><?php esc_html_e('Activate', 'wp-mailinglist'); ?></option>
	                    <option value="deactivate"><?php esc_html_e('Deactivate', 'wp-mailinglist'); ?></option>
                    </optgroup>
				</select>
				<button value="1" type="submit" class="button" name="execute">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
        
        <?php
        
        $orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 9;
        
        ?>
    
    	<table class="widefat">
        	<thead>
            	<tr>
                	<td class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo esc_html( $this -> sections -> autoresponders); ?>', 'autoresponderslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
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
                    <th><?php esc_html_e('List/s', 'wp-mailinglist'); ?></th>
                    <th><?php esc_html_e('Form/s', 'wp-mailinglist'); ?></th>
                    <th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-alwayssend <?php echo ($orderby == "alwayssend") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=alwayssend&order=' . (($orderby == "alwayssend") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Always Send', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-delay <?php echo ($orderby == "delay") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=delay&order=' . (($orderby == "delay") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Delay', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Status', 'wp-mailinglist'); ?></span>
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
                	<td class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo esc_html( $this -> sections -> autoresponders); ?>', 'autoresponderslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
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
                    <th><?php esc_html_e('List/s', 'wp-mailinglist'); ?></th>
                    <th><?php esc_html_e('Form/s', 'wp-mailinglist'); ?></th>
                    <th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-alwayssend <?php echo ($orderby == "alwayssend") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=alwayssend&order=' . (($orderby == "alwayssend") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Always Send', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-delay <?php echo ($orderby == "delay") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=delay&order=' . (($orderby == "delay") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Delay', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Status', 'wp-mailinglist'); ?></span>
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
        		<?php if (empty($autoresponders)) : ?>
        			<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No autoresponders found', 'wp-mailinglist'); ?></td>
					</tr>
        		<?php else : ?>
	            	<?php foreach ($autoresponders as $autoresponder) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	                    	<th class="check-column"><input type="checkbox" name="autoresponderslist[]" value="<?php echo esc_html( $autoresponder -> id); ?>" id="checklist<?php echo esc_html( $autoresponder -> id); ?>" /></th>
	                        <td><label for="checklist<?php echo esc_html( $autoresponder -> id); ?>"><?php echo esc_html( $autoresponder -> id); ?></label></td>
	                        <td>
	                        	<strong><a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> autoresponders); ?>&amp;method=save&amp;id=<?php echo esc_html( $autoresponder -> id); ?>" title="<?php echo esc_html( $autoresponder -> title); ?>"><?php echo esc_html( $autoresponder -> title); ?></a></strong>
	                        	
	                        	<?php
		                        
		                        $sent = $this -> Autoresponderemail() -> count(array('autoresponder_id' => $autoresponder -> id, 'status' => "sent"));
		                        $unsent = $this -> Autoresponderemail() -> count(array('autoresponder_id' => $autoresponder -> id, 'status' => "unsent"));
		                        echo '<span class="howto">' . sprintf(__('%s emails sent and %s waiting for their delay to send', 'wp-mailinglist'), $sent, $unsent) . '</span>';
		                        	
		                        ?>
	                        	
	                            <?php if (!empty($autoresponder -> pending)) : ?><small>(<?php echo ( $Html -> link($autoresponder -> pending, '?page=' . $this -> sections -> autoresponderemails . '&amp;id=' . $autoresponder -> id . '&amp;status=unsent')); ?> <?php esc_html_e('pending emails', 'wp-mailinglist'); ?>))</small><?php endif; ?>
	                            <div class="row-actions">
	                            	<span class="edit"><?php echo ( $Html -> link(__('Edit', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponders . '&amp;method=save&amp;id=' . $autoresponder -> id)); ?> |</span>
	                                <span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponders . '&amp;method=delete&amp;id=' . $autoresponder -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this autoresponder?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?> |</span>
	                                <span class="view"><?php echo ( $Html -> link(__('Emails', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponderemails . '&amp;status=all&amp;id=' . $autoresponder -> id)); ?></span>
	                            </div>
	                        </td>
	                        <td>
		                        <?php
			                        
			                    if (empty($autoresponder -> mailinglists)) {
									if ($autoresponderslists = $this -> AutorespondersList() -> find_all(array('autoresponder_id' => $autoresponder -> id))) {				
										foreach ($autoresponderslists as $autoresponderslist) {
											$Db -> model = $Mailinglist -> model;
											$autoresponder -> lists[] = $autoresponderslist -> list_id;
											$autoresponder -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
										}
									}
			                    }
			                        
			                    ?>
		                        
	                        	<?php if (!empty($autoresponder -> mailinglists)) : ?>
	                            	<?php $m = 1; ?>
	                            	<?php foreach ($autoresponder -> mailinglists as $mailinglist) : ?>
	                                	<?php echo ( $Html -> link(esc_html($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id)); ?>
	                                    <?php if ($m < count($autoresponder -> mailinglists)) : ?>
											<?php echo ', '; ?>
	                                    <?php endif; ?>
	                                    <?php $m++; ?>
	                                <?php endforeach; ?>
	                            <?php else : ?>
	                            	<?php esc_html_e('None', 'wp-mailinglist'); ?>
	                            <?php endif; ?>
	                        </td>
	                        <td>
		                        <?php
			                        
			                    if (empty($autoresponder -> forms)) {
				                    if ($autorespondersforms = $this -> AutorespondersForm() -> find_all(array('autoresponder_id' => $autoresponder -> id))) {
					                    foreach ($autorespondersforms as $autorespondersform) {
						                    $autoresponder -> forms[] = $autorespondersform -> form_id;
					                    }
				                    }
			                    }  
			                    
			                    if (!empty($autoresponder -> forms)) {
				                    $f = 1;
				                    foreach ($autoresponder -> forms as $form_id) {
					                    if ($form = $this -> Subscribeform() -> find(array('id' => $form_id))) {
						                    echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=' . $form_id) . '">' . esc_html($form -> title) . '</a>';
						                    
						                    if ($f < count($autoresponder -> forms)) {
							                    echo ', ';
						                    }
						                    
						                    $f++;
					                    }
				                    }
			                    } else {
				                    _e('None', 'wp-mailinglist');
			                    }
			                        
			                    ?>
	                        </td>
	                        <td>
	                        	<?php if ($history = $this -> History() -> find(array('id' => $autoresponder -> history_id))) : ?>
	                            	<?php echo ( $Html -> link(esc_html($history -> subject), '?page=' . $this -> sections -> history . '&amp;method=view&amp;id=' . $history -> id)); ?>
								<?php else : ?>
	                            	<?php esc_html_e('None', 'wp-mailinglist'); ?>
	                            <?php endif; ?>
	                        </td>
	                        <td>
	                        	<?php if (!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") : ?>
	                        		<span class="newsletters_success"><i class="fa fa-check"></i></span>
	                        	<?php else : ?>
	                        		<span class="newsletters_error"><i class="fa fa-times"></i></span>
	                        	<?php endif; ?>
	                        </td>
	                        <td>
	                        	<?php if (empty($autoresponder -> delay)) : ?>
	                            	<?php esc_html_e('Immediately', 'wp-mailinglist'); ?>
	                            <?php else : ?>
	                            	<?php echo esc_html( $autoresponder -> delay); ?> <?php echo esc_html( $autoresponder -> delayinterval); ?>
	                            <?php endif; ?>
	                        </td>
	                        <td>
	                        	<?php if (!empty($autoresponder -> status) && $autoresponder -> status == "inactive") : ?>
	                            	<span class="newsletters_error"><i class="fa fa-times"></i></span>
	                            <?php else : ?>
	                            	<span class="newsletters_success"><i class="fa fa-check"></i></span>
	                            <?php endif; ?>
	                        </td>
	                        <td><abbr title="<?php echo esc_html( $autoresponder -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($autoresponder -> modified))); ?></abbr></td>
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
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'autorespondersperpage']) && $_COOKIE[$this -> pre . 'autorespondersperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'autorespondersperpage'])) : ?>
							<option selected="selected" value="<?php echo $_COOKIE[$this -> pre . 'autorespondersperpage']; ?>"><?php echo $_COOKIE[$this -> pre . 'autorespondersperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>autorespondersperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponders" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
        	<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
    </form>