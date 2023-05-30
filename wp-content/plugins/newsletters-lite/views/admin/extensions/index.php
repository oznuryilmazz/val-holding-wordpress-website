<?php // phpcs:ignoreFile ?>

<div id="<?php echo esc_html( $this -> sections -> extensions); ?>" class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Manage Extensions', 'wp-mailinglist'); ?></h2>
    <?php $this -> render('extensions' . DS . 'navigation', array('section' => $this -> sections -> extensions), true, 'admin'); ?>
    <p><?php esc_html_e('These are extensions which extend the functionality of the Newsletter plugin.', 'wp-mailinglist'); ?></p>
    
    <?php if (!empty($this -> extensions)) : ?>
    	<table class="widefat">
        	<thead>
            	<tr>
                	<th colspan="2"><?php esc_html_e('Extension Name', 'wp-mailinglist'); ?></th>
                    <th><?php esc_html_e('Extension Status', 'wp-mailinglist'); ?></th>
                </tr>
            </thead>
            <tfoot>
            	<tr>
                	<th colspan="2"><?php esc_html_e('Extension Name', 'wp-mailinglist'); ?></th>
                    <th><?php esc_html_e('Extension Status', 'wp-mailinglist'); ?></th>
                </tr>
            </tfoot>
        	<tbody class="<?php echo esc_html( $this -> sections -> extensions); ?>-list">
            	<?php $class = ''; ?>
            	<?php foreach ($this -> extensions as $extension) : ?>                
                	<?php
					
					if ($this -> is_plugin_active($extension['slug'], false)) {
						$status = 2;	
					} elseif ($this -> is_plugin_active($extension['slug'], true)) {
						$status = 1;
					} else {
						$status = 0;
					}
					
					$context = 'all';
					$s = '';
					$page = 1;
					$path = $extension['plugin_name'] . DS . $extension['plugin_file'];
					$img = (empty($extension['image'])) ? $this -> url() . '/images/extensions/' . $extension['slug'] . '.png' : $extension['image'];
					
					?>
                
                	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                		<th style="width:85px;">
                			<a href="<?php echo esc_html( $extension['link']); ?>" target="_blank" title="<?php echo esc_attr($extension['name']); ?>" style="border:none;">
	                			<?php if (!empty($extension['icon'])) : ?>
	                				<i class="<?php echo esc_attr(wp_unslash($extension['icon'])); ?>"></i>
	                			<?php else : ?>
                					<img class="extensionicon" style="border:none; width:75px; height:75px;" border="0" src="<?php echo $img; ?>" alt="<?php echo esc_html( $extension['slug']); ?>" />
                				<?php endif; ?>
                			</a>
                		</th>
                    	<th>
							<a href="<?php echo esc_url_raw($extension['link']); ?>" target="_blank" title="<?php echo esc_attr($extension['name']); ?>" class="row-title newsletters-extension-name"><?php echo esc_html( $extension['name']); ?></a>
							<br/><small class="newsletters-extension-description howto"><?php echo esc_html( $extension['description']); ?></small>
                            <div class="row-actions">
                            	<?php 
								
								switch ($status) {
									case 0	:
										if (apply_filters('newsletters_whitelabel', true)) {
											?>
	                                        
	                                        <span class="edit"><a href="<?php echo esc_url_raw($extension['link']); ?>" target="_blank"><?php esc_html_e('Get this extension now', 'wp-mailinglist'); ?></a></span>
	                                        
	                                        <?php
		                                }
										break;
									case 1	:
										if (current_user_can('activate_plugins')) {
											?>
	                                        
	                                        <span class="edit"><?php echo ( $Html -> link(__('Activate', 'wp-mailinglist'), wp_nonce_url('?page=' . $this -> sections -> extensions . '&method=activate&plugin=' . plugin_basename($path), 'newsletters_extension_activate_' . plugin_basename($path)))); ?></span>
	                                        
	                                        <?php
	                                    }
										break;
									case 2	:
										if (current_user_can('activate_plugins')) {
											?>
	                                        
	                                        <span class="delete"><?php echo ( $Html -> link(__('Deactivate', 'wp-mailinglist'), wp_nonce_url('?page=' . $this -> sections -> extensions . '&method=deactivate&plugin=' . plugin_basename($path), 'newsletters_extension_deactivate_' . plugin_basename($path)), array('class' => "submitdelete"))); ?></span>
	                                        
	                                        <?php
		                                }
										break;	
								}
								
								if (!empty($extension['settings']) && current_user_can('newsletters_extensions_settings')) {
									?>| <span class="edit"><?php echo ( $Html -> link(__('Settings', 'wp-mailinglist'), $extension['settings'])); ?></span><?php
								}
								
								?>
                            </div>
                        </th>
                        <th>
                        	<?php 
							
							switch ($status) {
								case 0			:
									?>
									
									<span class="newsletters_error"><?php esc_html_e('Not Installed', 'wp-mailinglist'); ?></span>
									<p><?php echo ( $Html -> link(__('Get it now', 'wp-mailinglist'), $extension['link'], array('target' => "_blank", 'class' => "button button-primary"))); ?></p>
									
									<?php
									break;
								case 1			:
									?><span class="newsletters_error"><?php esc_html_e('Installed but Inactive', 'wp-mailinglist'); ?></span>
									<p><a href="<?php echo wp_nonce_url('admin.php?page=' . $this -> sections -> extensions . '&method=activate&plugin=' . plugin_basename($path), 'newsletters_extension_activate_' . plugin_basename($path)); ?>" class="button"><?php esc_html_e('Activate', 'wp-mailinglist'); ?></a></p><?php
									break;
								case 2			:
									?><span class="<?php echo esc_html($this -> pre); ?>success"><?php esc_html_e('Installed and Active', 'wp-mailinglist'); ?></span><?php
									break;	
							}
							
							?>
                        </th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <ul class="pagination"></ul>
    <?php else : ?>
    	<p class="newsletters_error"><?php esc_html_e('No extensions found.', 'wp-mailinglist'); ?></p>
    <?php endif; ?>
	
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var options = {
			listClass: '<?php echo esc_html( $this -> sections -> extensions); ?>-list',
			valueNames: ['newsletters-extension-name', 'newsletters-extension-description'],
			searchClass: '<?php echo esc_html( $this -> sections -> extensions); ?>-search'
		};
		
		var extensionsList = new List('<?php echo esc_html( $this -> sections -> extensions); ?>', options);
	});
	</script>
</div>