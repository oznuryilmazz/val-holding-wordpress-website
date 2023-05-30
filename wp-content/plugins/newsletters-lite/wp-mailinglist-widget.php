<?php
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Newsletters_Widget')) {
	class Newsletters_Widget extends WP_Widget {
		
		public function __construct() {
			$widget_ops = array('classname' => 'newsletters widget_newsletters wpml', 'description' => __('Subscribe form for your sidebar(s)', 'wp-mailinglist'));
			$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'newsletters');
			
			parent::__construct('newsletters', __(WPMAIL() -> name, 'wp-mailinglist'), $widget_ops, $control_ops);
		}
		
		public function widget($args, $instance) {	
			global $FieldsList, $Html, $Subscriber;
					
			extract($args);		
			echo wp_kses_post($before_widget);
			$title = apply_filters('widget_title', esc_html($instance['title']));
			if (!empty($title)) { echo wp_kses_post($before_title) . esc_html($title) . wp_kses_post($after_title); }
			$subtitle = apply_filters('widget_subtitle', $instance['subtitle']);
			if (!empty($subtitle)) { echo esc_html('<p>' . wp_kses_post($subtitle) . '</p>'); }
			$language = (empty($instance['language'])) ? false : $instance['language'];
			
			$widget = __CLASS__;
			
			$action = (WPMAIL() -> language_do()) ? WPMAIL() -> language_converturl(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $language) : sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
			$action = $Html -> retainquery(WPMAIL() -> pre . 'method=optin', $action) . '#' . $widget_id;
			
			$type = esc_html($instance['type']);
			if (empty($type) || $type == "list") {
				?>
				
				<div id="<?php echo esc_html($widget_id); ?>-wrapper" class="newsletters newsletters-widget-wrapper">
					<?php WPMAIL() -> render('widget', array('action' => $action, 'errors' => $Subscriber -> errors, 'widget' => $widget, 'args' => $args, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $this -> number), true, 'default'); ?>
				</div>
				
				<?php
			} elseif (!empty($type) && $type == "form") {
				$form_id = esc_html($instance['form']);
				$form = WPMAIL() -> Subscribeform() -> find(array('id' => $form_id));
				WPMAIL() -> render('subscribe', array('form' => $form, 'errors' => $Subscriber -> errors), true, 'default');
			}
			
			echo wp_kses_post($after_widget);
		}
		
		public function form($instance = null) {
			global $Html;
		
			if (class_exists('wpMail')) {									
				if (empty($instance)) {
					$instance['title'] = __('Stay up to date', 'wp-mailinglist');
					$instance['type'] = "list";
					$instance['list'] = "select";
					$instance['subtitle'] = __('Subscribe for email updates', 'wp-mailinglist');
					$instance['acknowledgement'] = __('Thank you for subscribing!', 'wp-mailinglist');
					$instance['ajax'] = "Y";
					$instance['scroll'] = 1;
					$instance['captcha'] = "N";
					$instance['button'] = __('Subscribe Now', 'wp-mailinglist');
				}
				
				if (WPMAIL() -> language_do()) {
					$languages = WPMAIL() -> language_getlanguages();
					
					foreach ($instance as $ikey => $ival) {
						$instance[$ikey] = WPMAIL() -> language_split($ival);
					}
				
					?>
					
					<div class="<?php echo wp_kses_post(WPMAIL() -> pre); ?>">
						<div id="languagetabs<?php echo esc_html($this -> number); ?>">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li>
										<a href="#languagetab<?php echo esc_attr($this -> number . $language); ?>"><?php echo wp_kses_post(WPMAIL() -> language_flag($language)); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="languagetab<?php echo esc_attr($this -> number . $language); ?>">
									<p>
										<label for="<?php echo esc_attr( $this -> get_field_id('title')); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Title:', 'wp-mailinglist'); ?></label>
										<?php echo ( $Html -> help(__('The title of your widget used as a heading for display to your users on the front.', 'wp-mailinglist'))); ?>
										<input class="widefat" name="<?php echo esc_attr( $this -> get_field_name('title')); ?>[<?php echo esc_attr($language); ?>]" value="<?php echo esc_attr(wp_unslash($instance['title'][$language])); ?>" id="<?php echo esc_attr( $this -> get_field_id('title')); ?>-<?php echo esc_attr($language); ?>" />
									</p>
									<p>
										<label for="<?php echo esc_attr( $this -> get_field_id('subtitle')); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Subtitle:', 'wp-mailinglist'); ?></label>
										<?php echo ( $Html -> help(__('Specify the subtitle to show below the title of the widget and above the fields.', 'wp-mailinglist'))); ?>
										<input type="text" name="<?php echo esc_attr( $this -> get_field_name('subtitle')); ?>[<?php echo esc_attr($language); ?>]" value="<?php echo esc_attr(wp_unslash($instance['subtitle'][$language])); ?>" id="<?php echo esc_attr( $this -> get_field_id('subtitle')); ?>-<?php echo esc_attr($language); ?>" class="widefat" />
									</p>
									<p>
										<label for="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_<?php echo esc_attr($language); ?>"><?php esc_html_e('Type:', 'wp-mailinglist'); ?></label>
										<label><input <?php echo (!empty($instance['type'][$language]) && $instance['type'][$language] == "form") ? 'checked="checked"' : ''; ?> onclick="jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_<?php echo esc_attr($language); ?>_div').show(); jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_<?php echo esc_attr($language); ?>_div').hide();" type="radio" name="<?php echo esc_attr( $this -> get_field_name('type')); ?>[<?php echo esc_attr($language); ?>]" value="form" id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_<?php echo esc_attr($language); ?>" /> <?php esc_html_e('Subscribe Form', 'wp-mailinglist'); ?></label>
										<label><input <?php echo (empty($instance['type'][$language]) || $instance['type'][$language] == "list") ? 'checked="checked"' : ''; ?> onclick="jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_<?php echo esc_attr($language); ?>_div').hide(); jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_<?php echo esc_attr($language); ?>_div').show();" type="radio" name="<?php echo esc_attr( $this -> get_field_name('type')); ?>[<?php echo esc_attr($language); ?>]" value="list" id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_<?php echo esc_attr($language); ?>" /> <?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></label>
									</p>
									
									<!-- Subscribe Forms -->
									<div id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_<?php echo esc_attr($language); ?>_div" style="display:<?php echo (!empty($instance['type'][$language]) && $instance['type'][$language] == "form") ? 'block' : 'none'; ?>;">
										<p>
											<label for="<?php echo esc_attr( $this -> get_field_id('form')); ?>_<?php echo esc_attr($language); ?>"><?php esc_html_e('Subscribe Form:', 'wp-mailinglist'); ?></label>
											<?php if ($forms = WPMAIL() -> Subscribeform() -> select()) : ?>
												<select class="widefat" name="<?php echo esc_attr( $this -> get_field_name('form')); ?>[<?php echo esc_attr($language); ?>]" id="<?php echo esc_attr( $this -> get_field_id('form')); ?>_<?php echo esc_attr($language); ?>">
													<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
													<?php foreach ($forms as $form_id => $form_title) : ?>
														<option <?php echo (!empty($instance['form'][$language]) && $instance['form'][$language] == $form_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $form_id); ?>"><?php echo esc_html( $form_title); ?></option>
													<?php endforeach; ?>
												</select>
											<?php else : ?>
												<br/><span class="newsletters_error"><?php esc_html_e('No forms are available', 'wp-mailinglist'); ?></span>
											<?php endif; ?>
										</p>
										
										<p>
											<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . WPMAIL() -> sections -> forms)) ?>"><?php esc_html_e('Manage Subscribe Forms', 'wp-mailinglist'); ?></a>
										</p>
									</div>
									
									<!-- Mailing List/s -->
									<div id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_<?php echo esc_attr($language); ?>_div" style="display:<?php echo (empty($instance['type'][$language]) || $instance['type'][$language] == "list") ? 'block' : 'none'; ?>;">
										<p>
											<label for="<?php echo esc_attr( $this -> get_field_id('list')); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Mailing List:', 'wp-mailinglist'); ?></label>
											<?php echo ( $Html -> help(__('Choose the list(s) that users will subscribe to. You can choose either a single, specific list or choose to have a select drop down of lists or checkboxes lists.', 'wp-mailinglist'))); ?>
											<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#<?php echo esc_attr( $this -> get_field_id('lists')); ?>-<?php echo esc_attr($language); ?>').show(); } else { jQuery('#<?php echo esc_attr( $this -> get_field_id('lists')); ?>-<?php echo esc_attr($language); ?>').hide(); }" class="widefat" name="<?php echo esc_attr( $this -> get_field_name('list')); ?>[<?php echo esc_attr($language); ?>]" id="<?php echo esc_attr( $this -> get_field_id('list')); ?>-<?php echo esc_attr($language); ?>">
												<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
												<optgroup label="<?php esc_html_e('Multiple', 'wp-mailinglist'); ?>">
													<option <?php echo ($instance['list'][$language] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Lists (no choice)', 'wp-mailinglist'); ?></option>
													<option <?php echo ($instance['list'][$language] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php esc_html_e('Select Drop Down', 'wp-mailinglist'); ?></option>
													<option <?php echo ($instance['list'][$language] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php esc_html_e('Checkbox List', 'wp-mailinglist'); ?></option>
												</optgroup>
												<?php if ($lists = WPMAIL() -> Mailinglist -> select(true)) : ?>
													<optgroup label="<?php esc_html_e('Specific', 'wp-mailinglist'); ?>">
														<?php foreach ($lists as $list_id => $list_title) : ?>
															<option <?php echo ($instance['list'][$language] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($list_id); ?>"><?php echo esc_html($list_title); ?></option>
														<?php endforeach; ?>
													</optgroup>
												<?php endif; ?>
											</select>
										</p>
										<div id="<?php echo esc_attr( $this -> get_field_id('lists')); ?>-<?php echo esc_attr($language); ?>" style="display:<?php echo ($instance['list'][$language] == "select" || $instance['list'][$language] == "checkboxes") ? 'block' : 'none'; ?>;">
											<p>
												<label for="<?php echo esc_attr( $this -> get_field_id('lists')); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Lists', 'wp-mailinglist'); ?></label>
												<?php echo ( $Html -> help(__('If you are using a select drop down or checkboxes list, you can now specify which lists should be included. Use comma separated list IDs eg. 2,3. To use all non-private mailing lists, leave this empty.', 'wp-mailinglist'))); ?>
												<input type="text" name="<?php echo esc_attr( $this -> get_field_name('lists')); ?>[<?php echo esc_attr($language); ?>]" value="<?php echo esc_attr(wp_unslash($instance['lists'][$language])); ?>" id="<?php echo esc_attr( $this -> get_field_id('lists')); ?>-<?php echo esc_attr($language); ?>" class="widefat" />
											</p>
										</div>
										<p>
											<label for="<?php echo esc_attr( $this -> get_field_id('acknowledgement')); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Success Message:', 'wp-mailinglist'); ?></label>
											<?php echo ( $Html -> help(__('The success message is the acknowledgement displayed to the user after successfully subscribing.', 'wp-mailinglist'))); ?>
											<input type="text" name="<?php echo esc_attr( $this -> get_field_name('acknowledgement')); ?>[<?php echo esc_attr($language); ?>]" value="<?php echo esc_attr(wp_unslash($instance['acknowledgement'][$language])); ?>" id="<?php echo esc_attr( $this -> get_field_id('acknowledgement')); ?>-<?php echo esc_attr($language); ?>" class="widefat" />
										</p>
										<p>
											<?php $captcha_type = WPMAIL() -> get_option('captcha_type'); ?>										
											<label for="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-<?php echo esc_attr($language); ?>-N"><?php esc_html_e('Security Captcha:', 'wp-mailinglist'); ?></label>
											<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($captcha_type) && $captcha_type != "none" && $instance['captcha'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('captcha')); ?>[<?php echo esc_attr($language); ?>]" value="Y" id="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-<?php echo esc_attr($language); ?>-Y" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
											<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (empty($captcha_type) || $captcha_type == "none" || $instance['captcha'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('captcha')); ?>[<?php echo esc_attr($language); ?>]" value="N" id="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-<?php echo esc_attr($language); ?>-N" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
											<?php echo ( $Html -> help(sprintf(__('Display a security captcha image on the subscribe form to prevent spam submissions. It is simply a "human" check to stop bots from subscribing. Configure a captcha under %s > Configuration > System > Captcha accordingly.', 'wp-mailinglist'), $this -> name))); ?>
											<?php if (empty($captcha_type) || $captcha_type == "none") : ?>
												<br/><small class="newsletters_error"><?php echo wp_kses_post(sprintf(__('Please configure a security captcha under %s > Configuration > System > Captcha in order to use this.', 'wp-mailinglist'), $this -> name)); ?></small>
											<?php endif; ?>
										</p>
										<p>
											<label for="<?php echo esc_attr( $this -> get_field_id('ajax')); ?>-<?php echo esc_attr($language); ?>-Y"><?php esc_html_e('Ajax Features:', 'wp-mailinglist'); ?></label>
											<label><input <?php echo ($instance['ajax'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('ajax')); ?>[<?php echo esc_attr($language); ?>]" value="Y" id="<?php echo esc_attr( esc_attr( $this -> get_field_id('ajax'))); ?>-<?php echo esc_attr($language); ?>-Y" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
											<label><input <?php echo ($instance['ajax'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('ajax')); ?>[<?php echo esc_attr($language); ?>]" value="N" id="<?php echo esc_attr( esc_attr( $this -> get_field_id('ajax'))); ?>-<?php echo esc_attr($language); ?>-N" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
											<?php echo ( $Html -> help(__('Turn on/off Ajax for the subscribe form. If you turn Ajax on, the subscribe form will submit without any page refresh and it is much quicker. Turning it off will generate a page refresh as the user submits the form to subscribe.', 'wp-mailinglist'))); ?>
										</p>
										<p>
											<label><input <?php echo (!empty($instance['scroll'][$language])) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo esc_attr( $this -> get_field_name('scroll')); ?>[<?php echo esc_attr($language); ?>]" value="1" id="<?php echo esc_attr( esc_attr( $this -> get_field_id('scroll'))); ?>-<?php echo esc_attr($language); ?>" /> <?php esc_html_e('Scroll to subscribe form', 'wp-mailinglist'); ?></label>
										</p>
										<p>
											<label for="<?php echo esc_attr( esc_attr( $this -> get_field_id('button'))); ?>-<?php echo esc_attr($language); ?>"><?php esc_html_e('Button Text:', 'wp-mailinglist'); ?></label>
											<?php echo ( $Html -> help(__('The text to display on the subscribe button at the bottom of the subscribe form.', 'wp-mailinglist'))); ?>
											<input type="text" name="<?php echo esc_attr( $this -> get_field_name('button')); ?>[<?php echo esc_attr($language); ?>]" value="<?php echo esc_attr(wp_unslash($instance['button'][$language])); ?>" id="<?php echo esc_attr( esc_attr( $this -> get_field_id('button'))); ?>-<?php echo esc_attr($language); ?>" class="widefat" />
										</p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
							
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#languagetabs<?php echo esc_attr($this -> number); ?>').tabs();
						}
					});
					</script>
					
					<?php
				} else {
					?>
					
					<p>
						<label for="<?php echo esc_attr( esc_attr( $this -> get_field_id('title'))); ?>"><?php esc_html_e('Title:', 'wp-mailinglist'); ?></label>
						<?php echo ( $Html -> help(__('The title of your widget used as a heading for display to your users on the front.', 'wp-mailinglist'))); ?>
						<input class="widefat" name="<?php echo esc_attr( $this -> get_field_name('title')); ?>" value="<?php echo esc_attr(wp_unslash($instance['title'])); ?>" id="<?php echo esc_attr( esc_attr( $this -> get_field_id('title'))); ?>" />
					</p>
					<p>
						<label for="<?php echo esc_attr( $this -> get_field_id('subtitle')); ?>"><?php esc_html_e('Subtitle:', 'wp-mailinglist'); ?></label>
						<?php echo ( $Html -> help(__('Specify the subtitle to show below the title of the widget and above the fields.', 'wp-mailinglist'))); ?>
						<input type="text" name="<?php echo esc_attr( $this -> get_field_name('subtitle')); ?>" value="<?php echo esc_attr(wp_unslash($instance['subtitle'])); ?>" id="<?php echo esc_attr( $this -> get_field_id('subtitle')); ?>" class="widefat" />
					</p>
					<p>
						<label for="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form"><?php esc_html_e('Type:', 'wp-mailinglist'); ?></label>
						<label><input <?php echo (empty($instance['type']) || $instance['type'] == "form") ? 'checked="checked"' : ''; ?> onclick="jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_div').show(); jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_div').hide();" type="radio" name="<?php echo esc_attr( $this -> get_field_name('type')); ?>" value="form" id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form" /> <?php esc_html_e('Subscribe Form', 'wp-mailinglist'); ?></label>
						<label><input <?php echo (!empty($instance['type']) && $instance['type'] == "list") ? 'checked="checked"' : ''; ?> onclick="jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_div').hide(); jQuery('#<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_div').show();" type="radio" name="<?php echo esc_attr( $this -> get_field_name('type')); ?>" value="list" id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_list" /> <?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></label>
					</p>
					
					<!-- Subscribe Forms -->
					<div id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_form_div" style="display:<?php echo (empty($instance['type']) || $instance['type'] == "form") ? 'block' : 'none'; ?>;">
						<p>
							<label for="<?php echo esc_attr( $this -> get_field_id('form')); ?>"><?php esc_html_e('Subscribe Form:', 'wp-mailinglist'); ?></label>
							<?php if ($forms = WPMAIL() -> Subscribeform() -> select()) : ?>
								<select class="widefat" name="<?php echo esc_attr( $this -> get_field_name('form')); ?>" id="<?php echo esc_attr( $this -> get_field_id('form')); ?>">
									<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
									<?php foreach ($forms as $form_id => $form_title) : ?>
										<option <?php echo (!empty($instance['form']) && $instance['form'] == $form_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($form_id); ?>"><?php echo esc_html( $form_title); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<br/><span class="newsletters_error"><?php esc_html_e('No forms are available', 'wp-mailinglist'); ?></span>
							<?php endif; ?>
						</p>
						
						<p>
							<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . WPMAIL() -> sections -> forms)) ?>"><?php esc_html_e('Manage Subscribe Forms', 'wp-mailinglist'); ?></a>
						</p>
					</div>
					
					<!-- Mailing List/s -->
					<div id="<?php echo esc_attr( $this -> get_field_id('type')); ?>_list_div" style="display:<?php echo (!empty($instance['type']) && $instance['type'] == "list") ? 'block' : 'none'; ?>;">
						<p>
							<label for="<?php echo esc_attr( $this -> get_field_id('list')); ?>"><?php esc_html_e('Mailing List:', 'wp-mailinglist'); ?></label>
							<?php echo ( $Html -> help(__('Choose the list(s) that users will subscribe to. You can choose either a single, specific list or choose to have a select drop down of lists or checkboxes lists.', 'wp-mailinglist'))); ?>
							<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#<?php echo esc_attr( $this -> get_field_id('lists')); ?>').show(); } else { jQuery('#<?php echo esc_attr( $this -> get_field_id('lists')); ?>').hide(); }" class="widefat" name="<?php echo esc_attr( $this -> get_field_name('list')); ?>" id="<?php echo esc_attr( $this -> get_field_id('list')); ?>">
								<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
								<optgroup label="<?php esc_html_e('Multiple', 'wp-mailinglist'); ?>">
									<option <?php echo ($instance['list'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Lists (no choice)', 'wp-mailinglist'); ?></option>
									<option <?php echo ($instance['list'] == "select") ? 'selected="selected"' : ''; ?> value="select"><?php esc_html_e('Select Drop Down', 'wp-mailinglist'); ?></option>
									<option <?php echo ($instance['list'] == "checkboxes") ? 'selected="selected"' : ''; ?> value="checkboxes"><?php esc_html_e('Checkbox List', 'wp-mailinglist'); ?></option>
								</optgroup>
								<?php if ($lists = WPMAIL() -> Mailinglist -> select(true)) : ?>
									<optgroup label="<?php esc_html_e('Specific', 'wp-mailinglist'); ?>">
										<?php foreach ($lists as $list_id => $list_title) : ?>
											<option <?php echo ($instance['list'] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($list_id); ?>"><?php echo esc_attr(esc_html($list_title)); ?></option>
										<?php endforeach; ?>
									</optgroup>
								<?php endif; ?>
							</select>
						</p>
						<div id="<?php echo esc_attr( $this -> get_field_id('lists')); ?>" style="display:<?php echo ($instance['list'] == "select" || $instance['list'] == "checkboxes") ? 'block' : 'none'; ?>;">
							<p>
								<label for="<?php echo esc_attr( $this -> get_field_id('lists')); ?>"><?php esc_html_e('Lists', 'wp-mailinglist'); ?></label>
								<?php echo ( $Html -> help(__('If you are using a select drop down or checkboxes list, you can now specify which lists should be included. Use comma separated list IDs eg. 2,3. To use all non-private mailing lists, leave this empty.', 'wp-mailinglist'))); ?>
								<input type="text" name="<?php echo esc_attr( $this -> get_field_name('lists')); ?>" value="<?php echo esc_attr(wp_unslash($instance['lists'])); ?>" id="<?php echo esc_attr( $this -> get_field_id('lists')); ?>" class="widefat" />
							</p>
						</div>
						<p>
							<label for="<?php echo esc_attr( $this -> get_field_id('acknowledgement')); ?>"><?php esc_html_e('Success Message:', 'wp-mailinglist'); ?></label>
							<?php echo ( $Html -> help(__('The success message is the acknowledgement displayed to the user after successfully subscribing.', 'wp-mailinglist'))); ?>
							<input type="text" name="<?php echo esc_attr( $this -> get_field_name('acknowledgement')); ?>" value="<?php echo esc_attr(wp_unslash($instance['acknowledgement'])); ?>" id="<?php echo esc_attr( $this -> get_field_id('acknowledgement')); ?>" class="widefat" />
						</p>
						<p>
							<?php $captcha_type = WPMAIL() -> get_option('captcha_type'); ?>
							<label for="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-N"><?php esc_html_e('Security Captcha:', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($captcha_type) && $instance['captcha'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('captcha')); ?>" value="Y" id="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-Y" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'disabled="disabled"' : ''; ?> <?php echo (empty($captcha_type) || $captcha_type == "none" || $instance['captcha'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('captcha')); ?>" value="N" id="<?php echo esc_attr( $this -> get_field_id('captcha')); ?>-N" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
							<?php echo ( $Html -> help(__('Display a security captcha image on the subscribe form to prevent spam submissions.', 'wp-mailinglist'))); ?>
						</p>
						<p>
							<label for="<?php echo esc_attr( $this -> get_field_id('ajax')); ?>-Y"><?php esc_html_e('Ajax Features:', 'wp-mailinglist'); ?></label>
							<label><input <?php echo ($instance['ajax'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('ajax')); ?>" value="Y" id="<?php echo esc_attr( $this -> get_field_id('ajax')); ?>-Y" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
							<label><input <?php echo ($instance['ajax'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo esc_attr( $this -> get_field_name('ajax')); ?>" value="N" id="<?php echo esc_attr( $this -> get_field_id('ajax')); ?>-N" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
							<?php echo ( $Html -> help(__('Turn on/off Ajax for the subscribe form. If you turn Ajax on, the subscribe form will submit without any page refresh and it is much quicker. Turning it off will generate a page refresh as the user submits the form to subscribe.', 'wp-mailinglist'))); ?>
						</p>
						<p>
							<label><input <?php echo (!empty($instance['scroll'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo esc_attr( $this -> get_field_name('scroll')); ?>" value="1" id="<?php echo esc_attr( $this -> get_field_id('scroll')); ?>" /> <?php esc_html_e('Scroll to subscribe form', 'wp-mailinglist'); ?></label>
						</p>
						<p>
							<label for="<?php echo esc_attr( $this -> get_field_id('button')); ?>"><?php esc_html_e('Button Text:', 'wp-mailinglist'); ?></label>
							<?php echo ( $Html -> help(__('The text to display on the subscribe button at the bottom of the subscribe form.', 'wp-mailinglist'))); ?>
							<input type="text" name="<?php echo esc_attr( $this -> get_field_name('button')); ?>" value="<?php echo esc_attr(wp_unslash($instance['button'])); ?>" id="<?php echo esc_attr( $this -> get_field_id('button')); ?>" class="widefat" />
						</p>
					</div>
					
					<?php
				}
				
				?>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					if (jQuery.isFunction(jQuery.fn.tooltip)) {
						jQuery(".wpmlhelp a").tooltip();
					}
				});
				</script>
				
				<?php
			}
		}
		
		public function update($new_instance, $old_instance) {		
			$instance = array();
				
			if (class_exists('wpMail')) {
				if (WPMAIL() -> language_do()) {
					foreach ($new_instance as $nikey => $nival) {
						$instance[$nikey] = $nival;
					
						if (is_array($new_instance[$nikey])) {
							$instance[$nikey] = WPMAIL() -> language_join($nival);
						}
					}
				} else {
					$instance = $new_instance;
				}
			}
			
			return $instance;
		}
	}
}

?>