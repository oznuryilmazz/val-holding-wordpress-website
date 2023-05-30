<?php // phpcs:ignoreFile ?>

<form action="<?php echo wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> autoresponderemails . '&method=mass'), $this -> sections -> autoresponderemails . '_mass'); ?>" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you want to execute this action on the selected autoresponder emails?', 'wp-mailinglist'); ?>')) { return false; }" method="post">
    	<div class="tablenav">
        	<div class="alignleft actions">
				<select name="action">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
                    <option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
                    <option value="send"><?php esc_html_e('Send Selected', 'wp-mailinglist'); ?></option>
				</select>
				<button type="submit" value="1" class="button" name="execute">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
        
        <?php
        
        $orderby = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		$colspan = 0;
        
        ?>
    
    	<table class="widefat">
        	<thead>
            	<tr>
                	<td class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo esc_html( $this -> sections -> autoresponderemails); ?>', 'autoresponderemailslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
                	<?php $colspan++; ?>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-autoresponder_id <?php echo ($orderby == "autoresponder_id") ? 'sorted ' . esc_url_raw($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=autoresponder_id&order=' . (($orderby == "autoresponder_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Autoresponder', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Status', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                    <th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php $colspan++; ?>
                </tr>
            </thead>
            <tfoot>
            	<tr>
                	<td class="check-column"><input type="checkbox" onclick="jqCheckAll(this, '<?php echo esc_html( $this -> sections -> autoresponderemails); ?>', 'autoresponderemailslist');" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-autoresponder_id <?php echo ($orderby == "autoresponder_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=autoresponder_id&order=' . (($orderby == "autoresponder_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Autoresponder', 'wp-mailinglist'); ?></span>
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
                    <th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                </tr>
            </tfoot>
        	<tbody>
        		<?php if (empty($autoresponderemails)) : ?>
        			<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No autoresponder emails found', 'wp-mailinglist'); ?></td>
					</tr>
        		<?php else : ?>
	            	<?php foreach ($autoresponderemails as $aemail) : ?>
	            		<?php
	            		
	            		if (!empty($aemail -> subscriber_id) && !empty($aemail -> list_id)) {
		            		global $wpdb;
		            		$query = "SELECT `active` FROM " . $wpdb -> prefix . $SubscribersList -> table . " WHERE `subscriber_id` = '" . $aemail -> subscriber_id . "' AND `list_id` = '" . $aemail -> list_id . "' LIMIT 1";
		            		
		            		$query_hash = md5($query);
		            		if ($ob_active = $this -> get_cache($query_hash)) {
			            		$active = $ob_active;
		            		} else {
			            		$active = $wpdb -> get_var($query);
			            		$this -> set_cache($query_hash, $active);
		            		}
		            		
		            		$aemail -> active = $active;
	            		}
	            		
	            		?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	                    	<th class="check-column"><input type="checkbox" name="autoresponderemailslist[]" value="<?php echo esc_html( $aemail -> id); ?>" id="checklist<?php echo esc_html( $aemail -> id); ?>" /></th>
	                        <td><label for="checklist<?php echo esc_html( $aemail -> id); ?>"><?php echo esc_html( $aemail -> id); ?></label></td>
	                        <td>
	                        	<?php $difference = $Html -> time_difference($aemail -> senddate, $Html -> gen_date("Y-m-d H:i:s"), $aemail -> autoresponder -> delayinterval); ?>
	                            <?php if ($difference >= 0) { $daysstring = __('This autoresponder email is due in ' . $difference . ' ' . $aemail -> autoresponder -> delayinterval . ' only.', 'wp-mailinglist'); } else { $daysstring = ""; }; ?>
	                        	<strong><?php echo ( $Html -> link($aemail -> subscriber -> email, '?page=' . $this -> sections -> subscribers . '&method=view&id=' . $aemail -> subscriber_id, array('class' => "row-title"))); ?></strong>
	                        	<?php if (!empty($aemail -> active)) : ?><span class="newsletters_<?php echo ($aemail -> active == "Y") ? 'success' : 'error'; ?>"><?php echo ($aemail -> active == "Y") ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?></span><?php endif; ?>
	                            <div class="row-actions">
	                            	<?php $sendtext = ($aemail -> status == "unsent") ? __('Send Now', 'wp-mailinglist') : __('Send Again', 'wp-mailinglist'); ?>
	                            	<span class="edit"><?php echo ( $Html -> link($sendtext, '?page=' . $this -> sections -> autoresponderemails . '&method=send&id=' . $aemail -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to send this autoresponder email now?', 'wp-mailinglist') . " " . $daysstring . "')) { return false; }"))); ?> |</span>
	                                <span class="delete"><?php echo ( $Html -> link(__('Delete', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponderemails . '&method=delete&id=' . $aemail -> id, array('onclick' => "if (!confirm('" . __('Are you sure you want to delete this autoresponder email?', 'wp-mailinglist') . "')) { return false; }"))); ?></span>
	                            </div>
	                        </td>
	                        <td>
	                        	<?php echo ( $Html -> link($aemail -> autoresponder -> title, admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&method=save&id=' . $aemail -> autoresponder_id))); ?>
	                        	
	                        	<?php if ($history = $this -> History() -> find(array('id' => $aemail -> autoresponder -> history_id))) : ?>
	                        		<br/><small><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id)) ?>"><?php echo esc_html($history -> subject); ?></a></small>
	                        	<?php endif; ?>
	                        </td>
	                        <td>
	                        	<?php if ($aemail -> status == "sent") : ?>
	                            	<span class="newsletters_success"><i class="fa fa-check"></i> <?php esc_html_e('Sent', 'wp-mailinglist'); ?></span>
	                            <?php else : ?>
	                            	<span class="newsletters_error"><i class="fa fa-times"></i> <?php esc_html_e('Unsent', 'wp-mailinglist'); ?></span>
	                            <?php endif; ?>
	                        </td>
	                        <td>
	                        	<abbr title="<?php echo esc_html( $aemail -> created); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($aemail -> created))); ?></abbr>
	                        </td>
	                        <td>
	                        	<abbr title="<?php echo esc_html( $aemail -> senddate); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($aemail -> senddate))); ?></abbr>
	                            <?php $difference = $Html -> time_difference($aemail -> senddate, $Html -> gen_date("Y-m-d H:i:s"), $aemail -> autoresponder -> delayinterval); ?>
	                            <?php if ($difference >= 0) : ?>(<?php echo wp_kses_post($difference); ?> <?php echo esc_html( $aemail -> autoresponder -> delayinterval); ?>+)<?php endif; ?>
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
						<?php while ($p < 200) : ?>
							<option <?php echo (!empty($_COOKIE[$this -> pre . 'autoresponderemailsperpage']) && $_COOKIE[$this -> pre . 'autoresponderemailsperpage'] == $p) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $p); ?>"><?php echo wp_kses_post($p); ?> <?php esc_html_e('per page', 'wp-mailinglist'); ?></option>
							<?php $p += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'autoresponderemailsperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'autoresponderemailsperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'autoresponderemailsperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
				
				<script type="text/javascript">
				function change_perpage(perpage) {				
					if (perpage != "") {
						document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderemailsperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
						window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
					}
				}
				
				function change_sorting(field, dir) {
					document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderemailssorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					document.cookie = "<?php echo esc_html($this -> pre); ?>autoresponderemails" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
					window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
				}
				</script>
			</div>
        	<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
        </div>
    </form>