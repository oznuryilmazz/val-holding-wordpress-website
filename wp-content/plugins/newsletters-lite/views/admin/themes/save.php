<?php // phpcs:ignoreFile ?>
<!-- Save a Newsletter Template -->

<?php
	
$isSerialKeyValid = false;
if ($this -> ci_serial_valid()) {
    $isSerialKeyValid = true;
}

$screen = get_current_screen();
$page = $screen -> id;	
	
if (!isset($post))
{
    $post = array();
}

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h1><?php esc_html_e('Save a Template', 'wp-mailinglist'); ?></h1>
    
    <p>
    	<?php esc_html_e('This is a full HTML template and should contain at least <code>[newsletters_main_content]</code> somewhere.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('You may use any of the', 'wp-mailinglist'); ?> <a class="button button-secondary" href="" onclick="jQuery.colorbox({title:'<?php esc_html_e('Shortcodes/Variables', 'wp-mailinglist'); ?>', maxHeight:'80%', maxWidth:'80%', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php')) ?>?action=<?php echo esc_html($this -> pre); ?>setvariables&security=<?php echo esc_html( wp_create_nonce('setvariables')); ?>'}); return false;"> <?php esc_html_e('shortcodes/variables', 'wp-mailinglist'); ?></a> <?php esc_html_e('inside templates.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('Upload your images, stylesheets and other elements via FTP or the media uploader in WordPress.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('Please ensure that all links, images and other references use full, absolute URLs.', 'wp-mailinglist'); ?>
    </p>
    
    <form action="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=save" method="post" enctype="multipart/form-data" id="newsletters-themes-form">
    	<?php echo ( $Form -> hidden('Theme[id]')); ?>
    	<?php echo ( $Form -> hidden('Theme[name]')); ?>
    	
    	<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
                            <label class="screen-reader-text" for="title"></label>
							<input placeholder="<?php echo esc_attr(wp_unslash(__('Enter template title here', 'wp-mailinglist'))); ?>" onclick="jQuery('iframe#content_ifr').attr('tabindex', '2');" tabindex="1" id="title" autocomplete="off" type="text" name="Theme[title]" value="<?php echo esc_attr(wp_unslash($Html -> field_value('Theme[title]'))); ?>" />
                        </div>
                    </div>
                    
                    <p class="builder_tabs">
                        <label <?php echo ($Html -> field_value('Theme[type]') == "upload") ? 'class="active"' : ''; ?> ><input <?php echo ($Html -> field_value('Theme[type]') == "upload") ? 'checked="checked"' : ''; ?> onclick="newsletters_theme_change_type(this.value);" type="radio" name="Theme[type]" value="upload" id="Theme.type_upload" /> <?php _e('Upload an HTML File', 'wp-mailinglist'); ?></label>
                        <label <?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'class="active"' : ''; ?>><input <?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'checked="checked"' : ''; ?> onclick="newsletters_theme_change_type(this.value);" type="radio" name="Theme[type]" value="paste" id="Theme.type_paste" /> <?php _e('HTML Code', 'wp-mailinglist'); ?></label>
                        <?php if ($isSerialKeyValid) { ?>
                            <label <?php echo ($Html -> field_value('Theme[type]') == "builder" || $Html -> field_value('Theme[type]') == "") ? 'class="active"' : ''; ?>><input <?php echo ($Html -> field_value('Theme[type]') == "builder" || $Html -> field_value('Theme[type]') == "") ? 'checked="checked"' : ''; ?> onclick="newsletters_theme_change_type(this.value);" type="radio" name="Theme[type]" value="builder" id="Theme.type_builder" /> <?php _e('Drag & drop builder (Beta)', 'wp-mailinglist'); ?></label>
                        <?php } else { ?>
                            <label><input  type="radio" name="Theme[type]" value="builder" id="Theme.type_builder" /> <?php _e('Drag & drop builder (Beta)', 'wp-mailinglist'); ?> <a href="<?php echo admin_url('admin.php?page=' . $this -> sections -> lite_upgrade); ?>"  ><?php echo __('(PRO only)', 'wp-mailinglist'); ?></a></label>
                        <?php } ?>
                    </p>
                    
                    <?php /*<div id="Theme_type_builder_div" class="postarea edit-form-section" style="display:<?php echo ($Html -> field_value('Theme[type]') == "builder" || $Html -> field_value('Theme[type]') == "") ? 'block' : 'none'; ?>;">
	                    <div id="gjs">
		                    <?php if (empty($Theme -> data -> content)) : ?>
		                    	<?php 
			                    	
			                    ob_start();
			                    include($this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'builder-default.php');
								$content = ob_get_clean();
								echo wp_kses_post( wp_unslash($content))
								
								?>
		                    <?php else : ?>
		                    	<?php echo wp_kses_post( wp_unslash($Theme -> data -> content)) ?>
		                    <?php endif; ?>
	                    </div>
	                    
	                    <textarea name="Theme[builder]" style="display:none;" id="Theme_builder"></textarea>
						
						<?php echo esc_html( $Html -> field_error('Template[builder]')); ?>
                    </div>*/ ?>
                    <?php if ($isSerialKeyValid) { ?>

                        <div id="Theme_type_builder_div" class="postarea edit-form-section" style="display:<?php echo ($Html -> field_value('Theme[type]') == "builder" || $Html -> field_value('Theme[type]') == "") ? 'block' : 'none'; ?>;">
                            <div id="gjs">
                                <?php if (empty($Theme -> data -> content)) : ?>
                                    <?php

                                    ob_start();
                                    include($this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'builder-default.php');
                                    $content = ob_get_clean();
                                    echo stripslashes($content);

                                    ?>
                                <?php else : ?>
		                    	<?php echo $this -> getbodyandcss($Theme -> data -> content); ?>
                                <?php endif; ?>
                            </div>

                            <textarea name="Theme[builder]" style="display:none;" id="Theme_builder"></textarea>

                            <script type="text/javascript">
                                var editor = grapesjs.init({
                                    container : '#gjs',
                                    clearOnRender: true,
                                    fromElement: true,
                                    storageManager: {
                                        id: 'newsletters-template-<?php echo $Theme -> data -> id; ?>',
                                        autosave: true,
                                        stepsBeforeSave: 1,
                                        type: ''
                                    },
                                    assetManager: {
                                        upload: newsletters_ajaxurl + "action=newsletters_importmedia",
                                    },
                                    plugins: ['gjs-preset-newsletter', 'gjs-plugin-wordpress'],
                                    pluginsOpts: {
                                        'gjs-preset-newsletter': {
                                            modalTitleImport: 'Import template',
                                            // ... other options
                                        },
                                        'gjs-plugin-wordpress': {
                                            // options here...
                                        }
                                    }
                                });

                                jQuery(document).ready(function() {
                                    jQuery('#gjs .gjs-frame').attr('id', "gjs-frame");
                                });

                                jQuery('#newsletters-themes-form').submit(function(event) {
                                    checkBox = document.getElementById('Theme.type_builder');
                                    if(checkBox.checked) {
                                        var content = '<!doctype html><html lang="en"><head><meta charset="utf-8"><style>' + editor.getCss() + '</style></head><body>' + editor.getHtml() + '</body></html>';
                                        jQuery('textarea#Theme_builder').text(content);
                                    }
                                    return true;
                                });
                            </script>

                            <?php echo $Html -> field_error('Template[builder]'); ?>
                        </div>
                    <?php } ?>
                    <div id="Theme_type_paste_div" class="postarea edit-form-section" style="display:<?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'block' : 'none'; ?>;">
						<p>
							<button type="button" class="button button-secondary" id="thememediaupload" value="1">
								<i class="fa fa-image fa-fw"></i> <?php esc_html_e('Add Media', 'wp-mailinglist'); ?>
							</button>
						</p>
	        
				        <script type="text/javascript">
			        	jQuery(document).ready(function() {
							var file_frame;
							
							jQuery('#thememediaupload').on('click', function(event) {
								event.preventDefault();
								
								// If the media frame already exists, reopen it.
								if (file_frame) {
									file_frame.open();
									return;
								}
								
								// Create the media frame.
								file_frame = wp.media.frames.file_frame = wp.media({
									title: '<?php esc_html_e('Upload Media', 'wp-mailinglist'); ?>',
									button: {
										text: '<?php esc_html_e('Copy URL', 'wp-mailinglist'); ?>',
									},
									multiple: false  // Set to true to allow multiple files to be selected
								});
									
								// When an image is selected, run a callback.
								file_frame.on( 'select', function() {
									// We set multiple to false so only get one image from the uploader
									attachment = file_frame.state().get('selection').first().toJSON();
									
									// Do something with attachment.id and/or attachment.url here									
									window.prompt("Copy to clipboard: Ctrl+C, Enter", attachment.url);
								});
								
								// Finally, open the modal
								file_frame.open();
							});
			        	});
			        	</script>
				        
			        	<textarea name="Theme[paste]" class="widefat" contenteditable="true" id="Theme_paste" rows="10" cols="100%"><?php echo esc_attr(wp_unslash($Theme -> data -> content)); ?></textarea>
                        
                        <?php echo esc_html( $Html -> field_error('Template[content]')); ?>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                	<?php do_action('submitpage_box'); ?>
                	<?php do_meta_boxes($page, 'side', $post); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                	<?php do_meta_boxes($page, 'normal', $post); ?>
                    <?php do_meta_boxes($page, 'advanced', $post); ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">	
jQuery(document).ready(function() {
	newsletters_focus('#Theme\\.title');
	

	jQuery('textarea#Theme_paste').ckeditor({
    	fullPage: true,
		allowedContent: true,
		height: 500,
		entities: false,
		//extraPlugins: 'image2,codesnippet,tableresize',
		autoGrow_onStartup: true
	});

    jQuery(document).on('click','.builder_tabs input[type="radio"]',function() {
        jQuery('.builder_tabs label').removeClass('active');
        if(!jQuery(this).is(':checked')) {
            jQuery(this).parent().removeClass('active');
        } else {
            jQuery(this).parent().addClass('active');
        }
    });
});

function newsletters_theme_change_type(type) {
	if (type == "paste") {
		jQuery('#Theme_type_upload_div').hide();
		jQuery('#Theme_type_builder_div').hide();
		jQuery('#Theme_type_paste_div').show();
	} else if (type == "upload") {
		jQuery('#Theme_type_paste_div').hide();
		jQuery('#Theme_type_builder_div').hide();
		jQuery('#Theme_type_upload_div').show();
	} else if (type == "builder") {
		jQuery('#Theme_type_paste_div').hide();
		jQuery('#Theme_type_upload_div').hide();
		jQuery('#Theme_type_builder_div').show();
	}
	
}
</script>
