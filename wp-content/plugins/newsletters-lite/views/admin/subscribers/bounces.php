<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h2>
		<?php esc_html_e('Manage Bounces', 'wp-mailinglist'); ?>
		<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&amp;method=bounces')) ?>" class="add-new-h2"><?php esc_html_e('Refresh', 'wp-mailinglist'); ?></a>
	</h2>
	
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; Back to Subscribers', 'wp-mailinglist'), "?page=" . $this -> sections -> subscribers)); ?></div>
	
	<form id="posts-filter" action="<?php echo wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))); ?>" method="post">
    	<?php if (!empty($bounces)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($bounces); ?> <?php esc_html_e('bounces', 'wp-mailinglist'); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $Html -> retainquery('showall=1'))); ?></li>
                <?php else : ?>
                    <li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), "?page=" . $this -> sections -> subscribers . '&method=bounces')); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])); ?>" />
			<button value="1" type="submit" class="button">
				<?php esc_html_e('Search Bounces', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<br class="clear" />
	
	<form id="posts-filter" action="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=bounces" method="get">
    	<input type="hidden" name="page" value="<?php echo esc_html( $this -> sections -> subscribers); ?>" />
    	<input type="hidden" name="method" value="bounces" />
    	<input type="hidden" name="order" value="<?php echo sanitize_text_field(wp_unslash($_GET['order'])); ?>" />
    	<input type="hidden" name="orderby" value="<?php echo sanitize_text_field(wp_unslash($_GET['orderby'])); ?>" />
    	
    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
    		<input type="hidden" name="<?php echo esc_html($this -> pre); ?>searchterm" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm']))); ?>" />
    	<?php endif; ?>
    	
    	<div class="alignleft actions widefat">
    		<?php esc_html_e('Filters:', 'wp-mailinglist'); ?>
	        
	        <select name="history_id[]" id="historiesautocomplete" style="min-width:300px; width:auto;" multiple="multiple">
		        <?php if (!empty($_GET['history_id'])) : ?>
		        	<?php $historiesarray = (is_array($_GET['history_id'])) ? sanitize_text_field(wp_unslash($_GET['history_id'])) : array(sanitize_text_field(wp_unslash($_GET['history_id']))); ?>
		        	<?php foreach ($historiesarray as $history_id) : ?>
		        		<?php
			        		
			        	$history_subject = $this -> History() -> field('subject', array('id' => $history_id));	
			        		
			        	?>
		        		<option value="<?php echo esc_html( $history_id); ?>" selected="selected"><?php echo esc_attr($history_subject); ?></option>
		        	<?php endforeach; ?>
		        <?php endif; ?>
	        </select>
	        
	        <input type="submit" name="filter" value="<?php esc_html_e('Filter', 'wp-mailinglist'); ?>" class="button button-primary" />
	        
	        <script type="text/javascript">			        	        
		    jQuery(document).ready(function() {
			    jQuery('#historiesautocomplete').select2({
				  placeholder: '<?php esc_html_e('Search newsletters', 'wp-mailinglist'); ?>',
				  ajax: {
				        url: newsletters_ajaxurl + "action=newsletters_autocomplete_histories&security=<?php echo esc_html( wp_create_nonce('autocomplete_histories')); ?>",
				        dataType: 'json',
				        data: function (params) {
					      return {
					        q: params.term, // search term
					        page: params.page
					      };
					    },
					    processResults: function (data, page) {
					      return {
					        results: data
					      };
					    },
				    },
				  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				  minimumInputLength: 1,
				  templateResult: formatResult,
				  templateSelection: formatSelection,
			    }).next().css('width', "auto").css('min-width', "300px");
		    });
			
			function formatResult(data) {
		        return data.text;
		    };
		
		    function formatSelection(data) {
		        return data.text;
		    };
		    
		    function filter_value(filtername, filtervalue) {	    			
		        if (filtername != "") {
		            document.cookie = "<?php echo esc_html($this -> pre); ?>filter_" + filtername + "=" + filtervalue + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
		        }
		    }
			</script>    		
    	</div>
    </form>
    <br class="clear" />
	
	<form action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bouncemass')) ?>" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action on the selected bounces?', 'wp-mailinglist'); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<?php if ($this -> get_option('bouncemethod') == "pop") : ?>
                    <a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=check-bounced" class="button" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to check your POP/IMAP mailbox for bounced emails?', 'wp-mailinglist'); ?>')) { return false; }"><?php esc_html_e('Check for Bounces', 'wp-mailinglist'); ?></a>
                <?php endif; ?>
				<select name="action" id="newsletters-bounce-action">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete Bounces', 'wp-mailinglist'); ?></option>
					<option value="deletesubscribers"><?php esc_html_e('Delete Subscribers', 'wp-mailinglist'); ?></option>
				</select>
				<input type="submit" name="execute" value="<?php esc_html_e('Apply', 'wp-mailinglist'); ?>" class="button-secondary" />
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<?php
		
		$orderby = (empty($_GET['orderby'])) ? 'created' :  sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 6;
		
		?>
	
		<table class="widefat">
			<thead>
				<td class="check-column"><input type="checkbox" name="bouncescheckall" value="1" /></td>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Email Address', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-count <?php echo ($orderby == "count") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=count&order=' . (($orderby == "count") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Count', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Status', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('History Email', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</thead>
			<tfoot>
				<td class="check-column"><input type="checkbox" name="bouncescheckall" value="1" /></td>
				<th class="column-email <?php echo ($orderby == "email") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=email&order=' . (($orderby == "email") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Email Address', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-count <?php echo ($orderby == "count") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=count&order=' . (($orderby == "count") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Count', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-status <?php echo ($orderby == "status") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=status&order=' . (($orderby == "status") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Status', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('History Email', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th class="column-created <?php echo ($orderby == "created") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
					<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=created&order=' . (($orderby == "created") ? $otherorder : "asc"))); ?>">
						<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tfoot>
			<tbody>
				<?php if (!empty($bounces)) : ?>
					<?php $class = false; ?>
					<?php foreach ($bounces as $bounce) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<th class="check-column"><input type="checkbox" name="bounces[]" value="<?php echo esc_html($bounce -> id); ?>" /></th>
							<td>
								<?php $Db -> model = $Subscriber -> model; ?>
								<?php if ($subscriber = $Db -> find(array('email' => $bounce -> email))) : ?>
									<a class="row-title" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=view&id=' . $subscriber -> id)) ?>"><?php echo esc_html( $bounce -> email); ?></a>
								<?php else : ?>
									<?php echo esc_html( $bounce -> email); ?>
								<?php endif; ?>
								
								<div class="row-actions">
									<span class="delete"><a class="submitdelete" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bouncedelete&id=' . $bounce -> id)) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this bounce?', 'wp-mailinglist'); ?>')) { return false; }"><?php esc_html_e('Delete Bounce', 'wp-mailinglist'); ?></a></span>
									<?php if (!empty($subscriber)) : ?>
										<span class="delete">| <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=delete&id=' . $subscriber -> id)) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this subscriber?', 'wp-mailinglist'); ?>')) { return false; }" class="submitdelete"><?php esc_html_e('Delete Subscriber', 'wp-mailinglist'); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
							<td>
								<?php echo esc_html( $bounce -> count); ?>
							</td>
							<td>
								<?php if (!empty($bounce -> status)) : ?>
									<?php echo esc_attr(wp_unslash($bounce -> status)); ?>
								<?php else : ?>
									<?php esc_html_e('None', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($bounce -> history_id)) : ?>
									<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $bounce -> history_id)) ?>"><?php echo esc_html($bounce -> history -> subject); ?></a>
								<?php else : ?>
									<?php esc_html_e('None', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
							<td>
								<abbr title="<?php echo esc_html( $bounce -> created); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($bounce -> created))); ?></abbr>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No bounces were found', 'wp-mailinglist'); ?></td>
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
							<option <?php echo (isset($_COOKIE[$this -> pre . 'bouncesperpage']) && $_COOKIE[$this -> pre . 'bouncesperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo wp_kses_post($s); ?>"><?php echo wp_kses_post($s); ?> <?php esc_html_e('bounces', 'wp-mailinglist'); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'bouncesperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'bouncesperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'bouncesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo esc_html($this -> pre); ?>bouncesperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
			}
		}
		</script>
	</form>
</div>